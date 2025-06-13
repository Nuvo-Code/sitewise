@if($isAiEnabled)
<div class="ai-floating-button-container">
    <!-- Floating Action Button -->
    <button
        wire:click="openPromptModal"
        class="ai-floating-button"
        title="Generate AI Content"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
    </button>

    <!-- Prompt Modal -->
    @if($showPromptModal)
    <div class="ai-modal-overlay" wire:click="closePromptModal">
        <div class="ai-modal-content" wire:click.stop>
            <div class="ai-modal-header">
                <h3 class="ai-modal-title">Generate AI Content</h3>
                <button wire:click="closePromptModal" class="ai-modal-close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="ai-modal-body">
                @if($error)
                <div class="ai-error-message">
                    {{ $error }}
                </div>
                @endif

                <div class="ai-form-group">
                    <label for="prompt" class="ai-form-label">
                        Describe the content you want to generate:
                    </label>
                    <textarea
                        wire:model="prompt"
                        id="prompt"
                        rows="4"
                        class="ai-form-textarea"
                        placeholder="Example: Create a hero section for a tech startup with a call-to-action button..."
                        @if($isGenerating) disabled @endif
                    ></textarea>
                    @error('prompt')
                    <div class="ai-field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="ai-modal-footer">
                <button
                    wire:click="closePromptModal"
                    class="ai-button ai-button-secondary"
                    @if($isGenerating) disabled @endif
                >
                    Cancel
                </button>
                <button
                    wire:click="generateContent"
                    class="ai-button ai-button-primary"
                    @if($isGenerating) disabled @endif
                >
                    @if($isGenerating)
                        <svg class="ai-spinner" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generating...
                    @else
                        Generate Content
                    @endif
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Response Modal -->
    @if($showResponseModal)
    <div class="ai-modal-overlay" wire:click="closeResponseModal">
        <div class="ai-modal-content ai-modal-large" wire:click.stop>
            <div class="ai-modal-header">
                <h3 class="ai-modal-title">Generated Content</h3>
                <div class="ai-model-info">
                    {{ ucfirst($provider) }} - {{ $model }}
                </div>
                <button wire:click="closeResponseModal" class="ai-modal-close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="ai-modal-body">
                <div class="ai-content-preview">
                    <div class="ai-content-tabs">
                        <button class="ai-tab ai-tab-active" onclick="showTab('preview')">Preview</button>
                        <button class="ai-tab" onclick="showTab('code')">HTML Code</button>
                    </div>

                    <div id="preview-tab" class="ai-tab-content ai-tab-content-active">
                        <div class="ai-content-rendered">
                            {!! $generatedContent !!}
                        </div>
                    </div>

                    <div id="code-tab" class="ai-tab-content">
                        <pre class="ai-code-block"><code>{{ $generatedContent }}</code></pre>
                    </div>
                </div>
            </div>

            <div class="ai-modal-footer">
                <button
                    wire:click="regenerateContent"
                    class="ai-button ai-button-secondary"
                >
                    Regenerate
                </button>
                <button
                    wire:click="copyContent"
                    class="ai-button ai-button-primary"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy HTML
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.ai-tab-content').forEach(tab => {
            tab.classList.remove('ai-tab-content-active');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.ai-tab').forEach(tab => {
            tab.classList.remove('ai-tab-active');
        });

        // Show selected tab content
        document.getElementById(tabName + '-tab').classList.add('ai-tab-content-active');

        // Add active class to clicked tab
        event.target.classList.add('ai-tab-active');
    }

    // Copy to clipboard functionality
    document.addEventListener('livewire:init', () => {
        Livewire.on('copy-to-clipboard', (event) => {
            navigator.clipboard.writeText(event.content).catch(err => {
                console.error('Failed to copy: ', err);
            });
        });
    });
</script>
@endif
