@extends('blog::layout')

@section('title', $post['title'])

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

    <!-- Article -->
    <article class="bg-white shadow rounded-borders overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-8 border-b border-gray-200">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center">
                        <span class="text-white font-medium text-lg">{{ substr($post['title'], 0, 1) }}</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Blog Author</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <time datetime="{{ $post['created_at']->toISOString() }}">
                            {{ $post['created_at']->format('F j, Y') }}
                        </time>
                        <span class="mx-2">•</span>
                        <span>{{ $post['created_at']->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-900 leading-tight">{{ $post['title'] }}</h1>
        </div>

        <!-- Content -->
        <div class="px-6 py-8">
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 leading-relaxed text-lg">{{ $post['content'] }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Blog Post
                    </span>
                    <span class="text-sm text-gray-500">
                        Published {{ $post['created_at']->format('M j, Y') }}
                    </span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"></path>
                        </svg>
                        Share
                    </button>
                </div>
            </div>
        </div>
    </article>

    <!-- Related Posts or Navigation -->
    <div class="bg-white shadow rounded-borders p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">More from the Blog</h3>
        <div class="flex items-center justify-between">
            <a href="{{ route('blog.index') }}" 
               class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                ← View all posts
            </a>
            <a href="{{ route('blog.about') }}" 
               class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                About this blog →
            </a>
        </div>
    </div>
</div>
@endsection
