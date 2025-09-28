# Layout Configuration Summary

## Current Layout Assignments in UnifiedViewService

### Pages Using NO LAYOUT (`LAYOUT_NONE`)
These pages render without any wrapper layout:

| Page | Purpose |
|------|---------|
| `user/register` | User registration form |
| `user/captcha` | Captcha generation/validation |

### Pages Using PUBLIC LAYOUT (`LAYOUT_PUBLIC`)
These pages use the minimal public layout with navbar and footer:

| Page | Purpose |
|------|---------|
| `user/login` | Login page |
| `user/main` | Main landing/home page |
| `user/haberler` | News detail page |
| `user/haberlist` | News list page |
| `home/smmnetwork` | SMM network map |

### Pages Using UNIFIED LAYOUT (`LAYOUT_UNIFIED`)
Default layout for authenticated pages (all pages not explicitly configured above):

- All admin pages (`objective/*`, `indicator/*`, `action/*`, etc.)
- User management pages (`user/manage`, `user/edit`, etc.)
- Settings and configuration pages
- Dashboard (`home/index`)

### Available Layouts

| Layout File | Constant | Components | Use Case |
|------------|----------|------------|----------|
| `unified.php` | `LAYOUT_UNIFIED` | header, navbar, footer, scripts, PermissionHelper | Authenticated pages (default) |
| `unified_main.php` | `LAYOUT_UNIFIED_MAIN` | navbar_with_sidebar, navbar, sidebar_menu, footer | Admin with sidebar |
| `public.php` | `LAYOUT_PUBLIC` | navbar, footer | Public/visitor pages |
| *(none)* | `LAYOUT_NONE` | - | Standalone pages (registration, captcha) |

## Recent Changes

### 2024-09-28 Updates:
1. **Changed from LAYOUT_NONE to LAYOUT_PUBLIC:**
   - `user/login` - Now has consistent navigation
   - `user/main` - Landing page with standard header/footer
   - `user/haberler` - News detail with navigation
   - `user/haberlist` - News list with navigation
   - `home/smmnetwork` - Network map with navigation

2. **Kept as LAYOUT_NONE:**
   - `user/register` - Clean registration focus
   - `user/captcha` - Technical endpoint

### Benefits:
- ✅ Consistent navigation across public pages
- ✅ Better user experience with standard header/footer
- ✅ Login page now matches site design
- ✅ News pages integrated with site navigation
- ✅ Registration remains focused without distractions

## How to Override Layout

Controllers can override the default layout by passing options:

```php
$this->render('view/name', $data, [
    'layout' => UnifiedViewService::LAYOUT_PUBLIC
]);
```

## Layout Hierarchy

```
No Layout (LAYOUT_NONE)
├── user/register
└── user/captcha

Public Layout (LAYOUT_PUBLIC)
├── user/login
├── user/main
├── user/haberler
├── user/haberlist
└── home/smmnetwork

Unified Layout (LAYOUT_UNIFIED) - DEFAULT
├── home/index
├── objective/*
├── indicator/*
├── action/*
├── user/manage
├── user/edit
└── (all other authenticated pages)

Unified Main Layout (LAYOUT_UNIFIED_MAIN)
└── (can be set via override for admin pages needing sidebar)
```