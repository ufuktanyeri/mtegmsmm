# Production Migration Plan - SMM Task System

## 🎯 AMAÇ
Local'de test edilmiş Task Management System'i production'a güvenli şekilde migrate etmek.

## 📋 MİGRATİON ADIMLAR SIRASI

### AŞAMA 1: LOCAL TEST (ŞİMDİ)
```bash
Status: 🟡 IN PROGRESS
Location: localhost/mtegmsmm
Database: fg5085y3xu1ag48qw (local)
```

**Görevler:**
- [x] Database connection test ✅
- [ ] Migration test sayfasını çalıştır
- [ ] Task system tablolarını oluştur
- [ ] SuperAdmin controller test et
- [ ] Admin interface test et
- [ ] UI component'leri doğrula

### AŞAMA 2: PRODUCTION HAZIRLIK
```bash
Status: ⏳ PENDING
Location: Remote server
Database: Production database
```

**Görevler:**
- [ ] Production database backup al
- [ ] Mevcut kullanıcı rollerini analiz et
- [ ] Server resource'larını kontrol et
- [ ] Maintenance window planla
- [ ] Rollback planı hazırla

### AŞAMA 3: PRODUCTION MİGRATİON
```bash
Status: ⏳ WAITING FOR STAGE 1
Location: Remote server
Database: Production database
```

**Görevler:**
- [ ] Maintenance mode aktif et
- [ ] Migration dosyasını upload et
- [ ] Database migration çalıştır
- [ ] Application files sync et
- [ ] Smoke test yap
- [ ] Maintenance mode kapat

## 🔄 SYNC STRATEJİSİ

### Development → Production Sync
```bash
# 1. Code sync (Git kullanarak)
git push origin main
git tag v2.0-task-system

# 2. Database sync (sadece yeni tablolar)
- task_definitions
- task_assignments
- task_delegations
- task_tracking
- user_widget_preferences
- task_comments

# 3. Permissions sync
- tasks.define → SuperAdmin
- tasks.assign → Admin
- tasks.delegate → Admin
- tasks.view_all → Admin, Coordinator
- tasks.report → SuperAdmin
```

### Bidirectional Sync (İhtiyaca göre)
```bash
# Production → Local (data sync için)
- Users, Roles, Permissions (mevcut)
- Coves, Fields (mevcut)
- Real data for testing
```

## ⚠️ RİSK YÖNETİMİ

### Yüksek Risk Alanları:
- **Foreign key constraints**: Mevcut users/coves referansları
- **Permission conflicts**: Rol-izin çakışmaları
- **Data integrity**: Mevcut sistemle entegrasyon
- **Performance impact**: Yeni tablolar ve indexler

### Mitigation Strategies:
- **Full backup** before migration
- **Rollback script** hazır
- **Step-by-step execution**
- **Immediate rollback** capability

## 📊 BAŞARI KRİTERLERİ

### Local Success:
- [ ] 6 yeni tablo başarıyla oluşturuldu
- [ ] Sample data insert edildi
- [ ] Views ve triggers çalışıyor
- [ ] SuperAdmin login → task creation çalışıyor
- [ ] Admin login → task assignment çalışıyor
- [ ] No SQL errors in logs

### Production Success:
- [ ] Migration tamamlandı (< 5 dakika downtime)
- [ ] Mevcut sistem çalışmaya devam ediyor
- [ ] Yeni task features erişilebilir
- [ ] Performance degradation yok
- [ ] User experience etkilenmemiş

## 🛠️ TOOLS & COMMANDS

### Local Migration:
```bash
# Test migration
http://localhost/mtegmsmm/test_migration.php

# Apply migration (phpMyAdmin)
- Database: fg5085y3xu1ag48qw
- SQL file: database/migrations/001_create_task_system_fixed.sql
```

### Production Migration:
```bash
# Backup (before migration)
mysqldump -u username -p database_name > backup_before_task_migration.sql

# Apply migration
mysql -u username -p database_name < 001_create_task_system_fixed.sql

# Verify
mysql -u username -p -e "SHOW TABLES LIKE 'task_%'; SELECT COUNT(*) FROM task_definitions;"
```

## 📞 CONTACT & ROLLBACK

### Migration Team:
- **Developer**: Claude Code Assistant
- **Database Admin**: [Your team member]
- **System Admin**: [Your team member]

### Emergency Rollback:
```bash
# If anything goes wrong:
mysql -u username -p database_name < rollback_task_system.sql
```

### Rollback Criteria:
- Migration fails with errors
- Performance severely impacted
- User functionality broken
- Data corruption detected

---

**Created**: 2024-09-25
**Last Updated**: 2024-09-25
**Status**: Draft - Awaiting Local Test Completion