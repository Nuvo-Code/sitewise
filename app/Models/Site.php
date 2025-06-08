<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'domain',
        'name',
        'settings',
        'active',
    ];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public static function findByDomain(string $domain): ?self
    {
        return static::where('domain', $domain)->first();
    }

    public static function createForDomain(string $domain): self
    {
        return static::create([
            'domain' => $domain,
            'name' => ucfirst(str_replace(['.', '-', '_'], ' ', $domain)),
            'active' => true,
        ]);
    }
}
