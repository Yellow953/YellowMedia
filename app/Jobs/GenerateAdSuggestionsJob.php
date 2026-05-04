<?php

namespace App\Jobs;

use App\Models\AiSuggestion;
use App\Models\MetaCampaign;
use App\Models\User;
use App\Notifications\HighPrioritySuggestionNotification;
use App\Services\GeminiTextService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAdSuggestionsJob implements ShouldQueue
{
    use Queueable;

    public function handle(GeminiTextService $service): void
    {
        $campaigns = MetaCampaign::whereNotNull('last_synced_at')->get();

        foreach ($campaigns as $campaign) {
            $data = [
                'Campaign'     => $campaign->name,
                'Status'       => $campaign->status,
                'Spend'        => '$' . $campaign->spend,
                'CTR'          => $campaign->ctr . '%',
                'CPC'          => '$' . $campaign->cpc,
                'ROAS'         => $campaign->roas,
                'Objective'    => $campaign->objective,
                'Days running' => $campaign->created_at->diffInDays(now()),
            ];

            $suggestions = $service->generateSuggestions($data);

            $highPriority = [];

            foreach ($suggestions as $s) {
                AiSuggestion::create([
                    'tenant_id'   => $campaign->tenant_id,
                    'campaign_id' => $campaign->id,
                    'suggestion'  => $s['suggestion'] ?? '',
                    'type'        => $s['type'] ?? 'general',
                    'priority'    => $s['priority'] ?? 'medium',
                ]);

                if (($s['priority'] ?? '') === 'high') {
                    $highPriority[] = $s;
                }
            }

            if (! empty($highPriority)) {
                User::where('tenant_id', $campaign->tenant_id)
                    ->where('role', 'tenant_admin')
                    ->each(fn($u) => $u->notify(new HighPrioritySuggestionNotification($campaign, $highPriority)));
            }
        }
    }
}
