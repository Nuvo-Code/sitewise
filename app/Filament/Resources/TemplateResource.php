<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Models\Template;
use App\Services\BladeTemplateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('site_id')
                    ->default(fn() => app('site')?->id),

                Forms\Components\Tabs::make('Template Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Template Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Template::class, 'name', ignoreRecord: true)
                                    ->prefixIcon('heroicon-o-document-duplicate')
                                    ->helperText('A unique name for this template'),

                                Forms\Components\Toggle::make('active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Enable to make this template available for pages')
                                    ->inline(false),

                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->helperText('Optional description of what this template is for')
                                    ->placeholder('Describe the purpose and usage of this template...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Structure')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Forms\Components\Repeater::make('structure')
                                    ->label('Template Fields')
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Field Name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $set('key', str_replace([' ', '-'], '_', strtolower($state)));
                                            })
                                            ->prefixIcon('heroicon-o-tag'),

                                        Forms\Components\Hidden::make('key'),

                                        Forms\Components\Select::make('type')
                                            ->label('Field Type')
                                            ->required()
                                            ->options([
                                                'text' => 'Text Input',
                                                'textarea' => 'Textarea',
                                                'rich_text' => 'Rich Text Editor',
                                                'number' => 'Number',
                                                'email' => 'Email',
                                                'url' => 'URL',
                                                'date' => 'Date',
                                                'datetime' => 'Date & Time',
                                                'select' => 'Select Dropdown',
                                                'checkbox' => 'Checkbox',
                                                'toggle' => 'Toggle Switch',
                                                'file' => 'File Upload',
                                                'image' => 'Image Upload',
                                                'color' => 'Color Picker',
                                            ])
                                            ->live()
                                            ->prefixIcon('heroicon-o-cog-6-tooth'),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Field Description')
                                            ->rows(2)
                                            ->helperText('Optional description for content editors')
                                            ->placeholder('Help text for content editors...')
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('required')
                                            ->label('Required Field')
                                            ->default(false)
                                            ->inline(false),

                                        // Validation rules
                                        Forms\Components\TagsInput::make('validation_rules')
                                            ->label('Validation Rules')
                                            ->helperText('Laravel validation rules (e.g., min:3, max:255)')
                                            ->placeholder('Add validation rule'),

                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default value for this field')
                                                ->placeholder('Default value...')
                                                ->visible(fn(Forms\Get $get) => in_array($get('type'), ['text', 'email', 'url']))
                                                ->default(''),

                                            Forms\Components\Textarea::make('default_value')
                                                ->label('Default Value')
                                                ->rows(2)
                                                ->helperText('Optional default value for this field')
                                                ->placeholder('Default value...')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'textarea'),

                                            Forms\Components\RichEditor::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default value for this field')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'rich_text'),

                                            Forms\Components\TextInput::make('default_value')
                                                ->label('Default Value')
                                                ->numeric()
                                                ->helperText('Optional default value for this field')
                                                ->placeholder('0')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'number'),

                                            Forms\Components\DatePicker::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default value for this field')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'date'),

                                            Forms\Components\DateTimePicker::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default value for this field')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'datetime'),

                                            Forms\Components\Select::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Select a default option')
                                                ->options(fn(Forms\Get $get) => $get('options') ?? [])
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'select'),

                                            Forms\Components\Toggle::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Default state for this field')
                                                ->visible(fn(Forms\Get $get) => in_array($get('type'), ['checkbox', 'toggle'])),

                                            Forms\Components\ColorPicker::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default color for this field')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'color'),
                                        ])->columnSpanFull(),

                                        // Options for select fields
                                        Forms\Components\KeyValue::make('options')
                                            ->label('Select Options')
                                            ->keyLabel('Value')
                                            ->valueLabel('Label')
                                            ->addActionLabel('Add Option')
                                            ->visible(fn(Forms\Get $get) => $get('type') === 'select')
                                            ->helperText('Define the available options for this select field'),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'New Field')
                                    ->addActionLabel('Add Template Field')
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->default([
                                        [
                                            'name' => 'Title',
                                            'key' => 'title',
                                            'type' => 'text',
                                            'required' => true,
                                            'description' => 'The main title for this content',
                                        ],
                                        [
                                            'name' => 'Content',
                                            'key' => 'content',
                                            'type' => 'rich_text',
                                            'required' => true,
                                            'description' => 'The main content body',
                                        ],
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Blade Template')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Forms\Components\Textarea::make('blade_template')
                                    ->label('Blade Template Content')
                                    ->rows(20)
                                    ->helperText('Define a Blade template to render pages with this template. Use variables like {{ $title }}, {{ $content }}, etc.')
                                    ->placeholder('Enter Blade template HTML...')
                                    ->columnSpanFull(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('generate_sample')
                                        ->label('Generate Sample Template')
                                        ->icon('heroicon-o-sparkles')
                                        ->color('success')
                                        ->action(function (Forms\Set $set, Forms\Get $get) {
                                            $structure = $get('structure') ?? [];
                                            if (!empty($structure)) {
                                                // Create a temporary template to generate sample
                                                $tempTemplate = new Template();
                                                $tempTemplate->structure = $structure;
                                                $sample = BladeTemplateService::generateSampleBladeTemplate($tempTemplate);
                                                $set('blade_template', $sample);
                                            }
                                        })
                                        ->visible(fn(Forms\Get $get) => !empty($get('structure')))
                                        ->tooltip('Generate a sample Blade template based on your fields'),

                                    Forms\Components\Actions\Action::make('show_variables')
                                        ->label('Show Available Variables')
                                        ->icon('heroicon-o-information-circle')
                                        ->color('info')
                                        ->modalHeading('Available Template Variables')
                                        ->modalContent(function (Forms\Get $get) {
                                            $structure = $get('structure') ?? [];
                                            if (empty($structure)) {
                                                return 'Define template fields first to see available variables.';
                                            }

                                            $tempTemplate = new Template();
                                            $tempTemplate->structure = $structure;
                                            $variables = BladeTemplateService::getAvailableVariables($tempTemplate);

                                            $content = '<div class="space-y-2">';
                                            $content .= '<h4 class="font-semibold">System Variables:</h4>';
                                            $content .= '<ul class="list-disc list-inside space-y-1">';
                                            foreach (['page', 'site', 'template', 'content', 'page_title', 'page_slug', 'site_name', 'site_domain'] as $var) {
                                                $content .= "<li><code>\${$var}</code> - {$variables[$var]}</li>";
                                            }
                                            $content .= '</ul>';

                                            $fieldVars = array_filter($variables, fn($key) => !in_array($key, ['page', 'site', 'template', 'content', 'page_title', 'page_slug', 'site_name', 'site_domain']), ARRAY_FILTER_USE_KEY);
                                            if (!empty($fieldVars)) {
                                                $content .= '<h4 class="font-semibold mt-4">Template Field Variables:</h4>';
                                                $content .= '<ul class="list-disc list-inside space-y-1">';
                                                foreach ($fieldVars as $var => $desc) {
                                                    $content .= "<li><code>\${$var}</code> - {$desc}</li>";
                                                }
                                                $content .= '</ul>';
                                            }
                                            $content .= '</div>';

                                            return new \Illuminate\Support\HtmlString($content);
                                        })
                                        ->modalSubmitAction(false)
                                        ->modalCancelActionLabel('Close')
                                        ->visible(fn(Forms\Get $get) => !empty($get('structure')))
                                        ->tooltip('View all available variables for your template'),
                                ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->default('—'),

                Tables\Columns\TextColumn::make('pages_count')
                    ->label('Pages Using')
                    ->counts('pages'),

                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('site_id', app('site')?->id)
            ->withCount('pages');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
