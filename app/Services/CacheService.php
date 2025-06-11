<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Page;
use App\Models\Template;
use App\Models\TemplateContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CacheService
{
    /**
     * Get cache TTL from configuration
     */
    public static function getDefaultTTL(): int
    {
        return config('cache.sitewise.default_ttl', 3600);
    }

    /**
     * Get stats cache TTL from configuration
     */
    public static function getStatsTTL(): int
    {
        return config('cache.sitewise.stats_ttl', 300);
    }

    /**
     * Get Blade template cache TTL from configuration
     */
    public static function getBladeTTL(): int
    {
        return config('cache.sitewise.blade_template_ttl', 86400);
    }
    
    /**
     * Cache key prefixes for different data types
     */
    const SITE_PREFIX = 'site';
    const PAGE_PREFIX = 'page';
    const TEMPLATE_PREFIX = 'template';
    const TEMPLATE_CONTENT_PREFIX = 'template_content';
    const BLADE_TEMPLATE_PREFIX = 'blade_template';
    const SITE_STATS_PREFIX = 'site_stats';
    
    /**
     * Cache tags for better invalidation
     */
    const SITE_TAG = 'sites';
    const PAGE_TAG = 'pages';
    const TEMPLATE_TAG = 'templates';
    const CONTENT_TAG = 'content';

    /**
     * Generate a site-specific cache key
     */
    public static function siteKey(string $prefix, int $siteId, string $identifier = ''): string
    {
        $key = "{$prefix}:{$siteId}";
        if ($identifier) {
            $key .= ":{$identifier}";
        }
        return $key;
    }

    /**
     * Get site data with caching
     */
    public static function getSite(int $siteId): ?Site
    {
        $key = self::siteKey(self::SITE_PREFIX, $siteId);
        
        return Cache::remember($key, self::getDefaultTTL(), function () use ($siteId) {
            return Site::find($siteId);
        });
    }

    /**
     * Get site by domain with caching
     */
    public static function getSiteByDomain(string $domain): ?Site
    {
        $key = self::siteKey(self::SITE_PREFIX, 0, "domain:{$domain}");
        
        return Cache::remember($key, self::getDefaultTTL(), function () use ($domain) {
            return Site::where('domain', $domain)->first();
        });
    }

    /**
     * Get page with caching
     */
    public static function getPage(int $siteId, string $slug): ?Page
    {
        $key = self::siteKey(self::PAGE_PREFIX, $siteId, $slug);
        
        return Cache::remember($key, self::getDefaultTTL(), function () use ($siteId, $slug) {
            return Page::where('site_id', $siteId)
                      ->where('slug', $slug)
                      ->where('active', true)
                      ->with(['template', 'templateContents'])
                      ->first();
        });
    }

    /**
     * Get all pages for a site with caching
     */
    public static function getSitePages(int $siteId): Collection
    {
        $key = self::siteKey(self::PAGE_PREFIX, $siteId, 'all');
        
        return Cache::remember($key, self::getDefaultTTL(), function () use ($siteId) {
            return Page::where('site_id', $siteId)
                      ->where('active', true)
                      ->with(['template'])
                      ->get();
        });
    }

    /**
     * Get template with caching
     */
    public static function getTemplate(int $siteId, int $templateId): ?Template
    {
        $key = self::siteKey(self::TEMPLATE_PREFIX, $siteId, $templateId);
        
        return Cache::remember($key, self::getDefaultTTL(), function () use ($siteId, $templateId) {
            return Template::where('site_id', $siteId)
                          ->where('id', $templateId)
                          ->where('active', true)
                          ->first();
        });
    }

    /**
     * Get template content for a page with caching
     */
    public static function getTemplateContent(int $pageId): array
    {
        $key = self::siteKey(self::TEMPLATE_CONTENT_PREFIX, 0, "page:{$pageId}");
        
        return Cache::remember($key, self::getDefaultTTL(), function () use ($pageId) {
            return TemplateContent::where('page_id', $pageId)
                                 ->pluck('value', 'key')
                                 ->toArray();
        });
    }

    /**
     * Clear all Blade template cache for a specific template
     * This clears all cached rendered outputs for the template
     */
    public static function clearBladeTemplateCache(int $siteId, int $templateId): void
    {
        $pattern = self::siteKey(self::BLADE_TEMPLATE_PREFIX, $siteId, $templateId) . ':*';
        self::clearCacheByPattern($pattern);
    }

    /**
     * Get site statistics with caching
     */
    public static function getSiteStats(int $siteId): array
    {
        $key = self::siteKey(self::SITE_STATS_PREFIX, $siteId);
        
        return Cache::remember($key, self::getStatsTTL(), function () use ($siteId) {
            return [
                'pages_count' => Page::where('site_id', $siteId)->count(),
                'active_pages_count' => Page::where('site_id', $siteId)->where('active', true)->count(),
                'templates_count' => Template::where('site_id', $siteId)->count(),
                'active_templates_count' => Template::where('site_id', $siteId)->where('active', true)->count(),
                'template_contents_count' => TemplateContent::whereHas('page', function ($query) use ($siteId) {
                    $query->where('site_id', $siteId);
                })->count(),
            ];
        });
    }

    /**
     * Clear all cache for a specific site
     */
    public static function clearSiteCache(int $siteId): void
    {
        $patterns = [
            self::siteKey(self::SITE_PREFIX, $siteId) . '*',
            self::siteKey(self::PAGE_PREFIX, $siteId) . '*',
            self::siteKey(self::TEMPLATE_PREFIX, $siteId) . '*',
            self::siteKey(self::BLADE_TEMPLATE_PREFIX, $siteId) . '*',
            self::siteKey(self::SITE_STATS_PREFIX, $siteId) . '*',
        ];

        foreach ($patterns as $pattern) {
            self::clearCacheByPattern($pattern);
        }
    }

    /**
     * Clear page cache
     */
    public static function clearPageCache(int $siteId, ?string $slug = null): void
    {
        if ($slug) {
            $key = self::siteKey(self::PAGE_PREFIX, $siteId, $slug);
            Cache::forget($key);
        } else {
            $pattern = self::siteKey(self::PAGE_PREFIX, $siteId) . '*';
            self::clearCacheByPattern($pattern);
        }
        
        // Also clear template content cache for pages
        $pattern = self::siteKey(self::TEMPLATE_CONTENT_PREFIX, 0) . '*';
        self::clearCacheByPattern($pattern);
    }

    /**
     * Clear template cache
     */
    public static function clearTemplateCache(int $siteId, ?int $templateId = null): void
    {
        if ($templateId) {
            // Clear template definition cache
            $templateKey = self::siteKey(self::TEMPLATE_PREFIX, $siteId, $templateId);
            Cache::forget($templateKey);

            // Clear all blade template rendered outputs for this template
            self::clearBladeTemplateCache($siteId, $templateId);
        } else {
            $patterns = [
                self::siteKey(self::TEMPLATE_PREFIX, $siteId) . '*',
                self::siteKey(self::BLADE_TEMPLATE_PREFIX, $siteId) . '*',
            ];

            foreach ($patterns as $pattern) {
                self::clearCacheByPattern($pattern);
            }
        }
    }

    /**
     * Clear cache by pattern (works with Redis and some other drivers)
     */
    protected static function clearCacheByPattern(string $pattern): void
    {
        $driver = config('cache.default');

        if ($driver === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // For other drivers, we'll need to track keys manually or use cache tags
            // For now, we'll use a more aggressive approach
            Cache::flush();
        }
    }

    /**
     * Warm up cache for a site
     */
    public static function warmSiteCache(int $siteId): array
    {
        $warmed = [];

        // Warm site data
        $site = self::getSite($siteId);
        if ($site) {
            $warmed['site'] = true;

            // Warm site statistics
            self::getSiteStats($siteId);
            $warmed['site_stats'] = true;

            // Warm all pages
            $pages = self::getSitePages($siteId);
            $warmed['pages'] = $pages->count();

            // Warm individual pages
            foreach ($pages as $page) {
                self::getPage($siteId, $page->slug);
                if ($page->template_id) {
                    self::getTemplateContent($page->id);
                }
            }

            // Warm templates
            $templates = Template::where('site_id', $siteId)->where('active', true)->get();
            foreach ($templates as $template) {
                self::getTemplate($siteId, $template->id);
            }
            $warmed['templates'] = $templates->count();
        }

        return $warmed;
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        $driver = config('cache.default');
        $stats = [
            'driver' => $driver,
            'total_keys' => 0,
            'memory_usage' => 0,
            'hit_rate' => 0,
        ];

        try {
            if ($driver === 'redis') {
                $redis = Cache::getRedis();
                $info = $redis->info();

                $stats['total_keys'] = $info['db0']['keys'] ?? 0;
                $stats['memory_usage'] = $info['used_memory_human'] ?? '0B';
                $stats['hit_rate'] = isset($info['keyspace_hits'], $info['keyspace_misses'])
                    ? round(($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses'])) * 100, 2)
                    : 0;
            } elseif ($driver === 'database') {
                $stats['total_keys'] = DB::table('cache')->count();
                $stats['memory_usage'] = DB::table('cache')->sum(DB::raw('LENGTH(value)')) . ' bytes';
            }
        } catch (\Exception $e) {
            // Silently handle errors
        }

        return $stats;
    }

    /**
     * Get cache usage by site
     */
    public static function getSiteCacheUsage(int $siteId): array
    {
        $prefixes = [
            'site' => self::SITE_PREFIX,
            'pages' => self::PAGE_PREFIX,
            'templates' => self::TEMPLATE_PREFIX,
            'template_content' => self::TEMPLATE_CONTENT_PREFIX,
            'blade_templates' => self::BLADE_TEMPLATE_PREFIX,
            'stats' => self::SITE_STATS_PREFIX,
        ];

        $usage = [];
        foreach ($prefixes as $type => $prefix) {
            $pattern = self::siteKey($prefix, $siteId);
            $usage[$type] = self::countCacheKeys($pattern);
        }

        return $usage;
    }

    /**
     * Count cache keys matching a pattern
     */
    protected static function countCacheKeys(string $pattern): int
    {
        $driver = config('cache.default');

        try {
            if ($driver === 'redis') {
                $redis = Cache::getRedis();
                return count($redis->keys($pattern . '*'));
            } elseif ($driver === 'database') {
                return DB::table('cache')
                    ->where('key', 'like', $pattern . '%')
                    ->count();
            }
        } catch (\Exception $e) {
            // Silently handle errors
        }

        return 0;
    }

    /**
     * Clear all application cache
     */
    public static function clearAllCache(): void
    {
        Cache::flush();

        // Clear OPcache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Get cache key information
     */
    public static function getCacheKeyInfo(string $key): ?array
    {
        $driver = config('cache.default');

        try {
            if ($driver === 'redis') {
                $redis = Cache::getRedis();
                if ($redis->exists($key)) {
                    return [
                        'exists' => true,
                        'ttl' => $redis->ttl($key),
                        'type' => $redis->type($key),
                        'size' => strlen($redis->get($key)),
                    ];
                }
            } elseif ($driver === 'database') {
                $record = DB::table('cache')->where('key', $key)->first();
                if ($record) {
                    return [
                        'exists' => true,
                        'ttl' => $record->expiration - time(),
                        'size' => strlen($record->value),
                        'expiration' => date('Y-m-d H:i:s', $record->expiration),
                    ];
                }
            }
        } catch (\Exception $e) {
            // Silently handle errors
        }

        return ['exists' => false];
    }
}
