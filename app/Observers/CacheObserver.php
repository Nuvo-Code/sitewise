<?php

namespace App\Observers;

use App\Models\Page;
use App\Models\Site;
use App\Models\Template;
use App\Models\TemplateContent;
use App\Services\CacheService;

class CacheObserver
{
    /**
     * Handle the Site "updated" event.
     */
    public function updated($model): void
    {
        if ($model instanceof Site) {
            CacheService::clearSiteCache($model->id);
        } elseif ($model instanceof Page) {
            $this->clearPageRelatedCache($model);
        } elseif ($model instanceof Template) {
            $this->clearTemplateRelatedCache($model);
        } elseif ($model instanceof TemplateContent) {
            $this->clearTemplateContentRelatedCache($model);
        }
    }

    /**
     * Handle the model "created" event.
     */
    public function created($model): void
    {
        if ($model instanceof Page) {
            $this->clearPageRelatedCache($model);
        } elseif ($model instanceof Template) {
            $this->clearTemplateRelatedCache($model);
        } elseif ($model instanceof TemplateContent) {
            $this->clearTemplateContentRelatedCache($model);
        }
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted($model): void
    {
        if ($model instanceof Site) {
            CacheService::clearSiteCache($model->id);
        } elseif ($model instanceof Page) {
            $this->clearPageRelatedCache($model);
        } elseif ($model instanceof Template) {
            $this->clearTemplateRelatedCache($model);
        } elseif ($model instanceof TemplateContent) {
            $this->clearTemplateContentRelatedCache($model);
        }
    }

    /**
     * Clear cache related to a page
     */
    protected function clearPageRelatedCache(Page $page): void
    {
        // Clear specific page cache
        CacheService::clearPageCache($page->site_id, $page->slug);

        // Clear site pages cache
        CacheService::clearPageCache($page->site_id);

        // Clear homepage cache (since this page might be the homepage or affect homepage selection)
        $homepageKey = CacheService::siteKey(CacheService::PAGE_PREFIX, $page->site_id, 'homepage');
        cache()->forget($homepageKey);

        // Clear site stats cache
        $statsKey = CacheService::siteKey(CacheService::SITE_STATS_PREFIX, $page->site_id);
        cache()->forget($statsKey);

        // Clear template content cache if page has template
        if ($page->template_id) {
            $contentKey = CacheService::siteKey(CacheService::TEMPLATE_CONTENT_PREFIX, 0, "page:{$page->id}");
            cache()->forget($contentKey);
        }
    }

    /**
     * Clear cache related to a template
     */
    protected function clearTemplateRelatedCache(Template $template): void
    {
        // Clear specific template cache
        CacheService::clearTemplateCache($template->site_id, $template->id);

        // Clear all blade template rendered outputs for this template
        CacheService::clearBladeTemplateCache($template->site_id, $template->id);

        // Clear all pages that use this template
        $pages = Page::where('template_id', $template->id)->get();
        foreach ($pages as $page) {
            CacheService::clearPageCache($page->site_id, $page->slug);

            // Clear template content cache for this page
            $contentKey = CacheService::siteKey(CacheService::TEMPLATE_CONTENT_PREFIX, 0, "page:{$page->id}");
            cache()->forget($contentKey);
        }

        // Clear site pages cache
        CacheService::clearPageCache($template->site_id);

        // Clear site stats cache
        $statsKey = CacheService::siteKey(CacheService::SITE_STATS_PREFIX, $template->site_id);
        cache()->forget($statsKey);
    }

    /**
     * Clear cache related to template content
     */
    protected function clearTemplateContentRelatedCache(TemplateContent $templateContent): void
    {
        // Clear template content cache
        $contentKey = CacheService::siteKey(CacheService::TEMPLATE_CONTENT_PREFIX, 0, "page:{$templateContent->page_id}");
        cache()->forget($contentKey);

        // Clear the page cache
        if ($templateContent->page) {
            CacheService::clearPageCache($templateContent->page->site_id, $templateContent->page->slug);

            // Clear blade template cache for this page's template since content changed
            if ($templateContent->page->template_id) {
                CacheService::clearBladeTemplateCache($templateContent->page->site_id, $templateContent->page->template_id);
            }
        }

        // Clear site stats cache
        if ($templateContent->page) {
            $statsKey = CacheService::siteKey(CacheService::SITE_STATS_PREFIX, $templateContent->page->site_id);
            cache()->forget($statsKey);
        }
    }
}
