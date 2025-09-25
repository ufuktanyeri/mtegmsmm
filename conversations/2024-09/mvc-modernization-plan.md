# SMM Portal MVC Modernizasyonu - Claude Code Talimatı

## TK ANALİZİ (Tablo-Kılavuz Eşleştirmesi)

### Veritabanı Tablo Yapısı ve İş Akışı

#### 1. HİYERARŞİK YAPI
```
Dayanaklar → Amaçlar → Hedefler → Göstergeler → Faaliyetler
```

| Seviye | Tablo | Kılavuzdaki Karşılık |
|--------|-------|---------------------|
| **Dayanaklar** | `documentstrategies` | Politika Belgesi Stratejileri |
| | `regulations` | Özel Ek Dayanaklar |
| | `coveregulations` | SMM Yönergeleri |
| **Amaçlar** | `aims` | Amaç ekleme/düzenleme ekranları |
| **Hedefler** | `objectives` | Hedef ekleme/düzenleme ekranları |
| **Göstergeler** | `indicators` | Performans göstergeleri ekranları |
| **Faaliyetler** | `actions` | Faaliyet ekleme/takvim ekranları |

#### 2. KULLANICI ROLLERİ VE EKRANLAR

| Rol | Erişebileceği Ekranlar | İlgili Tablolar |
|-----|------------------------|-----------------|
| **Yönetici** | Merkezler, Kullanıcılar, Mevzuat, Raporlar, Takvim, Loglar | Tüm tablolar |
| **SMM Koordinatör** | Amaçlar, Mevzuat, Takvim, Faaliyet Raporu | `aims`, `objectives`, `actions`, `indicators` |
| **Guest** | Sadece görüntüleme | Read-only erişim |

#### 3. EKRAN-TABLO EŞLEŞTİRMESİ

| Ekran | İlgili Tablolar | CRUD İşlemleri |
|-------|-----------------|----------------|
| **Mevzuat** | `regulations`, `documentstrategies`, `coveregulations` | Create, Read, Update, Delete |
| **Amaçlar** | `aims`, `aim_regulations` | Create, Read, Update, Delete |
| **Hedefler** | `objectives`, `aims_objectives` | Create, Read, Update, Delete |
| **Performans Göstergeleri** | `indicators`, `indicator_types`, `objectives_indicators` | Create, Read, Update |
| **Faaliyetler** | `actions` | Create, Read, Update, Delete |
| **Faaliyet Takvimi** | `actions` | Read (Calendar View) |
| **Eylem Planı Raporu** | Tüm tablolar | Read (Report Generation) |
| **SMM Merkezleri** | `coves`, `cove_fields`, `fields` | Create, Read, Update |
| **Kullanıcılar** | `users`, `user_roles`, `roles`, `permissions` | Create, Read, Update |
| **Giriş/İşlem Kayıtları** | `logs`, `detailedlogs`, `security_logs` | Read |

## GÖREV: TK Analiz Tabanlı MVC Yeniden Yapılandırma

### AŞAMA 1: ESKİ PRODUCTION ANALİZİ
**Dizin:** `C:\Users\proje-bap\Desktop\ayse.hoca\smmeylem`

#### 1.1 Analiz Edilecek Alanlar:
```bash
# Dosya yapısını tara
dir /s /b *.php > file_list.txt

# Her PHP dosyasında tablo kullanımını tespit et
# Aranacak pattern'ler:
# - mysql_query, mysqli_query
# - INSERT INTO, UPDATE, DELETE FROM, SELECT
# - Tablo isimleri: aims, objectives, actions, vb.
```

#### 1.2 Tespit Edilecek Sorunlar:
- [ ] Direkt SQL sorguları (SQL injection riski)
- [ ] Şifrelenmemiş bağlantılar
- [ ] Session yönetimi eksiklikleri
- [ ] Input validation eksiklikleri
- [ ] MEB güvenlik standartlarına uyumsuzluklar

### AŞAMA 2: YENİ PRODUCTION MİMARİSİ
**Dizin:** `C:\xampp\htdocs\mtegmsmm`

