<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSiteSetupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $site = app('site');

        // Skip if no site is bound (shouldn't happen in admin panel)
        if (! $site) {
            return $next($request);
        }

        // Skip if site setup is already complete
        if (! $site->needsSetup()) {
            return $next($request);
        }

        // Skip if we're already on the installation page
        if ($request->routeIs('filament.admin.pages.site-installation')) {
            return $next($request);
        }

        // Skip for API routes, assets, and other non-admin routes
        if (! $request->is('admin*')) {
            return $next($request);
        }

        // Skip for authentication routes to avoid redirect loops
        if ($request->routeIs('filament.admin.auth.*')) {
            return $next($request);
        }

        // Skip for Livewire requests to avoid redirect loops
        if ($request->header('X-Livewire')) {
            return $next($request);
        }

        // Redirect to installation page
        return redirect()->route('filament.admin.pages.site-installation');
    }
}
