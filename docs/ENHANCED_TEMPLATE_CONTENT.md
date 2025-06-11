# Enhanced Template Content System

The Sitewise platform now features a comprehensive template content system that allows you to define rich, structured content fields for each template with advanced field types, validation, and a seamless editing experience.

## Overview

The enhanced template content system provides:

- **Rich Field Types**: Support for text, textarea, rich text, number, email, URL, date, datetime, select, checkbox, toggle, file upload, image upload, and color picker fields
- **Field Configuration**: Each field can have a name, description, default value, validation rules, and options (for select fields)
- **Inline Editing**: Template content fields are now displayed directly in the page form for a seamless editing experience
- **Auto-generation**: Missing template content fields are automatically created when a template is assigned to a page
- **Backward Compatibility**: Existing templates with the old format are automatically converted to the new format

## Template Structure

### New Enhanced Format

Templates now use a rich structure format where each field is defined as an object with the following properties:

```php
[
    'name' => 'Hero Title',              // Display name for the field
    'key' => 'hero_title',               // Unique key for the field
    'type' => 'text',                    // Field type
    'required' => true,                  // Whether the field is required
    'description' => 'The main headline', // Help text for content editors
    'default_value' => null,             // Default value for new content
    'options' => [],                     // Options for select fields
    'validation_rules' => ['max:100'],   // Laravel validation rules
]
```

### Supported Field Types

| Type | Description | Form Component |
|------|-------------|----------------|
| `text` | Single line text input | TextInput |
| `textarea` | Multi-line text input | Textarea |
| `rich_text` | WYSIWYG rich text editor | RichEditor |
| `html` | HTML code editor with syntax highlighting | CodeEditor (html) |
| `css` | CSS code editor with syntax highlighting | CodeEditor (css) |
| `javascript` | JavaScript code editor with syntax highlighting | CodeEditor (javascript) |
| `number` | Numeric input | TextInput (numeric) |
| `email` | Email address input | TextInput (email) |
| `url` | URL input | TextInput (url) |
| `date` | Date picker | DatePicker |
| `datetime` | Date and time picker | DateTimePicker |
| `select` | Dropdown selection | Select |
| `checkbox` | Single checkbox | Checkbox |
| `toggle` | Toggle switch | Toggle |
| `file` | File upload | FileUpload |
| `image` | Image upload with editor | FileUpload (image) |
| `color` | Color picker | ColorPicker |

## Creating Templates

### Using the Admin Interface

1. Navigate to **Templates** in the admin panel
2. Click **Create Template**
3. Fill in the template information:
   - **Name**: Template name
   - **Description**: Optional description
   - **Active**: Whether the template is active
4. Define template fields using the repeater:
   - **Field Name**: Display name (automatically generates the key)
   - **Field Type**: Select from available types
   - **Description**: Help text for content editors
   - **Required Field**: Toggle for required validation
   - **Default Value**: Optional default value
   - **Select Options**: For select field types only
   - **Validation Rules**: Laravel validation rules

### Example Template Structure

```php
Template::create([
    'site_id' => $site->id,
    'name' => 'Landing Page',
    'description' => 'A template for landing pages',
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
            'name' => 'Hero Content',
            'key' => 'hero_content',
            'type' => 'rich_text',
            'required' => true,
            'description' => 'Rich content for the hero section',
            'default_value' => null,
            'options' => [],
            'validation_rules' => [],
        ],
        [
            'name' => 'CTA Button Text',
            'key' => 'cta_text',
            'type' => 'text',
            'required' => false,
            'description' => 'Call-to-action button text',
            'default_value' => 'Get Started',
            'options' => [],
            'validation_rules' => ['max:50'],
        ],
        [
            'name' => 'Page Type',
            'key' => 'page_type',
            'type' => 'select',
            'required' => true,
            'description' => 'Type of landing page',
            'default_value' => 'standard',
            'options' => [
                'standard' => 'Standard',
                'product' => 'Product',
                'service' => 'Service',
            ],
            'validation_rules' => [],
        ],
    ],
    'active' => true,
]);
```

## Using Template Content

### In Page Forms

When editing a page with a template assigned:

1. Select a template in the **Template** field
2. The **Template Content** section will automatically appear
3. Fill in the content for each template field
4. Save the page - template content is automatically saved

### Programmatic Access

```php
use App\Services\TemplateContentService;

// Get content for a page
$content = TemplateContentService::getContentForPage($page);

// Update content for a page
$contentData = [
    'hero_title' => 'Welcome to Our Site',
    'hero_content' => '<p>This is our amazing content</p>',
    'cta_text' => 'Learn More',
    'page_type' => 'product',
];
TemplateContentService::updateContentForPage($page, $contentData);

// Auto-generate missing fields
TemplateContentService::autoGenerateContentFields($page);

// Validate content
$errors = TemplateContentService::validateContent($template, $contentData);
```

## Migration and Backward Compatibility

### Automatic Migration

The system includes a migration that automatically converts existing templates from the old format to the new format:

- Old format: `['title' => 'text', 'content' => 'textarea']`
- New format: Array of field objects with full configuration

### Template Model Methods

The `Template` model provides several helpful methods:

```php
// Get fields in the new format (handles conversion)
$fields = $template->getFieldsForFormAttribute();

// Get just the field keys
$keys = $template->getFieldKeysAttribute();

// Get a specific field by key
$field = $template->getFieldByKey('hero_title');
```

## Best Practices

1. **Field Naming**: Use descriptive field names that clearly indicate their purpose
2. **Validation**: Add appropriate validation rules to ensure content quality
3. **Default Values**: Provide sensible default values for optional fields
4. **Descriptions**: Include helpful descriptions to guide content editors
5. **Field Types**: Choose the most appropriate field type for each content piece
6. **Required Fields**: Mark essential fields as required to ensure complete content

## File Uploads

For file and image fields:

- Files are stored in `storage/app/public/template-files/`
- Images are stored in `storage/app/public/template-images/`
- Image fields include an image editor for cropping and resizing
- Make sure to run `php artisan storage:link` to create the public symlink

## Rich Text Editor

The rich text editor includes the following toolbar buttons:
- Bold, italic, underline, strikethrough
- Headings (H2, H3)
- Bullet and ordered lists
- Links and blockquotes
- Code blocks

## Code Editors

The platform includes advanced code editors for HTML, CSS, and JavaScript fields:

### HTML Code Editor
- Syntax highlighting for HTML
- Auto-completion for HTML tags and attributes
- Bracket matching and indentation
- Perfect for custom HTML snippets, embeds, or structured content

### CSS Code Editor
- CSS syntax highlighting and validation
- Auto-completion for CSS properties and values
- Useful for custom styling, component-specific CSS, or theme overrides

### JavaScript Code Editor
- JavaScript syntax highlighting
- Auto-completion for JavaScript keywords and functions
- Ideal for custom scripts, analytics code, or interactive elements

All code editors feature:
- Line numbers
- Syntax error detection
- Code folding
- Find and replace functionality
- Multiple cursor support

## Validation

Template content validation supports all Laravel validation rules:

```php
'validation_rules' => [
    'required',
    'max:255',
    'min:10',
    'email',
    'url',
    'numeric',
    'between:1,100',
    // ... any Laravel validation rule
]
```

## Testing

The system includes comprehensive tests in `tests/Feature/EnhancedTemplateContentTest.php` covering:

- Template field generation
- Form component creation
- Content saving and retrieval
- Auto-generation of fields
- Content validation
- Backward compatibility with old format templates
