<?php

namespace App\Filament\Resources\SiteResource\Pages;

use App\Filament\Resources\SiteResource;
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
}
