<?php

return [
    'resources' => [
        'site' => [
            'navigation_label' => 'Site Settings',
            'model_label' => 'Site Settings',
            'plural_model_label' => 'Site Settings',
            'tabs' => [
                'site_settings' => 'Site Settings',
                'general' => 'General',
                'seo_analytics' => 'SEO & Analytics',
                'social_contact' => 'Social & Contact',
                'appearance' => 'Appearance',
                'advanced' => 'Advanced',
                'ai_configuration' => 'AI Configuration',
            ],
            'fields' => [
                'domain' => [
                    'label' => 'Domain',
                    'helper' => 'This domain is automatically detected and cannot be changed',
                ],
                'name' => [
                    'label' => 'Site Name',
                    'helper' => 'A friendly name for your site that appears in the admin panel',
                ],
                'description' => [
                    'label' => 'Site Description',
                    'helper' => 'Brief description of your site (used for SEO and social sharing)',
                ],
                'tagline' => [
                    'label' => 'Tagline',
                    'helper' => 'A short, catchy phrase that describes your site',
                ],
                'active' => [
                    'label' => 'Site Active',
                    'helper' => 'Disable to temporarily take the site offline',
                ],
                'meta_title' => [
                    'label' => 'Meta Title',
                    'helper' => 'Title that appears in search results (max 60 characters)',
                ],
                'meta_keywords' => [
                    'label' => 'Meta Keywords',
                    'helper' => 'Keywords related to your site content',
                ],
                'meta_description' => [
                    'label' => 'Meta Description',
                    'helper' => 'Description that appears in search results (max 160 characters)',
                ],
                'google_analytics_id' => [
                    'label' => 'Google Analytics ID',
                    'helper' => 'Your Google Analytics measurement ID',
                ],
                'google_search_console' => [
                    'label' => 'Google Search Console Verification',
                    'helper' => 'Google Search Console verification meta tag content',
                ],
                'custom_head_code' => [
                    'label' => 'Custom Head Code',
                    'helper' => 'Custom HTML code to insert in the <head> section',
                ],
                'contact_email' => [
                    'label' => 'Contact Email',
                    'helper' => 'Primary contact email for your site',
                ],
                'contact_phone' => [
                    'label' => 'Contact Phone',
                    'helper' => 'Primary contact phone number',
                ],
                'contact_address' => [
                    'label' => 'Contact Address',
                    'helper' => 'Physical address or mailing address',
                ],
                'social_links' => [
                    'label' => 'Social Links',
                    'helper' => 'Add links to your social media profiles',
                    'add_action' => 'Add Social Link',
                    'platform' => 'Platform',
                    'url' => 'URL',
                ],
                'primary_color' => [
                    'label' => 'Primary Color',
                    'helper' => 'Main brand color for your site',
                ],
                'secondary_color' => [
                    'label' => 'Secondary Color',
                    'helper' => 'Secondary brand color',
                ],
                'theme' => [
                    'label' => 'Theme',
                    'helper' => 'Default theme for your site',
                    'options' => [
                        'light' => 'Light',
                        'dark' => 'Dark',
                        'auto' => 'Auto (System Preference)',
                    ],
                ],
                'logo_url' => [
                    'label' => 'Logo',
                    'helper' => 'Upload your site logo image (PNG, JPG, SVG, max 2MB)',
                ],
                'favicon_url' => [
                    'label' => 'Favicon',
                    'helper' => 'Upload your site favicon (.ico or .png, max 512KB)',
                ],
                'maintenance_mode' => [
                    'label' => 'Maintenance Mode',
                    'helper' => 'Enable to show maintenance page to visitors',
                ],
                'maintenance_message' => [
                    'label' => 'Maintenance Message',
                    'helper' => 'Message to show visitors during maintenance',
                    'default' => 'We are currently performing scheduled maintenance. Please check back soon!',
                ],
                'timezone' => [
                    'label' => 'Timezone',
                    'helper' => 'Default timezone for your site',
                ],
                'language' => [
                    'label' => 'Default Language',
                    'helper' => 'Default language for your site content',
                ],
                'custom_css' => [
                    'label' => 'Custom CSS',
                    'helper' => 'Custom CSS styles to apply to your site',
                ],
                'custom_js' => [
                    'label' => 'Custom JavaScript',
                    'helper' => 'Custom JavaScript code to include on your site',
                ],
                'ai_enabled' => [
                    'label' => 'Enable AI Content Generation',
                    'helper' => 'Enable the AI floating button for content generation',
                ],
                'ai_provider' => [
                    'label' => 'AI Provider',
                    'helper' => 'Choose your preferred AI provider',
                ],
                'ai_api_key' => [
                    'label' => 'API Key',
                    'helper' => 'Your API key for the selected provider (stored securely)',
                    'placeholder' => 'Enter your API key...',
                ],
                'ai_model' => [
                    'label' => 'AI Model',
                    'helper' => 'Select the AI model to use for content generation',
                ],
            ],
            'actions' => [
                'test_configuration' => 'Test Configuration',
            ],
            'notifications' => [
                'test_success_title' => 'Configuration Test Successful',
                'test_success_body' => 'AI configuration is working correctly!',
                'test_failed_title' => 'Configuration Test Failed',
            ],
        ],
        'page' => [
            'navigation_label' => 'Pages',
            'model_label' => 'Page',
            'plural_model_label' => 'Pages',
            'tabs' => [
                'page_settings' => 'Page Settings',
                'basic_info' => 'Basic Info',
                'content' => 'Content',
                'seo_settings' => 'SEO Settings',
            ],
            'fields' => [
                'slug' => [
                    'label' => 'URL Slug',
                    'helper' => 'Only lowercase letters, numbers, and hyphens allowed',
                ],
                'title' => [
                    'label' => 'Page Title',
                ],
                'response_type' => [
                    'label' => 'Response Type',
                    'helper' => 'How this page should be rendered',
                    'options' => [
                        'html' => 'HTML',
                        'markdown' => 'Markdown',
                        'json' => 'JSON',
                        'template' => 'Template',
                    ],
                ],
                'template_id' => [
                    'label' => 'Template',
                    'helper' => 'Select a template for this page',
                ],
                'content' => [
                    'label' => 'Content',
                    'helper' => 'Page content (HTML, Markdown, or JSON)',
                ],
                'active' => [
                    'label' => 'Active',
                    'helper' => 'Whether this page is publicly accessible',
                ],
                'meta_title' => [
                    'label' => 'Meta Title',
                    'helper' => 'SEO title for this page',
                ],
                'meta_description' => [
                    'label' => 'Meta Description',
                    'helper' => 'SEO description for this page',
                ],
                'meta_keywords' => [
                    'label' => 'Meta Keywords',
                    'helper' => 'SEO keywords for this page',
                ],
            ],
            'table' => [
                'columns' => [
                    'slug' => 'Slug',
                    'title' => 'Title',
                    'type' => 'Type',
                    'template' => 'Template',
                    'active' => 'Active',
                    'last_updated' => 'Last Updated',
                ],
            ],
            'actions' => [
                'visit_page' => 'Visit Page',
            ],
        ],
        'template' => [
            'navigation_label' => 'Templates',
            'model_label' => 'Template',
            'plural_model_label' => 'Templates',
            'tabs' => [
                'template_settings' => 'Template Settings',
                'basic_info' => 'Basic Info',
                'template_content' => 'Template Content',
                'fields_configuration' => 'Fields Configuration',
                'asset_paths' => 'Asset Paths',
            ],
            'fields' => [
                'name' => [
                    'label' => 'Template Name',
                    'helper' => 'A descriptive name for this template',
                ],
                'description' => [
                    'label' => 'Description',
                    'helper' => 'Brief description of what this template is for',
                ],
                'content' => [
                    'label' => 'Template Content',
                    'helper' => 'Blade template content with placeholders',
                ],
                'fields' => [
                    'label' => 'Template Fields',
                    'helper' => 'Define fields that can be filled when using this template',
                    'add_action' => 'Add Field',
                    'key' => 'Field Key',
                    'name' => 'Field Name',
                    'type' => 'Field Type',
                    'required' => 'Required',
                    'default_value' => 'Default Value',
                ],
                'active' => [
                    'label' => 'Active',
                    'helper' => 'Whether this template is available for use',
                ],
            ],
            'table' => [
                'columns' => [
                    'name' => 'Name',
                    'description' => 'Description',
                    'pages_using' => 'Pages Using',
                    'active' => 'Active',
                    'created' => 'Created',
                ],
            ],
        ],
    ],
    'pages' => [
        'dashboard' => [
            'title' => 'Dashboard',
        ],
        'cache_management' => [
            'navigation_label' => 'Cache Management',
            'title' => 'Cache Management',
            'navigation_group' => 'System',
        ],
        'ai_content_generation' => [
            'navigation_label' => 'AI Content',
            'title' => 'AI Content Generation',
            'navigation_group' => 'Content',
        ],
        'site_installation' => [
            'title' => 'Site Installation',
            'heading' => 'Complete Site Setup',
            'subheading' => 'Configure your site settings to get started',
            'complete_setup' => 'Complete Setup',
        ],
    ],
    'widgets' => [
        'site_overview' => [
            'heading' => 'Site Overview',
            'current_site' => 'Current Site',
            'total_pages' => 'Total Pages',
            'active_pages' => 'active',
            'templates' => 'Templates',
            'reusable_layouts' => 'Reusable layouts',
            'page_types' => 'Page Types',
        ],
        'cache_performance' => [
            'site_cache_keys' => 'Site Cache Keys',
            'cached_items' => 'cached items',
            'simulated_data' => 'Simulated data (cache unavailable)',
            'pages_cached' => 'Pages Cached',
            'cached_page_content' => 'Cached page content',
            'simulated_count' => 'Simulated count',
        ],
    ],
    'cache' => [
        'actions' => [
            'clear_site_cache' => 'Clear Site Cache',
            'warm_site_cache' => 'Warm Site Cache',
            'clear_pages_cache' => 'Clear Pages Cache',
            'clear_templates_cache' => 'Clear Templates Cache',
            'debug_cache' => 'Debug Cache',
        ],
        'modals' => [
            'clear_site_cache_heading' => 'Clear Site Cache',
            'clear_site_cache_description' => 'This will clear all cached data for :site. Are you sure?',
            'clear_pages_cache_heading' => 'Clear Pages Cache',
            'clear_pages_cache_description' => 'This will clear all page cache for :site.',
            'clear_templates_cache_heading' => 'Clear Templates Cache',
            'clear_templates_cache_description' => 'This will clear all template cache for :site.',
        ],
        'notifications' => [
            'site_cache_cleared' => 'Site cache cleared successfully',
            'pages_cache_cleared' => 'Pages cache cleared successfully',
            'templates_cache_cleared' => 'Templates cache cleared successfully',
            'cache_warmed_title' => 'Site cache warmed successfully',
            'cache_warmed_body' => 'Cache warmed: :pages pages, :templates templates',
            'debug_complete' => 'Cache Debug Complete',
        ],
        'status' => [
            'cache_system_not_available' => 'Cache System Not Available',
            'cache_unavailable_description' => 'The cache system is currently not working (likely due to database connection issues). The numbers shown below are simulated for demonstration purposes. Cache functionality will work once the database connection is restored.',
            'domain' => 'Domain',
            'cache_status' => 'Cache Status',
            'working' => 'Working',
            'unavailable' => 'Unavailable',
            'site_cache_keys' => 'Site Cache Keys',
            'cache_driver' => 'Cache Driver',
            'simulated' => 'Simulated',
        ],
        'breakdown' => [
            'heading' => 'Cache Breakdown by Type',
            'simulated_data' => '(Simulated Data)',
            'site_data' => 'Site Data',
            'pages' => 'Pages',
            'templates' => 'Templates',
            'template_content' => 'Template Content',
            'compiled_templates' => 'Compiled Templates',
            'statistics' => 'Statistics',
        ],
        'management' => [
            'heading' => 'Site Cache Management',
            'tips' => [
                'auto_clear' => 'Cache is automatically cleared when you save changes',
                'bulk_changes' => 'Clear site cache when making bulk content changes',
                'warm_after_clear' => 'Warm cache after clearing to improve page load times',
                'clear_pages' => 'Clear pages cache when updating multiple pages',
                'clear_templates' => 'Clear templates cache when modifying template structure',
                'use_actions' => 'Use the action buttons above to manage this site\'s cache',
            ],
        ],
        'commands' => [
            'heading' => 'Command Line Management for :site',
            'clear_site' => '# Clear cache for this site',
            'warm_site' => '# Warm cache for this site',
            'clear_pages_only' => '# Clear only pages cache for this site',
            'clear_templates_only' => '# Clear only templates cache for this site',
            'show_stats' => '# Show cache statistics',
        ],
        'no_site' => [
            'heading' => 'No Site Available',
            'description' => 'Cache management is only available when accessing from a registered domain.',
        ],
    ],
    'ai' => [
        'generate_content' => [
            'heading' => 'Generate AI Content',
            'content_description' => 'Content Description',
            'placeholder' => 'Example: Create a hero section for a tech startup with a call-to-action button, modern design with blue gradient background...',
            'helper' => 'Describe the content you want to generate in detail. Be specific about styling, layout, and functionality.',
            'generating' => 'Generating...',
            'generate_content' => 'Generate Content',
            'new_generation' => 'New Generation',
        ],
        'results' => [
            'generated_content' => 'Generated Content',
            'preview' => 'Preview',
            'html_code' => 'HTML Code',
            'copy_html' => 'Copy HTML',
            'regenerate' => 'Regenerate',
            'regenerating' => 'Regenerating...',
        ],
    ],
];
