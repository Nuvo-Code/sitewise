<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page;
use App\Services\TemplateContentService;
use App\Services\BladeTemplateService;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['site_id'] = app('site')?->id;

        // Extract template content from the main data array
        $templateContent = $data['template_content'] ?? [];
        unset($data['template_content']);

        // Store template content temporarily for after creation
        $this->templateContent = $templateContent;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Save template content after page creation
        if (!empty($this->templateContent) && $this->record->template_id) {
            TemplateContentService::updateContentForPage($this->record, $this->templateContent);
        }

        // Auto-generate missing template content fields
        if ($this->record->template_id) {
            TemplateContentService::autoGenerateContentFields($this->record);
        }

        // Clear template cache to ensure fresh rendering
        if ($this->record->template) {
            BladeTemplateService::clearTemplateCache($this->record->template);
        }
    }

    private array $templateContent = [];
}
