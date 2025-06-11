<?php

namespace App\Filament\Pages;

use App\Services\CacheService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string $view = 'filament.pages.cache-management';

    protected static ?string $navigationLabel = 'Cache Management';

    protected static ?string $title = 'Cache Management';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'System';

    public function mount(): void
    {
        // Get cache data for the view
        $this->getCacheData();
    }

    protected function getHeaderActions(): array
    {
        $site = app('site');

        if (!$site) {
            return [];
        }

        return [
            Action::make('clear_site_cache')
                ->label('Clear Site Cache')
                ->icon('heroicon-o-building-office')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear Site Cache')
                ->modalDescription('This will clear all cached data for ' . $site->name . '. Are you sure?')
                ->action(function () use ($site) {
                    CacheService::clearSiteCache($site->id);

                    Notification::make()
                        ->title('Site cache cleared successfully')
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('warm_site_cache')
                ->label('Warm Site Cache')
                ->icon('heroicon-o-fire')
                ->color('success')
                ->action(function () use ($site) {
                    $warmed = CacheService::warmSiteCache($site->id);

                    $message = sprintf(
                        'Cache warmed: %d pages, %d templates',
                        $warmed['pages'] ?? 0,
                        $warmed['templates'] ?? 0
                    );

                    Notification::make()
                        ->title('Site cache warmed successfully')
                        ->body($message)
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('clear_pages_cache')
                ->label('Clear Pages Cache')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Clear Pages Cache')
                ->modalDescription('This will clear all page cache for ' . $site->name . '.')
                ->action(function () use ($site) {
                    CacheService::clearPageCache($site->id);

                    Notification::make()
                        ->title('Pages cache cleared successfully')
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('clear_templates_cache')
                ->label('Clear Templates Cache')
                ->icon('heroicon-o-squares-2x2')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Clear Templates Cache')
                ->modalDescription('This will clear all template cache for ' . $site->name . '.')
                ->action(function () use ($site) {
                    CacheService::clearTemplateCache($site->id);

                    Notification::make()
                        ->title('Templates cache cleared successfully')
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('debug_cache')
                ->label('Debug Cache')
                ->icon('heroicon-o-bug-ant')
                ->color('gray')
                ->action(function () use ($site) {
                    // Populate some test cache data
                    $populated = CacheService::populateTestCache($site->id);

                    // Debug cache keys
                    $debug = CacheService::debugSiteCacheKeys($site->id);

                    $message = "Test cache populated:\n" .
                              "Site: " . ($populated['site'] ?? 'none') . "\n" .
                              "Page: " . ($populated['page'] ?? 'none') . "\n" .
                              "Template: " . ($populated['template'] ?? 'none') . "\n" .
                              "Stats: " . ($populated['stats'] ?? 'none') . "\n\n" .
                              "Debug info: " . json_encode($debug, JSON_PRETTY_PRINT);

                    Notification::make()
                        ->title('Cache Debug Complete')
                        ->body($message)
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),
        ];
    }

    public function getCacheData(): array
    {
        $site = app('site');
        $siteId = $site?->id ?? 0;
        
        return [
            'cacheStats' => CacheService::getCacheStats(),
            'siteCacheUsage' => $siteId ? CacheService::getSiteCacheUsage($siteId) : [],
            'site' => $site,
        ];
    }

    protected function getViewData(): array
    {
        return $this->getCacheData();
    }
}
