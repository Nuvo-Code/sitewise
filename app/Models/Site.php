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
        'is_setup_complete',
    ];

    protected $casts = [
        'settings' => 'array',
        'active' => 'boolean',
        'is_setup_complete' => 'boolean',
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
            'is_setup_complete' => false,
        ]);
    }

    public function markSetupComplete(): void
    {
        $this->update(['is_setup_complete' => true]);
    }

    public function needsSetup(): bool
    {
        return !$this->is_setup_complete;
    }

    /**
     * Get the homepage for this site
     * Looks for pages with common homepage slugs in order of preference
     */
    public function getHomepage(): ?Page
    {
        $homepageSlugs = ['home', 'homepage', 'index'];

        foreach ($homepageSlugs as $slug) {
            $page = $this->pages()->where('slug', $slug)->where('active', true)->first();
            if ($page) {
                return $page;
            }
        }

        // If no specific homepage found, return the first active page
        return $this->pages()->where('active', true)->first();
    }
}
