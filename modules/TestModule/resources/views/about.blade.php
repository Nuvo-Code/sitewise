@extends('TestModule::layout')

@section('title', 'About')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('testmodule.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <svg class="mr-2 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
            </svg>
            Back to TestModule
        </a>
    </div>

    <!-- About Content -->
    <div class="bg-white shadow rounded-borders overflow-hidden">
        <div class="px-6 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">About TestModule</h1>
            
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 leading-relaxed mb-6">
                    This is the TestModule module for the Sitewise platform. This module demonstrates 
                    the modular architecture capabilities of Sitewise.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Module Information</h2>
                <div class="bg-gray-50 rounded-borders p-4 mb-6">
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>Namespace:</strong> Modules\TestModule</li>
                        <li><strong>Routes Prefix:</strong> /testmodule</li>
                        <li><strong>Views Namespace:</strong> testmodule::</li>
                        <li><strong>Service Provider:</strong> TestModuleServiceProvider</li>
                        <li><strong>Auto-discovery:</strong> Enabled</li>
                    </ul>
                </div>

                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Customization</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    You can customize this module by editing the files in the modules/TestModule directory:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                    <li>Controllers in Controllers/ directory</li>
                    <li>Views in resources/views/ directory</li>
                    <li>Routes in routes/web.php</li>
                    <li>Models in Models/ directory</li>
                    <li>Migrations in database/migrations/</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection