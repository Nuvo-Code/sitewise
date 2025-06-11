<?php

namespace App\Filament\Pages;

use App\Enums\Language;
use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

class SiteInstallation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static string $view = 'filament.pages.site-installation';
    
    protected static ?string $title = 'Complete Site Setup';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $site = app('site');

        if (!$site || !$site->needsSetup()) {
            redirect()->to('/admin');
            return;
        }

        $this->form->fill([
            'domain' => $site->domain,
            'name' => $site->name,
            'active' => $site->active ?? true,
            'settings' => $site->settings ?? [],
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Site Installation')
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
                                    ->helperText('Enable to make the site live immediately after setup')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Contact & Social')
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
                                    ->columnSpanFull()
                                    ->collapsible(),
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

                                Forms\Components\TextInput::make('settings.google_analytics_id')
                                    ->label('Google Analytics ID')
                                    ->prefixIcon('heroicon-o-chart-bar')
                                    ->placeholder('G-XXXXXXXXXX')
                                    ->helperText('Your Google Analytics measurement ID (optional)'),

                                Forms\Components\TextInput::make('settings.meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(60)
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->helperText('Title that appears in search results (max 60 characters)'),

                                Forms\Components\Textarea::make('settings.meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Description that appears in search results (max 160 characters)')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $site = app('site');

            $site->update([
                'name' => $data['name'],
                'active' => $data['active'] ?? true,
                'settings' => $data['settings'] ?? [],
            ]);

            $site->markSetupComplete();

            Notification::make()
                ->title('Site setup completed successfully!')
                ->success()
                ->send();

            redirect()->to('/admin');

        } catch (Halt) {
            return;
        }
    }
    
    public function getTitle(): string
    {
        return 'Complete Site Setup';
    }
    
    public function getHeading(): string
    {
        return 'Welcome to Sitewise!';
    }
    
    public function getSubheading(): string
    {
        $site = app('site');
        return "Let's set up your site for domain: {$site->domain}";
    }
}
