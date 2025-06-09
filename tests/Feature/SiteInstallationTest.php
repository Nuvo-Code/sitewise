<?php

use App\Models\Site;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create a test user for authentication
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
});

test('new domain creates site that needs setup', function () {
    // Simulate a request from a new domain
    $response = $this->withHeaders(['Host' => 'new-domain.test'])
                     ->get('/');
    
    // Check that a site was created
    $site = Site::where('domain', 'new-domain.test')->first();
    expect($site)->not->toBeNull();
    expect($site->needsSetup())->toBeTrue();
    expect($site->name)->toBe('New domain test');
});

test('unauthenticated user cannot access installation page', function () {
    // Create a site that needs setup
    $site = Site::create([
        'domain' => 'test-install.local',
        'name' => 'Test Install Site',
        'active' => true,
        'is_setup_complete' => false,
    ]);
    
    app()->instance('site', $site);
    
    // Try to access installation page without authentication
    $response = $this->withHeaders(['Host' => 'test-install.local'])
                     ->get('/admin/site-installation');
    
    // Should be redirected to login
    $response->assertRedirect();
});

test('authenticated user with incomplete site setup is redirected to installation', function () {
    // Create a site that needs setup
    $site = Site::create([
        'domain' => 'test-install.local',
        'name' => 'Test Install Site',
        'active' => true,
        'is_setup_complete' => false,
    ]);
    
    app()->instance('site', $site);
    
    // Access admin dashboard as authenticated user
    $response = $this->actingAs($this->user)
                     ->withHeaders(['Host' => 'test-install.local'])
                     ->get('/admin');
    
    // Should be redirected to installation page
    $response->assertRedirect('/admin/site-installation');
});

test('authenticated user with complete site setup can access admin', function () {
    // Create a site that is setup complete
    $site = Site::create([
        'domain' => 'test-complete.local',
        'name' => 'Test Complete Site',
        'active' => true,
        'is_setup_complete' => true,
    ]);
    
    app()->instance('site', $site);
    
    // Access admin dashboard as authenticated user
    $response = $this->actingAs($this->user)
                     ->withHeaders(['Host' => 'test-complete.local'])
                     ->get('/admin');
    
    // Should be able to access admin
    $response->assertStatus(200);
});

test('installation page can be accessed for incomplete site', function () {
    // Create a site that needs setup
    $site = Site::create([
        'domain' => 'test-install.local',
        'name' => 'Test Install Site',
        'active' => true,
        'is_setup_complete' => false,
    ]);
    
    app()->instance('site', $site);
    
    // Access installation page as authenticated user
    $response = $this->actingAs($this->user)
                     ->withHeaders(['Host' => 'test-install.local'])
                     ->get('/admin/site-installation');
    
    // Should be able to access installation page
    $response->assertStatus(200);
    $response->assertSee('Complete Site Setup');
    $response->assertSee('test-install.local');
});

test('site can be marked as setup complete', function () {
    // Create a site that needs setup
    $site = Site::create([
        'domain' => 'test-install.local',
        'name' => 'Test Install Site',
        'active' => true,
        'is_setup_complete' => false,
    ]);
    
    expect($site->needsSetup())->toBeTrue();
    
    // Mark as setup complete
    $site->markSetupComplete();
    
    expect($site->fresh()->needsSetup())->toBeFalse();
    expect($site->fresh()->is_setup_complete)->toBeTrue();
});
