<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="generateContent" class="space-y-4">
            {{ $this->form }}

            <div class="flex gap-3">
                <x-filament::button
                    type="submit"
                    :disabled="!$this->canGenerate"
                    color="primary"
                    size="lg">
                    @if($isGenerating)
                    Generating...
                    @else
                    Generate Content
                    @endif
                </x-filament::button>

                @if($showResult)
                <x-filament::button
                    wire:click="resetForm"
                    color="gray"
                    size="lg">
                    New Generation
                </x-filament::button>
                @endif
            </div>
        </form>

        <!-- Results Section -->
        @if($showResult)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-document-text class="w-5 h-5 text-success-500" />
                        Generated Content
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ ucfirst($provider) }} - {{ $model }}
                    </div>
                </div>
            </x-slot>

            <div class="space-y-4">
                <!-- Content Preview Tabs -->
                <div class="border border-gray-200 rounded-lg overflow-hidden dark:border-gray-700">
                    <div class="flex bg-gray-50 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <button
                            onclick="showTab('preview')"
                            id="preview-tab-btn"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-r border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 tab-button active">
                            Preview
                        </button>
                        <button
                            onclick="showTab('code')"
                            id="code-tab-btn"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-600 tab-button">
                            HTML Code
                        </button>
                    </div>

                    <!-- Preview Tab -->
                    <div id="preview-tab" class="p-4 tab-content active">
                        <div class="prose max-w-none dark:prose-invert">
                            {!! $generatedContent !!}
                        </div>
                    </div>

                    <!-- Code Tab -->
                    <div id="code-tab" class="p-4 tab-content hidden">
                        <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto text-sm"><code>{{ $generatedContent }}</code></pre>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <x-filament::button
                        wire:click="copyContent"
                        color="primary">
                        <x-heroicon-o-clipboard class="w-4 h-4" />
                        Copy HTML
                    </x-filament::button>

                    <x-filament::button
                        wire:click="regenerateContent"
                        color="gray"
                        :disabled="!$this->canGenerate">
                        @if($isGenerating)
                        Regenerating...
                        @else
                        Regenerate
                        @endif
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
        @endif
    </div>

    <!-- JavaScript for tabs and clipboard -->
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
                tab.classList.remove('active');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'bg-white', 'dark:bg-gray-700');
                button.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-600');
            });

            // Show selected tab content
            const selectedTab = document.getElementById(tabName + '-tab');
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
                selectedTab.classList.add('active');
            }

            // Add active class to clicked tab button
            const selectedButton = document.getElementById(tabName + '-tab-btn');
            if (selectedButton) {
                selectedButton.classList.add('active', 'bg-white', 'dark:bg-gray-700');
                selectedButton.classList.remove('hover:bg-gray-50', 'dark:hover:bg-gray-600');
            }
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

    <style>
        .tab-button.active {
            background-color: white;
            color: rgb(37 99 235);
        }

        .dark .tab-button.active {
            background-color: rgb(55 65 81);
            color: rgb(96 165 250);
        }
    </style>
</x-filament-panels::page>