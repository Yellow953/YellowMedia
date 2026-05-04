<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaCampaign extends Model
{
    protected $fillable = [
        'tenant_id', 'ad_account_id', 'campaign_id', 'name',
        'status', 'objective', 'budget', 'spend',
        'impressions', 'clicks', 'ctr', 'cpc', 'roas', 'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'budget' => 'decimal:2',
        'spend' => 'decimal:2',
        'ctr' => 'decimal:4',
        'cpc' => 'decimal:4',
        'roas' => 'decimal:4',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function adAccount(): BelongsTo
    {
        return $this->belongsTo(MetaAdAccount::class, 'ad_account_id');
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(AiSuggestion::class, 'campaign_id');
    }

    public function adSets(): HasMany
    {
        return $this->hasMany(MetaAdSet::class, 'campaign_id');
    }

    public function actionLog(): HasMany
    {
        return $this->hasMany(AiActionLog::class, 'campaign_id');
    }
}
