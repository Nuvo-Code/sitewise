@extends('blog::layout')

@section('title', 'Blog Posts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-borders p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Latest Blog Posts</h1>
        <p class="text-gray-600">Welcome to our blog! Here you'll find the latest updates and articles.</p>
    </div>

    <!-- Posts Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($posts as $post)
        <article class="bg-white shadow rounded-borders overflow-hidden hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-white font-medium">{{ substr($post['title'], 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Blog Author</p>
                        <p class="text-sm text-gray-500">{{ $post['created_at']->format('M j, Y') }}</p>
                    </div>
                </div>
                
                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                    <a href="{{ route('blog.show', $post['id']) }}" class="hover:text-blue-600 transition-colors">
                        {{ $post['title'] }}
                    </a>
                </h2>
                
                <p class="text-gray-600 mb-4">{{ $post['excerpt'] }}</p>
                
                <div class="flex items-center justify-between">
                    <a href="{{ route('blog.show', $post['id']) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                        Read More
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    <span class="text-sm text-gray-500">{{ $post['created_at']->diffForHumans() }}</span>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    @if(empty($posts))
    <div class="bg-white shadow rounded-borders p-12 text-center">
        <div class="mx-auto h-12 w-12 text-gray-400">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No posts yet</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating your first blog post.</p>
    </div>
    @endif
</div>
@endsection
