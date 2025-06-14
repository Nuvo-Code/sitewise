<?php

namespace App\Filament\Pages;

use App\Services\CacheService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string $view = 'filament.pages.cache-management';

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.cache_management.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament.pages.cache_management.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.pages.cache_management.navigation_group');
    }

    public function mount(): void
    {
        // Get cache data for the view
        $this->getCacheData();
    }

    protected function getHeaderActions(): array
    {
        $site = app('site');

        if (! $site) {
            return [];
        }

        return [
            Action::make('clear_site_cache')
                ->label(__('filament.cache.actions.clear_site_cache'))
                ->icon('heroicon-o-building-office')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('filament.cache.modals.clear_site_cache_heading'))
                ->modalDescription(__('filament.cache.modals.clear_site_cache_description', ['site' => $site->name]))
                ->action(function () use ($site) {
                    CacheService::clearSiteCache($site->id);

                    Notification::make()
                        ->title(__('filament.cache.notifications.site_cache_cleared'))
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('warm_site_cache')
                ->label(__('filament.cache.actions.warm_site_cache'))
                ->icon('heroicon-o-fire')
                ->color('success')
                ->action(function () use ($site) {
                    $warmed = CacheService::warmSiteCache($site->id);

                    $message = __('filament.cache.notifications.cache_warmed_body', [
                        'pages' => $warmed['pages'] ?? 0,
                        'templates' => $warmed['templates'] ?? 0,
                    ]);

                    Notification::make()
                        ->title(__('filament.cache.notifications.cache_warmed_title'))
                        ->body($message)
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('clear_pages_cache')
                ->label(__('filament.cache.actions.clear_pages_cache'))
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('filament.cache.modals.clear_pages_cache_heading'))
                ->modalDescription(__('filament.cache.modals.clear_pages_cache_description', ['site' => $site->name]))
                ->action(function () use ($site) {
                    CacheService::clearPageCache($site->id);

                    Notification::make()
                        ->title(__('filament.cache.notifications.pages_cache_cleared'))
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('clear_templates_cache')
                ->label(__('filament.cache.actions.clear_templates_cache'))
                ->icon('heroicon-o-squares-2x2')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('filament.cache.modals.clear_templates_cache_heading'))
                ->modalDescription(__('filament.cache.modals.clear_templates_cache_description', ['site' => $site->name]))
                ->action(function () use ($site) {
                    CacheService::clearTemplateCache($site->id);

                    Notification::make()
                        ->title(__('filament.cache.notifications.templates_cache_cleared'))
                        ->success()
                        ->send();

                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('debug_cache')
                ->label(__('filament.cache.actions.debug_cache'))
                ->icon('heroicon-o-bug-ant')
                ->color('gray')
                ->action(function () use ($site) {
                    // Populate some test cache data
                    $populated = CacheService::populateTestCache($site->id);

                    // Debug cache keys
                    $debug = CacheService::debugSiteCacheKeys($site->id);

                    $message = "Test cache populated:\n".
                              'Site: '.($populated['site'] ?? 'none')."\n".
                              'Page: '.($populated['page'] ?? 'none')."\n".
                              'Template: '.($populated['template'] ?? 'none')."\n".
                              'Stats: '.($populated['stats'] ?? 'none')."\n\n".
                              'Debug info: '.json_encode($debug, JSON_PRETTY_PRINT);

                    Notification::make()
                        ->title(__('filament.cache.notifications.debug_complete'))
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
