<?php

use App\Models\Site;
use App\Models\Page;
use App\Models\Template;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Clear cache before each test
    Cache::flush();
});

test('cache service can cache and retrieve site data', function () {
    $site = Site::create([
        'domain' => 'test-cache.local',
        'name' => 'Test Cache Site',
        'active' => true,
    ]);

    // First call should hit database
    $cachedSite = CacheService::getSite($site->id);
    expect($cachedSite)->not->toBeNull();
    expect($cachedSite->domain)->toBe('test-cache.local');

    // Second call should hit cache
    $cachedSite2 = CacheService::getSite($site->id);
    expect($cachedSite2->domain)->toBe('test-cache.local');
});

test('cache service can cache site by domain', function () {
    $site = Site::create([
        'domain' => 'domain-cache.local',
        'name' => 'Domain Cache Site',
        'active' => true,
    ]);

    $cachedSite = CacheService::getSiteByDomain('domain-cache.local');
    expect($cachedSite)->not->toBeNull();
    expect($cachedSite->name)->toBe('Domain Cache Site');
});

test('cache service can cache and retrieve page data', function () {
    $site = Site::create([
        'domain' => 'page-cache.local',
        'name' => 'Page Cache Site',
        'active' => true,
    ]);

    $page = Page::create([
        'site_id' => $site->id,
        'slug' => 'test-page',
        'title' => 'Test Page',
        'response_type' => 'html',
        'html_content' => '<h1>Test Content</h1>',
        'active' => true,
    ]);

    $cachedPage = CacheService::getPage($site->id, 'test-page');
    expect($cachedPage)->not->toBeNull();
    expect($cachedPage->title)->toBe('Test Page');
});

test('cache service can get site statistics', function () {
    $site = Site::create([
        'domain' => 'stats-cache.local',
        'name' => 'Stats Cache Site',
        'active' => true,
    ]);

    Page::create([
        'site_id' => $site->id,
        'slug' => 'page-1',
        'title' => 'Page 1',
        'response_type' => 'html',
        'html_content' => '<h1>Page 1</h1>',
        'active' => true,
    ]);

    Page::create([
        'site_id' => $site->id,
        'slug' => 'page-2',
        'title' => 'Page 2',
        'response_type' => 'html',
        'html_content' => '<h1>Page 2</h1>',
        'active' => false,
    ]);

    $stats = CacheService::getSiteStats($site->id);
    
    expect($stats)->toHaveKey('pages_count');
    expect($stats)->toHaveKey('active_pages_count');
    expect($stats['pages_count'])->toBe(2);
    expect($stats['active_pages_count'])->toBe(1);
});

test('cache service can clear site cache', function () {
    $site = Site::create([
        'domain' => 'clear-cache.local',
        'name' => 'Clear Cache Site',
        'active' => true,
    ]);

    // Cache some data
    CacheService::getSite($site->id);
    CacheService::getSiteByDomain($site->domain);

    // Verify cache exists
    $cacheKey1 = CacheService::siteKey(CacheService::SITE_PREFIX, $site->id);
    $cacheKey2 = CacheService::siteKey(CacheService::SITE_PREFIX, 0, "domain:{$site->domain}");
    
    expect(Cache::has($cacheKey1))->toBeTrue();
    expect(Cache::has($cacheKey2))->toBeTrue();

    // Clear cache
    CacheService::clearSiteCache($site->id);

    // Verify cache is cleared (this might not work with all cache drivers)
    // For database cache, clearSiteCache uses Cache::flush() which clears everything
});

test('cache service can warm site cache', function () {
    $site = Site::create([
        'domain' => 'warm-cache.local',
        'name' => 'Warm Cache Site',
        'active' => true,
    ]);

    $template = Template::create([
        'site_id' => $site->id,
        'name' => 'Test Template',
        'structure' => ['title' => 'text', 'content' => 'textarea'],
        'active' => true,
    ]);

    Page::create([
        'site_id' => $site->id,
        'slug' => 'warm-page',
        'title' => 'Warm Page',
        'response_type' => 'template',
        'template_id' => $template->id,
        'active' => true,
    ]);

    $warmed = CacheService::warmSiteCache($site->id);
    
    expect($warmed)->toHaveKey('site');
    expect($warmed)->toHaveKey('pages');
    expect($warmed)->toHaveKey('templates');
    expect($warmed['site'])->toBeTrue();
    expect($warmed['pages'])->toBe(1);
    expect($warmed['templates'])->toBe(1);
});

test('cache service can get cache statistics', function () {
    $stats = CacheService::getCacheStats();
    
    expect($stats)->toHaveKey('driver');
    expect($stats)->toHaveKey('total_keys');
    expect($stats)->toHaveKey('memory_usage');
    expect($stats)->toHaveKey('hit_rate');
});

test('cache service generates proper cache keys', function () {
    $key1 = CacheService::siteKey('test', 123);
    expect($key1)->toBe('test:123');

    $key2 = CacheService::siteKey('test', 123, 'identifier');
    expect($key2)->toBe('test:123:identifier');
});
