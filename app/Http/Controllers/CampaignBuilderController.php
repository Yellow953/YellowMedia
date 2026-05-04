<?php

namespace App\Http\Controllers;

use App\Jobs\ReviewCampaignsJob;
use App\Models\GeneratedImage;
use App\Models\MetaAdAccount;
use App\Models\MetaCampaign;
use App\Services\GeminiTextService;
use App\Services\MetaAdsService;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;

class CampaignBuilderController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = MetaCampaign::where('tenant_id', $request->user()->tenant_id)
            ->with('adAccount')
            ->latest()
            ->paginate(15);

        return view('campaign-builder.index', compact('campaigns'));
    }

    public function create(Request $request)
    {
        $adAccounts = MetaAdAccount::where('tenant_id', $request->user()->tenant_id)
            ->where('is_active', true)
            ->get();

        $images = GeneratedImage::where('tenant_id', $request->user()->tenant_id)
            ->where('status', 'done')
            ->latest()
            ->get();

        return view('campaign-builder.create', compact('adAccounts', 'images'));
    }

    public function store(Request $request, PlanLimitService $limits)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'objective' => 'required|string',
            'ad_account_id' => 'required|exists:meta_ad_accounts,id',
        ]);

        $user = $request->user();
        if (! $limits->canCreateCampaign($user->tenant)) {
            $plan = $user->tenant->planLimits();
            return back()->withErrors(['limit' => "You've reached the {$plan['max_campaigns']}-campaign limit on the {$plan['name']} plan."])->withInput();
        }

        $campaign = MetaCampaign::create([
            'tenant_id' => $request->user()->tenant_id,
            'ad_account_id' => $request->ad_account_id,
            'campaign_id' => '',
            'name' => $request->name,
            'objective' => $request->objective,
            'status' => 'DRAFT',
            'budget' => $request->budget,
        ]);

        return redirect()->route('campaigns.show', $campaign->id)->with('success', 'Campaign created.');
    }

    public function show(int $id, Request $request)
    {
        $campaign = MetaCampaign::where('tenant_id', $request->user()->tenant_id)
            ->with(['adSets.ads.image', 'actionLog.appliedBy'])
            ->findOrFail($id);

        return view('campaign-builder.show', compact('campaign'));
    }

    public function generateCaption(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'objective' => 'required|string',
            'tone' => 'nullable|string',
            'language' => 'nullable|string',
        ]);

        $service = app(GeminiTextService::class);
        $caption = $service->generateCaption(
            businessName: $request->user()->tenant->name ?? 'Our Business',
            topic: $request->topic,
            objective: $request->objective,
            tone: $request->tone ?? 'professional',
            language: $request->language ?? 'English'
        );

        return response()->json(['caption' => $caption]);
    }

    public function launch(int $id, Request $request)
    {
        $campaign = MetaCampaign::where('tenant_id', $request->user()->tenant_id)
            ->with('adSets.ads', 'adAccount')
            ->findOrFail($id);

        $service = app(MetaAdsService::class);
        $service->launchCampaign($campaign);

        return redirect()->route('campaigns.show', $id)->with('success', 'Campaign launched on Meta Ads.');
    }

    public function review(int $id, Request $request)
    {
        $campaign = MetaCampaign::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        ReviewCampaignsJob::dispatch($campaign);

        return back()->with('success', 'AI review started. Check back shortly.');
    }

    public function applyAction(int $id, Request $request)
    {
        // Handled by ReviewCampaignsJob / MetaAdsService — stub for now
        return back()->with('success', 'Action applied.');
    }
}
