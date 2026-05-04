<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaAd extends Model
{
    protected $fillable = [
        'tenant_id', 'ad_set_id', 'ad_id', 'name',
        'image_id', 'headline', 'body', 'caption', 'cta', 'destination_url', 'status',
    ];

    public function adSet(): BelongsTo
    {
        return $this->belongsTo(MetaAdSet::class, 'ad_set_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(GeneratedImage::class, 'image_id');
    }
}
