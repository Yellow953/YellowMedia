<?php

namespace App\Http\Controllers;

use App\Models\GeneratedImage;
use App\Models\MetaCampaign;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tenantId = $user->tenant_id;

        $stats = [
            'images_generated' => GeneratedImage::where('tenant_id', $tenantId)->where('status', 'done')->count(),
            'active_campaigns' => MetaCampaign::where('tenant_id', $tenantId)->where('status', 'ACTIVE')->count(),
            'total_spend' => MetaCampaign::where('tenant_id', $tenantId)->sum('spend'),
            'avg_roas' => MetaCampaign::where('tenant_id', $tenantId)->avg('roas') ?? 0,
        ];

        $recentImages = GeneratedImage::where('tenant_id', $tenantId)
            ->where('status', 'done')
            ->latest()
            ->take(4)
            ->get();

        $recentCampaigns = MetaCampaign::where('tenant_id', $tenantId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentImages', 'recentCampaigns'));
    }
}
