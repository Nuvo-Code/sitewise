<?php

namespace Tests\Feature;

use App\Services\ModuleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleSystemTest extends TestCase
{
    /**
     * Test that the module manager can list modules
     */
    public function test_module_manager_can_list_modules(): void
    {
        $moduleManager = app(ModuleManager::class);
        $modules = $moduleManager->getAllModules();
        
        $this->assertIsArray($modules);
        $this->assertArrayHasKey('Blog', $modules);
        $this->assertArrayHasKey('TestModule', $modules);
    }

    /**
     * Test that active modules are properly identified
     */
    public function test_module_manager_can_identify_active_modules(): void
    {
        $moduleManager = app(ModuleManager::class);
        
        // Blog should be active
        $this->assertTrue($moduleManager->isModuleActive('Blog'));
        
        // TestModule should be active (we activated it earlier)
        $this->assertTrue($moduleManager->isModuleActive('TestModule'));
    }

    /**
     * Test that blog module routes are accessible
     */
    public function test_blog_module_routes_are_accessible(): void
    {
        // Test blog index
        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSee('Latest Blog Posts');
        
        // Test blog post
        $response = $this->get('/blog/post/1');
        $response->assertStatus(200);
        $response->assertSee('Welcome to Our Blog');
        
        // Test blog about
        $response = $this->get('/blog/about');
        $response->assertStatus(200);
        $response->assertSee('About This Blog');
    }

    /**
     * Test that test module routes are accessible
     */
    public function test_test_module_routes_are_accessible(): void
    {
        // Test module index
        $response = $this->get('/testmodule');
        $response->assertStatus(200);
        $response->assertSee('Welcome to TestModule');
        
        // Test module about
        $response = $this->get('/testmodule/about');
        $response->assertStatus(200);
        $response->assertSee('About TestModule');
    }

    /**
     * Test module activation and deactivation
     */
    public function test_module_activation_and_deactivation(): void
    {
        $moduleManager = app(ModuleManager::class);
        
        // Create a test module for this test
        $testModulePath = base_path('modules/TempTestModule');
        if (!file_exists($testModulePath)) {
            mkdir($testModulePath, 0755, true);
            file_put_contents($testModulePath . '/module.json', json_encode([
                'name' => 'TempTestModule',
                'display_name' => 'Temporary Test Module',
                'description' => 'A temporary module for testing',
                'version' => '1.0.0',
                'author' => 'Test',
                'active' => false,
            ], JSON_PRETTY_PRINT));
        }
        
        // Clear cache to pick up the new module
        cache()->forget('modules.all');
        
        // Test activation
        $this->assertFalse($moduleManager->isModuleActive('TempTestModule'));
        $result = $moduleManager->activateModule('TempTestModule');
        $this->assertTrue($result);
        $this->assertTrue($moduleManager->isModuleActive('TempTestModule'));
        
        // Test deactivation
        $result = $moduleManager->deactivateModule('TempTestModule');
        $this->assertTrue($result);
        $this->assertFalse($moduleManager->isModuleActive('TempTestModule'));
        
        // Clean up
        if (file_exists($testModulePath)) {
            unlink($testModulePath . '/module.json');
            rmdir($testModulePath);
        }
        cache()->forget('modules.all');
    }

    /**
     * Test that module views use correct namespaces
     */
    public function test_module_views_use_correct_namespaces(): void
    {
        // Test that blog views are properly namespaced
        $response = $this->get('/blog');
        $response->assertStatus(200);
        
        // The view should contain elements that indicate it's using the blog namespace
        $response->assertSee('Blog Module'); // From the layout
        $response->assertSee('Latest Blog Posts'); // From the index view
    }
}
