<?php

namespace App\Jobs;

use App\Services\MetaAdsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncMetaCampaignsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ?int $tenantId = null) {}

    public function handle(MetaAdsService $service): void
    {
        if ($this->tenantId) {
            $service->syncCampaigns($this->tenantId);

            return;
        }

        $tenants = \App\Models\Tenant::where('is_active', true)->pluck('id');

        foreach ($tenants as $tenantId) {
            $service->syncCampaigns($tenantId);
        }
    }
}
