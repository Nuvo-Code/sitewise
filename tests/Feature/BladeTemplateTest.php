<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Site;
use App\Models\Template;
use App\Services\BladeTemplateService;
use App\Services\TemplateContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BladeTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;

    protected Template $template;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test site
        $this->site = Site::create([
            'domain' => 'test.local',
            'name' => 'Test Site',
            'settings' => ['description' => 'Test site'],
            'active' => true,
        ]);

        // Bind site to app for global scope
        app()->instance('site', $this->site);

        // Create a template with Blade template
        $this->template = Template::create([
            'site_id' => $this->site->id,
            'name' => 'Blade Template',
            'description' => 'A template with Blade rendering',
            'structure' => [
                [
                    'name' => 'Title',
                    'key' => 'title',
                    'type' => 'text',
                    'required' => true,
                    'description' => 'The main title',
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => ['max:100'],
                ],
                [
                    'name' => 'Body Content',
                    'key' => 'body_content',
                    'type' => 'rich_text',
                    'required' => true,
                    'description' => 'The main content',
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => [],
                ],
            ],
            'blade_template' => '<!DOCTYPE html>
<html>
<head>
    <title>{{ $page_title }} - {{ $site_name }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div>{!! $body_content !!}</div>
    <footer>Site: {{ $site_domain }}</footer>
</body>
</html>',
            'active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up template cache
        BladeTemplateService::clearAllTemplateCaches();
        parent::tearDown();
    }

    public function test_template_has_blade_template()
    {
        $this->assertTrue($this->template->hasBladeTemplate());
        $this->assertTrue($this->template->isBladeTemplate());
    }

    public function test_template_without_blade_template()
    {
        $template = Template::create([
            'site_id' => $this->site->id,
            'name' => 'Regular Template',
            'structure' => [['name' => 'Title', 'key' => 'title', 'type' => 'text']],
            'active' => true,
        ]);

        $this->assertFalse($template->hasBladeTemplate());
        $this->assertFalse($template->isBladeTemplate());
    }

    public function test_page_is_template_rendered()
    {
        $page = Page::create([
            'site_id' => $this->site->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
            'response_type' => 'template',
            'template_id' => $this->template->id,
            'active' => true,
        ]);

        $this->assertTrue($page->isTemplateRendered());
    }

    public function test_page_is_not_template_rendered_without_template()
    {
        $page = Page::create([
            'site_id' => $this->site->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
            'response_type' => 'template',
            'active' => true,
        ]);

        $this->assertFalse($page->isTemplateRendered());
    }

    public function test_blade_template_service_renders_page()
    {
        $page = Page::create([
            'site_id' => $this->site->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
            'response_type' => 'template',
            'template_id' => $this->template->id,
            'active' => true,
        ]);

        // Add template content
        TemplateContentService::updateContentForPage($page, [
            'title' => 'Welcome to Our Site',
            'body_content' => '<p>This is <strong>rich content</strong> with HTML.</p>',
        ]);

        $rendered = BladeTemplateService::renderPage($page);

        $this->assertStringContainsString('<!DOCTYPE html>', $rendered);
        $this->assertStringContainsString('<title>Test Page - Test Site</title>', $rendered);
        $this->assertStringContainsString('<h1>Welcome to Our Site</h1>', $rendered);
        $this->assertStringContainsString('<p>This is <strong>rich content</strong> with HTML.</p>', $rendered);
        $this->assertStringContainsString('Site: test.local', $rendered);
    }

    public function test_blade_template_service_generates_sample_template()
    {
        $sample = BladeTemplateService::generateSampleBladeTemplate($this->template);

        $this->assertStringContainsString('<!DOCTYPE html>', $sample);
        $this->assertStringContainsString('{{ $page_title }}', $sample);
        $this->assertStringContainsString('{{ $site_name }}', $sample);
        $this->assertStringContainsString('{{ $title }}', $sample);
        $this->assertStringContainsString('{!! $body_content !!}', $sample);
    }

    public function test_blade_template_service_gets_available_variables()
    {
        $variables = BladeTemplateService::getAvailableVariables($this->template);

        $this->assertArrayHasKey('page', $variables);
        $this->assertArrayHasKey('site', $variables);
        $this->assertArrayHasKey('template', $variables);
        $this->assertArrayHasKey('content', $variables);
        $this->assertArrayHasKey('page_title', $variables);
        $this->assertArrayHasKey('site_name', $variables);
        $this->assertArrayHasKey('title', $variables);
        $this->assertArrayHasKey('body_content', $variables);
    }

    public function test_blade_template_validation_passes_for_valid_template()
    {
        $validTemplate = '<!DOCTYPE html>
<html>
<head><title>{{ $title }}</title></head>
<body><h1>{{ $title }}</h1></body>
</html>';

        $errors = BladeTemplateService::validateBladeTemplate($validTemplate);
        $this->assertEmpty($errors);
    }

    public function test_blade_template_validation_fails_for_invalid_template()
    {
        $invalidTemplate = '<!DOCTYPE html>
<html>
<head><title>{{ $title }}</title></head>
<body><h1>{{ $title }</h1></body>  <!-- Missing closing brace -->
</html>';

        $errors = BladeTemplateService::validateBladeTemplate($invalidTemplate);
        $this->assertNotEmpty($errors);
    }

    public function test_template_cache_is_created_and_cleared()
    {
        $page = Page::create([
            'site_id' => $this->site->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
            'response_type' => 'template',
            'template_id' => $this->template->id,
            'active' => true,
        ]);

        TemplateContentService::updateContentForPage($page, [
            'title' => 'Test Title',
            'body_content' => 'Test Content',
        ]);

        // Render the page (this should create the template cache)
        BladeTemplateService::renderPage($page);

        // Check that template view file was created
        $viewName = 'templates.site_'.$this->site->id.'.template_'.$this->template->id;
        $viewPath = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');
        $this->assertTrue(File::exists($viewPath));

        // Clear the cache
        BladeTemplateService::clearTemplateCache($this->template);

        // Check that template view file was deleted
        $this->assertFalse(File::exists($viewPath));
    }

    public function test_template_rendering_throws_exception_for_page_without_blade_template()
    {
        $templateWithoutBlade = Template::create([
            'site_id' => $this->site->id,
            'name' => 'No Blade Template',
            'structure' => [['name' => 'Title', 'key' => 'title', 'type' => 'text']],
            'active' => true,
        ]);

        $page = Page::create([
            'site_id' => $this->site->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
            'response_type' => 'template',
            'template_id' => $templateWithoutBlade->id,
            'active' => true,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Page does not have a valid Blade template');

        BladeTemplateService::renderPage($page);
    }
}
