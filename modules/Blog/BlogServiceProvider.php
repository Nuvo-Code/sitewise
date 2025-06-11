<?php

namespace Modules\Blog;

use App\Services\BaseModuleServiceProvider;

class BlogServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Register module services
     */
    protected function registerServices(): void
    {
        // Register any module-specific services here
        // Example: $this->app->bind('blog.service', BlogService::class);
    }

    /**
     * Boot module
     */
    public function boot(): void
    {
        parent::boot();
        
        // Any additional boot logic for the blog module
        // Example: Event listeners, view composers, etc.
    }
}