```
/mtegmsmm/
├── /app/
│   ├── /Controllers/
│   │   ├── /Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── PasswordController.php
│   │   │   └── SessionController.php
│   │   ├── /Strategic/
│   │   │   ├── AimController.php
│   │   │   ├── ObjectiveController.php
│   │   │   └── RegulationController.php
│   │   ├── /Performance/
│   │   │   ├── IndicatorController.php
│   │   │   └── ActionController.php
│   │   ├── /Admin/
│   │   │   ├── CoveController.php
│   │   │   ├── UserController.php
│   │   │   └── ReportController.php
│   │   └── /Api/
│   │       └── RestController.php
│   │
│   ├── /Models/
│   │   ├── /Base/
│   │   │   ├── Model.php (PDO wrapper)
│   │   │   └── MEBSecurityTrait.php
│   │   ├── /Entities/
│   │   │   ├── User.php
│   │   │   ├── Cove.php
│   │   │   ├── Aim.php
│   │   │   ├── Objective.php
│   │   │   ├── Indicator.php
│   │   │   └── Action.php
│   │   └── /Repositories/
│   │       ├── UserRepository.php
│   │       ├── StrategicRepository.php
│   │       └── PerformanceRepository.php
│   │
│   ├── /Views/
│   │   ├── /layouts/
│   │   │   ├── admin.blade.php
│   │   │   └── coordinator.blade.php
│   │   ├── /coordinator/
│   │   │   ├── aims/
│   │   │   ├── objectives/
│   │   │   ├── indicators/
│   │   │   └── actions/
│   │   ├── /admin/
│   │   │   ├── coves/
│   │   │   ├── users/
│   │   │   └── reports/
│   │   └── /components/
│   │       ├── calendar.blade.php
│   │       └── charts.blade.php
│   │
│   ├── /Services/
│   │   ├── StrategicPlanningService.php
│   │   ├── PerformanceTrackingService.php
│   │   ├── ReportingService.php
│   │   └── MEBSecurityService.php
│   │
│   └── /Middleware/
│       ├── Authentication.php
│       ├── RoleAuthorization.php
│       ├── IPFilter.php
│       └── AuditLogger.php
│
├── /config/
│   ├── app.php
│   ├── database.php
│   ├── security.php (MEB 14114814)
│   └── routes.php
│
├── /database/
│   ├── /migrations/
│   └── /seeds/
│
├── /public/
│   ├── index.php
│   ├── .htaccess
│   └── /assets/
│       ├── /css/
│       ├── /js/
│       └── /images/
│
├── /storage/
│   ├── /logs/
│   ├── /sessions/
│   └── /uploads/
│
├── /tests/
│   ├── /Unit/
│   └── /Feature/
│
├── composer.json
├── .env.example
└── README.md
```

### AŞAMA 3: MODÜL BAZLI GEÇİŞ PLANI

#### HAFTA 1: Temel Altyapı
```php
// 1. Composer kurulumu
composer require vlucas/phpdotenv
composer require respect/validation
composer require monolog/monolog

// 2. Database sınıfı (PDO)
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $this->pdo = new PDO(
            'mysql:host='.DB_HOST.';dbname='.DB_NAME,
            DB_USER, 
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}

// 3. MEB Security trait
trait MEBSecurity {
    public function validatePassword($password) {
        // En az 8 karakter
        // Büyük/küçük harf, rakam, özel karakter
        // Son 5 şifre kontrolü
    }
    
    public function checkSessionTimeout() {
        // 20 dakika inaktivite
    }
    
    public function logSecurityEvent($action, $userId) {
        // security_logs tablosuna kayıt
    }
}
```

#### HAFTA 2: Authentication & Authorization
```php
// Kullanıcı girişi
class AuthController {
    public function login(Request $request) {
        // 1. Input validation
        // 2. User check (users table)
        // 3. Password verify
        // 4. Failed login count check
        // 5. Session create (user_sessions)
        // 6. Log entry (logs, security_logs)
    }
    
    public function checkPermission($userId, $permission) {
        // user_roles -> role_permissions -> permissions
    }
}
```

#### HAFTA 3: Stratejik Planlama Modülü
```php
// Hiyerarşik veri yönetimi
class StrategicPlanningService {
    public function createAim($data, $coveId) {
        // 1. aims tablosuna kayıt
        // 2. aim_regulations ilişkileri
        // 3. detailedlogs kaydı
    }
    
    public function getHierarchy($coveId) {
        // aims -> objectives -> indicators -> actions
        // Nested array döndür
    }
}
```

