<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\Page;
use App\Models\Template;
use App\Models\TemplateContent;
use App\Observers\CacheObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
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

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'tr']);
        });

        // Register AI Floating Button
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::BODY_END,
        //     function (): string {
        //         $site = app('site');
        //         if (!$site || !$site->isAiEnabled()) {
        //             return '';
        //         }

        //         return view('components.ai-floating-button-hook')->render();
        //     }
        // );
    }
}
