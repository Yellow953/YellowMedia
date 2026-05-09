<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiActionLog extends Model
{
    protected $table = 'ai_action_log';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'campaign_id', 'action_type', 'target',
        'target_meta_id', 'reason', 'before_value', 'after_value',
        'applied_by', 'applied_at',
    ];

    protected $casts = [
        'before_value' => 'array',
        'after_value' => 'array',
        'applied_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MetaCampaign::class, 'campaign_id');
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }
}
