# Blade Template Rendering

The Sitewise platform now supports Blade template rendering as an additional option to the existing template content system. This feature allows you to create complete HTML pages using Laravel's Blade templating engine with dynamic content from your template fields.

## Overview

Blade template rendering provides:

- **Complete Page Control**: Define the entire HTML structure using Blade templates
- **Dynamic Content**: Access template field content as Blade variables
- **Laravel Integration**: Full access to Laravel's Blade features (directives, components, etc.)
- **In-Memory Compilation**: Templates are compiled directly from database content without creating files
- **Security**: Templates are isolated from the project's view files
- **Validation**: Built-in syntax validation for Blade templates

## How It Works

1. **Template Definition**: Create a template with both field structure and Blade template content
2. **Page Assignment**: Assign the template to a page and set response type to "Template (Blade)"
3. **Content Population**: Fill in the template field content through the admin interface
4. **Rendering**: The system renders the Blade template with field content as variables

## Creating Blade Templates

### Using the Admin Interface

1. Navigate to **Templates** in the admin panel
2. Create or edit a template
3. Define your template fields as usual
4. In the **Blade Template (Optional)** section:
   - Write your Blade template HTML
   - Use the "Generate Sample Template" button for a starting point
   - Use "Show Available Variables" to see what variables you can use

### Example Blade Template

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page_title }} - {{ $site_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .hero { background: #f8f9fa; padding: 60px 20px; text-align: center; }
        .content { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .cta-button { 
            display: inline-block; 
            background: #007bff; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 4px; 
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>{{ $hero_title }}</h1>
        <p>{{ $hero_subtitle }}</p>
        @if($cta_link && $cta_text)
            <a href="{{ $cta_link }}" class="cta-button">{{ $cta_text }}</a>
        @endif
    </div>
    
    <div class="content">
        {!! $features !!}
    </div>
    
    <footer>
        <p>&copy; {{ date('Y') }} {{ $site_name }}</p>
    </footer>
</body>
</html>
```

## Available Variables

### System Variables

| Variable | Description |
|----------|-------------|
| `$page` | The current Page model instance |
| `$site` | The current Site model instance |
| `$template` | The current Template model instance |
| `$page_title` | The page title |
| `$page_slug` | The page slug |
| `$site_name` | The site name |
| `$site_domain` | The site domain |
| `$template_content` | Array of all template content |

### Template Field Variables

Each template field becomes available as a variable using its key:

- Field with key `hero_title` → `$hero_title`
- Field with key `cta_link` → `$cta_link`
- Field with key `features` → `$features`

## Using Pages with Blade Templates

### Creating a Template-Rendered Page

1. Create or edit a page
2. Set **Content Type** to "Template (Blade)"
3. Select a template that has a Blade template defined
4. Fill in the template content fields
5. Save the page

### Accessing the Page

Pages with Blade templates are accessed normally via their slug. The system automatically:

1. Detects the page uses template rendering
2. Loads the template content
3. Renders the Blade template with the content as variables
4. Returns the complete HTML page

## Field Type Handling

Different field types are handled appropriately in Blade templates:

### Rich Text Fields
```blade
{!! $rich_text_field !!}  <!-- Renders HTML content -->
```

### Image Fields
```blade
@if($image_field)
    <img src="{{ asset('storage/' . $image_field) }}" alt="Image">
@endif
```

### URL Fields
```blade
@if($url_field)
    <a href="{{ $url_field }}">Visit Link</a>
@endif
```

### Toggle/Checkbox Fields
```blade
@if($toggle_field)
    <div class="featured">This content is featured!</div>
@endif
```

### Select Fields
```blade
<div class="category-{{ $category_field }}">
    Category: {{ ucfirst($category_field) }}
</div>
```

## Template Management

### Generating Sample Templates

Use the "Generate Sample Template" button in the admin interface to automatically create a basic Blade template based on your field structure.

### Template Validation

The system validates Blade templates for:
- Unmatched braces (`{{` without `}}`)
- Basic syntax errors
- Compilation issues

### Template Processing

- Templates are compiled in-memory using Laravel's Blade compiler
- No physical files are created in the project's resources/views directory
- Templates are executed in temporary files that are immediately cleaned up
- Cache management uses Laravel's built-in caching system
- Manual cache clearing available via `BladeTemplateService::clearTemplateCache()`

## Programmatic Usage

### Rendering Pages

```php
use App\Services\BladeTemplateService;

// Render a page with Blade template
$html = BladeTemplateService::renderPage($page);
```

### Template Validation

```php
$errors = BladeTemplateService::validateBladeTemplate($bladeTemplate);
if (empty($errors)) {
    // Template is valid
} else {
    // Handle validation errors
}
```

### Available Variables

```php
$variables = BladeTemplateService::getAvailableVariables($template);
```

### Cache Management

```php
// Clear specific template cache
BladeTemplateService::clearTemplateCache($template);

// Clear all template caches
BladeTemplateService::clearAllTemplateCaches();
```

## Security & Isolation

The refactored Blade template system provides enhanced security:

- **File System Isolation**: Templates are never written to the project's view directories
- **In-Memory Processing**: Templates are compiled and executed in memory
- **Temporary Execution**: Compiled templates use temporary files that are immediately cleaned up
- **No Cross-Contamination**: User templates cannot interfere with project files
- **Permission Safety**: No file system permission issues with template creation

## Best Practices

### Template Design

1. **Responsive Design**: Use responsive CSS for mobile compatibility
2. **Semantic HTML**: Use proper HTML5 semantic elements
3. **Accessibility**: Include proper alt texts, ARIA labels, etc.
4. **Performance**: Optimize images and minimize CSS/JS

### Content Structure

1. **Field Validation**: Use appropriate validation rules for template fields
2. **Default Values**: Provide sensible defaults for optional fields
3. **Content Guidelines**: Document content requirements for editors

### Security

1. **Escape Output**: Use `{{ }}` for text content, `{!! !!}` only for trusted HTML
2. **Validate URLs**: Ensure URL fields contain valid URLs
3. **Sanitize Content**: Be careful with user-generated content

### Development

1. **Version Control**: Track template changes in version control
2. **Testing**: Test templates with various content scenarios
3. **Documentation**: Document template variables and usage

## Integration with Existing System

Blade template rendering works alongside the existing template system:

- **Field-based templates** continue to work as before
- **Blade templates** are an optional enhancement
- **Mixed usage** is supported (some templates with Blade, others without)
- **Backward compatibility** is maintained

## Troubleshooting

### Common Issues

1. **Template not rendering**: Check that the page response type is set to "Template (Blade)"
2. **Variables not showing**: Verify field keys match variable names in the template
3. **Syntax errors**: Use the validation feature to check template syntax
4. **Missing content**: Ensure template content is saved for the page

### Debug Information

Access debug information through the available variables:

```blade
@if(config('app.debug'))
    <pre>{{ json_encode($template_content, JSON_PRETTY_PRINT) }}</pre>
@endif
```

## Examples

### Landing Page Template

Perfect for marketing pages with hero sections, features, and CTAs.

### Blog Post Template

Ideal for content-heavy pages with structured layouts.

### Product Page Template

Great for e-commerce style pages with images, descriptions, and pricing.

### Contact Page Template

Useful for contact forms with company information and maps.

The Blade template rendering system provides powerful flexibility while maintaining the ease of use of the existing template content system!
