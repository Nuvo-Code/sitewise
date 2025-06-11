<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Template;
use App\Models\TemplateContent;
use Filament\Forms\Components;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class TemplateContentService
{
    /**
     * Generate form components for template fields
     */
    public static function generateFormComponents(Template $template): array
    {
        $components = [];
        $fields = $template->getFieldsForFormAttribute();

        foreach ($fields as $field) {
            $component = self::createFormComponent($field);
            if ($component) {
                $components[] = $component;
            }
        }

        return $components;
    }

    /**
     * Create a form component based on field configuration
     */
    private static function createFormComponent(array $field): ?Components\Component
    {
        $key = $field['key'];
        $type = $field['type'];
        $name = $field['name'] ?? ucwords(str_replace('_', ' ', $key));
        $required = $field['required'] ?? false;
        $description = $field['description'] ?? null;
        $defaultValue = $field['default_value'] ?? null;
        $validationRules = $field['validation_rules'] ?? [];

        $component = match ($type) {
            'text' => Components\TextInput::make("template_content.{$key}")
                ->label($name)
                ->maxLength(255),

            'textarea' => Components\Textarea::make("template_content.{$key}")
                ->label($name)
                ->rows(4),

            'rich_text' => Components\RichEditor::make("template_content.{$key}")
                ->label($name)
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'h2', 'h3', 'bulletList', 'orderedList',
                    'link', 'blockquote', 'codeBlock',
                ]),

            'html' => CodeEditor::make("template_content.{$key}")
                ->label($name),

            'css' => CodeEditor::make("template_content.{$key}")
                ->label($name),

            'javascript' => CodeEditor::make("template_content.{$key}")
                ->label($name),

            'number' => Components\TextInput::make("template_content.{$key}")
                ->label($name)
                ->numeric(),

            'email' => Components\TextInput::make("template_content.{$key}")
                ->label($name)
                ->email(),

            'url' => Components\TextInput::make("template_content.{$key}")
                ->label($name)
                ->url(),

            'date' => Components\DatePicker::make("template_content.{$key}")
                ->label($name),

            'datetime' => Components\DateTimePicker::make("template_content.{$key}")
                ->label($name),

            'select' => Components\Select::make("template_content.{$key}")
                ->label($name)
                ->options($field['options'] ?? []),

            'checkbox' => Components\Checkbox::make("template_content.{$key}")
                ->label($name),

            'toggle' => Components\Toggle::make("template_content.{$key}")
                ->label($name),

            'file' => Components\FileUpload::make("template_content.{$key}")
                ->label($name)
                ->disk('public')
                ->directory('template-files'),

            'image' => Components\FileUpload::make("template_content.{$key}")
                ->label($name)
                ->image()
                ->disk('public')
                ->directory('template-images')
                ->imageEditor(),

            'color' => Components\ColorPicker::make("template_content.{$key}")
                ->label($name),

            default => Components\TextInput::make("template_content.{$key}")
                ->label($name),
        };

        if ($component) {
            if ($required) {
                $component->required();
            }

            if ($description) {
                $component->helperText($description);
            }

            if ($defaultValue) {
                $component->default($defaultValue);
            }

            // Apply validation rules
            if (!empty($validationRules)) {
                $component->rules($validationRules);
            }
        }

        return $component;
    }

    /**
     * Get template content for a page as key-value pairs
     */
    public static function getContentForPage(Page $page): array
    {
        if (!$page->template) {
            return [];
        }

        return TemplateContent::where('page_id', $page->id)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Update template content for a page
     */
    public static function updateContentForPage(Page $page, array $content): void
    {
        if (!$page->template) {
            return;
        }

        $templateFields = $page->template->getFieldKeysAttribute();

        foreach ($content as $key => $value) {
            // Only save content for fields that exist in the template
            if (in_array($key, $templateFields)) {
                // Ensure value is never null - use empty string as fallback
                if ($value === null) {
                    $value = '';
                }

                TemplateContent::updateOrCreate(
                    [
                        'page_id' => $page->id,
                        'template_id' => $page->template_id,
                        'key' => $key,
                    ],
                    ['value' => $value]
                );
            }
        }

        // Remove content for fields that no longer exist in the template
        TemplateContent::where('page_id', $page->id)
            ->whereNotIn('key', $templateFields)
            ->delete();
    }

    /**
     * Auto-generate template content fields for a page
     */
    public static function autoGenerateContentFields(Page $page): void
    {
        if (!$page->template) {
            return;
        }

        $existingKeys = TemplateContent::where('page_id', $page->id)
            ->pluck('key')
            ->toArray();

        $templateFields = $page->template->getFieldsForFormAttribute();

        foreach ($templateFields as $field) {
            $key = $field['key'];

            if (!in_array($key, $existingKeys)) {
                // Ensure value is never null - use empty string as fallback
                $defaultValue = $field['default_value'] ?? '';
                if ($defaultValue === null) {
                    $defaultValue = '';
                }

                TemplateContent::create([
                    'page_id' => $page->id,
                    'template_id' => $page->template_id,
                    'key' => $key,
                    'value' => $defaultValue,
                ]);
            }
        }
    }

    /**
     * Validate template content against field requirements
     */
    public static function validateContent(Template $template, array $content): array
    {
        $errors = [];
        $fields = $template->getFieldsForFormAttribute();

        foreach ($fields as $field) {
            $key = $field['key'];
            $required = $field['required'] ?? false;
            $value = $content[$key] ?? null;

            if ($required && (is_null($value) || $value === '' || $value === [])) {
                $errors[$key] = "The {$field['name']} field is required.";
            }
        }

        return $errors;
    }
}
