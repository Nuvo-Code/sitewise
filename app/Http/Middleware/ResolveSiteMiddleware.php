<?php

namespace App\Http\Middleware;

use App\Models\Site;
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

        // Find existing site or create new one
        $site = Site::findByDomain($domain);

        if (!$site) {
            $site = Site::createForDomain($domain);
        }

        // Bind site to the application container
        app()->instance('site', $site);

        return $next($request);
    }
}
