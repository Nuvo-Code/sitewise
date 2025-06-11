<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\Page;
use App\Models\Template;
use App\Models\TemplateContent;
use App\Observers\CacheObserver;
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
    public function boot(): void
    {
        // Register cache observers for automatic cache invalidation
        $cacheObserver = new CacheObserver();

        Site::observe($cacheObserver);
        Page::observe($cacheObserver);
        Template::observe($cacheObserver);
        TemplateContent::observe($cacheObserver);
    }
}
