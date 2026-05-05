<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = ['name', 'slug', 'plan', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function generatedImages(): HasMany
    {
        return $this->hasMany(GeneratedImage::class);
    }

    public function metaAdAccounts(): HasMany
    {
        return $this->hasMany(MetaAdAccount::class);
    }

    public function planLimits(): array
    {
        return config("plans.{$this->plan}", config('plans.free'));
    }

    public function adminUsers(): HasMany
    {
        return $this->users()->where('role', 'tenant_admin');
    }

    public function brandProfile(): HasOne
    {
        return $this->hasOne(BrandProfile::class);
    }
}
