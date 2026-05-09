<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Notifications\SyncErrorNotification;
use App\Services\MetaAdsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncMetaCampaignsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ?int $tenantId = null) {}

    public function handle(MetaAdsService $service): void
    {
        $tenants = $this->tenantId
            ? Tenant::where('id', $this->tenantId)->get()
            : Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            try {
                Log::info("SyncMetaCampaignsJob: syncing tenant {$tenant->id} ({$tenant->name})");
                $service->syncCampaigns($tenant->id);
                Log::info("SyncMetaCampaignsJob: completed tenant {$tenant->id}");
            } catch (\Throwable $e) {
                Log::error("SyncMetaCampaignsJob: failed for tenant {$tenant->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $tenant->adminUsers()->each(
                    fn($user) => $user->notify(new SyncErrorNotification('Meta Ads', $e->getMessage()))
                );
                throw $e;
            }
        }
    }
}
