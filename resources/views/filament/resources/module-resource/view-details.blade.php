<div class="space-y-6">
    <!-- Module Header -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $module['display_name'] }}</h3>
                <p class="text-sm text-gray-600">{{ $module['description'] }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $module['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $module['active'] ? 'Active' : 'Inactive' }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    v{{ $module['version'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Module Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Basic Information</h4>
            <dl class="space-y-2">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Module Name</dt>
                    <dd class="text-sm text-gray-900">{{ $module['name'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Display Name</dt>
                    <dd class="text-sm text-gray-900">{{ $module['display_name'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Version</dt>
                    <dd class="text-sm text-gray-900">{{ $module['version'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Author</dt>
                    <dd class="text-sm text-gray-900">{{ $module['author'] ?? 'Unknown' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Status & Configuration -->
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Status & Configuration</h4>
            <dl class="space-y-2">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</dt>
                    <dd class="text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $module['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $module['active'] ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Module Path</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ $module['path'] ?? 'N/A' }}</dd>
                </div>
                @if(isset($module['providers']) && !empty($module['providers']))
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Service Providers</dt>
                    <dd class="text-sm text-gray-900">
                        @foreach($module['providers'] as $provider)
                            <div class="font-mono text-xs">{{ $provider }}</div>
                        @endforeach
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Requirements -->
    @if(isset($module['requirements']) && !empty($module['requirements']))
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Requirements</h4>
        <div class="grid grid-cols-2 gap-4">
            @foreach($module['requirements'] as $requirement => $version)
            <div class="flex justify-between items-center py-1">
                <span class="text-sm text-gray-600">{{ ucfirst($requirement) }}</span>
                <span class="text-sm font-mono text-gray-900">{{ $version }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tags -->
    @if(isset($module['tags']) && !empty($module['tags']))
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Tags</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($module['tags'] as $tag)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $tag }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Dependencies -->
    @if(isset($module['dependencies']) && !empty($module['dependencies']))
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Dependencies</h4>
        @if(empty($module['dependencies']))
            <p class="text-sm text-gray-500">No dependencies</p>
        @else
            <ul class="space-y-1">
                @foreach($module['dependencies'] as $dependency)
                <li class="text-sm text-gray-900">{{ $dependency }}</li>
                @endforeach
            </ul>
        @endif
    </div>
    @endif

    <!-- Description -->
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Description</h4>
        <p class="text-sm text-gray-700">{{ $module['description'] }}</p>
    </div>
</div>
