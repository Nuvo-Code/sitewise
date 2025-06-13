<?php

namespace App\Filament\Resources;

use AbdelhamidErrahmouni\FilamentMonacoEditor\MonacoEditor;
use App\Filament\Resources\TemplateResource\Pages;
use App\Models\Template;
use App\Services\BladeTemplateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

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
                                                'html' => 'HTML (Code Editor)',
                                                'css' => 'CSS (Code Editor)',
                                                'javascript' => 'JavaScript (Code Editor)',
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

                                            CodeEditor::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default HTML code for this field')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'html'),

                                            CodeEditor::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default CSS code for this field')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'css'),

                                            CodeEditor::make('default_value')
                                                ->label('Default Value')
                                                ->helperText('Optional default JavaScript code for this field')
                                                ->visible(fn(Forms\Get $get) => $get('type') === 'javascript'),

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
                                MonacoEditor::make('blade_template')
                                    ->label('Blade Template Content')
                                    ->columnSpanFull(),

                                Forms\Components\Placeholder::make('template_info')
                                    ->label('')
                                    ->content('Define a Blade template to render pages with this template. Use variables like {{ $title }}, {{ $content }}, etc.')
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

                        Forms\Components\Tabs\Tab::make('Asset Paths')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Placeholder::make('asset_paths_info')
                                    ->label('')
                                    ->content('Reference these prepared asset paths in your Blade templates. Click any path to copy it to your clipboard.')
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Vite Assets')
                                    ->description('Compiled CSS and JavaScript files via Laravel Vite')
                                    ->schema([
                                        Forms\Components\Placeholder::make('vite_assets')
                                            ->label('')
                                            ->content(function () {
                                                $paths = [
                                                    '@vite([\'resources/css/app.css\', \'resources/js/app.js\'])' => 'Main application assets',
                                                    '@vite(\'resources/css/app.css\')' => 'Main CSS file only',
                                                    '@vite(\'resources/js/app.js\')' => 'Main JavaScript file only',
                                                ];

                                                $content = '<div class="space-y-3">';
                                                foreach ($paths as $path => $description) {
                                                    $content .= '<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border">';
                                                    $content .= '<div class="flex-1">';
                                                    $content .= '<code class="text-sm font-mono text-blue-600 dark:text-blue-400 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 px-2 py-1 rounded" onclick="navigator.clipboard.writeText(\'' . htmlspecialchars($path) . '\'); this.style.backgroundColor=\'#10b981\'; this.style.color=\'white\'; setTimeout(() => { this.style.backgroundColor=\'\'; this.style.color=\'\'; }, 1000);">' . htmlspecialchars($path) . '</code>';
                                                    $content .= '<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">' . $description . '</p>';
                                                    $content .= '</div>';
                                                    $content .= '</div>';
                                                }
                                                $content .= '</div>';

                                                return new \Illuminate\Support\HtmlString($content);
                                            })
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Tailwind CSS Classes')
                                    ->description('Common Tailwind CSS utility classes for styling')
                                    ->schema([
                                        Forms\Components\Placeholder::make('tailwind_classes')
                                            ->label('')
                                            ->content(function () {
                                                $classes = [
                                                    'Layout' => [
                                                        'container mx-auto px-4' => 'Responsive container with padding',
                                                        'flex items-center justify-between' => 'Flexbox with space between',
                                                        'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' => 'Responsive grid layout',
                                                        'w-full h-screen' => 'Full width and height',
                                                    ],
                                                    'Typography' => [
                                                        'text-3xl font-bold text-gray-900 dark:text-white' => 'Large heading',
                                                        'text-lg text-gray-600 dark:text-gray-300' => 'Body text',
                                                        'text-sm text-gray-500 dark:text-gray-400' => 'Small text',
                                                    ],
                                                    'Buttons' => [
                                                        'bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg' => 'Primary button',
                                                        'bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg' => 'Secondary button',
                                                    ],
                                                    'Cards' => [
                                                        'bg-white dark:bg-gray-800 rounded-lg shadow-md p-6' => 'Basic card',
                                                        'bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700' => 'Card with border',
                                                    ],
                                                ];

                                                $content = '<div class="space-y-4">';
                                                foreach ($classes as $category => $categoryClasses) {
                                                    $content .= '<div>';
                                                    $content .= '<h4 class="font-semibold text-gray-900 dark:text-white mb-2">' . $category . '</h4>';
                                                    $content .= '<div class="space-y-2">';
                                                    foreach ($categoryClasses as $class => $description) {
                                                        $content .= '<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border">';
                                                        $content .= '<div class="flex-1">';
                                                        $content .= '<code class="text-sm font-mono text-green-600 dark:text-green-400 cursor-pointer hover:bg-green-50 dark:hover:bg-green-900 px-2 py-1 rounded break-all" onclick="navigator.clipboard.writeText(\'' . htmlspecialchars($class) . '\'); this.style.backgroundColor=\'#10b981\'; this.style.color=\'white\'; setTimeout(() => { this.style.backgroundColor=\'\'; this.style.color=\'\'; }, 1000);">' . htmlspecialchars($class) . '</code>';
                                                        $content .= '<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">' . $description . '</p>';
                                                        $content .= '</div>';
                                                        $content .= '</div>';
                                                    }
                                                    $content .= '</div>';
                                                    $content .= '</div>';
                                                }
                                                $content .= '</div>';

                                                return new \Illuminate\Support\HtmlString($content);
                                            })
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Static Assets')
                                    ->description('Public files and storage assets')
                                    ->schema([
                                        Forms\Components\Placeholder::make('static_assets')
                                            ->label('')
                                            ->content(function () {
                                                $paths = [
                                                    'Public Assets' => [
                                                        'asset(\'css/custom.css\')' => 'Custom CSS file in public/css/',
                                                        'asset(\'js/custom.js\')' => 'Custom JavaScript file in public/js/',
                                                        'asset(\'images/logo.png\')' => 'Image file in public/images/',
                                                        'asset(\'favicon.ico\')' => 'Favicon file',
                                                    ],
                                                    'Storage Assets' => [
                                                        'Storage::url(\'site-logos/logo.png\')' => 'Site logo from storage',
                                                        'Storage::url(\'site-favicons/favicon.ico\')' => 'Site favicon from storage',
                                                        'Storage::url(\'template-images/hero.jpg\')' => 'Template image from storage',
                                                        'Storage::url(\'template-files/document.pdf\')' => 'Template file from storage',
                                                    ],
                                                ];

                                                $content = '<div class="space-y-4">';
                                                foreach ($paths as $category => $categoryPaths) {
                                                    $content .= '<div>';
                                                    $content .= '<h4 class="font-semibold text-gray-900 dark:text-white mb-2">' . $category . '</h4>';
                                                    $content .= '<div class="space-y-2">';
                                                    foreach ($categoryPaths as $path => $description) {
                                                        $content .= '<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border">';
                                                        $content .= '<div class="flex-1">';
                                                        $content .= '<code class="text-sm font-mono text-purple-600 dark:text-purple-400 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900 px-2 py-1 rounded" onclick="navigator.clipboard.writeText(\'{{ ' . htmlspecialchars($path) . ' }}\'); this.style.backgroundColor=\'#10b981\'; this.style.color=\'white\'; setTimeout(() => { this.style.backgroundColor=\'\'; this.style.color=\'\'; }, 1000);">{{ ' . htmlspecialchars($path) . ' }}</code>';
                                                        $content .= '<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">' . $description . '</p>';
                                                        $content .= '</div>';
                                                        $content .= '</div>';
                                                    }
                                                    $content .= '</div>';
                                                    $content .= '</div>';
                                                }
                                                $content .= '</div>';

                                                return new \Illuminate\Support\HtmlString($content);
                                            })
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('External Resources')
                                    ->description('CDN and external asset links')
                                    ->schema([
                                        Forms\Components\Placeholder::make('external_resources')
                                            ->label('')
                                            ->content(function () {
                                                $resources = [
                                                    'Fonts' => [
                                                        'https://fonts.bunny.net/css?family=inter:400,500,600,700' => 'Inter font family',
                                                        'https://fonts.bunny.net/css?family=instrument-sans:400,500,600' => 'Instrument Sans font family',
                                                        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap' => 'Roboto font family',
                                                    ],
                                                    'Icons' => [
                                                        'https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/index.js' => 'Heroicons outline',
                                                        'https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js' => 'Lucide icons',
                                                        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' => 'Font Awesome icons',
                                                    ],
                                                    'Frameworks' => [
                                                        'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js' => 'Alpine.js framework',
                                                        'https://unpkg.com/htmx.org@1.9.10' => 'HTMX library',
                                                    ],
                                                ];

                                                $content = '<div class="space-y-4">';
                                                foreach ($resources as $category => $categoryResources) {
                                                    $content .= '<div>';
                                                    $content .= '<h4 class="font-semibold text-gray-900 dark:text-white mb-2">' . $category . '</h4>';
                                                    $content .= '<div class="space-y-2">';
                                                    foreach ($categoryResources as $url => $description) {
                                                        $content .= '<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border">';
                                                        $content .= '<div class="flex-1">';
                                                        $content .= '<code class="text-sm font-mono text-orange-600 dark:text-orange-400 cursor-pointer hover:bg-orange-50 dark:hover:bg-orange-900 px-2 py-1 rounded break-all" onclick="navigator.clipboard.writeText(\'' . htmlspecialchars($url) . '\'); this.style.backgroundColor=\'#10b981\'; this.style.color=\'white\'; setTimeout(() => { this.style.backgroundColor=\'\'; this.style.color=\'\'; }, 1000);">' . htmlspecialchars($url) . '</code>';
                                                        $content .= '<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">' . $description . '</p>';
                                                        $content .= '</div>';
                                                        $content .= '</div>';
                                                    }
                                                    $content .= '</div>';
                                                    $content .= '</div>';
                                                }
                                                $content .= '</div>';

                                                return new \Illuminate\Support\HtmlString($content);
                                            })
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Sitewise Assets')
                                    ->description('Shared platform assets via /sitewise-assets')
                                    ->schema([
                                        Forms\Components\Placeholder::make('sitewise_assets')
                                            ->label('')
                                            ->content(function () {
                                                $paths = [
                                                    '/sitewise-assets/css/tailwind.min.css' => 'Compiled Tailwind CSS',
                                                    '/sitewise-assets/css/components.css' => 'Sitewise UI components',
                                                    '/sitewise-assets/js/alpine.min.js' => 'Alpine.js framework',
                                                    '/sitewise-assets/js/sitewise.js' => 'Sitewise utilities',
                                                    '/sitewise-assets/images/placeholder.svg' => 'Default placeholder image',
                                                    '/sitewise-assets/fonts/inter.woff2' => 'Inter font file',
                                                ];

                                                $content = '<div class="space-y-3">';
                                                $content .= '<div class="p-3 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">';
                                                $content .= '<p class="text-sm text-blue-800 dark:text-blue-200"><strong>Note:</strong> These paths are planned for future implementation. They will provide shared, optimized assets across all Sitewise sites.</p>';
                                                $content .= '</div>';
                                                foreach ($paths as $path => $description) {
                                                    $content .= '<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border">';
                                                    $content .= '<div class="flex-1">';
                                                    $content .= '<code class="text-sm font-mono text-indigo-600 dark:text-indigo-400 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900 px-2 py-1 rounded" onclick="navigator.clipboard.writeText(\'' . htmlspecialchars($path) . '\'); this.style.backgroundColor=\'#10b981\'; this.style.color=\'white\'; setTimeout(() => { this.style.backgroundColor=\'\'; this.style.color=\'\'; }, 1000);">' . htmlspecialchars($path) . '</code>';
                                                    $content .= '<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">' . $description . '</p>';
                                                    $content .= '</div>';
                                                    $content .= '</div>';
                                                }
                                                $content .= '</div>';

                                                return new \Illuminate\Support\HtmlString($content);
                                            })
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
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
