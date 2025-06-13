<?php

namespace App\Filament\Pages;

use App\Services\AiContentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class AiContentGeneration extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    
    protected static string $view = 'filament.pages.ai-content-generation';
    
    protected static ?string $title = null;

    public function getTitle(): string
    {
        return __('filament.pages.ai_content_generation.title');
    }
    
    protected static bool $shouldRegisterNavigation = false;
    
    public ?array $data = [];
    
    public string $generatedContent = '';
    public string $model = '';
    public string $provider = '';
    public bool $isGenerating = false;
    public bool $showResult = false;

    protected AiContentService $aiContentService;

    public function boot(AiContentService $aiContentService): void
    {
        $this->aiContentService = $aiContentService;
    }

    public function mount(): void
    {
        $site = app('site');
        
        if (!$site || !$site->isAiEnabled()) {
            Notification::make()
                ->title('AI Not Available')
                ->body('AI content generation is not configured for this site.')
                ->warning()
                ->send();
                
            redirect()->to('/admin');
            return;
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.ai.generate_content.heading'))
                    ->schema([
                        Forms\Components\Textarea::make('prompt')
                            ->label(__('filament.ai.generate_content.content_description'))
                            ->hiddenLabel()
                            ->placeholder(__('filament.ai.generate_content.placeholder'))
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->helperText(__('filament.ai.generate_content.helper'))
                            ->disabled($this->isGenerating),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function generateContent(): void
    {
        $this->validate();
        
        $site = app('site');
        
        if (!$site || !$site->isAiEnabled()) {
            Notification::make()
                ->title('AI Not Available')
                ->body('AI content generation is not configured for this site.')
                ->danger()
                ->send();
            return;
        }

        $this->isGenerating = true;
        $this->showResult = false;

        try {
            $result = $this->aiContentService->generateContent($site, $this->data['prompt']);

            if ($result['success']) {
                $this->generatedContent = $result['content'];
                $this->model = $result['model'];
                $this->provider = $result['provider'];
                $this->showResult = true;

                Notification::make()
                    ->title('Content Generated Successfully')
                    ->body('Your AI content has been generated and is ready to use!')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Generation Failed')
                    ->body($result['error'])
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Unexpected Error')
                ->body('An error occurred while generating content: ' . $e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isGenerating = false;
        }
    }

    public function copyContent(): void
    {
        $this->dispatch('copy-to-clipboard', content: $this->generatedContent);

        Notification::make()
            ->title('Copied!')
            ->body('Content has been copied to clipboard.')
            ->success()
            ->send();
    }

    public function regenerateContent(): void
    {
        $this->generateContent();
    }

    public function resetForm(): void
    {
        $this->form->fill();
        $this->generatedContent = '';
        $this->model = '';
        $this->provider = '';
        $this->showResult = false;
    }

    #[Computed]
    public function canGenerate(): bool
    {
        $site = app('site');
        return $site && $site->isAiEnabled() && !$this->isGenerating;
    }

    public static function canAccess(): bool
    {
        $site = app('site');
        return $site && $site->isAiEnabled();
    }
}
