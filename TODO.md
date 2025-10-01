# MTEGM SMM Portal - İyileştirme Planı

## Genel Bakış
Bu TODO listesi, uygulama analizinde belirlenen potansiyel iyileştirme alanlarını kapsamaktadır. İyileştirmeler modern PHP uygulamaları, frontend modernizasyonu, API geliştirme ve test altyapısı olmak üzere dört ana kategoride gruplandırılmıştır.

## 1. Modern PHP Uygulamaları

### Namespace Tutarlılığı
- [ ] `app/controllers/` dizinindeki tüm controller sınıflarına namespace ekle
- [ ] `app/models/` dizinindeki model sınıflarını namespace ile güncelle
- [ ] `app/entities/` dizinindeki entity sınıflarına namespace ekle
- [ ] `app/helpers/` ve `app/services/` dizinlerini namespace ile yapılandır
- [ ] Autoloader'ı PSR-4 standardına uygun şekilde güncelle

### Bağımlılık Enjeksiyonu (Dependency Injection)
- [ ] Container sınıfı oluştur (örneğin `app/container.php`)
- [ ] Controller'larda constructor injection implementasyonu
- [ ] Service sınıflarında dependency injection kullanımı
- [ ] Database bağlantısını container üzerinden yönet

### Composer Paketleri Entegrasyonu
- [ ] `composer.json` dosyasını güncelle ve gerekli paketleri ekle:
  - [ ] Monolog (loglama için)
  - [ ] Symfony/Console (CLI komutları için)
  - [ ] PHP-DI (dependency injection için)
  - [ ] Doctrine/ORM (modern ORM için, opsiyonel)
- [ ] Vendor autoloader'ı entegre et

## 2. Frontend Modernizasyonu

### JavaScript Modernizasyonu
- [ ] jQuery bağımlılığını azalt/kaldır
- [ ] Vanilla JavaScript veya modern framework'e geçiş (Vue.js/React)
- [ ] ES6+ özelliklerini kullan (arrow functions, async/await, modules)
- [ ] AJAX çağrılarını Fetch API ile değiştir

### Build Sistemi Kurulumu
- [ ] Node.js ve npm kurulumu
- [ ] Webpack veya Vite build sistemi kur
- [ ] SCSS/Sass preprocessor entegrasyonu
- [ ] JavaScript minification ve bundling
- [ ] Development ve production build'leri ayır

### Asset Yönetimi
- [ ] CSS ve JS dosyalarını modüler hale getir
- [ ] Code splitting implementasyonu
- [ ] Lazy loading için asset'leri optimize et
- [ ] CDN entegrasyonu (örneğin Bootstrap, Font Awesome)

## 3. API Geliştirme

### RESTful API Yapısı
- [ ] `app/controllers/ApiController.php` base sınıfı oluştur
- [ ] JSON response formatı standardize et
- [ ] HTTP status code'larını doğru kullan
- [ ] API versioning sistemi implement et (`/api/v1/`)

### Ana API Endpoint'leri
- [ ] **Authentication API**:
  - [ ] `POST /api/v1/auth/login`
  - [ ] `POST /api/v1/auth/logout`
  - [ ] `GET /api/v1/auth/me` (current user info)
- [ ] **User Management API**:
  - [ ] `GET /api/v1/users`
  - [ ] `POST /api/v1/users`
  - [ ] `PUT /api/v1/users/{id}`
  - [ ] `DELETE /api/v1/users/{id}`
- [ ] **Strategic Management API**:
  - [ ] `GET /api/v1/aims`
  - [ ] `GET /api/v1/objectives`
  - [ ] `GET /api/v1/indicators`
  - [ ] `GET /api/v1/actions`

### API Güvenliği
- [ ] JWT token authentication implementasyonu
- [ ] Rate limiting middleware'i
- [ ] API key authentication (opsiyonel)
- [ ] CORS headers ayarlama

### Dokümantasyon
- [ ] Swagger/OpenAPI dokümantasyonu oluştur
- [ ] API endpoint'leri için test script'leri yaz

## 4. Test Altyapısı

### Unit Test Kurulumu
- [ ] PHPUnit framework'ü kur ve yapılandır
- [ ] `tests/` dizini oluştur
- [ ] Test database konfigürasyonu ayarla
- [ ] Test helper fonksiyonları yaz

### Controller Testleri
- [ ] BaseController test sınıfı oluştur
- [ ] Authentication controller testleri
- [ ] CRUD operation testleri
- [ ] Error handling testleri

### Model Testleri
- [ ] Database model testleri
- [ ] Entity validation testleri
- [ ] Business logic testleri
- [ ] Data transformation testleri

### Integration Testleri
- [ ] Full request/response cycle testleri
- [ ] Database integration testleri
- [ ] External service mock'ları
- [ ] API endpoint testleri

### CI/CD Entegrasyonu
- [ ] GitHub Actions workflow'u oluştur
- [ ] Automated testing pipeline'ı
- [ ] Code coverage reporting
- [ ] Quality gate'ler ayarla

## 5. Performans ve Güvenlik İyileştirmeleri

### Performans Optimizasyonları
- [ ] Query optimization ve indexing
- [ ] Caching layer improvement (Redis/Memcached)
- [ ] Asset optimization (compression, CDN)
- [ ] Database connection pooling

### Güvenlik Güçlendirmeleri
- [ ] Input validation enhancement
- [ ] XSS protection improvements
- [ ] CSRF protection review
- [ ] Security headers review (CSP, HSTS)

## 6. Kod Kalitesi ve Bakım

### Code Quality Tools
- [ ] PHPStan static analysis entegrasyonu
- [ ] PHPCS code style checking
- [ ] Pre-commit hooks kurulumu
- [ ] Code documentation improvement

### Refactoring
- [ ] Duplicate code elimination
- [ ] Long method refactoring
- [ ] SOLID principles application
- [ ] Design pattern improvements

## Uygulama Öncelikleri

### Faz 1 (Kritik) - 2-3 hafta
- [ ] Namespace tutarlılığı
- [ ] Temel API endpoint'leri
- [ ] Unit test kurulumu
- [ ] Güvenlik review

### Faz 2 (Önemli) - 4-6 hafta
- [ ] Dependency injection
- [ ] Frontend build sistemi
- [ ] Integration testleri
- [ ] Performans optimizasyonları

### Faz 3 (İyileştirme) - 6-8 hafta
- [ ] Advanced API features
- [ ] Full frontend modernizasyonu
- [ ] CI/CD pipeline
- [ ] Code quality tools

## Notlar
- Her iyileştirme adımında mevcut işlevselliğin bozulmaması kritik
- Değişiklikler küçük parçalara bölünerek uygulanmalı
- Her adım için test coverage sağlanmalı
- Dokümantasyon güncel tutulmalı
