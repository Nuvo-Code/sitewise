<?php

namespace App\Livewire;

use App\Models\Site;
use App\Services\AiContentService;
use Filament\Notifications\Notification;
use Livewire\Component;

class AiFloatingButton extends Component
{
    public $showPromptModal = false;

    public $showResponseModal = false;

    public $prompt = '';

    public $generatedContent = '';

    public $isGenerating = false;

    public $error = '';

    public $model = '';

    public $provider = '';

    protected $aiContentService;

    public function boot(AiContentService $aiContentService)
    {
        $this->aiContentService = $aiContentService;
    }

    public function mount()
    {
        // Check if AI is enabled for the current site
        $site = app('site');
        if (! $site || ! $site->isAiEnabled()) {
            return;
        }
    }

    public function openPromptModal()
    {
        $site = app('site');

        if (! $site || ! $site->isAiEnabled()) {
            Notification::make()
                ->title('AI Not Configured')
                ->body('Please configure AI settings in your site settings first.')
                ->warning()
                ->send();

            return;
        }

        $this->reset(['prompt', 'generatedContent', 'error', 'model', 'provider']);
        $this->showPromptModal = true;
    }

    public function closePromptModal()
    {
        $this->showPromptModal = false;
        $this->reset(['prompt', 'error']);
    }

    public function generateContent()
    {
        $this->validate([
            'prompt' => 'required|string|min:10|max:2000',
        ]);

        $site = app('site');

        if (! $site || ! $site->isAiEnabled()) {
            $this->error = 'AI is not configured for this site.';

            return;
        }

        $this->isGenerating = true;
        $this->error = '';

        try {
            $result = $this->aiContentService->generateContent($site, $this->prompt);

            if ($result['success']) {
                $this->generatedContent = $result['content'];
                $this->model = $result['model'];
                $this->provider = $result['provider'];
                $this->showPromptModal = false;
                $this->showResponseModal = true;

                Notification::make()
                    ->title('Content Generated')
                    ->body('AI content has been generated successfully!')
                    ->success()
                    ->send();
            } else {
                $this->error = $result['error'];
            }
        } catch (\Exception $e) {
            $this->error = 'An unexpected error occurred: '.$e->getMessage();
        } finally {
            $this->isGenerating = false;
        }
    }

    public function closeResponseModal()
    {
        $this->showResponseModal = false;
        $this->reset(['generatedContent', 'model', 'provider']);
    }

    public function copyContent()
    {
        $this->dispatch('copy-to-clipboard', content: $this->generatedContent);

        Notification::make()
            ->title('Copied!')
            ->body('Content has been copied to clipboard.')
            ->success()
            ->send();
    }

    public function regenerateContent()
    {
        $this->showResponseModal = false;
        $this->showPromptModal = true;
    }

    public function render()
    {
        $site = app('site');
        $isAiEnabled = $site && $site->isAiEnabled();

        return view('livewire.ai-floating-button', [
            'isAiEnabled' => $isAiEnabled,
        ]);
    }
}
