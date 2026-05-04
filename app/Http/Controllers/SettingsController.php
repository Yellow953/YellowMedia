<?php

namespace App\Http\Controllers;

use App\Models\MetaAdAccount;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function adAccounts(Request $request)
    {
        $accounts = MetaAdAccount::where('tenant_id', $request->user()->tenant_id)->get();

        return view('settings.ad-accounts', compact('accounts'));
    }

    public function removeAdAccount(int $id, Request $request)
    {
        MetaAdAccount::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id)
            ->delete();

        return back()->with('success', 'Ad account removed.');
    }
}
