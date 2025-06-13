<?php

use App\Services\BladeTemplateService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Route;
use League\CommonMark\CommonMarkConverter;

Route::get('/', function () {
    $site = app('site');

    if (!$site) {
        abort(404, 'Site not found');
    }

    // Use cached homepage lookup
    $page = CacheService::getHomepage($site->id);

    // If still no page found, show a default message
    if (!$page) {
        return response('<h1>Welcome to ' . e($site->name) . '</h1><p>No homepage has been configured yet. Please create a page with slug "home" in the admin panel.</p>', 200)
            ->header('Content-Type', 'text/html');
    }

    // Render the page using the same logic as the dynamic route
    return match ($page->response_type) {
        'html' => response($page->html_content)->header('Content-Type', 'text/html'),
        'markdown' => response((new CommonMarkConverter())->convert($page->markdown))->header('Content-Type', 'text/html'),
        'json' => response()->json($page->json_content),
        'template' => response(BladeTemplateService::renderPage($page))->header('Content-Type', 'text/html'),
        default => abort(500, 'Invalid page type'),
    };
});

// Dynamic page routes
Route::get('/{slug}', function (string $slug) {
    $site = app('site');

    if (!$site) {
        abort(404, 'Site not found');
    }

    // Use cached page lookup for better performance
    $page = CacheService::getPage($site->id, $slug);

    if (!$page) {
        abort(404, 'Page not found');
    }

    return match ($page->response_type) {
        'html' => response($page->html_content)->header('Content-Type', 'text/html'),
        'markdown' => response((new CommonMarkConverter())->convert($page->markdown))->header('Content-Type', 'text/html'),
        'json' => response()->json($page->json_content),
        'template' => response(BladeTemplateService::renderPage($page))->header('Content-Type', 'text/html'),
        default => abort(500, 'Invalid page type'),
    };
})->where('slug', '[a-z0-9\-]+');
