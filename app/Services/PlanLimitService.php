<?php

namespace App\Services;

use App\Models\GeneratedImage;
use App\Models\MetaCampaign;
use App\Models\Tenant;

class PlanLimitService
{
    public function limits(string $plan): array
    {
        return config("plans.{$plan}", config('plans.free'));
    }

    public function imagesThisMonth(int $tenantId): int
    {
        return GeneratedImage::where('tenant_id', $tenantId)
            ->where('status', 'done')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function campaignCount(int $tenantId): int
    {
        return MetaCampaign::where('tenant_id', $tenantId)->count();
    }

    public function canGenerateImage(Tenant $tenant): bool
    {
        $limit = $this->limits($tenant->plan)['images_per_month'];
        if ($limit === -1) return true;
        return $this->imagesThisMonth($tenant->id) < $limit;
    }

    public function canCreateCampaign(Tenant $tenant): bool
    {
        $limit = $this->limits($tenant->plan)['max_campaigns'];
        if ($limit === -1) return true;
        return $this->campaignCount($tenant->id) < $limit;
    }

    public function usage(Tenant $tenant): array
    {
        $limits       = $this->limits($tenant->plan);
        $imagesUsed   = $this->imagesThisMonth($tenant->id);
        $campaignsUsed = $this->campaignCount($tenant->id);

        return [
            'plan'            => $tenant->plan,
            'plan_name'       => $limits['name'],
            'images_used'     => $imagesUsed,
            'images_limit'    => $limits['images_per_month'],
            'images_pct'      => $limits['images_per_month'] > 0
                ? min(100, (int) round($imagesUsed / $limits['images_per_month'] * 100))
                : 0,
            'campaigns_used'  => $campaignsUsed,
            'campaigns_limit' => $limits['max_campaigns'],
            'campaigns_pct'   => $limits['max_campaigns'] > 0
                ? min(100, (int) round($campaignsUsed / $limits['max_campaigns'] * 100))
                : 0,
            'ai_suggestions'  => $limits['ai_suggestions'],
            'ai_review'       => $limits['ai_review'],
        ];
    }
}
