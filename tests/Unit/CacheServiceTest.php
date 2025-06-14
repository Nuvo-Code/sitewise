<?php

use App\Services\CacheService;

test('cache service can generate proper cache keys', function () {
    $key1 = CacheService::siteKey('test', 123);
    expect($key1)->toBe('test:123');

    $key2 = CacheService::siteKey('test', 123, 'identifier');
    expect($key2)->toBe('test:123:identifier');

    $key3 = CacheService::siteKey('page', 456, 'about');
    expect($key3)->toBe('page:456:about');
});

test('cache service can get configuration values', function () {
    $defaultTTL = CacheService::getDefaultTTL();
    expect($defaultTTL)->toBeInt();
    expect($defaultTTL)->toBeGreaterThan(0);

    $statsTTL = CacheService::getStatsTTL();
    expect($statsTTL)->toBeInt();
    expect($statsTTL)->toBeGreaterThan(0);

    $bladeTTL = CacheService::getBladeTTL();
    expect($bladeTTL)->toBeInt();
    expect($bladeTTL)->toBeGreaterThan(0);
});

test('cache service can get cache statistics', function () {
    $stats = CacheService::getCacheStats();

    expect($stats)->toBeArray();
    expect($stats)->toHaveKey('driver');
    expect($stats)->toHaveKey('total_keys');
    expect($stats)->toHaveKey('memory_usage');
    expect($stats)->toHaveKey('hit_rate');

    expect($stats['driver'])->toBeString();
    expect($stats['total_keys'])->toBeInt();
    expect($stats['hit_rate'])->toBeNumeric();
});
