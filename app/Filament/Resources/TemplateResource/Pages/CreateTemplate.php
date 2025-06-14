<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTemplate extends CreateRecord
{
    protected static string $resource = TemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['site_id'] = app('site')?->id;

        return $data;
    }
}
