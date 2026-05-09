<?php

namespace App\Services;

use App\Models\MetaCampaign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                'limit' => 100,
            ]);

            if ($response->failed()) {
                Log::error("MetaAdsService: campaigns API error for account {$accountId}", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                continue;
            }

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

        if ($response->failed()) {
            Log::warning("MetaAdsService: insights API error for campaign {$campaign->campaign_id}", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return;
        }

        $data = $response->json('data.0') ?? [];

        $updates = ['last_synced_at' => now()];

        if (! empty($data)) {
            $updates += [
                'spend'       => $data['spend'] ?? 0,
                'impressions' => $data['impressions'] ?? 0,
                'clicks'      => $data['clicks'] ?? 0,
                'ctr'         => $data['ctr'] ?? 0,
                'cpc'         => $data['cpc'] ?? 0,
                'roas'        => $data['purchase_roas'][0]['value'] ?? 0,
            ];
        }

        $campaign->update($updates);
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
