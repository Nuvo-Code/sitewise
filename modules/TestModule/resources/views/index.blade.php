@extends('TestModule::layout')

@section('title', 'TestModule')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-borders p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome to TestModule</h1>
        <p class="text-gray-600">This is the main page for the TestModule module.</p>
    </div>

    <!-- Content -->
    <div class="bg-white shadow rounded-borders p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Module Features</h2>
        <ul class="list-disc list-inside text-gray-700 space-y-2">
            <li>Self-contained module structure</li>
            <li>Namespaced views (TestModule::)</li>
            <li>Dedicated routes with prefix</li>
            <li>Modular service provider</li>
            <li>Easy activation/deactivation</li>
            <li>Responsive design with Tailwind CSS</li>
        </ul>
    </div>

    <!-- Call to Action -->
    <div class="bg-blue-50 border border-blue-200 rounded-borders p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Get Started
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Start customizing this module by editing the files in the modules/TestModule directory.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection