<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;

class ModuleListCommand extends Command
{
    protected $signature = 'module:list {--active : Show only active modules} {--inactive : Show only inactive modules}';

    protected $description = 'List all available modules';

    public function handle(): int
    {
        $moduleManager = app(ModuleManager::class);
        $modules = $moduleManager->getAllModules();

        if (empty($modules)) {
            $this->info('No modules found.');
            return 0;
        }

        // Filter modules based on options
        if ($this->option('active')) {
            $modules = array_filter($modules, fn($module) => $module['active'] ?? false);
        } elseif ($this->option('inactive')) {
            $modules = array_filter($modules, fn($module) => !($module['active'] ?? false));
        }

        if (empty($modules)) {
            $filter = $this->option('active') ? 'active' : 'inactive';
            $this->info("No {$filter} modules found.");
            return 0;
        }

        $headers = ['Name', 'Display Name', 'Version', 'Status', 'Author', 'Description'];
        $rows = [];

        foreach ($modules as $name => $module) {
            $rows[] = [
                $name,
                $module['display_name'] ?? $name,
                $module['version'] ?? 'N/A',
                ($module['active'] ?? false) ? '✅ Active' : '❌ Inactive',
                $module['author'] ?? 'Unknown',
                $this->truncate($module['description'] ?? '', 50),
            ];
        }

        $this->table($headers, $rows);

        $totalCount = count($modules);
        $activeCount = count(array_filter($modules, fn($module) => $module['active'] ?? false));
        $inactiveCount = $totalCount - $activeCount;

        $this->newLine();
        $this->line("Total: {$totalCount} modules ({$activeCount} active, {$inactiveCount} inactive)");

        return 0;
    }

    private function truncate(string $text, int $length): string
    {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }
}
