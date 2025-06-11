<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module}
                           {--author= : The author of the module}
                           {--description= : The description of the module}
                           {--module-version=1.0.0 : The version of the module}';

    protected $description = 'Create a new module with basic structure';

    public function handle(): int
    {
        $name = $this->argument('name');
        $author = $this->option('author') ?? 'Sitewise Team';
        $description = $this->option('description') ?? "A custom module for {$name}";
        $version = $this->option('module-version');

        // Validate module name
        if (!preg_match('/^[A-Z][a-zA-Z0-9]*$/', $name)) {
            $this->error('Module name must start with a capital letter and contain only alphanumeric characters.');
            return 1;
        }

        $moduleManager = app(ModuleManager::class);
        $modulePath = base_path("modules/{$name}");

        // Check if module already exists
        if (File::exists($modulePath)) {
            $this->error("Module '{$name}' already exists!");
            return 1;
        }

        $this->info("Creating module: {$name}");

        // Create module directory structure
        $this->createDirectoryStructure($modulePath);

        // Create module.json
        $this->createModuleJson($modulePath, $name, $author, $description, $version);

        // Create service provider
        $this->createServiceProvider($modulePath, $name);

        // Create controller
        $this->createController($modulePath, $name);

        // Create routes
        $this->createRoutes($modulePath, $name);

        // Create views
        $this->createViews($modulePath, $name);

        $this->info("Module '{$name}' created successfully!");
        $this->line("Location: {$modulePath}");
        $this->line("To activate the module, use the admin panel or run:");
        $this->line("  php artisan module:activate {$name}");

        return 0;
    }

    private function createDirectoryStructure(string $modulePath): void
    {
        $directories = [
            'Controllers',
            'Models',
            'resources/views',
            'routes',
            'database/migrations',
            'config',
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$modulePath}/{$directory}", 0755, true);
        }
    }

    private function createModuleJson(string $modulePath, string $name, string $author, string $description, string $version): void
    {
        $moduleData = [
            'name' => $name,
            'display_name' => Str::title(Str::snake($name, ' ')),
            'description' => $description,
            'version' => $version,
            'author' => $author,
            'active' => false,
            'dependencies' => [],
            'requirements' => [
                'php' => '^8.2',
                'laravel' => '^11.0|^12.0'
            ],
            'providers' => [
                "Modules\\{$name}\\{$name}ServiceProvider"
            ],
            'aliases' => [],
            'files' => [],
            'tags' => ['custom']
        ];

        File::put("{$modulePath}/module.json", json_encode($moduleData, JSON_PRETTY_PRINT));
    }

    private function createServiceProvider(string $modulePath, string $name): void
    {
        $content = "<?php

namespace Modules\\{$name};

use App\Services\BaseModuleServiceProvider;

class {$name}ServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Register module services
     */
    protected function registerServices(): void
    {
        // Register any module-specific services here
        // Example: \$this->app->bind('{$name}Service', {$name}Service::class);
    }

    /**
     * Boot module
     */
    public function boot(): void
    {
        parent::boot();
        
        // Any additional boot logic for the {$name} module
        // Example: Event listeners, view composers, etc.
    }
}";

        File::put("{$modulePath}/{$name}ServiceProvider.php", $content);
    }

    private function createController(string $modulePath, string $name): void
    {
        $controllerName = "{$name}Controller";
        $viewPrefix = strtolower($name);

        $content = "<?php

namespace Modules\\{$name}\\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class {$controllerName} extends Controller
{
    /**
     * Display the {$name} index page
     */
    public function index(): View
    {
        return view('{$viewPrefix}::index');
    }

    /**
     * Display the about page
     */
    public function about(): View
    {
        return view('{$viewPrefix}::about');
    }
}";

        File::put("{$modulePath}/Controllers/{$controllerName}.php", $content);
    }

    private function createRoutes(string $modulePath, string $name): void
    {
        $controllerName = "{$name}Controller";

        $content = "<?php

use Illuminate\Support\Facades\Route;
use Modules\\{$name}\\Controllers\\{$controllerName};

/*
|--------------------------------------------------------------------------
| {$name} Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the {$name} module. These
| routes are loaded by the {$name}ServiceProvider within a group which
| contains the \"web\" middleware group and is prefixed with \"" . strtolower($name) . "\".
|
*/

Route::get('/', [{$controllerName}::class, 'index'])->name('index');
Route::get('/about', [{$controllerName}::class, 'about'])->name('about');";

        File::put("{$modulePath}/routes/web.php", $content);
    }

    private function createViews(string $modulePath, string $name): void
    {
        $viewsPath = "{$modulePath}/resources/views";
        $moduleLower = strtolower($name);

        // Layout
        $layoutContent = $this->getLayoutTemplate($name);
        File::put("{$viewsPath}/layout.blade.php", $layoutContent);

        // Index view
        $indexContent = $this->getIndexTemplate($name);
        File::put("{$viewsPath}/index.blade.php", $indexContent);

        // About view
        $aboutContent = $this->getAboutTemplate($name);
        File::put("{$viewsPath}/about.blade.php", $aboutContent);
    }

    private function getLayoutTemplate(string $name): string
    {
        $moduleLower = strtolower($name);
        
        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>@yield('title', '{$name}') - {{ config('app.name') }}</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <style>
        .rounded-borders {
            border-radius: 0.75rem;
        }
    </style>
</head>
<body class=\"bg-gray-50 min-h-screen\">
    <!-- Navigation -->
    <nav class=\"bg-white shadow-sm border-b rounded-borders mx-4 mt-4\">
        <div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8\">
            <div class=\"flex justify-between h-16\">
                <div class=\"flex items-center\">
                    <h1 class=\"text-xl font-semibold text-gray-900\">{$name} Module</h1>
                </div>
                <div class=\"flex items-center space-x-4\">
                    <a href=\"{{ route('{$moduleLower}.index') }}\" class=\"text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium\">
                        Home
                    </a>
                    <a href=\"{{ route('{$moduleLower}.about') }}\" class=\"text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium\">
                        About
                    </a>
                    <a href=\"/\" class=\"bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700\">
                        Back to Site
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class=\"max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8\">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class=\"bg-white border-t rounded-borders mx-4 mb-4 mt-8\">
        <div class=\"max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8\">
            <p class=\"text-center text-gray-500 text-sm\">
                &copy; {{ date('Y') }} {$name} Module. Part of {{ config('app.name') }}.
            </p>
        </div>
    </footer>
</body>
</html>";
    }

    private function getIndexTemplate(string $name): string
    {
        return "@extends('{$name}::layout')

@section('title', '{$name}')

@section('content')
<div class=\"space-y-6\">
    <!-- Header -->
    <div class=\"bg-white shadow rounded-borders p-6\">
        <h1 class=\"text-3xl font-bold text-gray-900 mb-2\">Welcome to {$name}</h1>
        <p class=\"text-gray-600\">This is the main page for the {$name} module.</p>
    </div>

    <!-- Content -->
    <div class=\"bg-white shadow rounded-borders p-6\">
        <h2 class=\"text-xl font-semibold text-gray-900 mb-4\">Module Features</h2>
        <ul class=\"list-disc list-inside text-gray-700 space-y-2\">
            <li>Self-contained module structure</li>
            <li>Namespaced views ({$name}::)</li>
            <li>Dedicated routes with prefix</li>
            <li>Modular service provider</li>
            <li>Easy activation/deactivation</li>
            <li>Responsive design with Tailwind CSS</li>
        </ul>
    </div>

    <!-- Call to Action -->
    <div class=\"bg-blue-50 border border-blue-200 rounded-borders p-6\">
        <div class=\"flex\">
            <div class=\"flex-shrink-0\">
                <svg class=\"h-5 w-5 text-blue-400\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
                    <path fill-rule=\"evenodd\" d=\"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\" clip-rule=\"evenodd\"></path>
                </svg>
            </div>
            <div class=\"ml-3\">
                <h3 class=\"text-sm font-medium text-blue-800\">
                    Get Started
                </h3>
                <div class=\"mt-2 text-sm text-blue-700\">
                    <p>Start customizing this module by editing the files in the modules/{$name} directory.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection";
    }

    private function getAboutTemplate(string $name): string
    {
        $moduleLower = strtolower($name);
        
        return "@extends('{$name}::layout')

@section('title', 'About')

@section('content')
<div class=\"space-y-6\">
    <!-- Back Button -->
    <div>
        <a href=\"{{ route('{$moduleLower}.index') }}\" 
           class=\"inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors\">
            <svg class=\"mr-2 -ml-1 w-4 h-4\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
                <path fill-rule=\"evenodd\" d=\"M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z\" clip-rule=\"evenodd\"></path>
            </svg>
            Back to {$name}
        </a>
    </div>

    <!-- About Content -->
    <div class=\"bg-white shadow rounded-borders overflow-hidden\">
        <div class=\"px-6 py-8\">
            <h1 class=\"text-3xl font-bold text-gray-900 mb-6\">About {$name}</h1>
            
            <div class=\"prose prose-lg max-w-none\">
                <p class=\"text-gray-700 leading-relaxed mb-6\">
                    This is the {$name} module for the Sitewise platform. This module demonstrates 
                    the modular architecture capabilities of Sitewise.
                </p>

                <h2 class=\"text-2xl font-semibold text-gray-900 mb-4\">Module Information</h2>
                <div class=\"bg-gray-50 rounded-borders p-4 mb-6\">
                    <ul class=\"text-sm text-gray-600 space-y-1\">
                        <li><strong>Namespace:</strong> Modules\\{$name}</li>
                        <li><strong>Routes Prefix:</strong> /{$moduleLower}</li>
                        <li><strong>Views Namespace:</strong> {$moduleLower}::</li>
                        <li><strong>Service Provider:</strong> {$name}ServiceProvider</li>
                        <li><strong>Auto-discovery:</strong> Enabled</li>
                    </ul>
                </div>

                <h2 class=\"text-2xl font-semibold text-gray-900 mb-4\">Customization</h2>
                <p class=\"text-gray-700 leading-relaxed mb-4\">
                    You can customize this module by editing the files in the modules/{$name} directory:
                </p>
                <ul class=\"list-disc list-inside text-gray-700 space-y-2 mb-6\">
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
@endsection";

        File::put("{$modulePath}/resources/views/about.blade.php", $aboutContent);
    }
}
