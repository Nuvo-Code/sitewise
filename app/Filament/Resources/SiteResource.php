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
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $modelLabel = 'Site Settings';

    protected static ?string $pluralModelLabel = 'Site Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Site Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\TextInput::make('domain')
                                    ->label('Domain')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-o-link')
                                    ->helperText('This domain is automatically detected and cannot be changed'),

                                Forms\Components\TextInput::make('name')
                                    ->label('Site Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-identification')
                                    ->helperText('A friendly name for your site that appears in the admin panel'),

                                Forms\Components\Textarea::make('settings.description')
                                    ->label('Site Description')
                                    ->rows(3)
                                    ->helperText('Brief description of your site (used for SEO and social sharing)'),

                                Forms\Components\TextInput::make('settings.tagline')
                                    ->label('Tagline')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-chat-bubble-left-ellipsis')
                                    ->helperText('A short, catchy phrase that describes your site'),

                                Forms\Components\Toggle::make('active')
                                    ->label('Site Active')
                                    ->helperText('Disable to temporarily take the site offline')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('SEO & Analytics')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('settings.meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(60)
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->helperText('Title that appears in search results (max 60 characters)'),

                                Forms\Components\TagsInput::make('settings.meta_keywords')
                                    ->label('Meta Keywords')
                                    ->helperText('Keywords related to your site content'),

                                Forms\Components\Textarea::make('settings.meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Description that appears in search results (max 160 characters)')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('settings.google_analytics_id')
                                    ->label('Google Analytics ID')
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->placeholder('G-XXXXXXXXXX')
                                    ->helperText('Your Google Analytics measurement ID'),

                                Forms\Components\TextInput::make('settings.google_search_console')
                                    ->label('Google Search Console Verification')
                                    ->prefixIcon('heroicon-o-magnifying-glass-circle')
                                    ->helperText('Google Search Console verification meta tag content'),

                                CodeEditor::make('settings.custom_head_code')
                                    ->label('Custom Head Code')
                                    ->helperText('Custom HTML code to insert in the <head> section')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Social & Contact')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('settings.contact_email')
                                    ->label('Contact Email')
                                    ->email()
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->helperText('Primary contact email for your site'),

                                Forms\Components\TextInput::make('settings.contact_phone')
                                    ->label('Contact Phone')
                                    ->tel()
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText('Primary contact phone number'),

                                Forms\Components\Textarea::make('settings.contact_address')
                                    ->label('Contact Address')
                                    ->rows(3)
                                    ->helperText('Physical address or mailing address')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('settings.social_links')
                                    ->label('Social Links')
                                    ->schema([
                                        Forms\Components\Select::make('platform')
                                            ->label('Platform')
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
                                            ->label('URL')
                                            ->url()
                                            ->required()
                                            ->placeholder('https://'),
                                    ])
                                    ->addActionLabel('Add Social Link')
                                    ->columns(2)
                                    ->helperText('Add links to your social media profiles')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Appearance')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\ColorPicker::make('settings.primary_color')
                                    ->label('Primary Color')
                                    ->helperText('Main brand color for your site'),

                                Forms\Components\ColorPicker::make('settings.secondary_color')
                                    ->label('Secondary Color')
                                    ->helperText('Secondary brand color'),

                                Forms\Components\Select::make('settings.theme')
                                    ->label('Theme')
                                    ->options([
                                        'light' => 'Light',
                                        'dark' => 'Dark',
                                        'auto' => 'Auto (System Preference)',
                                    ])
                                    ->default('light')
                                    ->helperText('Default theme for your site'),

                                Group::make([
                                    Forms\Components\FileUpload::make('settings.logo_url')
                                        ->label('Logo')
                                        ->image()
                                        ->directory('site-logos')
                                        ->maxSize(2048)
                                        ->imagePreviewHeight('100')
                                        ->helperText('Upload your site logo image (PNG, JPG, SVG, max 2MB)'),

                                    Forms\Components\FileUpload::make('settings.favicon_url')
                                        ->label('Favicon')
                                        ->image()
                                        ->directory('site-favicons')
                                        ->maxSize(512)
                                        ->imagePreviewHeight('48')
                                        ->helperText('Upload your site favicon (.ico or .png, max 512KB)'),
                                ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),

                        Forms\Components\Tabs\Tab::make('Advanced')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Forms\Components\Toggle::make('settings.maintenance_mode')
                                    ->label('Maintenance Mode')
                                    ->helperText('Enable to show maintenance page to visitors')
                                    ->inline(false),

                                Forms\Components\Textarea::make('settings.maintenance_message')
                                    ->label('Maintenance Message')
                                    ->rows(3)
                                    ->helperText('Message to show visitors during maintenance')
                                    ->default('We are currently performing scheduled maintenance. Please check back soon!')
                                    ->disabled(fn(Forms\Get $get) => !$get('settings.maintenance_mode')),

                                TimezoneSelect::make('settings.timezone')
                                    ->label('Timezone')
                                    ->default('UTC')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->searchable()
                                    ->helperText('Default timezone for your site'),

                                Forms\Components\Select::make('settings.language')
                                    ->label('Default Language')
                                    ->options(Language::class)
                                    ->default('en')
                                    ->searchable()
                                    ->helperText('Default language for your site content'),

                                CodeEditor::make('settings.custom_css')
                                    ->label('Custom CSS')
                                    ->helperText('Custom CSS styles to apply to your site'),

                                CodeEditor::make('settings.custom_js')
                                    ->label('Custom JavaScript')
                                    ->helperText('Custom JavaScript code to include on your site'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('AI Configuration')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                Forms\Components\Toggle::make('ai_configuration.enabled')
                                    ->label('Enable AI Content Generation')
                                    ->helperText('Enable the AI floating button for content generation')
                                    ->live()
                                    ->inline(false),

                                Forms\Components\Select::make('ai_configuration.provider')
                                    ->label('AI Provider')
                                    ->options([
                                        'openai' => 'OpenAI',
                                        'anthropic' => 'Anthropic',
                                    ])
                                    ->default('openai')
                                    ->live()
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->helperText('Choose your preferred AI provider'),

                                Forms\Components\TextInput::make('ai_configuration.api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->revealable()
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->helperText('Your API key for the selected provider (stored securely)')
                                    ->placeholder('Enter your API key...'),

                                Forms\Components\Select::make('ai_configuration.model')
                                    ->label('AI Model')
                                    ->options(function (Forms\Get $get) {
                                        $provider = $get('ai_configuration.provider') ?? 'openai';
                                        $aiService = app(AiContentService::class);
                                        return $aiService->getAvailableModels($provider);
                                    })
                                    ->default('gpt-4o-mini')
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->live()
                                    ->helperText('Select the AI model to use for content generation'),

                                Forms\Components\Placeholder::make('ai_info')
                                    ->label('')
                                    ->content('
                                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                            <p><strong>How to get API keys:</strong></p>
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>OpenAI:</strong> Visit <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">platform.openai.com/api-keys</a></li>
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
                                    ')
                                    ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled'))
                                    ->columnSpanFull(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('test_configuration')
                                        ->label('Test Configuration')
                                        ->icon('heroicon-o-play')
                                        ->color('success')
                                        ->visible(fn(Forms\Get $get) => $get('ai_configuration.enabled') && $get('ai_configuration.api_key'))
                                        ->action(function (Forms\Get $get, Forms\Set $set) {
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
                                                    ->title('Configuration Test Successful')
                                                    ->body('AI configuration is working correctly!')
                                                    ->success()
                                                    ->send();
                                            } else {
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Configuration Test Failed')
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
