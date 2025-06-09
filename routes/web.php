<?php

use App\Models\Page;
use App\Services\BladeTemplateService;
use Illuminate\Support\Facades\Route;
use League\CommonMark\CommonMarkConverter;

Route::get('/', function () {
    return view('welcome');
});

// Dynamic page routes
Route::get('/{slug}', function (string $slug) {
    $site = app('site');

    if (!$site) {
        abort(404, 'Site not found');
    }

    $page = Page::where('site_id', $site->id)
        ->where('slug', $slug)
        ->where('active', true)
        ->first();

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
