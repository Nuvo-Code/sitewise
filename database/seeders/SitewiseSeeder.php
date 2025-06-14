<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Site;
use App\Models\Template;
use App\Models\TemplateContent;
use Illuminate\Database\Seeder;

class SitewiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find a sample site
        $site = Site::firstOrCreate(
            ['domain' => 'localhost'],
            [
                'name' => 'Demo Site',
                'settings' => [
                    'description' => 'A demo site for testing Sitewise functionality',
                ],
                'active' => true,
                'is_setup_complete' => true, // Mark demo site as setup complete
            ]
        );

        // Create a sample template with enhanced structure
        $template = Template::firstOrCreate(
            [
                'site_id' => $site->id,
                'name' => 'Landing Page',
            ],
            [
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
                'blade_template' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page_title }} - {{ $site_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 20px; text-align: center; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; font-weight: 700; }
        .hero p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
        .cta-button {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .cta-button:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .features { padding: 80px 20px; background: #f8f9fa; }
        .features h2 { text-align: center; margin-bottom: 3rem; font-size: 2.5rem; color: #2c3e50; }
        .features-content { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .footer { background: #2c3e50; color: white; padding: 40px 20px; text-align: center; }
        .footer p { opacity: 0.8; }
        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 1rem; }
            .features { padding: 40px 20px; }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="container">
            <h1>{{ $hero_title }}</h1>
            <p>{{ $hero_subtitle }}</p>
            @if($cta_link && $cta_text)
                <a href="{{ $cta_link }}" class="cta-button">{{ $cta_text }}</a>
            @endif
        </div>
    </div>

    <div class="features">
        <div class="container">
            <h2>Features & Benefits</h2>
            <div class="features-content">
                {!! $features !!}
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; {{ date("Y") }} {{ $site_name }} | Powered by Sitewise</p>
            <p><small>Domain: {{ $site_domain }} | Page: {{ $page_slug }}</small></p>
        </div>
    </div>
</body>
</html>',
                'active' => true,
            ]
        );

        // Create a blog post template
        $blogTemplate = Template::firstOrCreate(
            [
                'site_id' => $site->id,
                'name' => 'Blog Post',
            ],
            [
                'description' => 'A template for blog posts with author info and content',
                'structure' => [
                    [
                        'name' => 'Post Title',
                        'key' => 'post_title',
                        'type' => 'text',
                        'required' => true,
                        'description' => 'The main title of the blog post',
                        'default_value' => null,
                        'options' => [],
                        'validation_rules' => ['max:150'],
                    ],
                    [
                        'name' => 'Author Name',
                        'key' => 'author_name',
                        'type' => 'text',
                        'required' => true,
                        'description' => 'Name of the post author',
                        'default_value' => null,
                        'options' => [],
                        'validation_rules' => ['max:100'],
                    ],
                    [
                        'name' => 'Publication Date',
                        'key' => 'pub_date',
                        'type' => 'date',
                        'required' => true,
                        'description' => 'When the post was published',
                        'default_value' => null,
                        'options' => [],
                        'validation_rules' => [],
                    ],
                    [
                        'name' => 'Featured Image',
                        'key' => 'featured_image',
                        'type' => 'image',
                        'required' => false,
                        'description' => 'Main image for the blog post',
                        'default_value' => null,
                        'options' => [],
                        'validation_rules' => [],
                    ],
                    [
                        'name' => 'Post Content',
                        'key' => 'post_content',
                        'type' => 'rich_text',
                        'required' => true,
                        'description' => 'The main content of the blog post',
                        'default_value' => null,
                        'options' => [],
                        'validation_rules' => [],
                    ],
                    [
                        'name' => 'Tags',
                        'key' => 'tags',
                        'type' => 'text',
                        'required' => false,
                        'description' => 'Comma-separated tags',
                        'default_value' => null,
                        'options' => [],
                        'validation_rules' => [],
                    ],
                ],
                'blade_template' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post_title }} - {{ $site_name }}</title>
    <style>
        body { font-family: Georgia, serif; line-height: 1.8; color: #333; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid #eee; }
        .header h1 { font-size: 2.5rem; margin-bottom: 1rem; color: #2c3e50; }
        .meta { color: #666; font-size: 0.9rem; margin-bottom: 1rem; }
        .featured-image { text-align: center; margin: 2rem 0; }
        .featured-image img { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .content { font-size: 1.1rem; margin: 2rem 0; }
        .content p { margin-bottom: 1.5rem; }
        .tags { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #eee; }
        .tag { display: inline-block; background: #f8f9fa; padding: 0.3rem 0.8rem; margin: 0.2rem; border-radius: 15px; font-size: 0.8rem; color: #666; }
        .footer { text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #eee; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <article>
            <header class="header">
                <h1>{{ $post_title }}</h1>
                <div class="meta">
                    By <strong>{{ $author_name }}</strong> •
                    {{ \Carbon\Carbon::parse($pub_date)->format("F j, Y") }}
                </div>
            </header>

            @if($featured_image)
            <div class="featured-image">
                <img src="{{ asset("storage/" . $featured_image) }}" alt="{{ $post_title }}">
            </div>
            @endif

            <div class="content">
                {!! $post_content !!}
            </div>

            @if($tags)
            <div class="tags">
                <strong>Tags:</strong>
                @foreach(explode(",", $tags) as $tag)
                    <span class="tag">{{ trim($tag) }}</span>
                @endforeach
            </div>
            @endif
        </article>

        <footer class="footer">
            <p>&copy; {{ date("Y") }} {{ $site_name }} | <a href="/">Back to Home</a></p>
        </footer>
    </div>
</body>
</html>',
                'active' => true,
            ]
        );

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
                ],
            ],
            'active' => true,
        ]);

        $landingPage = Page::create([
            'site_id' => $site->id,
            'slug' => 'landing',
            'title' => 'Beautiful Landing Page',
            'response_type' => 'template',
            'template_id' => $template->id,
            'active' => true,
        ]);

        // Create template content for the landing page
        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'hero_title',
            'value' => 'Welcome to the Future',
        ]);

        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'hero_subtitle',
            'value' => 'Experience the power of Sitewise with beautiful, dynamic templates that bring your content to life.',
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
            'value' => 'https://github.com/sitewise/sitewise',
        ]);

        TemplateContent::create([
            'page_id' => $landingPage->id,
            'template_id' => $template->id,
            'key' => 'features',
            'value' => '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div style="text-align: center; padding: 2rem;">
                    <h3 style="color: #667eea; margin-bottom: 1rem;">🚀 Lightning Fast</h3>
                    <p>Built on Laravel with optimized performance and caching for blazing fast page loads.</p>
                </div>
                <div style="text-align: center; padding: 2rem;">
                    <h3 style="color: #667eea; margin-bottom: 1rem;">🎨 Beautiful Templates</h3>
                    <p>Create stunning pages with our Blade template system and rich content fields.</p>
                </div>
                <div style="text-align: center; padding: 2rem;">
                    <h3 style="color: #667eea; margin-bottom: 1rem;">🔧 Easy Management</h3>
                    <p>Intuitive admin interface powered by FilamentPHP for effortless content management.</p>
                </div>
                <div style="text-align: center; padding: 2rem;">
                    <h3 style="color: #667eea; margin-bottom: 1rem;">🌐 Multi-Tenant</h3>
                    <p>Support multiple sites with domain-based isolation and shared infrastructure.</p>
                </div>
                <div style="text-align: center; padding: 2rem;">
                    <h3 style="color: #667eea; margin-bottom: 1rem;">📱 Responsive</h3>
                    <p>Mobile-first design ensures your content looks great on all devices.</p>
                </div>
                <div style="text-align: center; padding: 2rem;">
                    <h3 style="color: #667eea; margin-bottom: 1rem;">🔒 Secure</h3>
                    <p>Built with security best practices and Laravel\'s robust security features.</p>
                </div>
            </div>',
        ]);

        // Create a blog post page
        $blogPage = Page::create([
            'site_id' => $site->id,
            'slug' => 'introducing-sitewise',
            'title' => 'Introducing Sitewise: The Future of Multi-Tenant Web Development',
            'response_type' => 'template',
            'template_id' => $blogTemplate->id,
            'active' => true,
        ]);

        // Add blog post content
        TemplateContent::create([
            'page_id' => $blogPage->id,
            'template_id' => $blogTemplate->id,
            'key' => 'post_title',
            'value' => 'Introducing Sitewise: The Future of Multi-Tenant Web Development',
        ]);

        TemplateContent::create([
            'page_id' => $blogPage->id,
            'template_id' => $blogTemplate->id,
            'key' => 'author_name',
            'value' => 'The Sitewise Team',
        ]);

        TemplateContent::create([
            'page_id' => $blogPage->id,
            'template_id' => $blogTemplate->id,
            'key' => 'pub_date',
            'value' => now()->format('Y-m-d'),
        ]);

        TemplateContent::create([
            'page_id' => $blogPage->id,
            'template_id' => $blogTemplate->id,
            'key' => 'post_content',
            'value' => '<p>We\'re excited to introduce <strong>Sitewise</strong>, a revolutionary multi-tenant static website platform that combines the power of Laravel with the flexibility of modern web development.</p>

<h2>What Makes Sitewise Special?</h2>

<p>Sitewise isn\'t just another content management system. It\'s a complete platform designed from the ground up for modern web development needs:</p>

<h3>🏗️ Multi-Tenant Architecture</h3>
<p>Each site operates in complete isolation while sharing the same infrastructure. This means better security, easier maintenance, and cost-effective scaling.</p>

<h3>🎨 Flexible Template System</h3>
<p>Our dual template system gives you the best of both worlds:</p>
<ul>
    <li><strong>Field-based templates</strong> for structured content management</li>
    <li><strong>Blade templates</strong> for complete design control</li>
</ul>

<h3>⚡ Multiple Response Types</h3>
<p>Support for HTML, Markdown, JSON, and now Blade template rendering means you can build anything from simple static sites to complex web applications.</p>

<h3>🛠️ Developer-Friendly</h3>
<p>Built on Laravel 11+ with FilamentPHP for the admin interface, Sitewise provides a familiar development environment with modern tooling.</p>

<h2>Getting Started</h2>

<p>Setting up Sitewise is straightforward:</p>

<ol>
    <li>Clone the repository</li>
    <li>Run <code>composer install</code> and <code>npm install</code></li>
    <li>Configure your environment</li>
    <li>Run migrations and seeders</li>
    <li>Start building amazing sites!</li>
</ol>

<p>Whether you\'re building a simple blog, a complex business site, or managing multiple client websites, Sitewise provides the tools and flexibility you need to succeed.</p>

<p><em>Ready to get started? Check out our documentation and join the community!</em></p>',
        ]);

        TemplateContent::create([
            'page_id' => $blogPage->id,
            'template_id' => $blogTemplate->id,
            'key' => 'tags',
            'value' => 'Laravel, Multi-tenant, Web Development, CMS, Blade Templates, FilamentPHP',
        ]);
    }
}
