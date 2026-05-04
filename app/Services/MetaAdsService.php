<?php

namespace App\Services;

use App\Models\MetaCampaign;
use Illuminate\Support\Facades\Http;

class MetaAdsService
{
    private string $apiBase = 'https://graph.facebook.com/v19.0';

    public function syncCampaigns(int $tenantId): void
    {
        $accounts = \App\Models\MetaAdAccount::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        foreach ($accounts as $account) {
            $token = $account->decrypted_token;
            $accountId = ltrim($account->account_id, 'act_');

            $response = Http::get("{$this->apiBase}/act_{$accountId}/campaigns", [
                'fields' => 'id,name,status,objective,daily_budget,lifetime_budget',
                'access_token' => $token,
            ]);

            foreach ($response->json('data') ?? [] as $c) {
                $campaign = MetaCampaign::updateOrCreate(
                    ['tenant_id' => $tenantId, 'campaign_id' => $c['id']],
                    [
                        'ad_account_id' => $account->id,
                        'name' => $c['name'],
                        'status' => $c['status'],
                        'objective' => $c['objective'] ?? null,
                        'budget' => ($c['daily_budget'] ?? $c['lifetime_budget'] ?? 0) / 100,
                    ]
                );

                $this->syncInsights($campaign, $token);
            }
        }
    }

    private function syncInsights(MetaCampaign $campaign, string $token): void
    {
        $response = Http::get("{$this->apiBase}/{$campaign->campaign_id}/insights", [
            'fields' => 'spend,impressions,clicks,ctr,cpc,purchase_roas',
            'date_preset' => 'last_30d',
            'access_token' => $token,
        ]);

        $data = $response->json('data.0') ?? [];

        if (! empty($data)) {
            $campaign->update([
                'spend' => $data['spend'] ?? 0,
                'impressions' => $data['impressions'] ?? 0,
                'clicks' => $data['clicks'] ?? 0,
                'ctr' => $data['ctr'] ?? 0,
                'cpc' => $data['cpc'] ?? 0,
                'roas' => $data['purchase_roas'][0]['value'] ?? 0,
                'last_synced_at' => now(),
            ]);
        }
    }

    public function launchCampaign(MetaCampaign $campaign): void
    {
        $account = $campaign->adAccount;
        $token = $account->decrypted_token;
        $accountId = ltrim($account->account_id, 'act_');

        $metaCampaign = Http::post("{$this->apiBase}/act_{$accountId}/campaigns", [
            'name' => $campaign->name,
            'objective' => $campaign->objective,
            'status' => 'PAUSED',
            'special_ad_categories' => [],
            'access_token' => $token,
        ]);

        $campaign->update(['campaign_id' => $metaCampaign->json('id'), 'status' => 'PAUSED']);
    }
}
