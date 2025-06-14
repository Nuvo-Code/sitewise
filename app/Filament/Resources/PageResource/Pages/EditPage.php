<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Services\BladeTemplateService;
use App\Services\TemplateContentService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('visit')
                ->label('Visit Page')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(function (): string {
                    $site = app('site');
                    if (! $site) {
                        return '#';
                    }

                    // Determine protocol based on environment
                    $protocol = env('APP_ENV') === 'local' ? 'http' : 'https';
                    $baseUrl = "{$protocol}://{$site->domain}";

                    // Handle homepage slugs
                    $homepageSlugs = ['home', 'homepage', 'index'];
                    if (in_array($this->record->slug, $homepageSlugs)) {
                        return $baseUrl;
                    }

                    return "{$baseUrl}/{$this->record->slug}";
                })
                ->openUrlInNewTab()
                ->visible(function (): bool {
                    return $this->record->active && app('site')?->is_setup_complete;
                }),
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

        // Check if template has changed
        $this->templateChanged = isset($data['template_id']) &&
                                $data['template_id'] !== $this->record->template_id;

        // Store old template for cache clearing
        if ($this->templateChanged && $this->record->template) {
            $this->oldTemplate = $this->record->template;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Clear old template cache if template changed
        if ($this->templateChanged && $this->oldTemplate) {
            BladeTemplateService::clearTemplateCache($this->oldTemplate);
        }

        // Save template content after page update
        if (! empty($this->templateContent) && $this->record->template_id) {
            TemplateContentService::updateContentForPage($this->record, $this->templateContent);
        }

        // Auto-generate missing template content fields if template changed
        if ($this->record->template_id) {
            TemplateContentService::autoGenerateContentFields($this->record);
        }

        // Clear new template cache to ensure fresh rendering
        if ($this->record->template) {
            BladeTemplateService::clearTemplateCache($this->record->template);
        }
    }

    private array $templateContent = [];

    private bool $templateChanged = false;

    private $oldTemplate = null;
}
