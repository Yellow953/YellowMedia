<?php

namespace App\Http\Controllers;

use App\Models\GeneratedImage;
use App\Models\MetaAdAccount;
use App\Models\MetaCampaign;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $tenantId = $user->tenant_id;

        $steps = [
            'account'  => true,
            'meta'     => MetaAdAccount::where('tenant_id', $tenantId)->exists(),
            'image'    => GeneratedImage::where('tenant_id', $tenantId)->where('status', 'done')->exists(),
            'campaign' => MetaCampaign::where('tenant_id', $tenantId)->exists(),
        ];

        $allDone = $steps['meta'] && $steps['image'] && $steps['campaign'];

        return view('onboarding.index', compact('steps', 'allDone'));
    }

    public function complete(Request $request)
    {
        $request->user()->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard')->with('success', 'Setup complete. Welcome to YellowMedia!');
    }
}
