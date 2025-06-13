<x-filament-panels::page>
    @if($site)
    <div class="space-y-6">
        {{-- Cache Status Warning --}}
        @php
            $cacheWorking = \App\Services\CacheService::isCacheWorking();
        @endphp

        @if(!$cacheWorking)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
            <div class="flex items-start">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5 flex-shrink-0" />
                <div>
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('filament.cache.status.cache_system_not_available') }}</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        {{ __('filament.cache.status.cache_unavailable_description') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Current Site Overview --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-building-office class="w-5 h-5 mr-2 text-blue-500" />
                {{ $site->name }} - {{ __('filament.pages.cache_management.title') }}
            </h3>

            <div class="grid grid-cols-4 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ __('filament.cache.status.domain') }}</div>
                    <div class="text-lg font-bold text-blue-900 dark:text-blue-100">
                        {{ $site->domain }}
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('filament.cache.status.cache_status') }}</div>
                    <div class="text-lg font-bold text-green-900 dark:text-green-100">
                        {{ $cacheWorking ? __('filament.cache.status.working') : __('filament.cache.status.unavailable') }}
                    </div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ __('filament.cache.status.site_cache_keys') }}</div>
                    <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                        {{ number_format(array_sum($siteCacheUsage)) }}
                    </div>
                    @if(!$cacheWorking)
                    <div class="text-xs text-purple-600 dark:text-purple-400 mt-1">{{ __('filament.cache.status.simulated') }}</div>
                    @endif
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-orange-600 dark:text-orange-400">{{ __('filament.cache.status.cache_driver') }}</div>
                    <div class="text-lg font-bold text-orange-900 dark:text-orange-100">
                        {{ ucfirst($cacheStats['driver']) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Site Cache Breakdown --}}
        @if(!empty($siteCacheUsage))
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 text-indigo-500" />
                {{ __('filament.cache.breakdown.heading') }}
                @if(!$cacheWorking)
                <span class="ml-2 text-sm text-yellow-600 dark:text-yellow-400">{{ __('filament.cache.breakdown.simulated_data') }}</span>
                @endif
            </h3>

            <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['site'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('filament.cache.breakdown.site_data') }}</div>
                </div>

                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['pages'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('filament.cache.breakdown.pages') }}</div>
                </div>

                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['templates'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('filament.cache.breakdown.templates') }}</div>
                </div>

                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['template_content'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('filament.cache.breakdown.template_content') }}</div>
                </div>

                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['blade_templates'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('filament.cache.breakdown.compiled_templates') }}</div>
                </div>

                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['stats'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('filament.cache.breakdown.statistics') }}</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Site Cache Management Tips --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-light-bulb class="w-5 h-5 mr-2 text-yellow-500" />
                Site Cache Management
            </h3>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Cache is automatically cleared when you save changes</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Clear site cache when making bulk content changes</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Warm cache after clearing to improve page load times</span>
                    </div>
                </div>

                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Clear pages cache when updating multiple pages</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Clear templates cache when modifying template structure</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Use the action buttons above to manage this site's cache</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Site-Specific Commands --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-command-line class="w-5 h-5 mr-2 text-gray-500" />
                Command Line Management for {{ $site->name }}
            </h3>

            <div class="bg-gray-900 rounded-lg p-4 text-green-400 font-mono text-sm overflow-x-auto">
                <div class="space-y-2">
                    <div># Clear cache for this site</div>
                    <div class="text-white">php artisan sitewise:cache clear-site --site={{ $site->domain }}</div>
                    <div class="mt-4"># Warm cache for this site</div>
                    <div class="text-white">php artisan sitewise:cache warm-site --site={{ $site->domain }}</div>
                    <div class="mt-4"># Clear only pages cache for this site</div>
                    <div class="text-white">php artisan sitewise:cache clear-site --site={{ $site->domain }} --type=pages</div>
                    <div class="mt-4"># Clear only templates cache for this site</div>
                    <div class="text-white">php artisan sitewise:cache clear-site --site={{ $site->domain }} --type=templates</div>
                    <div class="mt-4"># Show cache statistics</div>
                    <div class="text-white">php artisan sitewise:cache stats</div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-12">
        <x-heroicon-o-exclamation-triangle class="w-12 h-12 mx-auto text-yellow-500 mb-4" />
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Site Available</h3>
        <p class="text-gray-600 dark:text-gray-400">Cache management is only available when accessing from a registered domain.</p>
    </div>
    @endif
</x-filament-panels::page>
