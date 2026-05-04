<?php

namespace App\Http\Controllers;

use App\Models\GeneratedImage;
use App\Models\MetaAdAccount;
use App\Models\MetaCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $tenant   = $user->tenant;
        $accounts = MetaAdAccount::where('tenant_id', $user->tenant_id)->get();

        $stats = [
            'images'    => GeneratedImage::where('tenant_id', $user->tenant_id)->where('status', 'done')->count(),
            'campaigns' => MetaCampaign::where('tenant_id', $user->tenant_id)->count(),
            'accounts'  => $accounts->count(),
        ];

        return view('settings.index', compact('user', 'tenant', 'accounts', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $request->user()->update(['name' => $request->name]);

        return redirect()->route('settings.index', ['tab' => 'profile'])
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('tab', 'security');
        }

        $request->user()->update(['password' => Hash::make($request->password)]);

        return redirect()->route('settings.index', ['tab' => 'security'])
            ->with('success', 'Password changed successfully.');
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

        return redirect()->route('settings.index', ['tab' => 'connections'])
            ->with('success', 'Ad account removed.');
    }
}
