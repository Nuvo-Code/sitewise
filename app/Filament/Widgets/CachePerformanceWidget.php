<?php

namespace App\Filament\Widgets;

use App\Services\CacheService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CachePerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Cache Performance';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $site = app('site');

        if (! $site) {
            return [
                Stat::make('No Site Available', 'N/A')
                    ->description('Cache stats require a valid site')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        $cacheWorking = CacheService::isCacheWorking();
        $siteCacheUsage = CacheService::getSiteCacheUsage($site->id);

        $stats = [];

        // Cache Status
        $stats[] = Stat::make('Cache Status', $cacheWorking ? 'Working' : 'Unavailable')
            ->description($cacheWorking ? 'Cache system operational' : 'Cache system not working')
            ->descriptionIcon($cacheWorking ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
            ->color($cacheWorking ? 'success' : 'danger');

        // Site Cache Keys
        $totalSiteKeys = array_sum($siteCacheUsage);
        $description = $cacheWorking ? $site->name.' cached items' : 'Simulated data (cache unavailable)';
        $stats[] = Stat::make('Site Cache Keys', number_format($totalSiteKeys))
            ->description($description)
            ->descriptionIcon('heroicon-m-building-office')
            ->color($cacheWorking ? 'primary' : 'warning');

        // Pages Cache
        $pagesCache = $siteCacheUsage['pages'] ?? 0;
        $stats[] = Stat::make('Pages Cached', number_format($pagesCache))
            ->description($cacheWorking ? 'Cached page content' : 'Simulated count')
            ->descriptionIcon('heroicon-m-document-text')
            ->color($cacheWorking ? 'success' : 'warning');

        // Templates Cache
        $templatesCache = ($siteCacheUsage['templates'] ?? 0) + ($siteCacheUsage['blade_templates'] ?? 0);
        $stats[] = Stat::make('Templates Cached', number_format($templatesCache))
            ->description($cacheWorking ? 'Template definitions & renders' : 'Simulated count')
            ->descriptionIcon('heroicon-m-squares-2x2')
            ->color($cacheWorking ? 'info' : 'warning');

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
        return 4;
    }
}
