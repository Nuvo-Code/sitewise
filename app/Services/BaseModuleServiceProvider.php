<?php

namespace App\Services;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

abstract class BaseModuleServiceProvider extends ServiceProvider
{
    protected string $moduleName;
    protected string $modulePath;

    public function __construct($app)
    {
        parent::__construct($app);
        
        // Extract module name from class name (e.g., BlogServiceProvider -> Blog)
        $className = class_basename(static::class);
        $this->moduleName = str_replace('ServiceProvider', '', $className);
        $this->modulePath = base_path("modules/{$this->moduleName}");
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootRoutes();
        $this->bootViews();
        $this->bootMigrations();
        $this->bootTranslations();
    }

    /**
     * Register module configuration
     */
    protected function registerConfig(): void
    {
        $configPath = $this->modulePath . '/config';
        
        if (is_dir($configPath)) {
            foreach (glob($configPath . '/*.php') as $file) {
                $configName = strtolower($this->moduleName) . '.' . basename($file, '.php');
                $this->mergeConfigFrom($file, $configName);
            }
        }
    }

    /**
     * Register module services
     */
    protected function registerServices(): void
    {
        // Override in child classes to register module-specific services
    }

    /**
     * Boot module routes
     */
    protected function bootRoutes(): void
    {
        $webRoutesPath = $this->modulePath . '/routes/web.php';
        $apiRoutesPath = $this->modulePath . '/routes/api.php';

        if (file_exists($webRoutesPath)) {
            Route::middleware('web')
                ->prefix(strtolower($this->moduleName))
                ->name(strtolower($this->moduleName) . '.')
                ->group($webRoutesPath);
        }

        if (file_exists($apiRoutesPath)) {
            Route::middleware('api')
                ->prefix('api/' . strtolower($this->moduleName))
                ->name('api.' . strtolower($this->moduleName) . '.')
                ->group($apiRoutesPath);
        }
    }

    /**
     * Boot module views
     */
    protected function bootViews(): void
    {
        $viewsPath = $this->modulePath . '/resources/views';
        
        if (is_dir($viewsPath)) {
            View::addNamespace(strtolower($this->moduleName), $viewsPath);
        }
    }

    /**
     * Boot module migrations
     */
    protected function bootMigrations(): void
    {
        $migrationsPath = $this->modulePath . '/database/migrations';
        
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    /**
     * Boot module translations
     */
    protected function bootTranslations(): void
    {
        $translationsPath = $this->modulePath . '/resources/lang';
        
        if (is_dir($translationsPath)) {
            $this->loadTranslationsFrom($translationsPath, strtolower($this->moduleName));
        }
    }

    /**
     * Get module name
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * Get module path
     */
    public function getModulePath(): string
    {
        return $this->modulePath;
    }
}
