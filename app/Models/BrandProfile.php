<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandProfile extends Model
{
    protected $fillable = [
        'tenant_id', 'business_name', 'business_description', 'target_audience',
        'tone', 'content_pillars', 'brand_colors', 'hashtags',
        'instagram_handle', 'sample_captions', 'voice_summary',
    ];

    protected $casts = [
        'content_pillars' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function hasVoice(): bool
    {
        return ! empty($this->voice_summary);
    }

    public function brandContext(): string
    {
        $parts = [];

        if ($this->business_name) {
            $parts[] = "Brand: {$this->business_name}";
        }
        if ($this->business_description) {
            $parts[] = "About: {$this->business_description}";
        }
        if ($this->target_audience) {
            $parts[] = "Target audience: {$this->target_audience}";
        }
        if ($this->tone) {
            $parts[] = "Tone: {$this->tone}";
        }
        if ($this->voice_summary) {
            $parts[] = "Brand voice: {$this->voice_summary}";
        }

        return implode("\n", $parts);
    }
}
