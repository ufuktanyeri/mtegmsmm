# mtegmsmm
MTEGM SMM Portal

## 📋 Proje Genel Bakış
MTEGM (Meteoroloji ve Afet Yönetimi) Sosyal Medya Yönetimi portalı projesidir.

## 🔍 Dokümantasyon Takip Sistemi

Bu projede dokümantasyon süreklilik takibi ve uyarı sistemi mevcuttur. Sistem şu belgeleri izler:

- `_dev/docs/smm-mvc-modernization-plan.md` - MVC modernizasyon planı
- `_dev/docs/smm-complete-implementation-plan.md` - Tam uygulama planı

### 🚀 Hızlı Başlangıç

#### Manuel Takip
```bash
# Dokümantasyon durumu kontrolü
./_dev/docs/track-docs.sh

# Dashboard oluşturma
python3 _dev/docs/generate-dashboard.py
```

#### Otomatik Takip
- GitHub Actions her gün otomatik olarak çalışır
- Dokümantasyon değişikliklerinde otomatik kontrol
- Kritik durumlar için otomatik issue oluşturma

### 📊 Dashboard
Dokümantasyon durumunu görüntülemek için: `_dev/docs/dashboard.html`

### 🔧 Sistem Özellikleri
- ✅ Dosya varlık kontrolü
- 📅 Güncelleme tarihi takibi
- 📈 Görev ilerleme analizi
- 🚨 Otomatik uyarı sistemi
- 📊 Web tabanlı dashboard

### 📖 Detaylı Dokümantasyon
Sistem hakkında detaylar için: `_dev/docs/README.md`

---
**Son Güncelleme**: 2024-09-22
