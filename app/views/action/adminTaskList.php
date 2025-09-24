<?php 
ob_start();
$title = 'Admin Görev Listesi';
$page_title = 'Tüm Merkezlerin Görevleri';
$breadcrumb = [
    ['name' => 'Ana Sayfa', 'url' => 'index.php?url=home'],
    ['name' => 'Admin Panel', 'url' => 'index.php?url=admin'],
    ['name' => 'Görev Listesi', 'url' => '']
];

// Filtre parametreleri (controller'dan geliyorsa onu kullan, yoksa güvenli oku)
if(!isset($filter)) {
    $filter = isset($_GET['filter']) ? preg_replace('/[^a-z]/','', $_GET['filter']) : 'all';
}
if(!isset($coveId)) {
    $coveId = isset($_GET['coveId']) ? (int)$_GET['coveId'] : null;
}

// Başlık ve ikonlar
$filterTitles = [
    'all' => ['title' => 'Tüm Görevler', 'icon' => 'fas fa-list', 'color' => 'primary'],
    'overdue' => ['title' => 'Geciken Görevler', 'icon' => 'fas fa-exclamation-triangle', 'color' => 'danger'],
    'urgent' => ['title' => 'Acil Görevler', 'icon' => 'fas fa-fire', 'color' => 'warning'],
    'upcoming' => ['title' => 'Yaklaşan Görevler', 'icon' => 'fas fa-clock', 'color' => 'info'],
    'completed' => ['title' => 'Tamamlanan Görevler', 'icon' => 'fas fa-check-circle', 'color' => 'success']
];

$currentFilter = $filterTitles[$filter] ?? $filterTitles['all'];
?>

<!-- Sayfa Başlığı -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-<?= $currentFilter['color'] ?>">
            <div class="card-body text-white">
                <div class="d-flex align-items-center">
                    <i class="<?= $currentFilter['icon'] ?> fa-3x me-3 opacity-75"></i>
                    <div>
                        <h3 class="mb-1"><?= $currentFilter['title'] ?></h3>
                        <p class="mb-0 opacity-75">Sistem genelindeki tüm merkezlerin görevlerini yönetin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtre Butonları -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-center">
                    <div class="btn-group" role="group">
                        <a href="?url=action/adminTaskList&filter=all" 
                           class="btn btn-<?= $filter === 'all' ? 'primary' : 'outline-primary' ?>">
                            <i class="fas fa-list"></i> Tümü
                            <?php if(isset($allCount)): ?>
                                <span class="badge badge-light ms-1"><?= $allCount ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="?url=action/adminTaskList&filter=overdue" 
                           class="btn btn-<?= $filter === 'overdue' ? 'danger' : 'outline-danger' ?>">
                            <i class="fas fa-exclamation-triangle"></i> Geciken
                            <?php if(isset($overdueCount) && $overdueCount > 0): ?>
                                <span class="badge badge-light ms-1"><?= $overdueCount ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="?url=action/adminTaskList&filter=urgent" 
                           class="btn btn-<?= $filter === 'urgent' ? 'warning' : 'outline-warning' ?>">
                            <i class="fas fa-fire"></i> Acil
                            <?php if(isset($urgentCount) && $urgentCount > 0): ?>
                                <span class="badge badge-light ms-1"><?= $urgentCount ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="?url=action/adminTaskList&filter=upcoming" 
                           class="btn btn-<?= $filter === 'upcoming' ? 'info' : 'outline-info' ?>">
                            <i class="fas fa-clock"></i> Yaklaşan
                            <?php if(isset($upcomingCount) && $upcomingCount > 0): ?>
                                <span class="badge badge-light ms-1"><?= $upcomingCount ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="?url=action/adminTaskList&filter=completed" 
                           class="btn btn-<?= $filter === 'completed' ? 'success' : 'outline-success' ?>">
                            <i class="fas fa-check-circle"></i> Tamamlanan
                            <?php if(isset($completedCount) && $completedCount > 0): ?>
                                <span class="badge badge-light ms-1"><?= $completedCount ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Görev Kartları -->