#### HAFTA 4: Performans Takip Modülü
```php
class PerformanceTrackingService {
    public function updateIndicator($indicatorId, $completed) {
        // 1. indicators.completed güncelle
        // 2. Yüzde hesapla
        // 3. objectives durumunu güncelle
    }
    
    public function getActionCalendar($coveId, $month) {
        // actions tablosundan takvim verisi
        // Periyodik faaliyetleri hesapla
    }
}
```

#### HAFTA 5: Raporlama ve Widget Modülü
```php
// Raporlama servisi - Widget desteğiyle
class ReportingService {
    private $widgetFactory;
    
    public function generateActionPlan($coveId, $year) {
        $widgets = [
            'performanceGauge' => $this->widgetFactory->create('gauge'),
            'progressBars' => $this->widgetFactory->create('progress'),
            'hierarchyTree' => $this->widgetFactory->create('tree'),
            'calendar' => $this->widgetFactory->create('calendar')
        ];
        
        return $this->renderReport($widgets, $coveId, $year);
    }
    
    public function getDashboardStats($coveId) {
        return [
            'indicators' => $this->getIndicatorWidgets($coveId),
            'actions' => $this->getActionWidgets($coveId),
            'calendar' => $this->getCalendarWidget($coveId),
            'comparison' => $this->getComparisonWidget()
        ];
    }
}

// Widget Factory
class WidgetFactory {
    public function create($type, $data = []) {
        return match($type) {
            'gauge' => new PerformanceGaugeWidget($data),
            'progress' => new ProgressBarWidget($data),
            'tree' => new HierarchyTreeWidget($data),
            'calendar' => new CalendarWidget($data),
            'heatmap' => new HeatmapWidget($data),
            'chart' => new ChartWidget($data)
        };
    }
}
```

### AŞAMA 4: GÜVENLİK İMPLEMENTASYONU (MEB 14114814)

```php
// config/security.php
return [
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special' => true,
        'expires_days' => 90,
        'history_count' => 5
    ],
    'session' => [
        'timeout_minutes' => 20,
        'regenerate_id' => true
    ],
    'login' => [
        'max_attempts' => 5,
        'lockout_minutes' => 30
    ]
];
```

### AŞAMA 5: RAPORLAMA WIDGET'LARI VE COMPONENT'LER

#### Dashboard Widget'ları
```php
// app/Widgets/Dashboard/
├── PerformanceGaugeWidget.php      // Gösterge tamamlanma oranı (circular gauge)
├── ActionProgressWidget.php        // Faaliyet ilerleme çubukları
├── SMMComparisonWidget.php        // SMM'ler arası karşılaştırma
├── TimelineWidget.php              // Zaman çizelgesi görünümü
├── StatCardWidget.php              // İstatistik kartları
└── HeatmapWidget.php               // Performans ısı haritası

// Widget örneği
class PerformanceGaugeWidget extends BaseWidget {
    public function render($indicatorId) {
        $data = $this->getIndicatorProgress($indicatorId);
        return view('widgets.gauge', [
            'completed' => $data->completed,
            'target' => $data->target,
            'percentage' => ($data->completed / $data->target) * 100,
            'color' => $this->getColorByPercentage($percentage)
        ]);
    }
}
```

#### Raporlama Component'leri
```javascript
// resources/js/components/Reports/
├── AimHierarchyTree.vue           // Amaç-Hedef-Faaliyet ağaç görünümü
├── IndicatorChart.vue              // Chart.js grafikleri
├── ActionCalendar.vue              // FullCalendar entegrasyonu (mevcut)
├── SMMDashboard.vue                // Ana dashboard
├── ExportPanel.vue                 // PDF/Excel export panel
└── FilterPanel.vue                 // Dinamik filtreleme

// Vue component örneği
<template>
  <div class="indicator-chart-widget">
    <canvas ref="chartCanvas"></canvas>
    <div class="chart-legend">
      <span class="target">Hedef: {{ target }}</span>
      <span class="completed">Gerçekleşen: {{ completed }}</span>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js';

export default {
  props: ['indicatorId', 'chartType'],
  mounted() {
    this.renderChart();
  },
  methods: {
    renderChart() {
      new Chart(this.$refs.chartCanvas, {
        type: this.chartType || 'doughnut',
        data: this.chartData,
        options: this.chartOptions
      });
    }
  }
}
</script>
```

