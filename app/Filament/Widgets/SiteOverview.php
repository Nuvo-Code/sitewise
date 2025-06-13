<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Template;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Site Overview';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $site = app('site');

        if (!$site) {
            return [];
        }

        $totalPages = Page::where('site_id', $site->id)->count();
        $activePages = Page::where('site_id', $site->id)->where('active', true)->count();
        $totalTemplates = Template::where('site_id', $site->id)->count();

        $pagesByType = Page::where('site_id', $site->id)
            ->selectRaw('response_type, count(*) as count')
            ->groupBy('response_type')
            ->pluck('count', 'response_type')
            ->toArray();

        return [
            Stat::make('Current Site', $site->name)
                ->description($site->domain)
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),

            Stat::make('Total Pages', $totalPages)
                ->description($activePages . ' active')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Templates', $totalTemplates)
                ->description('Reusable layouts')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('warning'),

            Stat::make('Page Types', 'HTML: ' . ($pagesByType['html'] ?? 0))
                ->description('MD: ' . ($pagesByType['markdown'] ?? 0) . ' | JSON: ' . ($pagesByType['json'] ?? 0))
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('info'),
        ];
    }
}
