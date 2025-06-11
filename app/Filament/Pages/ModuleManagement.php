<?php

namespace App\Filament\Pages;

use App\Services\ModuleManager;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ModuleManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string $view = 'filament.pages.module-management';

    protected static ?string $navigationLabel = 'Modules';

    protected static ?string $title = 'Module Management';

    public $modules = [];

    public function mount(): void
    {
        $this->loadModules();
    }

    protected function loadModules(): void
    {
        $moduleManager = app(ModuleManager::class);
        $this->modules = $moduleManager->getAllModules();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh_modules')
                ->label('Refresh Modules')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    // Clear module cache
                    cache()->forget('modules.all');
                    cache()->forget('modules.active');

                    $this->loadModules();

                    Notification::make()
                        ->title('Modules refreshed successfully!')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function toggleModule(string $moduleName): void
    {
        $moduleManager = app(ModuleManager::class);
        $module = $moduleManager->getModule($moduleName);

        if (!$module) {
            Notification::make()
                ->title('Module not found!')
                ->danger()
                ->send();
            return;
        }

        if ($module['active']) {
            $success = $moduleManager->deactivateModule($moduleName);
            $message = $success ? 'Module deactivated successfully!' : 'Failed to deactivate module.';
        } else {
            $success = $moduleManager->activateModule($moduleName);
            $message = $success ? 'Module activated successfully!' : 'Failed to activate module.';
        }

        if ($success) {
            $this->loadModules();
            Notification::make()
                ->title($message)
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title($message)
                ->danger()
                ->send();
        }
    }


}