#### Özel Rapor Şablonları
```php
// app/Reports/Templates/
├── ActionPlanReport.php            // Eylem planı raporu
├── PerformanceReport.php           // Performans raporu
├── ComparativeAnalysis.php         // Karşılaştırmalı analiz
└── ExecutiveSummary.php            // Yönetici özeti

class ActionPlanReport extends BaseReport {
    protected $widgets = [
        'header' => HeaderWidget::class,
        'summary' => SummaryStatsWidget::class,
        'hierarchy' => AimHierarchyWidget::class,
        'timeline' => TimelineWidget::class,
        'indicators' => IndicatorTableWidget::class,
        'calendar' => CalendarWidget::class
    ];
    
    public function generate($coveId, $year) {
        $data = $this->collectData($coveId, $year);
        return $this->renderWithWidgets($data);
    }
}
```

### AŞAMA 6: TAKVİM ENTEGRASYONU (Mevcut Sistem)

```javascript
// Mevcut takvim sistemini modern yapıya entegre et
// resources/js/components/Calendar/ActionCalendar.vue

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

export default {
  mounted() {
    this.initCalendar();
  },
  methods: {
    initCalendar() {
      const calendar = new Calendar(this.$refs.calendar, {
        plugins: [dayGridPlugin, interactionPlugin],
        locale: 'tr',
        events: this.fetchEvents,
        eventClick: this.handleEventClick,
        eventColor: this.getEventColor
      });
      calendar.render();
    },
    
    fetchEvents(info, successCallback) {
      // actions tablosundan periyodik ve tek seferlik faaliyetleri çek
      axios.get('/api/actions/calendar', {
        params: {
          start: info.start,
          end: info.end,
          coveId: this.coveId
        }
      }).then(response => {
        const events = this.processPeriodicEvents(response.data);
        successCallback(events);
      });
    },
    
    processPeriodicEvents(actions) {
      // Periyodik faaliyetleri hesapla
      return actions.flatMap(action => {
        if (action.periodic) {
          return this.generateRecurringEvents(action);
        }
        return this.createSingleEvent(action);
      });
    }
  }
}
```

### AŞAMA 7: ROUTE YAPISI (Widget'larla güncellenmiş)

```php
// config/routes.php

// Authentication
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

// Dashboard & Widgets
Route::get('/dashboard', 'DashboardController@index');
Route::get('/widgets/{widget}/data', 'WidgetController@getData');

// Coordinator Routes (Middleware: auth, role:coordinator)
Route::group(['middleware' => ['auth', 'role:coordinator']], function() {
    // Amaçlar
    Route::get('/aims', 'Strategic\AimController@index');
    Route::post('/aims', 'Strategic\AimController@store');
    Route::put('/aims/{id}', 'Strategic\AimController@update');
    
    // Hedefler
    Route::get('/objectives', 'Strategic\ObjectiveController@index');
    Route::post('/objectives', 'Strategic\ObjectiveController@store');
    
    // Faaliyetler
    Route::get('/actions', 'Performance\ActionController@index');
    Route::post('/actions', 'Performance\ActionController@store');
    Route::get('/actions/calendar', 'Performance\ActionController@calendar');
    
    // Raporlar
    Route::get('/reports/action-plan', 'ReportController@actionPlan');
    Route::get('/reports/performance', 'ReportController@performance');
    Route::post('/reports/export', 'ReportController@export');
});

// Admin Routes (Middleware: auth, role:admin)
Route::group(['middleware' => ['auth', 'role:admin']], function() {
    Route::resource('/coves', 'Admin\CoveController');
    Route::resource('/users', 'Admin\UserController');
    Route::get('/reports/comparative', 'Admin\ReportController@comparative');
    Route::get('/reports/executive', 'Admin\ReportController@executive');
    Route::get('/logs', 'Admin\LogController@index');
});

// API Routes for Widgets
Route::prefix('api')->group(function() {
    Route::get('/widgets/gauge/{indicatorId}', 'Api\WidgetController@gauge');
    Route::get('/widgets/progress/{objectiveId}', 'Api\WidgetController@progress');
    Route::get('/widgets/comparison', 'Api\WidgetController@comparison');
    Route::get('/widgets/heatmap/{coveId}', 'Api\WidgetController@heatmap');
});
```

