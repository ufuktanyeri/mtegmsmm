# SMM Documentation Tracking System

Bu sistem, MTEGM SMM Portal projesi için belgelerin süreklilik takibi ve uyarı sistemi sağlar.

## Sistem Özellikleri

### 📋 İzlenen Belgeler
- `_dev/docs/smm-mvc-modernization-plan.md` - MVC modernizasyon planı
- `_dev/docs/smm-complete-implementation-plan.md` - Tam uygulama planı

### 🔍 Takip Edilen Durumlar
- Belge varlığı kontrolü
- Son güncelleme tarihi takibi (7 gün uyarısı)
- Görev ilerleme durumu analizi
- Otomatik uyarı sistemi

### 🚨 Uyarı Türleri
- **MISSING_FILE**: Gerekli belge bulunamadı
- **STALE_FILE**: Belge 7 günden uzun süredir güncellenmemiş
- **LOW_PROGRESS**: Görev tamamlama oranı %25'in altında

## Kullanım

### Manuel Çalıştırma
```bash
cd _dev/docs
./track-docs.sh
```

### Otomatik Çalıştırma
GitHub Actions workflow'u şu durumlarda otomatik çalışır:
- Her gün saat 09:00'da (UTC)
- `_dev/docs/` dizinindeki dosyalar değiştiğinde
- Pull request oluşturulduğunda
- Manuel tetikleme ile

## Çıktılar

### Log Dosyası
Tüm takip aktiviteleri `_dev/docs/.tracking.log` dosyasında kaydedilir.

### Uyarı Dosyası
Uyarılar JSON formatında `_dev/docs/.alerts.json` dosyasında saklanır.

### GitHub Issues
Kritik uyarılar durumunda otomatik olarak GitHub issue'su oluşturulur.

## Raporlar

Sistem her çalıştırıldığında aşağıdaki bilgileri içeren durum raporu oluşturur:
- Dosya durumları ve son güncelleme tarihleri
- Son uyarılar
- Görev tamamlama oranları

## Konfigürasyon

### Uyarı Eşikleri
- **Dosya yaşı uyarısı**: 7 gün
- **Düşük ilerleme uyarısı**: %25'in altında tamamlama
- **Log saklama süresi**: 30 gün

### Özelleştirme
`track-docs.sh` dosyasındaki konfigürasyon değişkenleri ile özelleştirme yapılabilir:
```bash
max_age_days=7          # Dosya yaşı uyarı eşiği
completion_threshold=25 # İlerleme uyarı eşiği (%)
```

## Sorun Giderme

### Eksik Bağımlılıklar
Sistem `jq` aracını gerektirir:
```bash
# Ubuntu/Debian
sudo apt-get install jq

# macOS
brew install jq
```

### İzin Sorunları
Script çalıştırma izni gerektirir:
```bash
chmod +x _dev/docs/track-docs.sh
```

## Katkıda Bulunma

Bu sistemin geliştirilmesi için:
1. Yeni kontrol türleri ekleyebilirsiniz
2. Uyarı eşiklerini optimize edebilirsiniz
3. Rapor formatını geliştirebilirsiniz
4. Entegrasyonlar ekleyebilirsiniz (Slack, Teams, vb.)

---
**Son Güncelleme**: 2024-09-22
**Versiyon**: 1.0
**Bakımcı**: MTEGM SMM Geliştirme Ekibi