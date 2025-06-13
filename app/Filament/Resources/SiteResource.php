<?php

namespace App\Filament\Resources;

use App\Enums\Language;
use App\Filament\Resources\SiteResource\Pages;
use App\Models\Site;
use App\Services\AiContentService;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.site.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.site.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.site.plural_model_label');
    }

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make(__('filament.resources.site.tabs.site_settings'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('filament.resources.site.tabs.general'))
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\TextInput::make('domain')
                                    ->label(__('filament.resources.site.fields.domain.label'))
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-o-link')
                                    ->helperText(__('filament.resources.site.fields.domain.helper')),

                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament.resources.site.fields.name.label'))
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-identification')
                                    ->helperText(__('filament.resources.site.fields.name.helper')),

                                Forms\Components\Textarea::make('settings.description')
                                    ->label(__('filament.resources.site.fields.description.label'))
                                    ->rows(3)
                                    ->helperText(__('filament.resources.site.fields.description.helper')),

                                Forms\Components\TextInput::make('settings.tagline')
                                    ->label(__('filament.resources.site.fields.tagline.label'))
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-chat-bubble-left-ellipsis')
                                    ->helperText(__('filament.resources.site.fields.tagline.helper')),

                                Forms\Components\Toggle::make('active')
                                    ->label(__('filament.resources.site.fields.active.label'))
                                    ->helperText(__('filament.resources.site.fields.active.helper'))
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make(__('filament.resources.site.tabs.seo_analytics'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('settings.meta_title')
                                    ->label(__('filament.resources.site.fields.meta_title.label'))
                                    ->maxLength(60)
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->helperText(__('filament.resources.site.fields.meta_title.helper')),

                                Forms\Components\TagsInput::make('settings.meta_keywords')
                                    ->label(__('filament.resources.site.fields.meta_keywords.label'))
                                    ->helperText(__('filament.resources.site.fields.meta_keywords.helper')),

                                Forms\Components\Textarea::make('settings.meta_description')
                                    ->label(__('filament.resources.site.fields.meta_description.label'))
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText(__('filament.resources.site.fields.meta_description.helper'))
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('settings.google_analytics_id')
                                    ->label(__('filament.resources.site.fields.google_analytics_id.label'))
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->placeholder('G-XXXXXXXXXX')
                                    ->helperText(__('filament.resources.site.fields.google_analytics_id.helper')),

                                Forms\Components\TextInput::make('settings.google_search_console')
                                    ->label(__('filament.resources.site.fields.google_search_console.label'))
                                    ->prefixIcon('heroicon-o-magnifying-glass-circle')
                                    ->helperText(__('filament.resources.site.fields.google_search_console.helper')),

                                CodeEditor::make('settings.custom_head_code')
                                    ->label(__('filament.resources.site.fields.custom_head_code.label'))
                                    ->helperText(__('filament.resources.site.fields.custom_head_code.helper'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make(__('filament.resources.site.tabs.social_contact'))
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('settings.contact_email')
                                    ->label(__('filament.resources.site.fields.contact_email.label'))
                                    ->email()
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->helperText(__('filament.resources.site.fields.contact_email.helper')),

                                Forms\Components\TextInput::make('settings.contact_phone')
                                    ->label(__('filament.resources.site.fields.contact_phone.label'))
                                    ->tel()
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText(__('filament.resources.site.fields.contact_phone.helper')),

                                Forms\Components\Textarea::make('settings.contact_address')
                                    ->label(__('filament.resources.site.fields.contact_address.label'))
                                    ->rows(3)
                                    ->helperText(__('filament.resources.site.fields.contact_address.helper'))
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('settings.social_links')
                                    ->label(__('filament.resources.site.fields.social_links.label'))
                                    ->schema([
                                        Forms\Components\Select::make('platform')
                                            ->label(__('filament.resources.site.fields.social_links.platform'))
                                            ->options([
                                                'facebook' => 'Facebook',
                                                'twitter' => 'Twitter/X',
                                                'instagram' => 'Instagram',
                                                'linkedin' => 'LinkedIn',
                                                'youtube' => 'YouTube',
                                                'tiktok' => 'TikTok',
                                                'github' => 'GitHub',
                                                'custom' => 'Custom',
                                            ])
                                            ->required()
                                            ->searchable(),

                                        Forms\Components\TextInput::make('url')
                                            ->label(__('filament.resources.site.fields.social_links.url'))
                                            ->url()
                                            ->required()
                                            ->placeholder('https://'),
                                    ])
                                    ->addActionLabel(__('filament.resources.site.fields.social_links.add_action'))
                                    ->columns(2)
                                    ->helperText(__('filament.resources.site.fields.social_links.helper'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make(__('filament.resources.site.tabs.appearance'))
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\ColorPicker::make('settings.primary_color')
                                    ->label(__('filament.resources.site.fields.primary_color.label'))
                                    ->helperText(__('filament.resources.site.fields.primary_color.helper')),

                                Forms\Components\ColorPicker::make('settings.secondary_color')
                                    ->label(__('filament.resources.site.fields.secondary_color.label'))
                                    ->helperText(__('filament.resources.site.fields.secondary_color.helper')),

                                Forms\Components\Select::make('settings.theme')
                                    ->label(__('filament.resources.site.fields.theme.label'))
                                    ->options([
                                        'light' => __('filament.resources.site.fields.theme.options.light'),
                                        'dark' => __('filament.resources.site.fields.theme.options.dark'),
                                        'auto' => __('filament.resources.site.fields.theme.options.auto'),
                                    ])
                                    ->default('light')
                                    ->helperText(__('filament.resources.site.fields.theme.helper')),

                                Group::make([
                                    Forms\Components\FileUpload::make('settings.logo_url')
                                        ->label(__('filament.resources.site.fields.logo_url.label'))
                                        ->image()
                                        ->directory('site-logos')
                                        ->maxSize(2048)
                                        ->imagePreviewHeight('100')
                                        ->helperText(__('filament.resources.site.fields.logo_url.helper')),

                                    Forms\Components\FileUpload::make('settings.favicon_url')
                                        ->label(__('filament.resources.site.fields.favicon_url.label'))
                                        ->image()
                                        ->directory('site-favicons')
                                        ->maxSize(512)
                                        ->imagePreviewHeight('48')
                                        ->helperText(__('filament.resources.site.fields.favicon_url.helper')),
                                ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),

                        Forms\Components\Tabs\Tab::make(__('filament.resources.site.tabs.advanced'))
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Forms\Components\Toggle::make('settings.maintenance_mode')
                                    ->label(__('filament.resources.site.fields.maintenance_mode.label'))
                                    ->helperText(__('filament.resources.site.fields.maintenance_mode.helper'))
                                    ->inline(false),

                                Forms\Components\Textarea::make('settings.maintenance_message')
                                    ->label(__('filament.resources.site.fields.maintenance_message.label'))
                                    ->rows(3)
                                    ->helperText(__('filament.resources.site.fields.maintenance_message.helper'))
                                    ->default(__('filament.resources.site.fields.maintenance_message.default'))
                                    ->disabled(fn(Forms\Get $get) => !$get('settings.maintenance_mode')),

                                TimezoneSelect::make('settings.timezone')
                                    ->label(__('filament.resources.site.fields.timezone.label'))
                                    ->default('UTC')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->searchable()
                                    ->helperText(__('filament.resources.site.fields.timezone.helper')),

                                Forms\Components\Select::make('settings.language')
                                    ->label(__('filament.resources.site.fields.language.label'))
                                    ->options(Language::class)
                                    ->default('en')
                                    ->searchable()
                                    ->helperText(__('filament.resources.site.fields.language.helper')),

                                CodeEditor::make('settings.custom_css')
                                    ->label(__('filament.resources.site.fields.custom_css.label'))
                                    ->helperText(__('filament.resources.site.fields.custom_css.helper')),

                                CodeEditor::make('settings.custom_js')
                                    ->label(__('filament.resources.site.fields.custom_js.label'))
                                    ->helperText(__('filament.resources.site.fields.custom_js.helper')),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make(__('filament.resources.site.tabs.ai_configuration'))
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                Forms\Components\Toggle::make('ai_configuration.enabled')
                                    ->label(__('filament.resources.site.fields.ai_enabled.label'))
                                    ->helperText(__('filament.resources.site.fields.ai_enabled.helper'))
                                    ->live()
                                    ->inline(false),

                                Forms\Components\Select::make('ai_configuration.provider')
                                    ->label(__('filament.resources.site.fields.ai_provider.label'))
                                    ->options([
                                        'openai' => 'OpenAI',
                                        'gemini' => 'Google Gemini',
                                        'anthropic' => 'Anthropic',
                                    ])
                                    ->default('openai')
                                    ->live()
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->helperText(__('filament.resources.site.fields.ai_provider.helper')),

                                Forms\Components\TextInput::make('ai_configuration.api_key')
                                    ->label(__('filament.resources.site.fields.ai_api_key.label'))
                                    ->password()
                                    ->revealable()
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->helperText(__('filament.resources.site.fields.ai_api_key.helper'))
                                    ->placeholder(__('filament.resources.site.fields.ai_api_key.placeholder')),

                                Forms\Components\Select::make('ai_configuration.model')
                                    ->label(__('filament.resources.site.fields.ai_model.label'))
                                    ->options(function (Forms\Get $get) {
                                        $provider = $get('ai_configuration.provider') ?? 'openai';
                                        $aiService = app(AiContentService::class);
                                        return $aiService->getAvailableModels($provider);
                                    })
                                    ->default('gpt-4o-mini')
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->live()
                                    ->helperText(__('filament.resources.site.fields.ai_model.helper')),

                                Forms\Components\Placeholder::make('ai_info')
                                    ->label('')
                                    ->content(new HtmlString('
                                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                            <p><strong>How to get API keys:</strong></p>
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>OpenAI:</strong> Visit <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">platform.openai.com/api-keys</a></li>
                                                <li><strong>Google Gemini:</strong> Visit <a href="https://makersuite.google.com/app/apikey" target="_blank" class="text-blue-600 hover:underline">makersuite.google.com/app/apikey</a></li>
                                                <li><strong>Anthropic:</strong> Visit <a href="https://console.anthropic.com/settings/keys" target="_blank" class="text-blue-600 hover:underline">console.anthropic.com/settings/keys</a></li>
                                            </ul>
                                            <p class="mt-3"><strong>Features:</strong></p>
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>Generate HTML content with AI assistance</li>
                                                <li>Preview generated content before using</li>
                                                <li>Copy HTML code to clipboard</li>
                                                <li>Optimized for web content creation</li>
                                            </ul>
                                        </div>
                                    '))
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->columnSpanFull(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('test_configuration')
                                        ->label(__('filament.resources.site.actions.test_configuration'))
                                        ->icon('heroicon-o-play')
                                        ->color('success')
                                        ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled') && $get('ai_configuration.api_key'))
                                        ->action(function (Forms\Get $get) {
                                            $site = app('site');
                                            if (!$site) return;

                                            // Temporarily update the site with current form values
                                            $site->ai_configuration = [
                                                'enabled' => $get('ai_configuration.enabled'),
                                                'provider' => $get('ai_configuration.provider'),
                                                'api_key' => $get('ai_configuration.api_key'),
                                                'model' => $get('ai_configuration.model'),
                                            ];

                                            $aiService = app(AiContentService::class);
                                            $result = $aiService->testConfiguration($site);

                                            if ($result['success']) {
                                                \Filament\Notifications\Notification::make()
                                                    ->title(__('filament.resources.site.notifications.test_success_title'))
                                                    ->body(__('filament.resources.site.notifications.test_success_body'))
                                                    ->success()
                                                    ->send();
                                            } else {
                                                \Filament\Notifications\Notification::make()
                                                    ->title(__('filament.resources.site.notifications.test_failed_title'))
                                                    ->body($result['error'])
                                                    ->danger()
                                                    ->send();
                                            }
                                        }),
                                ])
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Not used since we only show the edit form
        return $table
            ->columns([])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', app('site')?->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditSite::route('/'),
        ];
    }
}
