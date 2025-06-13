<?php

namespace App\Services;

use App\Models\Site;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Providers\OpenAI\OpenAI;
use EchoLabs\Prism\Providers\Anthropic\Anthropic;
use Illuminate\Support\Facades\Log;

class AiContentService
{
    /**
     * Generate HTML content using AI based on the site's configuration
     */
    public function generateContent(Site $site, string $prompt): array
    {
        try {
            if (!$site->isAiEnabled()) {
                return [
                    'success' => false,
                    'error' => 'AI is not enabled for this site. Please configure AI settings first.',
                ];
            }

            $config = $site->getAiConfiguration();
            $provider = $this->createProvider($site);
            
            if (!$provider) {
                return [
                    'success' => false,
                    'error' => 'Failed to initialize AI provider. Please check your configuration.',
                ];
            }

            // Create the enhanced prompt for HTML content generation
            $enhancedPrompt = $this->buildHtmlPrompt($prompt);

            // Generate content using Prism
            $response = Prism::text()
                ->using($provider, $site->getAiModel())
                ->withPrompt($enhancedPrompt)
                ->generate();

            $content = $response->text;

            // Clean and validate the generated HTML
            $cleanedContent = $this->cleanHtmlContent($content);

            return [
                'success' => true,
                'content' => $cleanedContent,
                'model' => $site->getAiModel(),
                'provider' => $site->getAiProvider(),
            ];

        } catch (\Exception $e) {
            Log::error('AI Content Generation Error', [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
                'prompt' => $prompt,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to generate content: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create the appropriate AI provider based on site configuration
     */
    private function createProvider(Site $site): ?object
    {
        $config = $site->getAiConfiguration();
        $provider = $site->getAiProvider();
        $apiKey = $config['api_key'] ?? null;

        if (!$apiKey) {
            return null;
        }

        return match ($provider) {
            'openai' => OpenAI::text()->withApiKey($apiKey),
            'anthropic' => Anthropic::text()->withApiKey($apiKey),
            default => null,
        };
    }

    /**
     * Build an enhanced prompt specifically for HTML content generation
     */
    private function buildHtmlPrompt(string $userPrompt): string
    {
        return "You are an expert web developer creating HTML content for a website. 

User Request: {$userPrompt}

Please generate clean, semantic HTML content that:
1. Uses modern HTML5 elements and best practices
2. Includes appropriate CSS classes for styling (assume Tailwind CSS is available)
3. Is responsive and accessible
4. Includes proper heading hierarchy (h1, h2, h3, etc.)
5. Uses semantic elements like <section>, <article>, <header>, <footer> where appropriate
6. Includes alt text for any images
7. Is ready to be inserted into a webpage

Return ONLY the HTML content without any markdown formatting, explanations, or code blocks. The response should be pure HTML that can be directly inserted into a page.";
    }

    /**
     * Clean and validate the generated HTML content
     */
    private function cleanHtmlContent(string $content): string
    {
        // Remove any markdown code block formatting
        $content = preg_replace('/^```html\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = preg_replace('/^```\s*/', '', $content);
        
        // Trim whitespace
        $content = trim($content);
        
        // Basic HTML validation - ensure it starts with a tag
        if (!preg_match('/^\s*</', $content)) {
            $content = '<div>' . $content . '</div>';
        }
        
        return $content;
    }

    /**
     * Get available AI models for a provider
     */
    public function getAvailableModels(string $provider): array
    {
        return match ($provider) {
            'openai' => [
                'gpt-4o' => 'GPT-4o (Latest)',
                'gpt-4o-mini' => 'GPT-4o Mini (Fast & Cost-effective)',
                'gpt-4-turbo' => 'GPT-4 Turbo',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            ],
            'anthropic' => [
                'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Latest)',
                'claude-3-opus-20240229' => 'Claude 3 Opus',
                'claude-3-sonnet-20240229' => 'Claude 3 Sonnet',
                'claude-3-haiku-20240307' => 'Claude 3 Haiku',
            ],
            default => [],
        };
    }

    /**
     * Get available AI providers
     */
    public function getAvailableProviders(): array
    {
        return [
            'openai' => 'OpenAI',
            'anthropic' => 'Anthropic',
        ];
    }

    /**
     * Test AI configuration
     */
    public function testConfiguration(Site $site): array
    {
        try {
            $provider = $this->createProvider($site);
            
            if (!$provider) {
                return [
                    'success' => false,
                    'error' => 'Failed to create provider. Check your API key.',
                ];
            }

            // Test with a simple prompt
            $response = Prism::text()
                ->using($provider, $site->getAiModel())
                ->withPrompt('Say "Hello, Sitewise!" in HTML format.')
                ->generate();

            return [
                'success' => true,
                'message' => 'AI configuration is working correctly.',
                'test_response' => $response->text,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Configuration test failed: ' . $e->getMessage(),
            ];
        }
    }
}
