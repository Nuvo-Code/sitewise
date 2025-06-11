<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Template;
use App\Services\CacheService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;

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

        // Use cached template content
        $templateContent = CacheService::getTemplateContent($page->id);

        // Prepare variables for the Blade template
        $variables = self::prepareTemplateVariables($page, $templateContent);

        // Create a cache key that includes the template version and content hash
        $contentHash = md5(serialize($variables) . $template->blade_template . $template->updated_at);
        $cacheKey = CacheService::siteKey(CacheService::BLADE_TEMPLATE_PREFIX, $page->site_id, $template->id . ':' . $contentHash);

        // Check for cached rendered output
        $cachedOutput = Cache::get($cacheKey);
        if ($cachedOutput !== null) {
            return $cachedOutput;
        }

        // Compile and render the Blade template directly from database content
        $rendered = self::compileAndRender($template->blade_template, $variables);

        // Cache the final rendered output with the content-specific key
        Cache::put($cacheKey, $rendered, CacheService::getBladeTTL());

        return $rendered;
    }



    /**
     * Compile and render a Blade template string with variables
     */
    protected static function compileAndRender(string $bladeTemplate, array $variables): string
    {
        // Process the Blade template content for security
        $processedTemplate = self::processBladeTemplate($bladeTemplate);

        // Create a temporary view instance using Blade's compileString
        $compiledTemplate = Blade::compileString($processedTemplate);

        // Create a temporary file to evaluate the compiled PHP
        $tempFile = tempnam(sys_get_temp_dir(), 'blade_template_');
        file_put_contents($tempFile, $compiledTemplate);

        // Extract variables to make them available in the template
        extract($variables);

        // Start output buffering
        ob_start();

        try {
            // Include the compiled template
            include $tempFile;
            $output = ob_get_contents();
        } catch (\Throwable $e) {
            ob_end_clean();
            unlink($tempFile);
            throw new \Exception('Error rendering Blade template: ' . $e->getMessage());
        } finally {
            ob_end_clean();
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }

        return $output;
    }

    /**
     * Process the Blade template content for security and compatibility
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

            // Try to compile the Blade template to check for syntax errors
            Blade::compileString($bladeTemplate);

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
            $variables['template_content[\'' . $field['type']['key'] . '\']'] = $field['description'] ?: $field['type']['name'];
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
        $sample .= "        <main class=\"content\">\n\n";

        foreach ($fields as $field) {
            $id = $field['key'];
            $key = $field['type']['key'];
            $name = $field['type']['name'];
            $type = $field['type']['type'];

            $sample .= "            <!-- {{-- {$name} ({$id}) --}} -->\n";
            
            if ($type === 'rich_text' || $type === 'textarea') {
                $sample .= "            <div class=\"{$id}\">\n";
                $sample .= "                {!! \$template_content['{$key}'] !!}\n";
                $sample .= "            </div>\n";
            } elseif ($type === 'image') {
                $sample .= "            @if(\${$key})\n";
                $sample .= "                <img src=\"{{ asset('storage/' . \$template_content['{$key}']) }}\" alt=\"{$name}\" class=\"{$id}\">\n";
                $sample .= "            @endif\n";
            } elseif ($type === 'url') {
                $sample .= "            @if(\${$key})\n";
                $sample .= "                <a href=\"{{ \$template_content['{$key}'] }}\" class=\"{$id}\">{$name}</a>\n";
                $sample .= "            @endif\n";
            } elseif ($type === 'toggle' || $type === 'checkbox') {
                $sample .= "            @if(\${$key})\n";
                $sample .= "                <div class=\"{$id}\">✓ {$name}</div>\n";
                $sample .= "            @endif\n";
            } else {
                $sample .= "            <div class=\"{$id}\">{{ \$template_content['{$key}'] }}</div>\n";
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
        // Use the new CacheService for better cache management
        CacheService::clearTemplateCache($template->site_id, $template->id);

        // Clear any compiled view cache that might exist
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Clear all template caches
     */
    public static function clearAllTemplateCaches(): void
    {
        // Use the new CacheService for better cache management
        CacheService::clearAllCache();

        // Clear any compiled view cache that might exist
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}
