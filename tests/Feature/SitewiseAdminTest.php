<?php

use App\Models\Site;
use App\Models\Page;
use App\Models\Template;
use App\Models\TemplateContent;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create a test site and bind it to the app
    $site = Site::create([
        'domain' => 'test.local',
        'name' => 'Test Site',
        'active' => true,
    ]);

    app()->instance('site', $site);
});

test('admin dashboard loads', function () {
    $response = $this->get('/admin');
    $response->assertStatus(200);
});

test('site isolation works', function () {
    // Create another site
    $otherSite = Site::create([
        'domain' => 'other.local',
        'name' => 'Other Site',
        'active' => true,
    ]);

    // Create pages for both sites
    $currentSitePage = Page::create([
        'site_id' => app('site')->id,
        'slug' => 'test-page',
        'title' => 'Test Page',
        'response_type' => 'html',
        'html_content' => '<h1>Current Site Page</h1>',
        'active' => true,
    ]);

    $otherSitePage = Page::create([
        'site_id' => $otherSite->id,
        'slug' => 'other-page',
        'title' => 'Other Page',
        'response_type' => 'html',
        'html_content' => '<h1>Other Site Page</h1>',
        'active' => true,
    ]);

    // Test that only current site pages are visible
    $pages = Page::all();
    expect($pages)->toHaveCount(1);
    expect($pages->first()->id)->toBe($currentSitePage->id);
});

test('page rendering works', function () {
    // Update the site domain to match the test request
    $site = app('site');
    $site->update(['domain' => '127.0.0.1']);

    // Create test pages
    Page::create([
        'site_id' => $site->id,
        'slug' => 'html-test',
        'title' => 'HTML Test',
        'response_type' => 'html',
        'html_content' => '<h1>HTML Content</h1>',
        'active' => true,
    ]);

    Page::create([
        'site_id' => $site->id,
        'slug' => 'markdown-test',
        'title' => 'Markdown Test',
        'response_type' => 'markdown',
        'markdown' => '# Markdown Content',
        'active' => true,
    ]);

    Page::create([
        'site_id' => $site->id,
        'slug' => 'json-test',
        'title' => 'JSON Test',
        'response_type' => 'json',
        'json_content' => ['message' => 'Hello World'],
        'active' => true,
    ]);

    // Test HTML page
    $response = $this->get('/html-test');
    $response->assertStatus(200);
    $response->assertSee('HTML Content');

    // Test Markdown page
    $response = $this->get('/markdown-test');
    $response->assertStatus(200);
    $response->assertSee('Markdown Content');

    // Test JSON page
    $response = $this->get('/json-test');
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Hello World']);
});

test('template system works', function () {
    // Create a template
    $template = Template::create([
        'site_id' => app('site')->id,
        'name' => 'Test Template',
        'structure' => [
            'title' => 'text',
            'content' => 'textarea',
        ],
        'active' => true,
    ]);

    // Create a page with template
    $page = Page::create([
        'site_id' => app('site')->id,
        'slug' => 'template-test',
        'title' => 'Template Test',
        'response_type' => 'html',
        'template_id' => $template->id,
        'active' => true,
    ]);

    // Create template content
    TemplateContent::create([
        'page_id' => $page->id,
        'template_id' => $template->id,
        'key' => 'title',
        'value' => 'Test Title',
    ]);

    TemplateContent::create([
        'page_id' => $page->id,
        'template_id' => $template->id,
        'key' => 'content',
        'value' => 'Test Content',
    ]);

    // Test that template content is accessible
    $content = TemplateContent::getContentForPage($page);
    expect($content['title'])->toBe('Test Title');
    expect($content['content'])->toBe('Test Content');
});
