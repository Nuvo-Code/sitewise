# Sitewise Installation Screen

## Overview

The Sitewise installation screen is designed to handle the scenario where multiple domains (like `laravel.test`, `localhost`, `127.0.0.1`) redirect to the same Sitewise instance. When a user accesses the admin panel (`/admin`) for a domain that doesn't exist in the sites table, they will be guided through a setup process to configure their site.

## How It Works

### 1. Domain Resolution
- When a request comes in, the `ResolveSiteMiddleware` checks if a site exists for the current domain
- If no site exists, it automatically creates one with minimal information and marks it as `is_setup_complete = false`
- The site is bound to the application container for use throughout the request

### 2. Authentication
- Users must log in to access the admin panel using the standard Filament authentication
- Default credentials for testing:
  - Email: `admin@sitewise.local`
  - Password: `password`

### 3. Setup Requirement Check
- After authentication, the `RequireSiteSetupMiddleware` checks if the current site needs setup
- If `is_setup_complete = false`, users are redirected to the installation page
- The middleware skips this check for:
  - The installation page itself (to avoid redirect loops)
  - Non-admin routes
  - Livewire requests
  - API routes and assets

### 4. Installation Form
- The installation page (`/admin/site-installation`) presents a user-friendly form
- Form sections include:
  - **General Information**: Site name, description, tagline
  - **Contact Information**: Email, phone, address (collapsible)
  - **Basic Appearance**: Primary color, logo URL (collapsible)
- The form is pre-populated with the auto-generated site data

### 5. Completion
- When the form is submitted, the site information is updated
- The site is marked as `is_setup_complete = true`
- Users are redirected to the main admin dashboard

## Files Created/Modified

### New Files
- `app/Filament/Pages/SiteInstallation.php` - Installation page component
- `resources/views/filament/pages/site-installation.blade.php` - Installation page view
- `app/Http/Middleware/RequireSiteSetupMiddleware.php` - Middleware to enforce setup
- `database/migrations/2025_06_09_091511_add_is_setup_complete_to_sites_table.php` - Database migration

### Modified Files
- `app/Models/Site.php` - Added setup completion tracking
- `app/Providers/Filament/AdminPanelProvider.php` - Added authentication and middleware
- `database/migrations/2025_06_08_082615_create_sites_table.php` - Added setup completion field
- `database/seeders/DatabaseSeeder.php` - Added default admin user
- `database/seeders/SitewiseSeeder.php` - Mark demo site as setup complete

## Testing the Installation Screen

1. **Setup Database**: Run migrations to add the new field
   ```bash
   php artisan migrate
   ```

2. **Create Admin User**: Run the seeder to create a default admin user
   ```bash
   php artisan db:seed
   ```

3. **Access New Domain**: Visit a domain that doesn't exist in your sites table:
   - `http://127.0.0.1/admin`
   - `http://test.local/admin` (if configured)
   - Any other domain pointing to your Sitewise instance

4. **Login**: Use the admin credentials to log in

5. **Complete Setup**: You'll be redirected to the installation screen where you can configure your site

6. **Access Admin**: After completing setup, you'll have full access to the admin panel

## Customization

### Adding More Fields
To add more fields to the installation form, modify the `form()` method in `app/Filament/Pages/SiteInstallation.php`.

### Changing Required Fields
Modify the validation rules and required fields in the installation form schema.

### Skipping Installation
To mark a site as setup complete programmatically:
```php
$site = Site::findByDomain('example.com');
$site->markSetupComplete();
```

### Custom Redirect Logic
Modify the `RequireSiteSetupMiddleware` to change when and where users are redirected.

## Security Considerations

- The installation screen only appears for sites that need setup
- Authentication is required before accessing any admin functionality
- The middleware prevents access to other admin pages until setup is complete
- Existing sites are automatically marked as setup complete during migration

## Benefits

1. **Seamless Onboarding**: New domains get a guided setup experience
2. **Prevents Incomplete Sites**: Ensures all sites have proper configuration
3. **User-Friendly**: Clear, step-by-step process for site configuration
4. **Flexible**: Easy to customize and extend for specific needs
5. **Secure**: Requires authentication and proper authorization
