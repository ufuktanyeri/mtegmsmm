# Conversations Archive

Bu dizin Claude Code ile yapılan konuşmaları ve önemli dokümanları organize etmek için kullanılır.

## Dizin Yapısı

```
conversations/
├── README.md                           # Bu dosya
└── 2024-09/                           # Aylık arşiv (Eylül 2024)
    ├── system-analysis.md              # Sistem analizi (Controller/View/Rol analizi)
    ├── mvc-modernization-context.md    # MVC modernizasyon context'i
    ├── mvc-modernization-plan.md       # MVC modernizasyon detaylı planı
    ├── bootstrap-migration-summary.md  # Bootstrap 5.3.6 migration özeti
    ├── claude-session-memory.md        # Claude session hafızası (CLAUDE.md)
    ├── claude-helper-script.php        # Claude helper script
    ├── claude-tool-guide.md           # Claude kullanım rehberi
    ├── project-architecture.md        # Proje mimarisi dokümantasyonu
    ├── portal-guide.md                # Portal kullanım rehberi
    └── deployment-guide.md            # Production deployment rehberi
```

## Dosya İçerikleri

### Ana Konuşma Kayıtları
- **system-analysis.md**: 19 Controller, 3 render sistemi, rol karmaşası analizi
- **mvc-modernization-plan.md**: Tablo-kılavuz eşleştirmesi ve MVC yeniden yapılandırması
- **bootstrap-migration-summary.md**: Bootstrap 5.3.6 güncelleme ve dark mode implementasyonu

### Context & Session Dosyaları
- **claude-session-memory.md**: En güncel proje durumu ve aktif görevler
- **mvc-modernization-context.md**: MVC modernizasyonu için aktif task context'i
- **claude-helper-script.php**: Context recovery ve session yönetimi için PHP script

### Teknik Dokümantasyon
- **project-architecture.md**: Proje yapısı ve mimari bilgileri
- **portal-guide.md**: MTEGM SMM Portal kullanım rehberi
- **deployment-guide.md**: Production ortamına deploy rehberi

## Aktif Durumlar (Son Güncelleme: 2024-09-25)

### ❌ Çözülemeyen Sorunlar
- **Dashboard Layout**: Header içeriği kapatıyor, 200+ satır gereksiz CSS
- **Token İsrafı**: Test etmeden "tamam" deniliyor, kullanıcı onayı beklenmeli

### ✅ Tamamlanan İşlemler
- Bootstrap 5.3.6 migration (views dizinindeki dosyalarda)
- Test menüsü development-only yapıldı (localhost kontrolü)
- Conversations dizini organize edildi
- Türkçe karakter encoding sorunu çözüldü

### 🔄 Devam Eden İşler
- MVC Modernization Phase 1
- Dashboard layout sorunlarının çözümü

## Kullanım

### Claude Code Oturumu Başlatma
```
"CLAUDE.md dosyasını oku, dashboard.php layout sorununu çözelim.
Header içeriği kapatıyor, önceki denemeler başarısız oldu."
```

### Context Recovery
```php
php _dev/scripts/claude-helper.php context
```

## Dosya Adlandırma Kuralları

- **Tarih formatı**: `YYYY-MM` (örn: 2024-09)
- **Dosya adları**: kebab-case (örn: `system-analysis.md`)
- **Konular**: açıklayıcı ve kısa isimlendirme
- **PHP dosyaları**: `.php` uzantısı korunur