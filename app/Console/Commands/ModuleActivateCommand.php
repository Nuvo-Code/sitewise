<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;

class ModuleActivateCommand extends Command
{
    protected $signature = 'module:activate {name : The name of the module to activate}';

    protected $description = 'Activate a module';

    public function handle(): int
    {
        $name = $this->argument('name');
        $moduleManager = app(ModuleManager::class);

        $module = $moduleManager->getModule($name);
        
        if (!$module) {
            $this->error("Module '{$name}' not found!");
            return 1;
        }

        if ($module['active']) {
            $this->info("Module '{$name}' is already active.");
            return 0;
        }

        $success = $moduleManager->activateModule($name);

        if ($success) {
            $this->info("Module '{$name}' activated successfully!");
            $this->line("The module is now available and its routes are registered.");
            return 0;
        } else {
            $this->error("Failed to activate module '{$name}'.");
            return 1;
        }
    }
}
