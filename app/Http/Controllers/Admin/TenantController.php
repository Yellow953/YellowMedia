<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount('users')->latest()->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'plan' => 'required|in:free,starter,pro',
        ]);

        Tenant::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'plan' => $request->plan,
            'is_active' => true,
        ]);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant created.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('users');

        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'plan' => 'required|in:free,starter,pro',
            'is_active' => 'boolean',
        ]);

        $tenant->update($request->only('name', 'plan', 'is_active'));

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant deleted.');
    }
}
