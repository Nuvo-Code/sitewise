<?php

namespace App\Filament\Resources;

use AbdelhamidErrahmouni\FilamentMonacoEditor\MonacoEditor;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Models\Template;
use App\Services\TemplateContentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('site_id')
                    ->default(fn () => app('site')?->id),

                Forms\Components\Tabs::make('Page Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(
                                        Page::class,
                                        'slug',
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule) => $rule->where('site_id', app('site')?->id)
                                    )
                                    ->rules(['regex:/^[a-z0-9\-]+$/'])
                                    ->helperText('Only lowercase letters, numbers, and hyphens allowed')
                                    ->prefixIcon('heroicon-o-link'),

                                Forms\Components\TextInput::make('title')
                                    ->label('Page Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-document-text'),

                                Forms\Components\Select::make('response_type')
                                    ->label('Content Type')
                                    ->options([
                                        'html' => 'HTML',
                                        'markdown' => 'Markdown',
                                        'json' => 'JSON',
                                        'template' => 'Template (Blade)',
                                    ])
                                    ->required()
                                    ->default('html')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        // When response_type is changed away from 'template', clear template_id
                                        if ($state !== 'template') {
                                            $set('template_id', null);
                                        }
                                    })
                                    ->prefixIcon('heroicon-o-code-bracket'),

                                Forms\Components\Select::make('template_id')
                                    ->label('Template')
                                    ->relationship('template', 'name')
                                    ->options(fn () => Template::where('site_id', app('site')?->id)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        // When a template is selected, automatically set response_type to 'template'
                                        if ($state) {
                                            $set('response_type', 'template');
                                        }
                                    })
                                    ->visible(fn (Forms\Get $get) => $get('response_type') === 'template')
                                    ->helperText('Select a template to use structured content with Blade rendering.')
                                    ->prefixIcon('heroicon-o-document-duplicate'),

                                Forms\Components\Toggle::make('active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Enable to make this page visible on your site')
                                    ->inline(false),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                // Raw HTML Content
                                MonacoEditor::make('html_content')
                                    ->label('HTML Content')
                                    ->visible(fn (Forms\Get $get) => $get('response_type') === 'html')
                                    ->helperText('Enter your HTML content here'),

                                // Markdown Content
                                MonacoEditor::make('markdown')
                                    ->label('Markdown Content')
                                    ->visible(fn (Forms\Get $get) => $get('response_type') === 'markdown')
                                    ->helperText('Write your content using Markdown syntax'),

                                // JSON Content
                                MonacoEditor::make('json_content')
                                    ->label('JSON Content')
                                    ->visible(fn (Forms\Get $get) => $get('response_type') === 'json')
                                    ->helperText('Enter valid JSON data'),

                                // Info when template is used
                                Forms\Components\Placeholder::make('content_info')
                                    ->label('')
                                    ->content('Content is managed through the Template Content tab when using a template.')
                                    ->visible(fn (Forms\Get $get) => $get('response_type') === 'template' && $get('template_id')),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Tabs\Tab::make('Template Content')
                            ->icon('heroicon-o-document-duplicate')
                            ->visible(fn (Forms\Get $get) => $get('response_type') === 'template' && $get('template_id'))
                            ->schema(function (Forms\Get $get) {
                                $templateId = $get('template_id');

                                if (!$templateId) {
                                    return [
                                        Forms\Components\Placeholder::make('template_info')
                                            ->label('')
                                            ->content('Select a template in the Basic Info tab to use structured content.')
                                            ->columnSpanFull(),
                                    ];
                                }

                                $template = Template::find($templateId);
                                if (!$template) {
                                    return [
                                        Forms\Components\Placeholder::make('template_error')
                                            ->label('')
                                            ->content('Template not found.')
                                            ->columnSpanFull(),
                                    ];
                                }

                                $components = TemplateContentService::generateFormComponents($template);

                                if (empty($components)) {
                                    return [
                                        Forms\Components\Placeholder::make('no_fields')
                                            ->label('')
                                            ->content('This template has no fields defined.')
                                            ->columnSpanFull(),
                                    ];
                                }

                                return $components;
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('response_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'html' => 'primary',
                        'markdown' => 'success',
                        'json' => 'warning',
                        'template' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->default('—'),

                Tables\Columns\IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('response_type')
                    ->options([
                        'html' => 'HTML',
                        'markdown' => 'Markdown',
                        'json' => 'JSON',
                        'template' => 'Template',
                    ]),
                Tables\Filters\TernaryFilter::make('active'),
                Tables\Filters\SelectFilter::make('template_id')
                    ->relationship('template', 'name')
                    ->label('Template'),
            ])
            ->actions([
                Tables\Actions\Action::make('visit')
                    ->label('Visit Page')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(function (Page $record): string {
                        $site = app('site');
                        if (!$site) {
                            return '#';
                        }

                        // Determine protocol based on environment
                        $protocol = env('APP_ENV') === 'local' ? 'http' : 'https';
                        $baseUrl = "{$protocol}://{$site->domain}";

                        // Handle homepage slugs
                        $homepageSlugs = ['home', 'homepage', 'index'];
                        if (in_array($record->slug, $homepageSlugs)) {
                            return $baseUrl;
                        }

                        return "{$baseUrl}/{$record->slug}";
                    })
                    ->openUrlInNewTab()
                    ->visible(function (Page $record): bool {
                        return $record->active && app('site')?->is_setup_complete;
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('site_id', app('site')?->id)
            ->with(['template']);
    }



    public static function getRelations(): array
    {
        return [
            // \App\Filament\Resources\PageResource\RelationManagers\TemplateContentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
