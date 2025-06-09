<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Template;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class BladeTemplateService
{
    /**
     * Render a page using its template's Blade template
     */
    public static function renderPage(Page $page): string
    {
        if (!$page->template || !$page->template->hasBladeTemplate()) {
            throw new \Exception('Page does not have a valid Blade template');
        }

        $template = $page->template;
        $templateContent = TemplateContentService::getContentForPage($page);

        // Create a unique view name for this template
        $viewName = self::getViewName($template);

        // Ensure the template view exists
        self::ensureTemplateViewExists($template, $viewName);

        // Prepare variables for the Blade template
        $variables = self::prepareTemplateVariables($page, $templateContent);

        // Render the template
        return View::make($viewName, $variables)->render();
    }

    /**
     * Get the view name for a template
     */
    protected static function getViewName(Template $template): string
    {
        return 'templates.site_' . $template->site_id . '.template_' . $template->id;
    }

    /**
     * Ensure the template view file exists
     */
    protected static function ensureTemplateViewExists(Template $template, string $viewName): void
    {
        $viewPath = resource_path('views/' . str_replace('.', '/', $viewName) . '.blade.php');
        $viewDir = dirname($viewPath);

        // Create directory if it doesn't exist
        if (!File::exists($viewDir)) {
            File::makeDirectory($viewDir, 0755, true);
        }

        // Write or update the template file
        $bladeContent = self::processBladeTemplate($template->blade_template);
        File::put($viewPath, $bladeContent);
    }

    /**
     * Process the Blade template content
     */
    protected static function processBladeTemplate(string $bladeTemplate): string
    {
        // Add any necessary processing here
        // For now, we'll just return the template as-is
        // You could add custom directives, security checks, etc.
        
        return $bladeTemplate;
    }

    /**
     * Prepare variables for the Blade template
     */
    protected static function prepareTemplateVariables(Page $page, array $templateContent): array
    {
        $variables = [
            'page' => $page,
            'site' => $page->site,
            'template' => $page->template,
            'content' => '', // Initialize content as empty string
        ];

        // Add template content as individual variables
        foreach ($templateContent as $key => $value) {
            $variables[$key] = $value;
        }

        // Add template content as a grouped array (for backward compatibility)
        $variables['template_content'] = $templateContent;

        // Add some helper variables
        $variables['page_title'] = $page->title;
        $variables['page_slug'] = $page->slug;
        $variables['site_name'] = $page->site->name;
        $variables['site_domain'] = $page->site->domain;

        return $variables;
    }

    /**
     * Validate Blade template syntax
     */
    public static function validateBladeTemplate(string $bladeTemplate): array
    {
        $errors = [];

        try {
            // Check for unmatched Blade syntax
            $openBraces = substr_count($bladeTemplate, '{{');
            $closeBraces = substr_count($bladeTemplate, '}}');

            if ($openBraces !== $closeBraces) {
                $errors[] = 'Blade template has unmatched braces';
            }

            // Check for specific syntax errors
            if (preg_match('/\{\{\s*[^}]*\s*\}(?!\})/', $bladeTemplate)) {
                $errors[] = 'Blade template has malformed syntax (single closing brace)';
            }

            // Try to compile the Blade template
            $compiled = Blade::compileString($bladeTemplate);

        } catch (\ParseError $e) {
            $errors[] = 'Blade template has syntax errors: ' . $e->getMessage();
        } catch (\Exception $e) {
            $errors[] = 'Blade compilation error: ' . $e->getMessage();
        }

        return $errors;
    }

    /**
     * Get available variables for a template
     */
    public static function getAvailableVariables(Template $template): array
    {
        $variables = [
            'page' => 'The current page object',
            'site' => 'The current site object',
            'template' => 'The current template object',
            'content' => 'Array of all template content',
            'page_title' => 'The page title',
            'page_slug' => 'The page slug',
            'site_name' => 'The site name',
            'site_domain' => 'The site domain',
        ];

        // Add template fields as available variables
        $fields = $template->getFieldsForFormAttribute();
        foreach ($fields as $field) {
            $variables[$field['key']] = $field['description'] ?: $field['name'];
        }

        return $variables;
    }

    /**
     * Generate a sample Blade template for a template
     */
    public static function generateSampleBladeTemplate(Template $template): string
    {
        $fields = $template->getFieldsForFormAttribute();
        
        $sample = "<!DOCTYPE html>\n";
        $sample .= "<html lang=\"en\">\n";
        $sample .= "<head>\n";
        $sample .= "    <meta charset=\"UTF-8\">\n";
        $sample .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $sample .= "    <title>{{ \$page_title }} - {{ \$site_name }}</title>\n";
        $sample .= "    <style>\n";
        $sample .= "        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }\n";
        $sample .= "        .container { max-width: 800px; margin: 0 auto; }\n";
        $sample .= "        .header { border-bottom: 1px solid #ccc; padding-bottom: 20px; margin-bottom: 20px; }\n";
        $sample .= "        .content { line-height: 1.6; }\n";
        $sample .= "    </style>\n";
        $sample .= "</head>\n";
        $sample .= "<body>\n";
        $sample .= "    <div class=\"container\">\n";
        $sample .= "        <header class=\"header\">\n";
        $sample .= "            <h1>{{ \$page_title }}</h1>\n";
        $sample .= "            <p>Site: {{ \$site_name }} ({{ \$site_domain }})</p>\n";
        $sample .= "        </header>\n";
        $sample .= "        \n";
        $sample .= "        <main class=\"content\">\n";

        foreach ($fields as $field) {
            $key = $field['key'];
            $name = $field['name'];
            $type = $field['type'];

            $sample .= "            {{-- {$name} --}}\n";
            
            if ($type === 'rich_text' || $type === 'textarea') {
                $sample .= "            <div class=\"{$key}\">\n";
                $sample .= "                {!! \${$key} !!}\n";
                $sample .= "            </div>\n";
            } elseif ($type === 'image') {
                $sample .= "            @if(\${$key})\n";
                $sample .= "                <img src=\"{{ asset('storage/' . \${$key}) }}\" alt=\"{$name}\" class=\"{$key}\">\n";
                $sample .= "            @endif\n";
            } elseif ($type === 'url') {
                $sample .= "            @if(\${$key})\n";
                $sample .= "                <a href=\"{{ \${$key} }}\" class=\"{$key}\">{$name}</a>\n";
                $sample .= "            @endif\n";
            } elseif ($type === 'toggle' || $type === 'checkbox') {
                $sample .= "            @if(\${$key})\n";
                $sample .= "                <div class=\"{$key}\">✓ {$name}</div>\n";
                $sample .= "            @endif\n";
            } else {
                $sample .= "            <div class=\"{$key}\">{{ \${$key} }}</div>\n";
            }
            $sample .= "            \n";
        }

        $sample .= "        </main>\n";
        $sample .= "        \n";
        $sample .= "        <footer>\n";
        $sample .= "            <p><small>Generated by Sitewise Template System</small></p>\n";
        $sample .= "        </footer>\n";
        $sample .= "    </div>\n";
        $sample .= "</body>\n";
        $sample .= "</html>";

        return $sample;
    }

    /**
     * Clear template cache for a specific template
     */
    public static function clearTemplateCache(Template $template): void
    {
        $viewName = self::getViewName($template);
        $viewPath = resource_path('views/' . str_replace('.', '/', $viewName) . '.blade.php');
        
        if (File::exists($viewPath)) {
            File::delete($viewPath);
        }

        // Clear compiled view cache
        $compiledPath = config('view.compiled');
        if ($compiledPath && File::exists($compiledPath)) {
            $compiledFiles = File::glob($compiledPath . '/*' . str_replace('.', '_', $viewName) . '*');
            foreach ($compiledFiles as $file) {
                File::delete($file);
            }
        }
    }

    /**
     * Clear all template caches
     */
    public static function clearAllTemplateCaches(): void
    {
        $templatesDir = resource_path('views/templates');
        if (File::exists($templatesDir)) {
            File::deleteDirectory($templatesDir);
        }

        // Clear compiled view cache
        $compiledPath = config('view.compiled');
        if ($compiledPath && File::exists($compiledPath)) {
            $compiledFiles = File::glob($compiledPath . '/*templates*');
            foreach ($compiledFiles as $file) {
                File::delete($file);
            }
        }
    }
}
