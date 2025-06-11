@extends('blog::layout')

@section('title', 'About')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('blog.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <svg class="mr-2 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
            </svg>
            Back to Blog
        </a>
    </div>

    <!-- About Content -->
    <div class="bg-white shadow rounded-borders overflow-hidden">
        <div class="px-6 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">About This Blog</h1>
            
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 leading-relaxed mb-6">
                    Welcome to our blog module! This is a demonstration of how modules work within the Sitewise platform. 
                    This blog module is completely self-contained and can be easily activated or deactivated through the admin panel.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Module Features</h2>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                    <li>Self-contained module structure</li>
                    <li>Namespaced views (blog::)</li>
                    <li>Dedicated routes with prefix</li>
                    <li>Modular service provider</li>
                    <li>Easy activation/deactivation</li>
                    <li>Responsive design with Tailwind CSS</li>
                </ul>

                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Technical Details</h2>
                <div class="bg-gray-50 rounded-borders p-4 mb-6">
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>Namespace:</strong> Modules\Blog</li>
                        <li><strong>Routes Prefix:</strong> /blog</li>
                        <li><strong>Views Namespace:</strong> blog::</li>
                        <li><strong>Service Provider:</strong> BlogServiceProvider</li>
                        <li><strong>Auto-discovery:</strong> Enabled</li>
                    </ul>
                </div>

                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Module Management</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    This module can be managed through the Sitewise admin panel. Administrators can:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                    <li>View all available modules</li>
                    <li>Activate or deactivate modules</li>
                    <li>View module information and metadata</li>
                    <li>Install new modules</li>
                </ul>

                <div class="bg-blue-50 border border-blue-200 rounded-borders p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Module Information
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>This is a demonstration module showing how the modular architecture works in Sitewise. In a real application, you would extend this with database models, migrations, and more complex functionality.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
