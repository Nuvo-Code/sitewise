<x-filament-panels::page>
    <div class="space-y-6">
        {{-- System Cache Stats --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-cpu-chip class="w-5 h-5 mr-2 text-blue-500" />
                System Cache Statistics
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Cache Driver</div>
                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        {{ ucfirst($cacheStats['driver']) }}
                    </div>
                </div>
                
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-green-600 dark:text-green-400">Total Keys</div>
                    <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                        {{ number_format($cacheStats['total_keys']) }}
                    </div>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-purple-600 dark:text-purple-400">Memory Usage</div>
                    <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                        {{ $cacheStats['memory_usage'] }}
                    </div>
                </div>
                
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                    <div class="text-sm font-medium text-orange-600 dark:text-orange-400">Hit Rate</div>
                    <div class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                        {{ $cacheStats['hit_rate'] }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- Site Cache Usage --}}
        @if($site && !empty($siteCacheUsage))
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-building-office class="w-5 h-5 mr-2 text-indigo-500" />
                Site Cache Usage: {{ $site->name }}
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['site'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Site Data</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['pages'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pages</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['templates'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Templates</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['template_content'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Template Content</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['blade_templates'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Compiled Templates</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($siteCacheUsage['stats'] ?? 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Statistics</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Cache Performance Tips --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-light-bulb class="w-5 h-5 mr-2 text-yellow-500" />
                Performance Optimization Tips
            </h3>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Cache hit rates above 80% indicate good performance</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Use Redis cache driver for better performance in production</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Warm cache after clearing to maintain optimal response times</span>
                    </div>
                </div>
                
                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Monitor memory usage to prevent cache overflow</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Clear site-specific cache when making content changes</span>
                    </div>
                    <div class="flex items-start">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                        <span>Use the action buttons above to manage cache efficiently</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cache Management Commands --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-command-line class="w-5 h-5 mr-2 text-gray-500" />
                Command Line Management
            </h3>
            
            <div class="bg-gray-900 rounded-lg p-4 text-green-400 font-mono text-sm overflow-x-auto">
                <div class="space-y-2">
                    <div># Show cache statistics</div>
                    <div class="text-white">php artisan sitewise:cache stats</div>
                    <div class="mt-4"># Clear all cache</div>
                    <div class="text-white">php artisan sitewise:cache clear</div>
                    <div class="mt-4"># Warm cache for all sites</div>
                    <div class="text-white">php artisan sitewise:cache warm</div>
                    @if($site)
                    <div class="mt-4"># Clear cache for current site</div>
                    <div class="text-white">php artisan sitewise:cache clear-site --site={{ $site->domain }}</div>
                    <div class="mt-4"># Warm cache for current site</div>
                    <div class="text-white">php artisan sitewise:cache warm-site --site={{ $site->domain }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
