<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaAdAccount extends Model
{
    protected $fillable = [
        'tenant_id', 'account_id', 'account_name',
        'access_token', 'token_expires_at', 'is_active',
    ];

    protected $hidden = ['access_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(MetaCampaign::class, 'ad_account_id');
    }

    public function getDecryptedTokenAttribute(): string
    {
        return decrypt($this->access_token);
    }
}
