<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;

class ModuleDeactivateCommand extends Command
{
    protected $signature = 'module:deactivate {name : The name of the module to deactivate}';

    protected $description = 'Deactivate a module';

    public function handle(): int
    {
        $name = $this->argument('name');
        $moduleManager = app(ModuleManager::class);

        $module = $moduleManager->getModule($name);
        
        if (!$module) {
            $this->error("Module '{$name}' not found!");
            return 1;
        }

        if (!$module['active']) {
            $this->info("Module '{$name}' is already inactive.");
            return 0;
        }

        $success = $moduleManager->deactivateModule($name);

        if ($success) {
            $this->info("Module '{$name}' deactivated successfully!");
            $this->line("The module routes are no longer available.");
            return 0;
        } else {
            $this->error("Failed to deactivate module '{$name}'.");
            return 1;
        }
    }
}