<div class="row">
    <?php if(isset($actions) && count($actions) > 0): ?>
        <?php foreach($actions as $action): ?>
            <?php
            // Object/Array hybrid desteği
            $status          = is_object($action) ? ($action->status ?? $action->actionStatus ?? 0) : ($action['status'] ?? ($action['actionStatus'] ?? 0));
            $alertType       = is_object($action) ? ($action->alert_type ?? null) : ($action['alert_type'] ?? null);
            $daysRemaining   = is_object($action) ? ($action->days_remaining ?? 0) : ($action['days_remaining'] ?? 0);
            $coveName        = is_object($action) ? ($action->cove_name ?? 'Bilinmeyen Merkez') : ($action['cove_name'] ?? 'Bilinmeyen Merkez');
            $cityDistrict    = is_object($action) ? ($action->city_district ?? '') : ($action['city_district'] ?? '');
            $actionTitleSafe = htmlspecialchars(is_object($action) ? ($action->actionTitle ?? '') : ($action['actionTitle'] ?? ''), ENT_QUOTES,'UTF-8');
            $actionDescRaw   = is_object($action) ? ($action->actionDesc ?? '') : ($action['actionDesc'] ?? '');
            $actionDescSafe  = htmlspecialchars(mb_substr($actionDescRaw,0,120), ENT_QUOTES,'UTF-8');
            $aimTitleSafe    = htmlspecialchars(is_object($action) ? ($action->aimTitle ?? '') : ($action['aimTitle'] ?? ''), ENT_QUOTES,'UTF-8');
            $objectiveTitleSafe = htmlspecialchars(is_object($action) ? ($action->objectiveTitle ?? '') : ($action['objectiveTitle'] ?? ''), ENT_QUOTES,'UTF-8');
            $responsibleSafe = htmlspecialchars(is_object($action) ? ($action->actionResponsible ?? '') : ($action['actionResponsible'] ?? ''), ENT_QUOTES,'UTF-8');
            $createdAtRaw    = is_object($action) ? ($action->createdAt ?? null) : ($action['createdAt'] ?? null);
            $dateStartRaw    = is_object($action) ? ($action->dateStart ?? '') : ($action['dateStart'] ?? '');
            $dateEndRaw      = is_object($action) ? ($action->dateEnd ?? '') : ($action['dateEnd'] ?? '');

            // Görev tipini belirleme
            $taskType = 'normal';
            $alertClass = 'primary';
            $alertIcon = 'fas fa-tasks';
            $daysText = '';

            if($status == 1) {
                $taskType = 'completed';
                $alertClass = 'success';
                $alertIcon = 'fas fa-check-circle';
                $daysText = 'Tamamlandı';
            } elseif($alertType) {
                switch($alertType) {
                    case 'overdue':
                        $taskType = 'overdue';
                        $alertClass = 'danger';
                        $alertIcon = 'fas fa-exclamation-triangle';
                        $daysText = abs($daysRemaining) . ' gün gecikmiş';
                        break;
                    case 'urgent':
                        $taskType = 'urgent';
                        $alertClass = 'warning';
                        $alertIcon = 'fas fa-fire';
                        $daysText = $daysRemaining . ' gün kaldı';
                        break;
                    case 'warning':
                        $taskType = 'upcoming';
                        $alertClass = 'info';
                        $alertIcon = 'fas fa-clock';
                        $daysText = $daysRemaining . ' gün kaldı';
                        break;
                    default:
                        $daysText = $daysRemaining . ' gün kaldı';
                }
            }
            ?>
            
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card admin-task-card <?= $taskType ?> h-100">
                    <!-- Kart Başlığı -->
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="cove-info text-<?= $alertClass ?>">
                            <i class="fas fa-building"></i>
                            <strong><?= htmlspecialchars($coveName, ENT_QUOTES,'UTF-8') ?></strong>
                            <?php if($cityDistrict): ?>
                                <br><small class="text-body-secondary"><?= htmlspecialchars($cityDistrict, ENT_QUOTES,'UTF-8') ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="task-badges">
                            <span class="badge badge-<?= $alertClass ?>">
                                <i class="<?= $alertIcon ?>"></i>
                                <?= ucfirst($taskType) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Kart İçeriği -->
                    <div class="card-body d-flex flex-column">
                        <!-- Görev Başlığı -->
                        <h6 class="card-title text-<?= $alertClass ?> mb-3">
                            <i class="<?= $alertIcon ?> me-2"></i>
                            <?= $actionTitleSafe ?>
                        </h6>
                        
                        <!-- Görev Açıklaması -->
            <?php if($actionDescRaw): ?>
                            <p class="card-text text-sm text-body-secondary mb-3">
                <?= $actionDescSafe ?>
                <?= mb_strlen($actionDescRaw) > 120 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Hedef ve Amaç -->
                        <?php if(!empty($action->objectiveTitle) || !empty($action->aimTitle)): ?>
                            <div class="mb-3">
                <?php if($aimTitleSafe): ?>
                                    <small class="d-block text-body-secondary">
                                        <i class="fas fa-bullseye me-1"></i>
                    <strong>Amaç:</strong> <?= $aimTitleSafe ?>
                                    </small>
                                <?php endif; ?>
                <?php if($objectiveTitleSafe): ?>
                                    <small class="d-block text-body-secondary">
                                        <i class="fas fa-target me-1"></i>
                    <strong>Hedef:</strong> <?= $objectiveTitleSafe ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Sorumlu -->
                        <?php if($responsibleSafe): ?>
                            <div class="mb-3">
                                <small class="text-body-secondary">
                                    <i class="fas fa-user me-1"></i>
                                    <strong>Sorumlu:</strong> <?= $responsibleSafe ?>
                                </small>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Spacer - Kart altını iter -->
                        <div class="flex-grow-1"></div>
                        
                        <!-- Tarih Bilgileri -->
                        <div class="mt-auto">
                            <div class="row text-sm">
                                <div class="col-6">
                                    <small class="text-body-secondary">
                                        <i class="fas fa-calendar-plus me-1"></i>
                                        <strong>Başlangıç:</strong><br>
                                        <?= $dateStartRaw ? date('d.m.Y', strtotime($dateStartRaw)) : '-' ?>
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-body-secondary">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        <strong>Bitiş:</strong><br>
                                        <?= $dateEndRaw ? date('d.m.Y', strtotime($dateEndRaw)) : '-' ?>
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Durum Bar -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-body-secondary">Durum</small>
                                    <small class="text-<?= $alertClass ?> font-weight-bold">
                                        <?= $daysText ?>
                                    </small>
                                </div>
                                
                                <?php
                                $progressPercentage = 0;
                                if($status == 1) {
                                    $progressPercentage = 100;
                                } else {
                                    $startTs = $dateStartRaw ? strtotime($dateStartRaw) : 0;
                                    $endTs   = $dateEndRaw   ? strtotime($dateEndRaw)   : 0;
                                    if($startTs && $endTs && $endTs > $startTs) {
                                        $totalDays = ($endTs - $startTs) / 86400;
                                        $elapsedDays = (time() - $startTs) / 86400;
                                        $progressPercentage = max(0, min(100, ($elapsedDays / $totalDays) * 100));
                                    } else {
                                        $progressPercentage = 0;
                                    }
                                }
                                ?>
                                
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-<?= $alertClass ?>" 
                                         style="width: <?= $progressPercentage ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kart Footer -->
                    <div class="card-footer bg-light text-center">
                        <small class="text-body-secondary">
                            <i class="fas fa-clock me-1"></i>
                            Oluşturulma: <?= ($createdAtRaw ? date('d.m.Y H:i', strtotime($createdAtRaw)) : '-') ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Boş Durum -->
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center empty-state">
                    <i class="<?= $currentFilter['icon'] ?> fa-5x text-<?= $currentFilter['color'] ?> mb-3"></i>
                    <h4 class="text-body-secondary">Henüz <?= strtolower($currentFilter['title']) ?> bulunmuyor</h4>
                    <p class="text-body-secondary">
                        <?php if($filter === 'all'): ?>
                            Sistemde henüz hiç görev tanımlanmamış.
                        <?php elseif($filter === 'overdue'): ?>
                            Harika! Geciken bir görev yok.
                        <?php elseif($filter === 'urgent'): ?>
                            Şu anda acil bir görev bulunmuyor.
                        <?php elseif($filter === 'upcoming'): ?>
                            Yaklaşan bir görev bulunmuyor.
                        <?php elseif($filter === 'completed'): ?>
                            Henüz tamamlanan bir görev yok.
                        <?php endif; ?>
                    </p>
                    
                    <?php if($filter !== 'all'): ?>
                        <a href="?url=action/adminTaskList&filter=all" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>
                            Tüm Görevleri Görüntüle
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Sayfalama Bilgisi -->
<?php if(isset($actions) && count($actions) > 0): ?>
<div class="row mt-4">
    <div class="col-12 text-center">
        <div class="card">
            <div class="card-body">
                <h6 class="text-body-secondary mb-0">
                    <i class="fas fa-info-circle"></i>
                    Toplam <?= count($actions) ?> görev gösteriliyor
                    <?php if($filter !== 'all'): ?>
                        (<?= $currentFilter['title'] ?> filtresi uygulandı)
                    <?php endif; ?>
                </h6>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Footer boşluğu -->
