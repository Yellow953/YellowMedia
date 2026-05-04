<?php

namespace App\Jobs;

use App\Models\AiSuggestion;
use App\Models\MetaCampaign;
use App\Models\User;
use App\Notifications\AiReviewResultNotification;
use App\Services\GeminiTextService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReviewCampaignsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ?MetaCampaign $campaign = null) {}

    public function handle(GeminiTextService $service): void
    {
        $campaigns = $this->campaign
            ? collect([$this->campaign])
            : MetaCampaign::where('spend', '>=', 5)
                ->where('created_at', '<=', now()->subDays(3))
                ->get();

        foreach ($campaigns as $campaign) {
            $payload = $campaign->load('adSets.ads')->toJson();
            $review  = $service->reviewCampaign($payload);

            $actions      = $review['actions'] ?? [];
            $highPriority = array_filter($actions, fn($a) => ($a['priority'] ?? '') === 'high');

            foreach ($actions as $action) {
                AiSuggestion::create([
                    'tenant_id'   => $campaign->tenant_id,
                    'campaign_id' => $campaign->id,
                    'suggestion'  => $action['reason'] ?? '',
                    'type'        => 'general',
                    'priority'    => $action['priority'] ?? 'medium',
                ]);
            }

            if (! empty($highPriority)) {
                User::where('tenant_id', $campaign->tenant_id)
                    ->where('role', 'tenant_admin')
                    ->each(fn($u) => $u->notify(new AiReviewResultNotification($campaign, array_values($highPriority))));
            }
        }
    }
}
