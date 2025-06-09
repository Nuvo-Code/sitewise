<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                                Forms\Components\Textarea::make('settings.meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Description that appears in search results (max 160 characters)'),

                                Forms\Components\TagsInput::make('settings.meta_keywords')
                                    ->label('Meta Keywords')
                                    ->helperText('Keywords related to your site content'),

                                Forms\Components\TextInput::make('settings.google_analytics_id')
                                    ->label('Google Analytics ID')
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->placeholder('G-XXXXXXXXXX')
                                    ->helperText('Your Google Analytics measurement ID'),

                                Forms\Components\TextInput::make('settings.google_search_console')
                                    ->label('Google Search Console Verification')
                                    ->prefixIcon('heroicon-o-magnifying-glass-circle')
                                    ->helperText('Google Search Console verification meta tag content'),

                                Forms\Components\Textarea::make('settings.custom_head_code')
                                    ->label('Custom Head Code')
                                    ->rows(4)
                                    ->helperText('Custom HTML code to insert in the <head> section'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Social & Contact')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('settings.facebook_url')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link')
                                    ->placeholder('https://facebook.com/yourpage'),

                                Forms\Components\TextInput::make('settings.twitter_url')
                                    ->label('Twitter/X URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link')
                                    ->placeholder('https://twitter.com/youraccount'),

                                Forms\Components\TextInput::make('settings.instagram_url')
                                    ->label('Instagram URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link')
                                    ->placeholder('https://instagram.com/youraccount'),

                                Forms\Components\TextInput::make('settings.linkedin_url')
                                    ->label('LinkedIn URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link')
                                    ->placeholder('https://linkedin.com/company/yourcompany'),

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
                                    ->helperText('Physical address or mailing address'),
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

                                Forms\Components\TextInput::make('settings.logo_url')
                                    ->label('Logo URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-photo')
                                    ->helperText('URL to your site logo image'),

                                Forms\Components\TextInput::make('settings.favicon_url')
                                    ->label('Favicon URL')
                                    ->url()
                                    ->prefixIcon('heroicon-o-star')
                                    ->helperText('URL to your site favicon (.ico or .png)'),

                                Forms\Components\Select::make('settings.theme')
                                    ->label('Theme')
                                    ->options([
                                        'light' => 'Light',
                                        'dark' => 'Dark',
                                        'auto' => 'Auto (System Preference)',
                                    ])
                                    ->default('light')
                                    ->helperText('Default theme for your site'),
                            ])
                            ->columns(2),

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
                                    ->visible(fn (Forms\Get $get) => $get('settings.maintenance_mode')),

                                Forms\Components\TextInput::make('settings.timezone')
                                    ->label('Timezone')
                                    ->default('UTC')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->helperText('Default timezone for your site'),

                                Forms\Components\Select::make('settings.language')
                                    ->label('Default Language')
                                    ->options([
                                        'tr' => 'Turkish',
                                        'en' => 'English',
                                        'et' => 'Estonian',
                                        'de' => 'German',
                                        'es' => 'Spanish',
                                        'fr' => 'French',
                                        'it' => 'Italian',
                                        'pt' => 'Portuguese',
                                        'ru' => 'Russian',
                                        'ja' => 'Japanese',
                                        'ko' => 'Korean',
                                        'zh' => 'Chinese',
                                    ])
                                    ->default('en')
                                    ->searchable()
                                    ->helperText('Default language for your site content'),

                                Forms\Components\Textarea::make('settings.custom_css')
                                    ->label('Custom CSS')
                                    ->rows(6)
                                    ->helperText('Custom CSS styles to apply to your site'),

                                Forms\Components\Textarea::make('settings.custom_js')
                                    ->label('Custom JavaScript')
                                    ->rows(6)
                                    ->helperText('Custom JavaScript code to include on your site'),
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
