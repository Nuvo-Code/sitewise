<x-filament-panels::page>
    <div class="space-y-6">
        <div class="text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                <x-heroicon-o-cog-6-tooth class="h-6 w-6 text-primary-600 dark:text-primary-400" />
            </div>
            <h2 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                {{ $this->getHeading() }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ $this->getSubheading() }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-filament::button
                    type="submit"
                    size="lg"
                    class="min-w-[120px]"
                >
                    Complete Setup
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
