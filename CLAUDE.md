# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MTEGM SMM Portal - A multi-tenant strategic management system built with PHP MVC architecture for Turkish Ministry of Education. The system manages strategic planning, indicators, objectives, and actions across multiple educational institutions (COVE).

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
# Start local development server
php -S localhost:8000 -t wwwroot

# Or using composer
composer run start
```

### Code Quality & Testing
```bash
# Run PHPStan static analysis (level 2)
vendor\bin\phpstan.bat analyse

# Run PHPStan with Pro features
composer run phpstan:pro

# Run PHPUnit tests
composer test

# Minify assets
composer run minify

# Force minify (rebuild all)
composer run minify:force
```

### Windows-Specific Commands
```bash
# PHPStan on Windows
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
- Session regeneration every 30 minutes
- CSRF protection on forms

### Error Handling
- Custom error handler with Sentry integration
- Local file logging in `logs/` directory
- Development shows errors, production logs only

### Bootstrap Integration
- Version: 5.3.6 (CDN-based)
- Dark mode support via `data-bs-theme`
- Custom enhancements in `wwwroot/assets/css/bootstrap-5.3-enhancements.css`
- Prefer Bootstrap utilities over custom CSS

### Multi-Tenant Considerations
- Always filter queries by `cove_id` for regular users
- SuperAdmin can access all COVE data
- Use `UnifiedViewService::checkPermission()` for access control
- Session stores: `user_id`, `cove_id`, `role`, permissions

### URL Routing Pattern
```
index.php?url=controller/method/param1/param2
Examples:
- index.php?url=user/edit/5
- index.php?url=auth/login
- index.php?url=objective/create
```

## Project-Specific Patterns

### View Rendering
```php
// In controllers
$this->render('module/page', [
    'data' => $data
], [
    'title' => 'Page Title',
    'layout' => 'default' // or 'minimal', 'admin'
]);
```

### Database Queries
```php
// Always use prepared statements
$stmt = $this->db->prepare("SELECT * FROM table WHERE cove_id = :cove_id");
$stmt->execute(['cove_id' => $_SESSION['cove_id']]);
```

### Permission Checks
```php
if (!hasPermission('permission_name')) {
    redirect('auth/unauthorized');
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
wwwroot/test_*.php       # Server test scripts
_dev/                    # Development-only files
```

### Debug Tools
- `debug_500.php`: Detailed error debugging
- `test_server.php`: Environment verification
- `db_test.php`: Database connection testing

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

1. Update `app/config/config.php` with production database
2. Rename `.htaccess.production` to `.htaccess`
3. Set proper file permissions
4. Import database schema from `database/schema.sql`
5. Clear cache directory
6. Disable debug mode in production
7. Verify Sentry DSN for error tracking

## Important Conventions

- Controllers: PascalCase with "Controller" suffix
- Views: lowercase with underscores
- Database tables: lowercase with underscores
- URL routes: lowercase, no underscores
- Turkish language for UI, English for code
- Follow existing code patterns in neighboring files