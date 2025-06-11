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

        return [
            Action::make('clear_all_cache')
                ->label('Clear All Cache')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear All Cache')
                ->modalDescription('This will clear all cached data across the entire application. Are you sure?')
                ->action(function () {
                    CacheService::clearAllCache();
                    
                    Notification::make()
                        ->title('All cache cleared successfully')
                        ->success()
                        ->send();
                        
                    $this->redirect(request()->header('Referer'));
                }),

            Action::make('clear_site_cache')
                ->label('Clear Site Cache')
                ->icon('heroicon-o-building-office')
                ->color('warning')
                ->visible(fn () => $site && $site->id > 0)
                ->requiresConfirmation()
                ->modalHeading('Clear Site Cache')
                ->modalDescription('This will clear all cached data for the current site. Are you sure?')
                ->action(function () use ($site) {
                    if ($site) {
                        CacheService::clearSiteCache($site->id);
                        
                        Notification::make()
                            ->title('Site cache cleared successfully')
                            ->success()
                            ->send();
                            
                        $this->redirect(request()->header('Referer'));
                    }
                }),

            Action::make('warm_site_cache')
                ->label('Warm Site Cache')
                ->icon('heroicon-o-fire')
                ->color('success')
                ->visible(fn () => $site && $site->id > 0)
                ->action(function () use ($site) {
                    if ($site) {
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
                    }
                }),

            Action::make('clear_pages_cache')
                ->label('Clear Pages Cache')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->visible(fn () => $site && $site->id > 0)
                ->requiresConfirmation()
                ->action(function () use ($site) {
                    if ($site) {
                        CacheService::clearPageCache($site->id);
                        
                        Notification::make()
                            ->title('Pages cache cleared successfully')
                            ->success()
                            ->send();
                            
                        $this->redirect(request()->header('Referer'));
                    }
                }),

            Action::make('clear_templates_cache')
                ->label('Clear Templates Cache')
                ->icon('heroicon-o-squares-2x2')
                ->color('warning')
                ->visible(fn () => $site && $site->id > 0)
                ->requiresConfirmation()
                ->action(function () use ($site) {
                    if ($site) {
                        CacheService::clearTemplateCache($site->id);
                        
                        Notification::make()
                            ->title('Templates cache cleared successfully')
                            ->success()
                            ->send();
                            
                        $this->redirect(request()->header('Referer'));
                    }
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
