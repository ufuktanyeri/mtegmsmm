# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MTEGM SMM Portal - A multi-tenant strategic management system built with PHP MVC architecture for Turkish Ministry of Education. The system manages strategic planning, indicators, objectives, and actions across multiple educational institutions (COVE).

**Key Features:**
- Multi-tenant architecture with organization isolation via `cove_id`
- Role-based access control (SuperAdmin, Coordinator, Admin, User)
- Strategic planning tools (objectives, indicators, actions)
- Document and regulation management
- Task management system (currently being migrated)
- PHP 5.5+ compatibility maintained alongside PHP 8.2 support

## Architecture

### MVC Structure
- **Router:** `app/router.php` handles URL routing with pattern `index.php?url=controller/method`
- **Controllers:** Extend `BaseController` with unified view rendering through `UnifiedViewService`
- **Views:** Located in `app/views/`, rendered with layout support (header, navbar, footer)
- **Models:** Database models in `app/models/` using PDO with prepared statements
- **Entry Point:** `wwwroot/index.php` → loads config → starts session → routes through `app/router.php`

### Database Architecture
- Multi-tenant system with `cove_id` for organization isolation
- Role-based permissions: SuperAdmin, Admin, User roles
- Entities: Users, Objectives, Indicators, Actions, Documents, Regulations
- Unified view system for cross-COVE data aggregation

## Development Commands

### Running the Application
```bash
# Start local development server (port 8000)
php -S localhost:8000 -t wwwroot

# Or using composer script
composer run start
```

### Code Quality & Testing
```bash
# Run PHPStan static analysis (level 2)
vendor\bin\phpstan.bat analyse

# Run PHPStan with Pro features
composer run phpstan:pro

# PHPStan alternative using phar file
php _dev/tools/phpstan.phar analyse

# Run PHPUnit tests (if configured)
composer test

# Minify CSS/JS assets
composer run minify
php _dev/scripts/minify-assets.php

# Force rebuild all minified assets
composer run minify:force
php _dev/scripts/minify-assets.php --force
```

### Database & Migration Commands
```bash
# Test database connection
php wwwroot/test.php

# Run database migrations (development)
php database/migrations/test_migration.php

# Check database structure
php _dev/scripts/check_table_structure.php

# Pre-deployment checks
php _dev/scripts/pre_deploy_check.php
```

### Windows-Specific Commands
```bash
# PHPStan on Windows (use .bat file)
vendor\bin\phpstan.bat analyse

# Directory listing
dir /B app

# Copy files recursively
xcopy /Y /I source\*.* destination\
```

## Environment Configuration

### Environment Detection
- Production: `mtegmsmm.meb.gov.tr`
- Development: `localhost`
- Config file: `app/config/config.php` (auto-detects environment)
- Environment variables: `.env` file (loaded via `Environment::load()`)

### Key Configuration Constants
- `BASE_URL`: Application base URL
- `APP_ENV`: 'production' or 'development'
- `APP_DEBUG`: Boolean for debug mode
- `DB_*`: Database connection parameters
- `UPLOAD_PATH`: File upload directory
- `MAX_FILE_SIZE`: Upload size limit

## Critical Implementation Notes

### Session Security
- HTTP-only cookies enabled
- Strict same-site policy
- Session regeneration every 30 minutes (automatic in index.php:100)
- Session timeout after 1 hour of inactivity
- CSRF token validation on all POST requests
- CSRF tokens expire after 30 minutes

### Error Handling
- Custom error handler with Sentry integration (BaseController:26-50)
- Local file logging in `logs/` directory
- Development shows errors (APP_DEBUG=true), production logs only
- Performance tracking via `trackPerformance()` method in BaseController

### Bootstrap Integration
- Version: 5.3.6 (CDN-based)
- Dark mode support via `data-bs-theme`
- Custom enhancements in `wwwroot/assets/css/bootstrap-5.3-enhancements.css`
- Prefer Bootstrap utilities over custom CSS

### Multi-Tenant Considerations
- Always filter queries by `cove_id` for regular users (except SuperAdmin)
- SuperAdmin and Coordinator can access all COVE data
- Use `UnifiedViewService::checkPermission()` for access control
- Use `hasPermission()` helper function for quick permission checks
- Use `hasPermissionForCove()` for COVE-specific permission checks
- Session stores: `user_id`, `cove_id`, `role`, `role_name`, permissions array

### URL Routing Pattern
```
index.php?url=controller/method/param1/param2
Examples:
- index.php?url=user/edit/5
- index.php?url=user/login
- index.php?url=objective/create
```

## Project-Specific Patterns