### DASHBOARD VE WIDGET TASARIMLARI

### Dashboard Layout Örneği
```html
<!-- resources/views/dashboard/coordinator.blade.php -->
<div class="dashboard-container">
    <!-- Üst İstatistik Kartları -->
    <div class="stat-cards-row">
        @widget('StatCard', ['type' => 'aims', 'coveId' => $coveId])
        @widget('StatCard', ['type' => 'objectives', 'coveId' => $coveId])
        @widget('StatCard', ['type' => 'actions', 'coveId' => $coveId])
        @widget('StatCard', ['type' => 'completion', 'coveId' => $coveId])
    </div>
    
    <!-- Ana Göstergeler -->
    <div class="main-widgets">
        <div class="col-md-8">
            <!-- Hiyerarşi Ağacı -->
            @widget('HierarchyTree', ['coveId' => $coveId])
            
            <!-- Performans Grafikleri -->
            <div class="charts-row">
                @widget('PerformanceChart', ['type' => 'bar', 'coveId' => $coveId])
                @widget('ProgressGauge', ['indicators' => $topIndicators])
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Yaklaşan Faaliyetler -->
            @widget('UpcomingActions', ['days' => 7])
            
            <!-- Takvim Mini -->
            @widget('CalendarMini', ['coveId' => $coveId])
            
            <!-- Hızlı Erişim -->
            @widget('QuickActions')
        </div>
    </div>
    
    <!-- Alt Detay Panelleri -->
    <div class="detail-panels">
        @widget('ActionTimeline', ['limit' => 10])
        @widget('IndicatorHeatmap', ['coveId' => $coveId])
    </div>
</div>
```

### Widget CSS Framework
```css
/* resources/css/widgets.css */
.widget {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
}

.gauge-widget {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 300px;
}

.progress-bar-widget {
    .progress-item {
        margin-bottom: 15px;
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .progress-bar {
            height: 10px;
            border-radius: 5px;
            background: #f0f0f0;
            overflow: hidden;
            
            .progress-fill {
                height: 100%;
                transition: width 0.5s ease;
                background: linear-gradient(90deg, #4CAF50, #8BC34A);
            }
        }
    }
}
```

### React/Vue Widget Örnekleri
```javascript
// resources/js/widgets/PerformanceGauge.jsx
import React, { useEffect, useRef } from 'react';
import * as d3 from 'd3';

export default function PerformanceGauge({ value, target, title }) {
    const svgRef = useRef();
    
    useEffect(() => {
        const percentage = (value / target) * 100;
        
        const svg = d3.select(svgRef.current);
        const width = 300;
        const height = 200;
        const radius = Math.min(width, height) / 2;
        
        // Gauge arc oluştur
        const arc = d3.arc()
            .innerRadius(radius - 30)
            .outerRadius(radius)
            .startAngle(-Math.PI / 2)
            .endAngle(Math.PI / 2);
        
        // Renk skalası
        const color = d3.scaleLinear()
            .domain([0, 50, 100])
            .range(['#f44336', '#ff9800', '#4caf50']);
        
        svg.append('path')
            .attr('d', arc)
            .attr('fill', color(percentage))
            .attr('transform', `translate(${width/2}, ${height/2})`);
        
        // Değer metni
        svg.append('text')
            .attr('x', width/2)
            .attr('y', height/2)
            .attr('text-anchor', 'middle')
            .attr('font-size', '32px')
            .attr('font-weight', 'bold')
            .text(`${percentage.toFixed(1)}%`);
            
    }, [value, target]);
    
    return (
        <div className="gauge-widget">
            <h4>{title}</h4>
            <svg ref={svgRef} width="300" height="200"></svg>
            <div className="gauge-details">
                <span>Hedef: {target}</span>
                <span>Gerçekleşen: {value}</span>
            </div>
        </div>
    );
}
```

