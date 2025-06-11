<div class="space-y-6">
    @if($site)
    {{-- Current Site Cache Overview --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <x-heroicon-o-building-office class="w-5 h-5 mr-4 text-blue-500" />
            {{ $site->name }} Cache Overview
        </h3>

        <div class="grid grid-cols-4 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Cache Driver</div>
                <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                    {{ ucfirst($cacheStats['driver']) }}
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                <div class="text-sm font-medium text-green-600 dark:text-green-400">Site Cache Keys</div>
                <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                    {{ number_format(array_sum($siteCacheUsage)) }}
                </div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                <div class="text-sm font-medium text-purple-600 dark:text-purple-400">Domain</div>
                <div class="text-lg font-bold text-purple-900 dark:text-purple-100">
                    {{ $site->domain }}
                </div>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                <div class="text-sm font-medium text-orange-600 dark:text-orange-400">Status</div>
                <div class="text-lg font-bold text-orange-900 dark:text-orange-100">
                    {{ $site->active ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Site Cache Usage --}}
    @if($site && !empty($siteCacheUsage))
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <x-heroicon-o-building-office class="w-5 h-5 mr-2 text-indigo-500" />
            Site Cache Usage: {{ $site->name }}
        </h3>
        
        <div class="grid grid-cols-3 gap-4">
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

    {{-- Site Cache Management Tips --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <x-heroicon-o-light-bulb class="w-5 h-5 mr-2 text-yellow-500" />
            Site Cache Management Tips
        </h3>

        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
            <div class="flex items-start">
                <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                <span>Clear site cache when making content changes to this site</span>
            </div>
            <div class="flex items-start">
                <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                <span>Warm cache after clearing to improve page load times</span>
            </div>
            <div class="flex items-start">
                <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                <span>Clear pages cache when updating page content</span>
            </div>
            <div class="flex items-start">
                <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                <span>Clear templates cache when modifying template structure</span>
            </div>
            <div class="flex items-start">
                <x-heroicon-o-check-circle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
                <span>Cache is automatically cleared when you save changes</span>
            </div>
        </div>
    </div>

    @if($site)
    {{-- Site-Specific Cache Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <x-heroicon-o-cog-6-tooth class="w-5 h-5 mr-2 text-gray-500" />
            Available Actions for {{ $site->name }}
        </h3>

        <div class="text-sm text-gray-600 dark:text-gray-400">
            Use the action buttons in the header to manage this site's cache:
            <ul class="mt-2 space-y-1 list-disc list-inside">
                <li><strong>Clear Site Cache:</strong> Removes all cached data for this site</li>
                <li><strong>Warm Site Cache:</strong> Pre-loads cache for better performance</li>
                <li><strong>Clear Pages Cache:</strong> Removes only page-related cache</li>
                <li><strong>Clear Templates Cache:</strong> Removes only template-related cache</li>
            </ul>
        </div>
    </div>
    @endif
</div>