### View Rendering
```php
// In controllers extending BaseController
$this->render('module/page', [
    'data' => $data
], [
    'title' => 'Page Title',
    'layout' => 'default', // or 'minimal', 'admin'
    'breadcrumbs' => [...] // optional breadcrumb array
]);
```

### Database Queries
```php
// Always use prepared statements
$stmt = $this->db->prepare("SELECT * FROM table WHERE cove_id = :cove_id");
$stmt->execute(['cove_id' => $_SESSION['cove_id']]);

// For multi-tenant queries (non-SuperAdmin)
$coveFilter = $this->isSuperAdmin() ? "" : "AND cove_id = :cove_id";
```

### Permission Checks
```php
// In controllers
$this->checkPermission('objectives', 'select'); // throws exception if denied
$this->checkCovePermission('objectives', $coveId, 'update');

// In views or helpers
if (!hasPermission('objectives', 'insert')) {
    // hide create button
}
```

### CSRF Protection
```php
// Generate token in controller
$token = $this->getCSRFToken();

// Include in forms
<input type="hidden" name="csrf_token" value="<?= $token ?>">

// Validate in controller
if (!$this->validateCSRFToken($_POST['csrf_token'])) {
    $this->handleError('Invalid CSRF token', 403);
}
```

## Active Development Tasks

### Current Focus Areas
- Task System Migration for admin_gazi (ID: 45)
- Bootstrap 5.3 migration (replacing custom CSS)
- Dashboard layout improvements
- Production/development environment separation

### Known Issues
- Dashboard fixed header overlapping content
- Test menu visibility in production
- Custom CSS overuse (200+ lines in dashboard)

### Migration Status
- Database migrations in `database/migrations/`
- Task system tables ready for deployment
- User migration completed (12 SuperAdmins identified)

## Testing & Debugging

### Test Files Location
```
app/views/test/          # Test views (development only)
wwwroot/test.php         # Main test entry point
_dev/scripts/            # Development scripts and tools
_dev/tools/              # PHPStan and other tools
database/migrations/     # Database migration scripts
```

### Debug Tools
```bash
# Test server configuration
php wwwroot/test.php

# Run pre-deployment checks
php _dev/scripts/pre_deploy_check.php
php _dev/scripts/pre_deploy_check_php5.php  # For PHP 5 compatibility

# Database testing
php _dev/scripts/check_table_structure.php
php database/migrations/test_migration.php
```

### Common Issues & Solutions

1. **Class not found errors**
   - Check file naming (case-sensitive on Linux)
   - Verify autoloader configuration
   - Ensure proper namespace usage

2. **Database connection issues**
   - Verify credentials in config.php
   - Check if PDO MySQL extension enabled
   - Test with db_test.php

3. **Permission errors**
   - Set directories to 755
   - Set PHP files to 644
   - uploads/ and logs/ need 777

## Deployment Checklist

1. **Pre-deployment:**
   - Run `php _dev/scripts/pre_deploy_check.php`
   - Verify PHP version >= 7.4 (or 5.5 for legacy support)
   - Check required extensions: PDO, mbstring, json, session

2. **Configuration:**
   - Update `app/config/config.php` with production credentials
   - Rename `.htaccess.production` to `.htaccess`
   - Set APP_ENV to 'production' and APP_DEBUG to false

3. **File Permissions:**
   - Set directories to 755: `logs/`, `uploads/`, `cache/`
   - Set PHP files to 644
   - Ensure write permissions for session storage

4. **Database:**
   - Import schema from `database/schema.sql`
   - Run migrations from `database/migrations/`
   - Verify database host: `mebmysql.meb.gov.tr` (production)

5. **Post-deployment:**
   - Clear cache directory
   - Test authentication flow
   - Verify Sentry error tracking integration
   - Check maintenance mode settings if needed

## Important Conventions

### Naming Conventions
- **Controllers:** PascalCase with "Controller" suffix (e.g., `ObjectiveController`)
- **Views:** lowercase with underscores (e.g., `user_list.php`)
- **Database tables:** lowercase with underscores (e.g., `user_permissions`)
- **URL routes:** lowercase, no underscores (e.g., `user/login`)
- **Helper functions:** snake_case (e.g., `has_permission()`)

### Code Style
- **Language:** Turkish for UI text, English for code/comments
- **PHP compatibility:** Maintain PHP 5.5+ compatibility where possible
- **Security:** Always use prepared statements for database queries
- **Error handling:** Use BaseController's `handleError()` method
- **View data:** Always escape output with `htmlspecialchars()` or `<?= ?>`

### Development Workflow
- Always check existing patterns in neighboring files before implementing new features
- Use Bootstrap 5.3 utilities instead of writing custom CSS
- Prefer editing existing files over creating new ones
- Test with both development and production configurations
- t