<?php

namespace Modules\TestModule;

use App\Services\BaseModuleServiceProvider;

class TestModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Register module services
     */
    protected function registerServices(): void
    {
        // Register any module-specific services here
        // Example: $this->app->bind('TestModuleService', TestModuleService::class);
    }

    /**
     * Boot module
     */
    public function boot(): void
    {
        parent::boot();
        
        // Any additional boot logic for the TestModule module
        // Example: Event listeners, view composers, etc.
    }
}