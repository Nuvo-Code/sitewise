<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Page;
use App\Models\Template;
use App\Models\TemplateContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SitewiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample site
        $site = Site::create([
            'domain' => 'localhost',
            'name' => 'Demo Site',
            'settings' => [
                'description' => 'A demo site for testing Sitewise functionality',
            ],
            'active' => true,
        ]);

        // Create a sample template with enhanced structure
        $template = Template::create([
            'site_id' => $site->id,
            'name' => 'Landing Page',
            'description' => 'A template for landing pages with hero section and CTA',
            'structure' => [
                [
                    'name' => 'Hero Title',
                    'key' => 'hero_title',
                    'type' => 'text',
                    'required' => true,
                    'description' => 'The main headline for the hero section',
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => ['max:100'],
                ],
                [
                    'name' => 'Hero Subtitle',
                    'key' => 'hero_subtitle',
                    'type' => 'textarea',
                    'required' => false,
                    'description' => 'Supporting text for the hero section',
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => ['max:255'],
                ],
                [
                    'name' => 'CTA Text',
                    'key' => 'cta_text',
                    'type' => 'text',
                    'required' => true,
                    'description' => 'Call-to-action button text',
                    'default_value' => 'Get Started',
                    'options' => [],
                    'validation_rules' => ['max:50'],
                ],
                [
                    'name' => 'CTA Link',
                    'key' => 'cta_link',
                    'type' => 'url',
                    'required' => true,
                    'description' => 'URL for the call-to-action button',
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => ['url'],
                ],
                [
                    'name' => 'Features',
                    'key' => 'features',
                    'type' => 'rich_text',
                    'required' => false,
                    'description' => 'List of key features or benefits',
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => [],
                ],
            ],
            'active' => true,
        ]);

        // Create sample pages
        $homePage = Page::create([
            'site_id' => $site->id,
            'slug' => 'home',
            'title' => 'Welcome to Our Site',
            'response_type' => 'html',
            'html_content' => '<h1>Welcome to Our Demo Site</h1><p>This is a sample homepage created with Sitewise.</p>',
            'active' => true,
        ]);

        $aboutPage = Page::create([
            'site_id' => $site->id,
            'slug' => 'about',
            'title' => 'About Us',
            'response_type' => 'markdown',
            'markdown' => "# About Us\n\nWe are a **demo company** showcasing the power of Sitewise.\n\n## Our Mission\n\nTo make website management simple and efficient.",
            'active' => true,
        ]);

        $apiPage = Page::create([
            'site_id' => $site->id,
            'slug' => 'api',
            'title' => 'API Endpoint',
            'response_type' => 'json',
            'json_content' => [
                'status' => 'success',
                'message' => 'Welcome to our API',
                'version' => '1.0',
                'endpoints' => [
                    '/api/pages',
                    '/api/templates',
                ]
            ],
            'active' => true,
        ]);

        $landingPage = Page::create([
            'site_id' => $site->id,
            'slug' => 'landing',
            'title' => 'Product Landing',
            'response_type' => 'html',
            'template_id' => $template->id,
            'active' => true,
        ]);

        // Create template content for the landing page
        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'hero_title',
            'value' => 'Revolutionary Product Launch',
        ]);

        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'hero_subtitle',
            'value' => 'Experience the future of web development with our cutting-edge platform.',
        ]);

        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'cta_text',
            'value' => 'Get Started Today',
        ]);

        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'cta_link',
            'value' => 'https://example.com/signup',
        ]);

        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'features',
            'value' => "• Multi-tenant architecture\n• Flexible content types\n• Template-based design\n• Easy administration",
        ]);
    }
}
