<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class ModuleManager
{
    protected string $modulesPath;
    protected array $modules = [];

    public function __construct()
    {
        $this->modulesPath = base_path('modules');
    }

    /**
     * Get all available modules from the modules directory
     */
    public function getAllModules(): array
    {
        return Cache::remember('modules.all', 3600, function () {
            $modules = [];
            
            if (!File::exists($this->modulesPath)) {
                return $modules;
            }

            $directories = File::directories($this->modulesPath);
            
            foreach ($directories as $directory) {
                $moduleName = basename($directory);
                $moduleJsonPath = $directory . '/module.json';
                
                if (File::exists($moduleJsonPath)) {
                    $moduleData = json_decode(File::get($moduleJsonPath), true);
                    if ($moduleData) {
                        $moduleData['path'] = $directory;
                        $moduleData['name'] = $moduleName;
                        $modules[$moduleName] = $moduleData;
                    }
                }
            }
            
            return $modules;
        });
    }

    /**
     * Get active modules
     */
    public function getActiveModules(): array
    {
        $allModules = $this->getAllModules();
        return array_filter($allModules, function ($module) {
            return $module['active'] ?? false;
        });
    }

    /**
     * Check if a module is active
     */
    public function isModuleActive(string $moduleName): bool
    {
        $modules = $this->getAllModules();
        return isset($modules[$moduleName]) && ($modules[$moduleName]['active'] ?? false);
    }

    /**
     * Activate a module
     */
    public function activateModule(string $moduleName): bool
    {
        $modules = $this->getAllModules();
        
        if (!isset($modules[$moduleName])) {
            return false;
        }

        $moduleJsonPath = $modules[$moduleName]['path'] . '/module.json';
        $moduleData = $modules[$moduleName];
        $moduleData['active'] = true;

        File::put($moduleJsonPath, json_encode($moduleData, JSON_PRETTY_PRINT));
        
        // Clear cache
        Cache::forget('modules.all');
        Cache::forget('modules.active');
        
        return true;
    }

    /**
     * Deactivate a module
     */
    public function deactivateModule(string $moduleName): bool
    {
        $modules = $this->getAllModules();
        
        if (!isset($modules[$moduleName])) {
            return false;
        }

        $moduleJsonPath = $modules[$moduleName]['path'] . '/module.json';
        $moduleData = $modules[$moduleName];
        $moduleData['active'] = false;

        File::put($moduleJsonPath, json_encode($moduleData, JSON_PRETTY_PRINT));
        
        // Clear cache
        Cache::forget('modules.all');
        Cache::forget('modules.active');
        
        return true;
    }

    /**
     * Register active module service providers
     */
    public function registerActiveModules(): void
    {
        $activeModules = $this->getActiveModules();
        
        foreach ($activeModules as $moduleName => $moduleData) {
            $serviceProviderClass = "Modules\\{$moduleName}\\{$moduleName}ServiceProvider";
            
            if (class_exists($serviceProviderClass)) {
                app()->register($serviceProviderClass);
            }
        }
    }

    /**
     * Get module by name
     */
    public function getModule(string $moduleName): ?array
    {
        $modules = $this->getAllModules();
        return $modules[$moduleName] ?? null;
    }

    /**
     * Install a module (create directory structure)
     */
    public function installModule(string $moduleName, array $moduleData): bool
    {
        $modulePath = $this->modulesPath . '/' . $moduleName;
        
        if (File::exists($modulePath)) {
            return false; // Module already exists
        }

        // Create module directory structure
        File::makeDirectory($modulePath, 0755, true);
        File::makeDirectory($modulePath . '/Controllers', 0755, true);
        File::makeDirectory($modulePath . '/Models', 0755, true);
        File::makeDirectory($modulePath . '/resources/views', 0755, true);
        File::makeDirectory($modulePath . '/routes', 0755, true);

        // Create module.json
        $moduleData['active'] = false;
        File::put($modulePath . '/module.json', json_encode($moduleData, JSON_PRETTY_PRINT));

        // Clear cache
        Cache::forget('modules.all');
        
        return true;
    }
}