### Takvim Widget'ı (Mevcut Sisteme Ek)
```javascript
// resources/js/widgets/CalendarWidget.vue
<template>
    <div class="calendar-widget">
        <div class="widget-header">
            <h3>Faaliyet Takvimi</h3>
            <div class="calendar-filters">
                <select v-model="filterStatus">
                    <option value="">Tümü</option>
                    <option value="planned">Planlandı</option>
                    <option value="ongoing">Devam Ediyor</option>
                    <option value="completed">Tamamlandı</option>
                </select>
            </div>
        </div>
        
        <FullCalendar
            :options="calendarOptions"
            ref="calendar"
        />
        
        <!-- Faaliyet Detay Modal -->
        <ActionDetailModal 
            v-if="selectedAction"
            :action="selectedAction"
            @close="selectedAction = null"
            @update="handleActionUpdate"
        />
    </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import trLocale from '@fullcalendar/core/locales/tr';

export default {
    components: { FullCalendar },
    props: ['coveId'],
    data() {
        return {
            selectedAction: null,
            filterStatus: '',
            calendarOptions: {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                locale: trLocale,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: this.fetchEvents,
                eventClick: this.handleEventClick,
                eventDidMount: this.handleEventMount,
                eventColor: '#3498db'
            }
        };
    },
    methods: {
        fetchEvents(info, successCallback, failureCallback) {
            axios.get('/api/actions/calendar', {
                params: {
                    start: info.start.toISOString(),
                    end: info.end.toISOString(),
                    coveId: this.coveId,
                    status: this.filterStatus
                }
            }).then(response => {
                const events = this.processEvents(response.data);
                successCallback(events);
            }).catch(failureCallback);
        },
        
        processEvents(actions) {
            return actions.map(action => {
                const event = {
                    id: action.id,
                    title: action.actionTitle,
                    start: action.dateStart,
                    end: action.dateEnd,
                    extendedProps: {
                        description: action.actionDesc,
                        responsible: action.actionResponsible,
                        status: action.actionStatus,
                        periodic: action.periodic,
                        objectiveId: action.objectiveId
                    }
                };
                
                // Periyodik olayları işle
                if (action.periodic) {
                    event.rrule = this.generateRRule(action);
                    event.duration = { days: action.periodDuration };
                }
                
                // Duruma göre renk belirle
                event.backgroundColor = this.getStatusColor(action.actionStatus);
                
                return event;
            });
        },
        
        getStatusColor(status) {
            const colors = {
                'planned': '#3498db',
                'ongoing': '#f39c12',
                'completed': '#27ae60',
                'cancelled': '#e74c3c',
                'delayed': '#9b59b6'
            };
            return colors[status] || '#95a5a6';
        },
        
        generateRRule(action) {
            // Periyodik kuralları oluştur
            const rules = {
                1: 'FREQ=DAILY',
                2: 'FREQ=WEEKLY',
                3: 'FREQ=MONTHLY'
            };
            return {
                freq: rules[action.periodType],
                interval: action.periodTime,
                dtstart: action.dateStart
            };
        },
        
        handleEventClick(info) {
            this.selectedAction = {
                ...info.event.extendedProps,
                id: info.event.id,
                title: info.event.title,
                start: info.event.start,
                end: info.event.end
            };
        },
        
        handleEventMount(info) {
            // Tooltip ekle
            tippy(info.el, {
                content: `
                    <strong>${info.event.title}</strong><br>
                    Sorumlu: ${info.event.extendedProps.responsible}<br>
                    Durum: ${info.event.extendedProps.status}
                `,
                allowHTML: true
            });
        }
    }
};
</script>
```

## ÖNCELİKLİ EYLEMLER

1. **İLK ADIM:** Eski sistemdeki `login.php`, `dashboard.php` dosyalarını incele
2. **İKİNCİ ADIM:** Veritabanı bağlantı dosyasını modern PDO'ya dönüştür
3. **ÜÇÜNCÜ ADIM:** Authentication sistemini MEB standartlarında yeniden yaz
4. **DÖRDÜNCÜ ADIM:** Her modül için API endpoint'leri oluştur
5. **BEŞİNCİ ADIM:** Frontend'i modern JavaScript framework'e taşı

## BAŞARI KRİTERLERİ

- [ ] PHP 8.0+ kullanımı
- [ ] PDO ve prepared statements
- [ ] MEB 14114814 güvenlik uyumluluğu
- [ ] %100 SQL injection koruması
- [ ] Rol bazlı yetkilendirme
- [ ] Audit log sistemi
- [ ] Unit test coverage > 70%
- [ ] API documentation
- [ ] Responsive tasarım
- [ ] Performance optimization (< 2 saniye sayfa yüklenme)