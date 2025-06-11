# Sitewise Cache System

The Sitewise platform includes a comprehensive caching system designed to improve performance and provide efficient cache management through the admin panel.

## Features

### 🚀 Performance Caching
- **Site Data Caching**: Site information cached by domain and ID
- **Page Caching**: Individual pages and site page collections
- **Template Caching**: Template definitions and rendered Blade template outputs
- **Template Content Caching**: Dynamic content for template-based pages
- **Statistics Caching**: Site statistics with shorter TTL for real-time feel

### 🎛️ Admin Panel Management
- **Site-Specific Cache Management**: Dedicated FilamentPHP page for current site cache management
- **Site Cache Statistics**: Real-time cache metrics for the current site
- **Selective Clearing**: Clear specific cache types (pages, templates) for current site
- **Cache Warming**: Pre-populate cache for the current site
- **Site Performance Monitoring**: Site-specific cache usage and performance tracking

### 🔄 Automatic Cache Invalidation
- **Model Observers**: Automatic cache clearing when data changes
- **Smart Invalidation**: Only clears relevant cache keys
- **Multi-tenant Isolation**: Site-specific cache keys prevent cross-contamination

### 📊 Performance Monitoring
- **Dashboard Widget**: Cache performance metrics on admin dashboard
- **Detailed Statistics**: Memory usage, hit rates, key counts
- **Site-specific Metrics**: Per-site cache usage breakdown

## Configuration

Cache settings can be configured in `config/cache.php`:

```php
'sitewise' => [
    'default_ttl' => env('SITEWISE_CACHE_TTL', 3600),        // 1 hour
    'stats_ttl' => env('SITEWISE_STATS_TTL', 300),           // 5 minutes  
    'blade_template_ttl' => env('SITEWISE_BLADE_TTL', 86400), // 24 hours
    'enable_auto_warm' => env('SITEWISE_AUTO_WARM', true),
],
```

### Environment Variables

Add these to your `.env` file to customize cache behavior:

```env
# Cache driver (database, redis, file, etc.)
CACHE_STORE=database

# Sitewise cache TTL settings (in seconds)
SITEWISE_CACHE_TTL=3600      # Default cache TTL (1 hour)
SITEWISE_STATS_TTL=300       # Statistics cache TTL (5 minutes)
SITEWISE_BLADE_TTL=86400     # Blade template cache TTL (24 hours)
SITEWISE_AUTO_WARM=true      # Enable automatic cache warming
```

## Usage

### Admin Panel

Access site-specific cache management through the admin panel:

1. **Navigation**: Go to System → Cache Management
2. **View Site Statistics**: See cache metrics and usage for the current site
3. **Clear Site Cache**: Use action buttons to clear cache for the current site
4. **Warm Site Cache**: Pre-populate cache for the current site
5. **Selective Clearing**: Clear only pages or templates cache as needed

### Artisan Commands

Manage cache via command line:

```bash
# Show cache statistics
php artisan sitewise:cache stats

# Clear all cache
php artisan sitewise:cache clear

# Warm cache for all sites
php artisan sitewise:cache warm

# Clear cache for specific site
php artisan sitewise:cache clear-site --site=example.com
php artisan sitewise:cache clear-site --site=1 --type=pages

# Warm cache for specific site
php artisan sitewise:cache warm-site --site=example.com
```

### Programmatic Usage

Use the CacheService in your code:

```php
use App\Services\CacheService;

// Get cached site data
$site = CacheService::getSiteByDomain('example.com');

// Get cached page
$page = CacheService::getPage($siteId, 'about');

// Clear site cache
CacheService::clearSiteCache($siteId);

// Warm site cache
$warmed = CacheService::warmSiteCache($siteId);

// Get cache statistics
$stats = CacheService::getCacheStats();
```

## Cache Keys Structure

The system uses structured cache keys for organization:

