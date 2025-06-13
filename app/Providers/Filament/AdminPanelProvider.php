<?php

namespace App\Providers\Filament;


use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Sitewise')
            ->colors([
                'primary' => [
                    50 => '#fef7f5',
                    100 => '#fdeef0',
                    200 => '#fcdde1',
                    300 => '#f9bcc7',
                    400 => '#f591a8',
                    500 => '#EA7BC4', // Pastel Nuvo Pink
                    600 => '#e056a8',
                    700 => '#c8458c',
                    800 => '#a53870',
                    900 => '#8a305c',
                    950 => '#4d1a32',
                ],
                'secondary' => [
                    50 => '#fafbff',
                    100 => '#f4f6ff',
                    200 => '#eaedff',
                    300 => '#d5daff',
                    400 => '#b8c0ff',
                    500 => '#9B8AFF', // Pastel Nuvo Violet
                    600 => '#8470ff',
                    700 => '#6b5beb',
                    800 => '#5a4bc7',
                    900 => '#4c3fa1',
                    950 => '#2e2660',
                ],
                'danger' => [
                    50 => '#fef2f2',
                    100 => '#fee2e2',
                    200 => '#fecaca',
                    300 => '#fca5a5',
                    400 => '#f87171',
                    500 => '#ef4444',
                    600 => '#dc2626',
                    700 => '#b91c1c',
                    800 => '#991b1b',
                    900 => '#7f1d1d',
                    950 => '#450a0a',
                ],
                'warning' => [
                    50 => '#fffbeb',
                    100 => '#fef3c7',
                    200 => '#fde68a',
                    300 => '#fcd34d',
                    400 => '#fbbf24',
                    500 => '#f59e0b',
                    600 => '#d97706',
                    700 => '#b45309',
                    800 => '#92400e',
                    900 => '#78350f',
                    950 => '#451a03',
                ],
                'success' => [
                    50 => '#f0fdf4',
                    100 => '#dcfce7',
                    200 => '#bbf7d0',
                    300 => '#86efac',
                    400 => '#4ade80',
                    500 => '#22c55e',
                    600 => '#16a34a',
                    700 => '#15803d',
                    800 => '#166534',
                    900 => '#14532d',
                    950 => '#052e16',
                ],
                'info' => [
                    50 => '#f0fdff',
                    100 => '#ccfbff',
                    200 => '#a5f3fc',
                    300 => '#7AF3FF', // Pastel Neon Blue
                    400 => '#22d3ee',
                    500 => '#06b6d4',
                    600 => '#0891b2',
                    700 => '#0e7490',
                    800 => '#155e75',
                    900 => '#164e63',
                    950 => '#083344',
                ],
            ])
            ->topNavigation()
            ->breadcrumbs(false)
            ->spa()
            ->login()
            ->authGuard('web')
            // ->maxContentWidth(MaxWidth::Full)
            // ->unsavedChangesAlerts()

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Pages\SiteInstallation::class,
                \App\Filament\Pages\CacheManagement::class,
                \App\Filament\Pages\AiContentGeneration::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\ResolveSiteMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\RequireSiteSetupMiddleware::class,
            ])
            ->widgets([
                \App\Filament\Widgets\SiteOverview::class,
                \App\Filament\Widgets\CachePerformanceWidget::class,
            ])
            ->userMenuItems([
                'visit-site' => MenuItem::make()
                    ->label('Visit Site')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(function (): string {
                        $site = app('site');
                        if (!$site) {
                            return '#';
                        }

                        // Determine protocol based on environment
                        $protocol = env('APP_ENV') === 'local' ? 'http' : 'https';
                        return "{$protocol}://{$site->domain}";
                    })
                    ->openUrlInNewTab()
                    ->visible(function (): bool {
                        $site = app('site');
                        return $site && $site->is_setup_complete;
                    }),
                'ai-content' => MenuItem::make()
                    ->label('Generate AI Content')
                    ->icon('heroicon-o-sparkles')
                    ->url(fn (): string => \App\Filament\Pages\AiContentGeneration::getUrl())
                    ->visible(function (): bool {
                        $site = app('site');
                        return $site && $site->isAiEnabled();
                    }),
            ]);
    }
}
