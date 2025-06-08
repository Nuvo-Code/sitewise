<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Site;
use App\Models\Template;
use App\Models\TemplateContent;
use App\Services\TemplateContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnhancedTemplateContentTest extends TestCase
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

        // Create a template with enhanced structure
        $this->template = Template::create([
            'site_id' => $this->site->id,
            'name' => 'Enhanced Template',
            'description' => 'A template with enhanced field structure',
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
                    'name' => 'Content',
                    'key' => 'content',
                    'type' => 'rich_text',
                    'required' => true,
                    'description' => 'The main content',
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => [],
                ],
                [
                    'name' => 'Category',
                    'key' => 'category',
                    'type' => 'select',
                    'required' => false,
                    'description' => 'Content category',
                    'default_value' => 'general',
                    'options' => [
                        'general' => 'General',
                        'news' => 'News',
                        'blog' => 'Blog',
                    ],
                    'validation_rules' => [],
                ],
                [
                    'name' => 'Featured',
                    'key' => 'featured',
                    'type' => 'toggle',
                    'required' => false,
                    'description' => 'Is this content featured?',
                    'default_value' => false,
                    'options' => [],
                    'validation_rules' => [],
                ],
            ],
            'active' => true,
        ]);
    }

    public function test_template_fields_for_form_attribute_works()
    {
        $fields = $this->template->getFieldsForFormAttribute();

        $this->assertCount(4, $fields);
        $this->assertArrayHasKey('title', $fields);
        $this->assertArrayHasKey('content', $fields);
        $this->assertArrayHasKey('category', $fields);
        $this->assertArrayHasKey('featured', $fields);

        $titleField = $fields['title'];
        $this->assertEquals('Title', $titleField['name']);
        $this->assertEquals('text', $titleField['type']);
        $this->assertTrue($titleField['required']);
    }

    public function test_template_content_service_generates_form_components()
    {
        $components = TemplateContentService::generateFormComponents($this->template);

        $this->assertCount(4, $components);
        
        // Check that components are generated for each field
        $componentNames = array_map(fn($component) => $component->getName(), $components);
        $this->assertContains('template_content.title', $componentNames);
        $this->assertContains('template_content.content', $componentNames);
        $this->assertContains('template_content.category', $componentNames);
        $this->assertContains('template_content.featured', $componentNames);
    }

    public function test_template_content_can_be_saved_and_retrieved()
    {
        $page = Page::create([
            'site_id' => $this->site->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
            'response_type' => 'html',
            'template_id' => $this->template->id,
            'active' => true,
        ]);

        $contentData = [
            'title' => 'Test Title',
            'content' => '<p>Test content with <strong>formatting</strong></p>',
            'category' => 'news',
            'featured' => true,
        ];

        TemplateContentService::updateContentForPage($page, $contentData);

        $retrievedContent = TemplateContentService::getContentForPage($page);

        $this->assertEquals('Test Title', $retrievedContent['title']);
        $this->assertEquals('<p>Test content with <strong>formatting</strong></p>', $retrievedContent['content']);
        $this->assertEquals('news', $retrievedContent['category']);
        $this->assertEquals('1', $retrievedContent['featured']); // Toggle values are stored as strings
    }

    public function test_auto_generate_content_fields_works()
    {
        $page = Page::create([
            'site_id' => $this->site->id,
            'slug' => 'auto-test-page',
            'title' => 'Auto Test Page',
            'response_type' => 'html',
            'template_id' => $this->template->id,
            'active' => true,
        ]);

        TemplateContentService::autoGenerateContentFields($page);

        $templateContents = TemplateContent::where('page_id', $page->id)->get();
        $this->assertCount(4, $templateContents);

        $keys = $templateContents->pluck('key')->toArray();
        $this->assertContains('title', $keys);
        $this->assertContains('content', $keys);
        $this->assertContains('category', $keys);
        $this->assertContains('featured', $keys);

        // Check default values
        $categoryContent = $templateContents->where('key', 'category')->first();
        $this->assertEquals('general', $categoryContent->value);
    }

    public function test_content_validation_works()
    {
        $validContent = [
            'title' => 'Valid Title',
            'content' => 'Valid content',
            'category' => 'news',
            'featured' => true,
        ];

        $errors = TemplateContentService::validateContent($this->template, $validContent);
        $this->assertEmpty($errors);

        $invalidContent = [
            'category' => 'news',
            // Missing required fields: title and content
        ];

        $errors = TemplateContentService::validateContent($this->template, $invalidContent);
        $this->assertArrayHasKey('title', $errors);
        $this->assertArrayHasKey('content', $errors);
    }

    public function test_old_template_format_is_converted()
    {
        // Create a template with old format
        $oldTemplate = Template::create([
            'site_id' => $this->site->id,
            'name' => 'Old Format Template',
            'description' => 'A template with old structure format',
            'structure' => [
                'title' => 'text',
                'description' => 'textarea',
                'url' => 'url',
            ],
            'active' => true,
        ]);

        $fields = $oldTemplate->getFieldsForFormAttribute();

        $this->assertCount(3, $fields);
        $this->assertArrayHasKey('title', $fields);
        $this->assertArrayHasKey('description', $fields);
        $this->assertArrayHasKey('url', $fields);

        // Check that old format is properly converted
        $titleField = $fields['title'];
        $this->assertEquals('Title', $titleField['name']);
        $this->assertEquals('text', $titleField['type']);
        $this->assertFalse($titleField['required']); // Default for converted fields
    }
}
