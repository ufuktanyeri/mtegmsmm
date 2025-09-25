# CLAUDE.md - AI Assistant Session Memory

**Tarih:** 2024-09-24 (Son Güncelleme)
**Dosya Türü:** Claude Code Session Memory
**Konu:** Proje durumu, aktif görevler ve sonuçlar
**Durum:** Conversations dizinine arşivlendi (2024-09-25)

---

> Bu dosya Claude AI asistanı için kalıcı hafıza görevi görür. Her oturumda bu dosyayı okuyarak kaldığı yerden devam edebilir.

## 🔄 Son Güncelleme: 2024-09-24

## 📌 Proje Durumu

### Aktif Görevler
- [ ] Dashboard.php'deki layout sorunları çözülüyor
- [ ] Test menüsü production'dan kaldırılıp development-only yapılacak
- [ ] Bootstrap 5.3 migration devam ediyor

### Tamamlanan Görevler
- [x] Test menüsü navbar'a eklendi (development-only olarak güncellendi)
- [x] TestController oluşturuldu
- [x] Bootstrap 5.3 enhancements CSS dosyası oluşturuldu
- [x] Dashboard-offcanvas.php - Modern dashboard örneği
- [x] Bootstrap-5.3-showcase.php - Özellik vitrin sayfası

## 🚨 Kritik Sorunlar

### 1. Dashboard Layout Sorunu
**Sorun:** `app/views/test/dashboard.php` sayfasında:
- Header fixed olduğu için içeriği kapatıyor
- "Sistem Güncellendi" başlığı kesiliyor
- 200+ satır gereksiz custom CSS var
- Bootstrap utilities kullanılmıyor

**Denenen Çözümler:**
- Body padding-top eklendi
- Main margin-top ayarlandı
- Z-index hiyerarşisi düzenlendi
- Sidebar class'ı dashboard-sidebar olarak değiştirildi

**Durum:** ❌ Hala çözülmedi

### 2. Test Menüsü Görünürlüğü
**Çözüm:** navbar.php'de localhost kontrolü eklendi
```php
$isDevelopment = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);
```

## 📁 Önemli Dosyalar

### Test Dosyaları
```
app/views/test/
├── dashboard.php                 # ⚠️ Layout sorunu var
├── dashboard-offcanvas.php       # ✅ Modern Offcanvas örneği
├── dashboard-simple.php          # ✅ Basit çalışan versiyon
├── bootstrap-5.3-showcase.php    # ✅ BS 5.3 özellikleri
├── component_test.php            # ✅ Component testleri
├── container_query_test.php      # ✅ Container query örnekleri
└── index.php                     # ✅ Test ana sayfası

wwwroot/assets/css/
└── bootstrap-5.3-enhancements.css # ✅ Modern CSS özellikleri
```

### Controller
```
app/controllers/TestController.php
- index()
- dashboard()
- dashboardSimple()
- components()
- container()
```

## 🎯 Öneriler ve Notlar

### Dashboard İçin Öneriler
1. **Custom CSS yerine Bootstrap kullan:**
   - `position-fixed` class'ı
   - `navbar` component'i
   - `offcanvas` sidebar'ı
   - Bootstrap grid system

2. **Doğru HTML5 yapısı:**
   ```html
   <nav class="navbar">        <!-- Üst bar -->
   <div class="container-fluid">
       <aside>                 <!-- Sidebar -->
       <main>                  <!-- İçerik -->
   </div>
   ```

### Kullanıcı Geri Bildirimleri
- "Token'lar boşa gidiyor" - Çözüm test edilmeden onay verilmemeli
- "Cevap beklemeden işlem yapma" - Kullanıcı onayı beklenmeli
- "Inspect element bilgisi istenmeli" - Browser'da görüneni anlamak için

## 🔧 Yapılandırma Notları

### Bootstrap Version
- Bootstrap 5.3.6 kullanılıyor
- CDN: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css`

### Dark Mode
- `data-bs-theme` attribute kullanılıyor
- LocalStorage'da tema tercihi saklanıyor

### MVC Yapısı
- Controllers: BaseController extend ediliyor
- Views: `$this->render()` ile render ediliyor
- URL Pattern: `index.php?url=controller/method`

## 💡 Bir Sonraki Oturumda

**Claude'a şunu söyle:**
```
"CLAUDE.md dosyasını oku, dashboard.php layout sorununu çözelim.
Header içeriği kapatıyor, önceki denemeler başarısız oldu."
```

## 📝 Git Commit Mesajları
```bash
# Son commitler
43ab86a refactor: Apply Bootstrap 5.3 migration to test pages
19c7ce9 refactor: Apply lowercase naming convention and enhance portal features
```

## ⚠️ Dikkat Edilecekler

1. **Test etmeden "tamam" deme** - Kullanıcı test edip onay vermeli
2. **Browser inspect bilgisi iste** - Computed styles kontrol edilmeli
3. **Bootstrap docs kontrol et** - Custom CSS yazmadan önce
4. **Basit çözümler tercih et** - Karmaşık CSS yerine Bootstrap utilities

---

### Nasıl Kullanılır?
1. Yeni oturum başladığında: `"CLAUDE.md dosyasını oku"`
2. Önemli değişikliklerden sonra bu dosyayı güncelle
3. Git'e commit'le: `git add CLAUDE.md && git commit -m "Update Claude session memory"`

### Son Güncelleme Yapan: Claude
### Tarih: 2024-09-24
### Kullanıcı Notu: Dashboard layout sorunu çözülemedi, token israfı var
- to memorize