# Sitewise Module System

The Sitewise platform includes a powerful modular architecture that allows you to extend functionality through self-contained modules.

## Overview

The module system provides:
- **Self-contained packages** located in `/modules` directory
- **Auto-discovery and registration** of active modules
- **Namespaced views** (e.g., `blog::index`)
- **Dedicated routes** with module prefixes
- **FilamentPHP integration** for module management
- **PSR-4 autoloading** with `Modules\{ModuleName}` namespace

## Module Structure

Each module follows this directory structure:

```
modules/
└── ModuleName/
    ├── module.json                 # Module metadata
    ├── ModuleNameServiceProvider.php # Service provider
    ├── Controllers/                # Controllers
    ├── Models/                     # Models (optional)
    ├── resources/
    │   └── views/                  # Blade templates
    ├── routes/
    │   ├── web.php                 # Web routes
    │   └── api.php                 # API routes (optional)
    └── database/
        └── migrations/             # Migrations (optional)
```

## Module Configuration (module.json)

```json
{
    "name": "ModuleName",
    "display_name": "Human Readable Name",
    "description": "Module description",
    "version": "1.0.0",
    "author": "Author Name",
    "active": false,
    "dependencies": [],
    "requirements": {
        "php": "^8.2",
        "laravel": "^11.0|^12.0"
    },
    "providers": [
        "Modules\\ModuleName\\ModuleNameServiceProvider"
    ],
    "tags": ["tag1", "tag2"]
}
```

## Creating a New Module

### 1. Create Module Directory Structure

```bash
mkdir -p modules/YourModule/{Controllers,Models,resources/views,routes,database/migrations}
```

### 2. Create module.json

```json
{
    "name": "YourModule",
    "display_name": "Your Module",
    "description": "Description of your module",
    "version": "1.0.0",
    "author": "Your Name",
    "active": false,
    "dependencies": [],
    "requirements": {
        "php": "^8.2",
        "laravel": "^11.0|^12.0"
    },
    "providers": [
        "Modules\\YourModule\\YourModuleServiceProvider"
    ],
    "tags": ["custom"]
}
```

### 3. Create Service Provider

```php
<?php

namespace Modules\YourModule;

use App\Services\BaseModuleServiceProvider;

class YourModuleServiceProvider extends BaseModuleServiceProvider
{
    protected function registerServices(): void
    {
        // Register module-specific services
    }

    public function boot(): void
    {
        parent::boot();
        // Additional boot logic
    }
}
```

### 4. Create Routes

**routes/web.php:**
```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\YourModule\Controllers\YourModuleController;

Route::get('/', [YourModuleController::class, 'index'])->name('index');
```

### 5. Create Controller

```php
<?php

namespace Modules\YourModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class YourModuleController extends Controller
{
    public function index(): View
    {
        return view('yourmodule::index');
    }
}
```

### 6. Create Views

**resources/views/index.blade.php:**
```blade
<h1>Your Module</h1>
<p>Welcome to your custom module!</p>
```

## Module Management

### Via FilamentPHP Admin Panel

1. Navigate to **Admin Panel > Modules**
2. View all available modules
3. Activate/deactivate modules
4. View module details and metadata

### Via Code

```php
use App\Services\ModuleManager;

$moduleManager = app(ModuleManager::class);

// Get all modules
$allModules = $moduleManager->getAllModules();

// Get active modules
$activeModules = $moduleManager->getActiveModules();

// Activate a module
$moduleManager->activateModule('ModuleName');

// Deactivate a module
$moduleManager->deactivateModule('ModuleName');

// Check if module is active
$isActive = $moduleManager->isModuleActive('ModuleName');
```

## Module Features

### Automatic Route Registration

- Web routes are prefixed with module name (e.g., `/blog/`)
- Route names are prefixed (e.g., `blog.index`)
- API routes are prefixed with `/api/modulename/`

### Namespaced Views

Views are automatically namespaced:
```php
// In controller
return view('modulename::viewname');

// In Blade templates
@extends('modulename::layout')
@include('modulename::partial')
```

### Auto-loading

Modules use PSR-4 autoloading with the `Modules\` namespace:
```php
use Modules\Blog\Controllers\BlogController;
use Modules\Blog\Models\Post;
```

### Service Provider Features

The `BaseModuleServiceProvider` automatically handles:
- Route registration
- View namespace registration
- Migration loading
- Translation loading
- Configuration merging

## Example: Blog Module

The included Blog module demonstrates:
- Complete module structure
- Namespaced views with layout
- Multiple routes and controllers
- Responsive design with Tailwind CSS
- Module activation/deactivation

### Accessing the Blog Module

Once activated, the blog module is available at:
- `/blog/` - Blog index
- `/blog/post/{id}` - Individual post
- `/blog/about` - About page

## Best Practices

1. **Follow naming conventions**: Use PascalCase for module names
2. **Keep modules self-contained**: Avoid dependencies on other modules
3. **Use proper namespacing**: Follow PSR-4 standards
4. **Document your modules**: Include clear descriptions and requirements
5. **Test thoroughly**: Ensure modules work independently
6. **Version your modules**: Use semantic versioning

## Troubleshooting

### Module Not Loading

1. Check `module.json` syntax
2. Verify service provider class name matches file name
3. Run `composer dump-autoload`
4. Clear application cache: `php artisan cache:clear`

### Routes Not Working

1. Ensure module is activated
2. Check route file syntax
3. Clear route cache: `php artisan route:clear`
4. Verify controller namespace

### Views Not Found

1. Check view namespace usage
2. Verify view files exist in `resources/views/`
3. Clear view cache: `php artisan view:clear`

## Sail Commands

When using Laravel Sail:

```bash
# Dump autoload
./vendor/bin/sail composer dump-autoload

# Clear caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan config:clear

# List routes
./vendor/bin/sail artisan route:list
```
