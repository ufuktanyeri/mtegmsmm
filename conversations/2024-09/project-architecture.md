# MTEGM SMM Portal - Proje Yapısı

## Production Klasör Yapısı (Deployment'a Gidecek)
```
mtegmsmm/
├── app/                    # Ana uygulama klasörü
│   ├── config/            # Yapılandırma dosyaları
│   ├── controllers/       # MVC Controller'ları
│   ├── entities/          # Veri modelleri
│   ├── helpers/           # Yardımcı fonksiyonlar
│   ├── lib/              # 3. parti kütüphaneler
│   ├── models/           # Ek modeller
│   ├── services/         # İş mantığı servisleri
│   ├── validators/       # Veri doğrulama
│   ├── views/           # Görünüm dosyaları
│   └── Router.php       # Ana yönlendirici
├── includes/              # Core sistem dosyaları
│   ├── AccountSecurity.php
│   ├── Database.php
│   ├── Environment.php
│   ├── PermissionHelper.php
│   └── SessionManager.php
├── wwwroot/              # Web root (public)
│   ├── assets/          # CSS, JS, fontlar
│   ├── img/            # Görseller
│   ├── uploads/        # Kullanıcı yüklemeleri
│   ├── index.php       # Ana giriş noktası
│   └── .htaccess       # Apache yapılandırması
├── logs/                 # Uygulama logları
├── cache/               # Önbellek dosyaları
├── .env                 # Environment değişkenleri
└── .htaccess           # Root Apache güvenliği
```

## Development Klasör Yapısı (_dev - Production'a GİTMEZ!)
```
_dev/
├── backups/             # Yedekleme dosyaları
├── database/           # Veritabanı dump'ları
│   ├── phpmyadmin_safe_deploy.sql
│   └── migration_compatible.sql
├── docs/               # Dokümantasyon
│   ├── DEPLOYMENT_GUIDE.md
│   ├── DATABASE_MIGRATION_GUIDE.md
│   └── *.md
├── emails/            # E-posta şablonları
│   ├── php8_takip_mail.txt
│   └── hosting_talep_email.txt
├── html/              # Test HTML dosyaları
│   └── index.html
├── scripts/           # Yardımcı scriptler
│   ├── pre_deploy_check.php
│   ├── pre_deploy_check_php5.php
│   └── deploy.php
├── tests/            # Test dosyaları
│   ├── test_simple.php
│   ├── test_local.php
│   └── phpinfo.php
└── tools/           # Geliştirme araçları
    └── phpstan.phar
```

## Önemli Notlar

### Production'a GİDECEK:
✅ app/ klasörü
✅ includes/ klasörü
✅ wwwroot/ klasörü
✅ logs/ klasörü (boş)
✅ cache/ klasörü (boş)
✅ .env dosyası (production değerleriyle)

### Production'a GİTMEYECEK:
❌ _dev/ klasörü (TÜM İÇERİĞİ)
❌ .git/ klasörü
❌ vendor/ klasörü
❌ node_modules/
❌ composer.json, package.json
❌ Test dosyaları
❌ Dokümantasyon dosyaları
❌ Yedek dosyalar
❌ CLAUDE.md

## Deployment Komutları

### Temiz Production Paketi Oluşturma:
```bash
# 1. Deployment paketi oluştur
mkdir deployment_package

# 2. Production dosyalarını kopyala
cp -r app deployment_package/
cp -r includes deployment_package/
cp -r wwwroot deployment_package/
mkdir deployment_package/logs
mkdir deployment_package/cache

# 3. Production .env oluştur
cp .env deployment_package/.env
# EDIT: Production değerlerini düzenle

# 4. Temizlik kontrol
ls deployment_package/
# Sadece production dosyaları görünmeli
```

### FTP/SFTP Upload:
```bash
# Sadece deployment_package içeriğini yükle
# _dev klasörünü ASLA yükleme!
```

## Güvenlik Kontrolleri

### Upload Öncesi:
- [ ] _dev klasörü pakette YOK
- [ ] Test dosyaları YOK
- [ ] .git klasörü YOK
- [ ] composer.json YOK
- [ ] Yedek dosyalar (.bak, .old) YOK

### Upload Sonrası:
- [ ] _dev klasörüne erişim testi (404 vermeli)
- [ ] test.php dosyalarına erişim testi (404 vermeli)
- [ ] .env dosyası web'den erişilemez
- [ ] logs/ klasörü web'den erişilemez

## Localhost Çalışma:
```bash
# PHP Development Server
php -S localhost:8000 -t wwwroot

# XAMPP kullanım
# Apache DocumentRoot: C:/xampp/htdocs/mtegmsmm/wwwroot
```

---
**Son Güncelleme:** 2025-01-22
**Temiz Yapı:** ✅ Production Ready