<div style="height: 120px;"></div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>

<style>
/* Admin görev kartları */
.admin-task-card {
    transition: all 0.3s ease;
    border-left: 4px solid #007bff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-task-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

/* Görev tiplerine göre renk kodları */
.admin-task-card.overdue {
    border-left-color: #dc3545;
}

.admin-task-card.urgent {
    border-left-color: #fd7e14;
}

.admin-task-card.upcoming {
    border-left-color: #17a2b8;
}

.admin-task-card.completed {
    border-left-color: #28a745;
    opacity: 0.95;
}

/* Merkez bilgisi */
.cove-info {
    font-size: 0.9rem;
}

.cove-info i {
    margin-right: 5px;
}

/* Badge'ler */
.task-badges .badge {
    font-size: 0.7rem;
    padding: 4px 8px;
}

.task-badges .badge i {
    margin-right: 4px;
}

/* Text boyutları */
.text-sm {
    font-size: 0.875rem;
}

/* Progress bar */
.progress {
    border-radius: 6px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 6px;
}

/* Empty state */
.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.3;
}

/* Filtre butonları */
.btn-group .btn {
    border-radius: 4px;
    margin: 2px;
}

/* Badge'lerde sayı */
.badge {
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-task-card:hover {
        transform: none;
    }
    
    .task-badges {
        margin-top: 10px;
        text-align: right;
    }
    
    .card-title {
        font-size: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin: 2px 0;
        width: 100%;
    }
    
    .cove-info {
        margin-bottom: 10px;
    }
}

/* Animasyonlar */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.admin-task-card {
    animation: slideInLeft 0.6s ease forwards;
}

.admin-task-card:nth-child(2n) {
    animation-delay: 0.1s;
}

.admin-task-card:nth-child(3n) {
    animation-delay: 0.2s;
}

/* Kart başlığı */
.card-header.bg-light {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
}

/* İkon renkleri */
.text-success { color: #28a745 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }
.text-warning { color: #fd7e14 !important; }
.text-secondary { color: #6c757d !important; }
.text-primary { color: #007bff !important; }

/* Opacity sınıfı */
.opacity-75 {
    opacity: 0.75;
}

/* Footer çakışması önleme */
.main-footer {
    margin-top: 100px !important;
}

body .content-wrapper {
    min-height: calc(100vh - 150px) !important;
}

/* Flexbox yardımcıları */
.flex-grow-1 {
    flex-grow: 1;
}

/* Hover efektleri */
.btn:hover {
    transform: translateY(-1px);
}

.card:hover .card-title {
    color: inherit;
}
</style>