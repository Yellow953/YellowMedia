<?php

namespace App\Http\Controllers;

use App\Models\BrandProfile;
use App\Models\GeneratedImage;
use App\Models\MetaAdAccount;
use App\Models\MetaCampaign;
use App\Services\GeminiTextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user         = $request->user();
        $tenant       = $user->tenant;
        $accounts     = MetaAdAccount::where('tenant_id', $user->tenant_id)->get();
        $brandProfile = BrandProfile::where('tenant_id', $user->tenant_id)->first();

        $stats = [
            'images'    => GeneratedImage::where('tenant_id', $user->tenant_id)->where('status', 'done')->count(),
            'campaigns' => MetaCampaign::where('tenant_id', $user->tenant_id)->count(),
            'accounts'  => $accounts->count(),
        ];

        return view('settings.index', compact('user', 'tenant', 'accounts', 'stats', 'brandProfile'));
    }

    public function saveBrandProfile(Request $request)
    {
        $request->validate([
            'business_name'        => 'nullable|string|max:255',
            'business_description' => 'nullable|string|max:1000',
            'target_audience'      => 'nullable|string|max:255',
            'tone'                 => 'nullable|string|max:50',
            'brand_colors'         => 'nullable|string|max:255',
            'hashtags'             => 'nullable|string|max:500',
            'instagram_handle'     => 'nullable|string|max:100',
            'sample_captions'      => 'nullable|string|max:5000',
            'voice_summary'        => 'nullable|string|max:2000',
            'content_pillars'      => 'nullable|string|max:500',
        ]);

        $tenantId = $request->user()->tenant_id;

        $pillars = [];
        if ($request->filled('content_pillars')) {
            $pillars = array_filter(array_map('trim', explode(',', $request->input('content_pillars'))));
        }

        BrandProfile::updateOrCreate(
            ['tenant_id' => $tenantId],
            [
                'business_name'        => $request->input('business_name'),
                'business_description' => $request->input('business_description'),
                'target_audience'      => $request->input('target_audience'),
                'tone'                 => $request->input('tone', 'professional'),
                'brand_colors'         => $request->input('brand_colors'),
                'hashtags'             => $request->input('hashtags'),
                'instagram_handle'     => $request->input('instagram_handle'),
                'sample_captions'      => $request->input('sample_captions'),
                'voice_summary'        => $request->input('voice_summary'),
                'content_pillars'      => array_values($pillars),
            ]
        );

        return redirect()->route('settings.index', ['tab' => 'brand'])
            ->with('success', 'Brand profile saved.');
    }

    public function analyzeVoice(Request $request, GeminiTextService $gemini)
    {
        $request->validate([
            'sample_captions' => 'required|string|min:50',
            'business_name'   => 'nullable|string|max:255',
        ]);

        $summary = $gemini->analyzeVoice(
            $request->input('sample_captions'),
            $request->input('business_name', 'this business')
        );

        return response()->json(['voice_summary' => $summary]);
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
