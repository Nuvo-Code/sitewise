<?php

namespace App\Http\Middleware;

use App\Models\Site;
use App\Services\CacheService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveSiteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->getHost();

        // Use cached site lookup for better performance
        $site = CacheService::getSiteByDomain($domain);

        if (!$site) {
            $site = Site::createForDomain($domain);
            // Clear the cache to ensure the new site is cached on next request
            CacheService::clearSiteCache($site->id);
        }

        // Bind site to the application container
        app()->instance('site', $site);

        return $next($request);
    }
}
