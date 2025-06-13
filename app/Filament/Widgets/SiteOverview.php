<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Template;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = null;

    public function getHeading(): string
    {
        return __('filament.widgets.site_overview.heading');
    }

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
            Stat::make(__('filament.widgets.site_overview.current_site'), $site->name)
                ->description($site->domain)
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),

            Stat::make(__('filament.widgets.site_overview.total_pages'), $totalPages)
                ->description($activePages . ' ' . __('filament.widgets.site_overview.active_pages'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make(__('filament.widgets.site_overview.templates'), $totalTemplates)
                ->description(__('filament.widgets.site_overview.reusable_layouts'))
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('warning'),

            Stat::make(__('filament.widgets.site_overview.page_types'), 'HTML: ' . ($pagesByType['html'] ?? 0))
                ->description('MD: ' . ($pagesByType['markdown'] ?? 0) . ' | JSON: ' . ($pagesByType['json'] ?? 0))
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('info'),
        ];
    }
}
