<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\Page;
use App\Models\Template;
use App\Models\TemplateContent;
use App\Observers\CacheObserver;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (env('APP_ENV') !== 'local') {
            $url->forceScheme('https');
        }

        // Register cache observers for automatic cache invalidation
        $cacheObserver = new CacheObserver();

        Site::observe($cacheObserver);
        Page::observe($cacheObserver);
        Template::observe($cacheObserver);
        TemplateContent::observe($cacheObserver);

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): string => '<link rel="stylesheet" href="/css/filament/admin/theme.css">'
        );
    }
}
