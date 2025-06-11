<?php

namespace Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display the blog index page
     */
    public function index(): View
    {
        $posts = [
            [
                'id' => 1,
                'title' => 'Welcome to Our Blog',
                'excerpt' => 'This is the first post in our blog module.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'created_at' => now()->subDays(5),
            ],
            [
                'id' => 2,
                'title' => 'Laravel Modules are Awesome',
                'excerpt' => 'Learn how to build modular Laravel applications.',
                'content' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'created_at' => now()->subDays(3),
            ],
            [
                'id' => 3,
                'title' => 'Building Scalable Applications',
                'excerpt' => 'Tips and tricks for building scalable web applications.',
                'content' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                'created_at' => now()->subDay(),
            ],
        ];

        return view('blog::index', compact('posts'));
    }

    /**
     * Display a specific blog post
     */
    public function show(int $id): View
    {
        // In a real application, you would fetch this from the database
        $posts = [
            1 => [
                'id' => 1,
                'title' => 'Welcome to Our Blog',
                'content' => 'This is the full content of our first blog post. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'created_at' => now()->subDays(5),
            ],
            2 => [
                'id' => 2,
                'title' => 'Laravel Modules are Awesome',
                'content' => 'Learn how to build modular Laravel applications with this comprehensive guide. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                'created_at' => now()->subDays(3),
            ],
            3 => [
                'id' => 3,
                'title' => 'Building Scalable Applications',
                'content' => 'Tips and tricks for building scalable web applications that can handle millions of users. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'created_at' => now()->subDay(),
            ],
        ];

        $post = $posts[$id] ?? abort(404);

        return view('blog::show', compact('post'));
    }

    /**
     * Display the about page
     */
    public function about(): View
    {
        return view('blog::about');
    }
}
