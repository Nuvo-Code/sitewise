<?php

namespace App\Filament\Resources\SiteResource\Pages;

use App\Filament\Resources\SiteResource;
use App\Services\CacheService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;

    protected static ?string $title = 'Site Settings';

    public function mount(int | string | null $record = null): void
    {
        // Always use the current site instead of a record parameter
        $this->record = app('site');

        if (!$this->record) {
            abort(404, 'Site not found');
        }

        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [
            // Remove delete action - sites shouldn't be deleted from admin
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Redirect back to the same page after saving
        return static::getUrl();
    }

    protected function afterSave(): void
    {
        // Clear site cache after saving to ensure fresh data is displayed
        if ($this->record) {
            CacheService::clearSiteCache($this->record->id);

            // Refresh the site instance in the app container with fresh data
            app()->instance('site', $this->record->fresh());

            // Show success notification with cache clearing info
            Notification::make()
                ->title('Site settings updated successfully')
                ->body('Cache has been cleared to reflect your changes.')
                ->success()
                ->send();
        }
    }
}
