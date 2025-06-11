<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\CacheService;
use Illuminate\Console\Command;

class CacheManagementCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sitewise:cache 
                            {action : The cache action to perform (clear|warm|stats|clear-site|warm-site)}
                            {--site= : Site ID or domain for site-specific actions}
                            {--type= : Cache type to clear (pages|templates|all)}';

    /**
     * The console command description.
     */
    protected $description = 'Manage Sitewise cache system';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $siteOption = $this->option('site');
        $typeOption = $this->option('type');

        switch ($action) {
            case 'clear':
                return $this->clearCache($typeOption);
            
            case 'warm':
                return $this->warmCache();
            
            case 'stats':
                return $this->showStats();
            
            case 'clear-site':
                return $this->clearSiteCache($siteOption, $typeOption);
            
            case 'warm-site':
                return $this->warmSiteCache($siteOption);
            
            default:
                $this->error("Unknown action: {$action}");
                $this->showHelp();
                return 1;
        }
    }

    protected function clearCache(?string $type): int
    {
        $this->info('Clearing cache...');

        switch ($type) {
            case 'pages':
                $this->warn('Page-specific clearing requires site context. Use clear-site instead.');
                return 1;
            
            case 'templates':
                $this->warn('Template-specific clearing requires site context. Use clear-site instead.');
                return 1;
            
            case 'all':
            case null:
                CacheService::clearAllCache();
                $this->info('✅ All cache cleared successfully');
                break;
            
            default:
                $this->error("Unknown cache type: {$type}");
                return 1;
        }

        return 0;
    }

    protected function warmCache(): int
    {
        $this->info('Warming cache for all sites...');

        $sites = Site::all();
        $totalWarmed = 0;

        foreach ($sites as $site) {
            $this->line("Warming cache for site: {$site->name} ({$site->domain})");
            $warmed = CacheService::warmSiteCache($site->id);
            
            $this->line("  - Pages: {$warmed['pages']}");
            $this->line("  - Templates: {$warmed['templates']}");
            
            $totalWarmed += ($warmed['pages'] + $warmed['templates']);
        }

        $this->info("✅ Cache warmed for {$sites->count()} sites ({$totalWarmed} items)");
        return 0;
    }

    protected function showStats(): int
    {
        $this->info('Cache Statistics');
        $this->line('================');

        $stats = CacheService::getCacheStats();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Driver', ucfirst($stats['driver'])],
                ['Total Keys', number_format($stats['total_keys'])],
                ['Memory Usage', $stats['memory_usage']],
                ['Hit Rate', $stats['hit_rate'] . '%'],
            ]
        );

        // Show site-specific stats
        $sites = Site::take(10)->get();
        if ($sites->isNotEmpty()) {
            $this->line('');
            $this->info('Site Cache Usage (Top 10)');
            $this->line('==========================');

            $siteData = [];
            foreach ($sites as $site) {
                $usage = CacheService::getSiteCacheUsage($site->id);
                $total = array_sum($usage);
                
                $siteData[] = [
                    $site->name,
                    $site->domain,
                    number_format($total),
                    number_format($usage['pages'] ?? 0),
                    number_format($usage['templates'] ?? 0),
                ];
            }

            $this->table(
                ['Site', 'Domain', 'Total Keys', 'Pages', 'Templates'],
                $siteData
            );
        }

        return 0;
    }

    protected function clearSiteCache(?string $siteOption, ?string $type): int
    {
        $site = $this->resolveSite($siteOption);
        if (!$site) {
            return 1;
        }

        $this->info("Clearing cache for site: {$site->name} ({$site->domain})");

        switch ($type) {
            case 'pages':
                CacheService::clearPageCache($site->id);
                $this->info('✅ Pages cache cleared');
                break;
            
            case 'templates':
                CacheService::clearTemplateCache($site->id);
                $this->info('✅ Templates cache cleared');
                break;
            
            case 'all':
            case null:
                CacheService::clearSiteCache($site->id);
                $this->info('✅ All site cache cleared');
                break;
            
            default:
                $this->error("Unknown cache type: {$type}");
                return 1;
        }

        return 0;
    }

    protected function warmSiteCache(?string $siteOption): int
    {
        $site = $this->resolveSite($siteOption);
        if (!$site) {
            return 1;
        }

        $this->info("Warming cache for site: {$site->name} ({$site->domain})");
        
        $warmed = CacheService::warmSiteCache($site->id);
        
        $this->line("✅ Cache warmed:");
        $this->line("  - Pages: {$warmed['pages']}");
        $this->line("  - Templates: {$warmed['templates']}");

        return 0;
    }

    protected function resolveSite(?string $siteOption): ?Site
    {
        if (!$siteOption) {
            $this->error('Site option is required for site-specific actions');
            $this->line('Use --site=ID or --site=domain.com');
            return null;
        }

        // Try to find by ID first
        if (is_numeric($siteOption)) {
            $site = Site::find($siteOption);
            if ($site) {
                return $site;
            }
        }

        // Try to find by domain
        $site = Site::where('domain', $siteOption)->first();
        if ($site) {
            return $site;
        }

        $this->error("Site not found: {$siteOption}");
        return null;
    }

    protected function showHelp(): void
    {
        $this->line('');
        $this->info('Available actions:');
        $this->line('  clear          Clear all application cache');
        $this->line('  warm           Warm cache for all sites');
        $this->line('  stats          Show cache statistics');
        $this->line('  clear-site     Clear cache for specific site');
        $this->line('  warm-site      Warm cache for specific site');
        $this->line('');
        $this->info('Examples:');
        $this->line('  php artisan sitewise:cache clear');
        $this->line('  php artisan sitewise:cache warm');
        $this->line('  php artisan sitewise:cache stats');
        $this->line('  php artisan sitewise:cache clear-site --site=1');
        $this->line('  php artisan sitewise:cache clear-site --site=example.com --type=pages');
        $this->line('  php artisan sitewise:cache warm-site --site=example.com');
    }
}
