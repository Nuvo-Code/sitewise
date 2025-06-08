<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Services\TemplateContentService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load template content into the form
        if ($this->record->template_id) {
            $templateContent = TemplateContentService::getContentForPage($this->record);
            $data['template_content'] = $templateContent;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract template content from the main data array
        $templateContent = $data['template_content'] ?? [];
        unset($data['template_content']);

        // Store template content temporarily for after save
        $this->templateContent = $templateContent;

        return $data;
    }

    protected function afterSave(): void
    {
        // Save template content after page update
        if (!empty($this->templateContent) && $this->record->template_id) {
            TemplateContentService::updateContentForPage($this->record, $this->templateContent);
        }

        // Auto-generate missing template content fields if template changed
        if ($this->record->template_id) {
            TemplateContentService::autoGenerateContentFields($this->record);
        }
    }

    private array $templateContent = [];
}
