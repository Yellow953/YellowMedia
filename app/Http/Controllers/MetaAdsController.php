<?php

namespace App\Http\Controllers;

use App\Jobs\SyncMetaCampaignsJob;
use App\Models\MetaAdAccount;
use App\Models\MetaCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MetaAdsController extends Controller
{
    public function index(Request $request)
    {
        $accounts = MetaAdAccount::where('tenant_id', $request->user()->tenant_id)->get();

        return view('meta-ads.index', compact('accounts'));
    }

    public function connect()
    {
        $params = http_build_query([
            'client_id' => config('services.meta.app_id'),
            'redirect_uri' => config('services.meta.redirect_uri'),
            'scope' => 'ads_read,ads_management,business_management',
            'response_type' => 'code',
        ]);

        return redirect('https://www.facebook.com/v19.0/dialog/oauth?' . $params);
    }

    public function callback(Request $request)
    {
        if (! $request->has('code')) {
            return redirect()->route('settings.ad-accounts')->with('error', 'Meta connection cancelled.');
        }

        $tokenResponse = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'client_id' => config('services.meta.app_id'),
            'client_secret' => config('services.meta.app_secret'),
            'redirect_uri' => config('services.meta.redirect_uri'),
            'code' => $request->code,
        ]);

        $accessToken = $tokenResponse->json('access_token');

        $accountsResponse = Http::get('https://graph.facebook.com/v19.0/me/adaccounts', [
            'fields' => 'id,name',
            'access_token' => $accessToken,
        ]);

        foreach ($accountsResponse->json('data') ?? [] as $account) {
            MetaAdAccount::updateOrCreate(
                ['tenant_id' => $request->user()->tenant_id, 'account_id' => $account['id']],
                ['account_name' => $account['name'], 'access_token' => encrypt($accessToken), 'is_active' => true]
            );
        }

        SyncMetaCampaignsJob::dispatch($request->user()->tenant_id);

        return redirect()->route('settings.ad-accounts')->with('success', 'Meta Ads connected successfully.');
    }

    public function dashboard(Request $request)
    {
        $tenantId = $request->user()->tenant_id;

        $campaigns = MetaCampaign::where('tenant_id', $tenantId)
            ->with('adAccount')
            ->latest()
            ->paginate(20);

        $totals = MetaCampaign::where('tenant_id', $tenantId)->selectRaw(
            'SUM(spend) as total_spend, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(roas) as avg_roas'
        )->first();

        return view('meta-ads.dashboard', compact('campaigns', 'totals'));
    }

    public function sync(Request $request)
    {
        SyncMetaCampaignsJob::dispatch($request->user()->tenant_id);

        return back()->with('success', 'Sync started. Data will update shortly.');
    }
}