- **Sites**: `site:{siteId}` or `site:0:domain:{domain}`
- **Pages**: `page:{siteId}:{slug}` or `page:{siteId}:all`
- **Templates**: `template:{siteId}:{templateId}`
- **Template Content**: `template_content:0:page:{pageId}`
- **Rendered Templates**: `blade_template:{siteId}:{templateId}:{contentHash}`
- **Statistics**: `site_stats:{siteId}`

### Blade Template Caching Strategy

The system uses a smart caching approach for Blade templates:

1. **Content-Aware Caching**: Cache keys include a hash of template content and variables
2. **Variable-Specific**: Different variable combinations create separate cache entries
3. **Template Version Tracking**: Cache automatically invalidates when templates are updated
4. **Automatic Invalidation**: Cache clears when template content or page content changes

This ensures that:
- Templates with different content render correctly
- Variables are properly interpolated on each request
- Cache stays fresh when content is updated
- Performance is optimized for repeated requests with same content

## Performance Tips

### 🎯 Optimization Strategies

1. **Use Redis in Production**: Better performance than database cache
2. **Monitor Hit Rates**: Aim for 80%+ cache hit rates
3. **Warm Cache After Clearing**: Maintain optimal response times
4. **Clear Selectively**: Only clear what's necessary
5. **Monitor Memory Usage**: Prevent cache overflow

### 📈 Best Practices

- **Development**: Use database cache for simplicity
- **Production**: Use Redis for better performance and features
- **Staging**: Mirror production cache configuration
- **Testing**: Use array cache to avoid persistence

### 🔧 Troubleshooting

**Low Hit Rates**:
- Check if cache is being cleared too frequently
- Verify TTL settings are appropriate
- Monitor for cache key conflicts

**High Memory Usage**:
- Reduce TTL for less critical data
- Implement cache size limits
- Consider using Redis with memory policies

**Slow Performance**:
- Warm cache after clearing
- Check cache driver performance
- Monitor database cache table size

## Architecture

### Cache Service Layer
- `CacheService`: Main service class with static methods
- Site-specific cache key generation
- Configurable TTL values
- Multi-driver support (Redis, Database, File)

### Model Observers
- `CacheObserver`: Handles automatic cache invalidation
- Registered for Site, Page, Template, TemplateContent models
- Smart invalidation based on relationships

### FilamentPHP Integration
- `CacheManagement`: Site-specific admin page for cache management
- `CachePerformanceWidget`: Site-focused dashboard performance metrics
- Site-scoped cache statistics and controls

### Artisan Commands
- `CacheManagementCommand`: CLI interface for cache operations
- Support for site-specific and type-specific operations
- Detailed statistics and usage information

## Site-Specific Cache Management

The cache management interface is designed to be **site-specific**, meaning:

### 🏢 **Current Site Focus**
- Admin panel shows only the current site's cache data
- All cache operations are scoped to the current site
- No global cache statistics or cross-site data exposure
- Site-specific cache keys and usage metrics

### 🔒 **Multi-Tenant Security**
- Each site can only manage its own cache
- No access to other sites' cached data
- Domain-based isolation ensures data privacy
- Site administrators see only relevant information

### 🎯 **Benefits of Site-Specific Approach**
- **Simplified Interface**: Users see only what's relevant to their site
- **Enhanced Security**: No cross-site data exposure
- **Better Performance**: Focused metrics and operations
- **Clearer Actions**: All operations clearly scoped to current site
- **Reduced Confusion**: No global settings that might affect other sites

### 📊 **What You'll See**
- Cache breakdown by type (pages, templates, content) for your site
- Site-specific cache key counts and usage
- Actions that only affect your current site
- Command examples tailored to your site's domain

This approach ensures that each site administrator has a clear, secure, and focused cache management experience without the complexity of system-wide cache administration.

This cache system provides a robust foundation for high-performance multi-tenant websites while maintaining ease of use, security, and site-specific management capabilities.
