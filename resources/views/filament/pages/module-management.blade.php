<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Module Management</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage and configure modules for your Sitewise installation. Activate or deactivate modules as needed.
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-puzzle-piece class="h-8 w-8 text-blue-500" />
                </div>
            </div>
        </div>

        <!-- Module Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $totalModules = count($this->modules);
                $activeCount = count(array_filter($this->modules, fn($module) => $module['active'] ?? false));
                $inactiveCount = $totalModules - $activeCount;
            @endphp
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-cube class="h-8 w-8 text-blue-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Modules</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalModules }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-check-circle class="h-8 w-8 text-green-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Modules</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $activeCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-x-circle class="h-8 w-8 text-red-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Inactive Modules</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $inactiveCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if(empty($this->modules))
                <div class="p-12 text-center">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <x-heroicon-o-puzzle-piece class="h-12 w-12" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No modules found</h3>
                    <p class="text-gray-500">No modules are available in the modules directory.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($this->modules as $name => $module)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                    <x-heroicon-o-puzzle-piece class="h-6 w-6 text-blue-600" />
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $module['display_name'] ?? $name }}</div>
                                                <div class="text-sm text-gray-500">{{ $name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ Str::limit($module['description'] ?? '', 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            v{{ $module['version'] ?? '1.0.0' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($module['active'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <x-heroicon-s-check-circle class="w-4 h-4 mr-1" />
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <x-heroicon-s-x-circle class="w-4 h-4 mr-1" />
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $module['author'] ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button
                                                wire:click="toggleModule('{{ $name }}')"
                                                wire:confirm="Are you sure you want to {{ ($module['active'] ?? false) ? 'deactivate' : 'activate' }} the '{{ $module['display_name'] ?? $name }}' module?"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md {{ ($module['active'] ?? false) ? 'text-orange-700 bg-orange-100 hover:bg-orange-200' : 'text-green-700 bg-green-100 hover:bg-green-200' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                            >
                                                @if($module['active'] ?? false)
                                                    <x-heroicon-s-pause class="w-4 h-4 mr-1" />
                                                    Deactivate
                                                @else
                                                    <x-heroicon-s-play class="w-4 h-4 mr-1" />
                                                    Activate
                                                @endif
                                            </button>

                                            <button
                                                onclick="$dispatch('open-modal', { id: 'module-details-{{ $name }}' })"
                                                class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                            >
                                                <x-heroicon-s-eye class="w-4 h-4 mr-1" />
                                                Details
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Module Details Modals -->
        @foreach($this->modules as $name => $module)
            <x-filament::modal id="module-details-{{ $name }}" width="2xl">
                <x-slot name="heading">
                    {{ $module['display_name'] ?? $name }} - Module Details
                </x-slot>

                @include('filament.resources.module-resource.view-details', ['module' => $module])
            </x-filament::modal>
        @endforeach

        <!-- Help Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        About Modules
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>
                            Modules are self-contained packages that extend the functionality of your Sitewise installation. 
                            Each module can include its own routes, controllers, views, and other components. 
                            You can activate or deactivate modules as needed without affecting the core system.
                        </p>
                        <ul class="mt-2 list-disc list-inside">
                            <li>Modules are located in the <code>/modules</code> directory</li>
                            <li>Each module has its own <code>module.json</code> configuration file</li>
                            <li>Active modules are automatically registered with the application</li>
                            <li>Views are namespaced (e.g., <code>blog::index</code>)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
