<?php

namespace App\Http\Controllers;

use App\Models\MetaCampaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function show(int $id, Request $request)
    {
        $campaign = MetaCampaign::where('tenant_id', $request->user()->tenant_id)
            ->with(['suggestions' => fn ($q) => $q->where('is_dismissed', false)->orderBy('priority', 'desc')])
            ->findOrFail($id);

        return view('meta-ads.campaign', compact('campaign'));
    }
}
