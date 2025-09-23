# PROJECT CONTEXT - MTEGM SMM Portal Modernization

## 🎯 Project Overview
- **Project:** MTEGM Sektörel Mükemmeliyet Merkezleri (SMM) Faaliyet Takip Portalı
- **Current Version:** Legacy PHP 5.5
- **Target Version:** PHP 8.2+ with Modern MVC
- **Database:** fg5085Y3XU1aG48Qw (MariaDB 10.4.32)
- **Production URL:** https://mtegmsmm.meb.gov.tr/
- **GitHub Repo:** https://github.com/ufuktanyeri/mtegmsmm

## 📊 Current Status
- **Phase:** MVC Modernization & SuperAdmin Integration
- **Started:** 2025-09-23
- **Current Task:** Context Management Setup
- **Progress:** 5% - Initial Setup

## 🏗️ Architecture Goals
1. Modern MVC Framework (PHP 8.2+)
2. SuperAdmin Task Management System
3. MEB Security Compliance (14114814)
4. Widget-based Dashboard System
5. Progressive Migration Strategy

## 👥 Role Hierarchy
```
superadmin (5) → Reporting & Task Assignment Only
admin (4) → Full CRUD + Task Delegation
editor (6) → Content Management
coordinator (3) → SMM Data Entry
standard (2) → View Only
guest (1) → Basic Access
```

## 🔐 Security Requirements (MEB 14114814)
- Password: 8+ chars, upper/lower/digit/special
- Password expiry: 90 days
- Password history: Last 5 passwords
- Max login attempts: 5
- Account lockout: 30 minutes
- Session timeout: 120 minutes

## 📁 Directory Structure
```
/mtegmsmm/
├── app/                 # Current application
├── app_new/            # New MVC structure (in progress)
├── _dev/               # Development files
├── database/           # Migrations & seeds
├── wwwroot/           # Web root
└── PROJECT_CONTEXT.md  # This file
```

## 🚦 Migration Status
- [ ] Context Management Setup - IN PROGRESS
- [ ] Base MVC Structure
- [ ] Security Middleware
- [ ] SuperAdmin System
- [ ] Widget System
- [ ] Testing & Deployment

## 📝 Session Notes
- Session Start: 2025-09-23
- Working Branch: feature/mvc-modernization
- Last Commit: Initial GitHub setup