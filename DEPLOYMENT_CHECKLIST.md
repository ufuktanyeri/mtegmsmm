# PRODUCTION DEPLOYMENT CHECKLIST

## 🔍 500 HATASI ÖNLEMELERİ

### 1. Database Bağlantısı
- [ ] Production database credential'ları doğru mu?
- [ ] `app/config/config.php` dosyasında production ayarları kontrol edildi mi?
- [ ] Database host: `mebmysql.meb.gov.tr` erişilebilir mi?

### 2. Dosya İzinleri
- [ ] `logs/` dizini yazılabilir (755 veya 777)
- [ ] `uploads/` dizini yazılabilir (755 veya 777)
- [ ] PHP dosyaları okunabilir (644)

### 3. .htaccess Dosyası
- [ ] `.htaccess.production` dosyası `.htaccess` olarak rename edildi mi?
- [ ] mod_rewrite aktif mi?
- [ ] RewriteBase ayarı doğru mu?

### 4. PHP Versiyon Kontrolü
- [ ] Server PHP versiyonu >= 7.4 mü?
- [ ] Gerekli PHP extension'lar yüklü mü? (mysqli, session, json, mbstring)

## 📦 DEPLOYMENT SIRALAMA

### Aşama 1: View Dosyaları (Düşük Risk)
```
app/views/action/
app/views/aim/
app/views/cove/
app/views/documentStrategy/
app/views/field/
app/views/help/
app/views/home/
app/views/indicator/
app/views/log/
app/views/news/
app/views/objective/
app/views/regulation/
app/views/user/
```

### Aşama 2: Component ve Layout'lar
```
app/views/components/header.php
app/views/components/navbar.php
app/views/components/scripts.php
app/views/components/navbar_with_sidebar.php
app/views/components/sidebar_menu.php
app/views/layouts/
```

### Aşama 3: Services
```
app/services/NavbarService.php
```

### Aşama 4: Controllers
```
app/controllers/AuthController.php
app/controllers/CoveController.php
app/controllers/HealthController.php
app/controllers/UserController.php
```

### Aşama 5: Core Includes (En Riskli)
```
includes/AssetManager.php
includes/Database.php
includes/Security.php
```

### Aşama 6: Statik Dosyalar
```
wwwroot/img/news/placeholder.svg
```

## 🧪 TEST PROSEDÜRÜ

### Her aşamadan sonra test et:
1. Ana sayfa açılıyor mu? (`/`)
2. Login sayfası açılıyor mu? (`/user/login`)
3. Console'da JavaScript hatası var mı?
4. Network tab'da 404 veya 500 hatası var mı?

## 🚨 ACİL DURUM - ROLLBACK

Eğer 500 hatası alırsanız:

1. **Error log'u kontrol et:**
   ```
   /logs/php_errors.log
   /logs/error.log
   ```

2. **En son yüklenen dosyayı geri al:**
   - FTP'den son yüklenen dosyayı sil
   - Bir önceki versiyonu geri yükle

3. **Database bağlantısını kontrol et:**
   ```php
   <?php
   // test_db.php - Production'da test et, sonra sil!
   define('DB_HOST', 'mebmysql.meb.gov.tr');
   define('DB_NAME', 'fg5085Y3XU1aG48Qw');
   define('DB_USER', 'fg508_5Y3XU1aGwa');
   define('DB_PASS', 'Jk6C73Pf');
   
   $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   echo "Database connection successful!";
   $conn->close();
   ?>
   ```

## ✅ BAŞARILI DEPLOYMENT SONRASI

1. [ ] Test dosyalarını sil (`test_db.php` vb.)
2. [ ] Cache'leri temizle
3. [ ] Error reporting'i kapat (production mode)
4. [ ] HTTPS yönlendirmesini aktifleştir
5. [ ] Monitoring araçlarını kontrol et

## 📝 NOTLAR

- **Bootstrap 5.3 Migration:** Tüm view dosyaları güncellendi
- **reCAPTCHA:** Test key'leri kullanılıyor, production key'leri güncellenmeli
- **Session güvenliği:** Production'da HTTPS zorunlu olmalı

---

**Son Güncelleme:** 2025-01-26
**Hazırlayan:** Claude AI Assistant