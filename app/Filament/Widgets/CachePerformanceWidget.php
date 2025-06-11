<?php

namespace App\Filament\Widgets;

use App\Services\CacheService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CachePerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $site = app('site');
        $siteId = $site?->id ?? 0;
        
        $cacheStats = CacheService::getCacheStats();
        $siteCacheUsage = $siteId ? CacheService::getSiteCacheUsage($siteId) : [];
        
        $stats = [];

        // Cache Hit Rate
        $hitRate = $cacheStats['hit_rate'] ?? 0;
        $hitRateColor = $hitRate >= 80 ? 'success' : ($hitRate >= 60 ? 'warning' : 'danger');
        
        $stats[] = Stat::make('Cache Hit Rate', $hitRate . '%')
            ->description($this->getHitRateDescription($hitRate))
            ->descriptionIcon($this->getHitRateIcon($hitRate))
            ->color($hitRateColor);

        // Total Cache Keys
        $totalKeys = $cacheStats['total_keys'] ?? 0;
        $stats[] = Stat::make('Total Cache Keys', number_format($totalKeys))
            ->description('System-wide cached items')
            ->descriptionIcon('heroicon-m-key')
            ->color('info');

        // Memory Usage
        $memoryUsage = $cacheStats['memory_usage'] ?? '0B';
        $stats[] = Stat::make('Cache Memory', $memoryUsage)
            ->description('Current memory usage')
            ->descriptionIcon('heroicon-m-cpu-chip')
            ->color('primary');

        // Site Cache Usage (if available)
        if ($siteId && !empty($siteCacheUsage)) {
            $totalSiteKeys = array_sum($siteCacheUsage);
            $stats[] = Stat::make('Site Cache Keys', number_format($totalSiteKeys))
                ->description('Current site cached items')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success');
        }

        return $stats;
    }

    protected function getHitRateDescription(float $hitRate): string
    {
        if ($hitRate >= 90) {
            return 'Excellent performance';
        } elseif ($hitRate >= 80) {
            return 'Good performance';
        } elseif ($hitRate >= 60) {
            return 'Fair performance';
        } elseif ($hitRate >= 40) {
            return 'Poor performance';
        } else {
            return 'Very poor performance';
        }
    }

    protected function getHitRateIcon(float $hitRate): string
    {
        if ($hitRate >= 80) {
            return 'heroicon-m-check-circle';
        } elseif ($hitRate >= 60) {
            return 'heroicon-m-exclamation-triangle';
        } else {
            return 'heroicon-m-x-circle';
        }
    }

    public function getColumns(): int
    {
        $site = app('site');
        return $site ? 4 : 3;
    }
}
