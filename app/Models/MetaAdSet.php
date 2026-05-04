<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaAdSet extends Model
{
    protected $fillable = [
        'tenant_id', 'campaign_id', 'ad_set_id', 'name',
        'targeting', 'placement', 'daily_budget', 'start_time', 'end_time', 'status',
    ];

    protected $casts = [
        'targeting' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'daily_budget' => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MetaCampaign::class, 'campaign_id');
    }

    public function ads(): HasMany
    {
        return $this->hasMany(MetaAd::class, 'ad_set_id');
    }
}
