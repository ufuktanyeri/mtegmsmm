# MTEGM SMM Portal - Deployment Kılavuzu

## 500 Internal Server Error Çözümü ve Güvenli Deployment

### Hızlı Çözüm (500 Hatası İçin)

500 hatası genellikle şu sebeplerden kaynaklanır:
1. Veritabanı bağlantı hatası
2. Eksik veritabanı tabloları
3. Yanlış dosya izinleri
4. PHP extension eksikliği

---

## ADIM 1: Deployment Öncesi Kontrol

```bash
# Kontrol scriptini çalıştır
php _dev/scripts/pre_deploy_check.php
```

Bu script şunları kontrol eder:
- PHP versiyonu ve extensionlar
- Veritabanı bağlantısı
- Dosya/klasör izinleri
- Güvenlik ayarları

---

## ADIM 2: Veritabanı Kurulumu (PHPMyAdmin)

### 2.1 Veritabanı Yedeği
1. PHPMyAdmin'e giriş yap
2. `fg5085Y3XU1aG48Qw` veritabanını seç
3. **Dışa Aktar** → **Hızlı** → **Git** ile yedek al

### 2.2 SQL Script Çalıştırma
1. PHPMyAdmin'de **SQL** sekmesine tıkla
2. `_dev/database/phpmyadmin_safe_deploy.sql` dosyasının içeriğini yapıştır
3. **Git** butonuna tıkla

**NOT**: Eğer hata alırsanız:
- "Table already exists" hataları göz ardı edilebilir
- "Column already exists" hataları normaldir
- Kritik hatalar kırmızı ile gösterilir

### 2.3 Alternatif: Parçalı Yükleme
Eğer SQL dosyası çok büyükse, parçalara ayırın:

```sql
-- PARÇA 1: Tablolar
-- Users tablosu güncellemeleri
ALTER TABLE `users` ...

-- PARÇA 2: Yeni tablolar
CREATE TABLE IF NOT EXISTS `password_history` ...

-- PARÇA 3: İndeksler ve foreign keyler
CREATE INDEX IF NOT EXISTS ...
```

---

## ADIM 3: Dosya Yapılandırması

### 3.1 Config Dosyası Kontrolü
`app/config/config.php` dosyasında:

```php
// Production ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'fg5085Y3XU1aG48Qw');
define('DB_USER', 'fg508_5Y3XU1aGwa');
define('DB_PASS', 'Jk6C73Pf');
```

### 3.2 .env Dosyası (Opsiyonel)
```bash
# .env dosyası oluştur
cp .env.example .env

# Production değerlerini düzenle
APP_ENV=production
APP_DEBUG=false
DB_HOST=localhost
DB_NAME=fg5085Y3XU1aG48Qw
```

### 3.3 Klasör İzinleri (Linux/Unix)
```bash
# Yazılabilir klasörler
chmod -R 775 logs/
chmod -R 775 cache/
chmod -R 775 wwwroot/uploads/

# Sahiplik ayarları (web sunucu kullanıcısı)
chown -R www-data:www-data logs/
chown -R www-data:www-data cache/
chown -R www-data:www-data wwwroot/uploads/
```

### 3.4 Windows/IIS İzinleri
1. Klasöre sağ tıkla → Özellikler → Güvenlik
2. IIS_IUSRS kullanıcısına "Değiştir" izni ver
3. logs/, cache/, uploads/ klasörleri için tekrarla

---

## ADIM 4: Web Sunucu Yapılandırması

### Apache (.htaccess)
`wwwroot/.htaccess` dosyası:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

### IIS (web.config)
`wwwroot/web.config` dosyası:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="MTEGM Routes">
                    <match url="^(.*)$" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php?url={R:1}" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

---

## ADIM 5: Deployment Sonrası

### 5.1 Güvenlik Temizliği
```bash
# Development dosyalarını sil
rm -rf _dev/
rm -rf .git/
rm composer.json composer.lock
rm package.json package-lock.json
```

### 5.2 Web Sunucusu Yeniden Başlatma
```bash
# Apache
sudo systemctl restart apache2

# IIS
iisreset

# PHP-FPM
sudo systemctl restart php8.2-fpm
```

### 5.3 Test Kontrolleri
1. Ana sayfa: https://mtegmsmm.meb.gov.tr/
2. Giriş sayfası: https://mtegmsmm.meb.gov.tr/auth/login
3. Hata logları: `logs/error.log`

---

## Sık Karşılaşılan Hatalar ve Çözümleri

### Hata: 500 Internal Server Error
**Çözüm**:
1. PHP error log kontrol: `logs/php_errors.log`
2. Apache/IIS error log kontrol
3. Veritabanı bağlantısını test et: `php _dev/tests/test_db_connection.php`

### Hata: "Table doesn't exist"
**Çözüm**:
1. PHPMyAdmin'de SQL scriptini tekrar çalıştır
2. Tablo adlarının doğru olduğunu kontrol et

### Hata: "Access denied for user"
**Çözüm**:
1. Veritabanı kullanıcı bilgilerini kontrol et
2. MySQL kullanıcı izinlerini kontrol et:
```sql
GRANT ALL PRIVILEGES ON fg5085Y3XU1aG48Qw.* TO 'fg508_5Y3XU1aGwa'@'localhost';
FLUSH PRIVILEGES;
```

### Hata: "Session could not be started"
**Çözüm**:
1. Session klasörü izinlerini kontrol et
2. `session_save_path` ayarını kontrol et

---

## Kontrol Listesi

- [ ] Veritabanı yedeği alındı
- [ ] PHPMyAdmin SQL script çalıştırıldı
- [ ] Config.php dosyası production ayarlarında
- [ ] Klasör izinleri ayarlandı (logs, cache, uploads)
- [ ] Development dosyaları temizlendi
- [ ] Web sunucusu yeniden başlatıldı
- [ ] Site ana sayfası açılıyor
- [ ] Giriş yapılabiliyor
- [ ] Error loglarında kritik hata yok

---

## Acil Durumda

Eğer site çalışmıyorsa:
1. Veritabanı yedeğini geri yükle
2. Eski config dosyasına dön
3. Error loglarını kontrol et
4. Basit bir test dosyası oluştur:

```php
// wwwroot/test.php
<?php
phpinfo();
?>
```

Bu dosya çalışıyorsa PHP çalışıyor demektir, sorun uygulamadadır.

---

## İletişim ve Destek

Deployment sırasında sorun yaşarsanız:
- Error loglarını saklayın
- Veritabanı yedeğini muhafaza edin
- PHPMyAdmin'den screenshot alın

---

**Son Güncelleme**: 2025-01-22
**Versiyon**: 2.0