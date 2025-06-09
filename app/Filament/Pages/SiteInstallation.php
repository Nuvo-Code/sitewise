<?php

namespace App\Filament\Pages;

use App\Models\Site;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

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
            'settings' => $site->settings ?? [],
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Welcome to Sitewise!')
                    ->description('Let\'s set up your site with some basic information to get you started.')
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
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Contact Information')
                    ->description('Provide contact details for your site visitors.')
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
                            ->helperText('Physical address or mailing address'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
                Forms\Components\Section::make('Basic Appearance')
                    ->description('Customize the basic look of your site.')
                    ->schema([
                        Forms\Components\ColorPicker::make('settings.primary_color')
                            ->label('Primary Color')
                            ->helperText('Main brand color for your site'),

                        Forms\Components\TextInput::make('settings.logo_url')
                            ->label('Logo URL')
                            ->url()
                            ->prefixIcon('heroicon-o-photo')
                            ->helperText('URL to your site logo image'),
                    ])
                    ->columns(2)
                    ->collapsible(),
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
                'settings' => $data['settings'] ?? [],
            ]);
            
            $site->markSetupComplete();
            
            Notification::make()
                ->title('Site setup completed successfully!')
                ->success()
                ->send();
                
            redirect()->to('/admin');
            
        } catch (Halt $exception) {
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
