<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Views\action\taskList.php

$pageTitle = $title ?? 'Görev Listesi';
$breadcrumb = [
    ['name' => 'Ana Sayfa', 'url' => 'index.php?url=home'],
    ['name' => 'Faaliyet Takvimi', 'url' => 'index.php?url=action/calendar'],
    ['name' => $title, 'url' => '']
];
?>

<!-- Başlık ve Filtre Bilgisi -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tasks"></i> <?= htmlspecialchars($title) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="index.php?url=action/calendar" class="btn btn-sm btn-light">
                            <i class="fas fa-calendar-alt"></i> Takvime Dön
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0">
                    <i class="fas fa-info-circle"></i>
                    <strong>Filtreleme:</strong> 
                    <?php
                    switch($type ?? 'all') {
                        case 'overdue':
                            echo '<span class="badge badge-danger ms-1">Geciken Görevler</span> - Bitiş tarihi geçmiş ama tamamlanmamış görevler gösteriliyor.';
                            break;
                        case 'urgent':
                            echo '<span class="badge badge-warning ms-1">Acil Görevler</span> - Önümüzdeki 3 gün içinde bitiş tarihi olan görevler gösteriliyor.';
                            break;
                        case 'upcoming':
                            echo '<span class="badge badge-info ms-1">Yaklaşan Görevler</span> - Önümüzdeki 7 gün içinde bitiş tarihi olan görevler gösteriliyor.';
                            break;
                        case 'completed':
                            echo '<span class="badge badge-success ms-1">Tamamlanan Görevler</span> - Başarıyla tamamlanmış görevler gösteriliyor.';
                            break;
                        default:
                            echo '<span class="badge badge-primary ms-1">Tüm Görevler</span> - Merkeze ait tüm görevler gösteriliyor.';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İstatistik Kartları -->
<div class="row mb-4">
    <?php
    // Controller tarafında ileride sayılar gönderilirse (örn: $stats) onları kullan; yoksa fallback hesapla
    $totalLocal = $allCount ?? count($actions ?? []);
    $completedLocal = $completedCount ?? 0;
    $overdueLocal = $overdueCount ?? 0;
    $urgentLocal = $urgentCount ?? 0;
    $upcomingLocal = $upcomingCount ?? 0; // Şimdilik gösterilmiyor kartlarda
    ?>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stats-card text-center border-primary">
            <div class="card-body">
                <div class="stats-icon text-primary"><i class="fas fa-list-ol fa-2x"></i></div>
                <h5 class="text-primary mb-1"><?= $totalLocal ?></h5>
                <p class="text-body-secondary mb-0 small">Toplam Görev</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stats-card text-center border-success">
            <div class="card-body">
                <div class="stats-icon text-success"><i class="fas fa-check-circle fa-2x"></i></div>
                <h5 class="text-success mb-1"><?= $completedLocal ?></h5>
                <p class="text-body-secondary mb-0 small">Tamamlanan</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stats-card text-center border-danger">
            <div class="card-body">
                <div class="stats-icon text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                <h5 class="text-danger mb-1"><?= $overdueLocal ?></h5>
                <p class="text-body-secondary mb-0 small">Geciken</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stats-card text-center border-warning">
            <div class="card-body">
                <div class="stats-icon text-warning"><i class="fas fa-fire fa-2x"></i></div>
                <h5 class="text-warning mb-1"><?= $urgentLocal ?></h5>
                <p class="text-body-secondary mb-0 small">Acil</p>
            </div>
        </div>
    </div>
    <!-- Yaklaşan opsiyonel; ayrı gösterilmek istenirse alan açılabilir -->
</div>

<!-- Filtre Düğmeleri -->
<div class="row mb-4">
    <div class="col-12">
        <div class="btn-group btn-group-sm flex-wrap w-100" role="group" aria-label="Filtreler">
            <?php
            $currentType = $type ?? 'all';
            $filters = [
                'all' => ['Tümü', 'primary', $allCount ?? 0],
                'overdue' => ['Geciken', 'danger', $overdueLocal],
                'urgent' => ['Acil (≤3g)', 'warning', $urgentLocal],
                'upcoming' => ['Yaklaşan (7g)', 'info', $upcomingLocal],
                'completed' => ['Tamamlanan', 'success', $completedLocal]
            ];
            foreach($filters as $key => $meta):
                [$label,$color,$cnt] = $meta;
                $active = $currentType === $key ? $color : 'outline-' . $color;
            ?>
                <a href="index.php?url=action/taskList&type=<?= $key ?>" class="btn btn-<?= $active ?> m-1">
                    <i class="fas fa-<?= $key==='all'?'list':($key==='overdue'?'exclamation-triangle':($key==='urgent'?'fire':($key==='upcoming'?'clock':'check-circle'))) ?> me-1"></i>
                    <?= $label ?>
                    <span class="badge badge-light ms-1"><?= (int)$cnt ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Görev Listesi -->
<div class="row">
    <?php if(empty($actions)): ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="empty-state">
                        <?php
                        $emptyIcon = 'fas fa-tasks';
                        $emptyTitle = 'Görev Bulunamadı';
                        $emptyMessage = 'Bu kategoride herhangi bir görev bulunmamaktadır.';
                        
                        switch($type ?? 'all') {
                            case 'overdue':
                                $emptyIcon = 'fas fa-smile';
                                $emptyTitle = 'Harika! Geciken Görev Yok';
                                $emptyMessage = 'Şu anda hiç geciken göreviniz bulunmuyor.';
                                break;
                            case 'urgent':
                                $emptyIcon = 'fas fa-coffee';
                                $emptyTitle = 'Acil Görev Yok';
                                $emptyMessage = 'Acil bir göreviniz yok.';
                                break;
                            case 'upcoming':
                                $emptyIcon = 'fas fa-calendar-check';
                                $emptyTitle = 'Yaklaşan Görev Yok';
                                $emptyMessage = 'Önümüzdeki hafta için planlanmış görev bulunmuyor.';
                                break;
                            case 'completed':
                                $emptyIcon = 'fas fa-trophy';
                                $emptyTitle = 'Henüz Tamamlanan Görev Yok';
                                $emptyMessage = 'İlk tamamladığınız görev burada görünecek.';
                                break;
                        }
                        ?>
                        
                        <i class="<?= $emptyIcon ?> fa-5x text-body-secondary mb-4 empty-icon"></i>
                        <h4 class="text-body-secondary mb-3"><?= $emptyTitle ?></h4>
                        <p class="text-body-secondary mb-4"><?= $emptyMessage ?></p>
                        <div class="empty-actions">
                            <a href="index.php?url=action/calendar" class="btn btn-primary">
                                <i class="fas fa-calendar-alt"></i> Takvime Dön
                            </a>
                            <?php if(($type ?? 'all') !== 'all'): ?>
                                <a href="index.php?url=action/taskList&type=all" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-list"></i> Tüm Görevler
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($actions as $index => $action): ?>
            <?php
            // ✅ DÜZELTME: Object/Array hybrid desteği ile tüm değerleri al
            $actionId = is_object($action) ? ($action->id ?? 0) : ($action['id'] ?? 0);
            $actionTitle = is_object($action) ? ($action->actionTitle ?? '') : ($action['actionTitle'] ?? '');
            $actionDesc = is_object($action) ? ($action->actionDesc ?? '') : ($action['actionDesc'] ?? '');
            $actionStatus = is_object($action) ? ($action->actionStatus ?? 0) : ($action['actionStatus'] ?? 0);
            $actionResponsible = is_object($action) ? ($action->actionResponsible ?? '') : ($action['actionResponsible'] ?? '');
            $dateStart = is_object($action) ? ($action->dateStart ?? '') : ($action['dateStart'] ?? '');
            $dateEnd = is_object($action) ? ($action->dateEnd ?? '') : ($action['dateEnd'] ?? '');
            $createdAt = is_object($action) ? ($action->createdAt ?? '') : ($action['createdAt'] ?? '');
            $objectiveId = is_object($action) ? ($action->objectiveId ?? 0) : ($action['objectiveId'] ?? 0);
            $objectiveTitle = is_object($action) ? ($action->objectiveTitle ?? '') : ($action['objectiveTitle'] ?? '');
            $aimTitle = is_object($action) ? ($action->aimTitle ?? '') : ($action['aimTitle'] ?? '');
            $alertType = is_object($action) ? ($action->alert_type ?? '') : ($action['alert_type'] ?? '');
            $daysRemaining = is_object($action) ? ($action->days_remaining ?? 0) : ($action['days_remaining'] ?? 0);
            $daysOverdue = is_object($action) ? ($action->days_overdue ?? 0) : ($action['days_overdue'] ?? 0);
            $taskType = is_object($action) ? ($action->task_type ?? ($type ?? 'normal')) : ($action['task_type'] ?? ($type ?? 'normal'));
            
            // Görev tipini ve özelliklerini belirleme
            $alertClass = 'primary';
            $alertIcon = 'fas fa-tasks';
            $statusText = '';
            $isCompleted = ($actionStatus == 1);
            
            if($isCompleted) {
                $taskType = 'completed';
                $alertClass = 'success';
                $alertIcon = 'fas fa-check-circle';
                $statusText = 'Tamamlandı';
            } else {
                // Alert type'dan görev durumunu belirleme
                $effectiveAlertType = $alertType ?: $taskType;
                $effectiveDaysRemaining = (int)$daysRemaining;
                $effectiveDaysOverdue = (int)$daysOverdue;
                
                switch($effectiveAlertType) {
                    case 'overdue':
                        $alertClass = 'danger';
                        $alertIcon = 'fas fa-exclamation-triangle';
                        $overdueDays = $effectiveDaysOverdue > 0 ? $effectiveDaysOverdue : abs($effectiveDaysRemaining);
                        $statusText = $overdueDays . ' gün gecikmiş';
                        break;
                    case 'urgent':
                        $alertClass = 'warning';
                        $alertIcon = 'fas fa-fire';
                        $statusText = max(0, $effectiveDaysRemaining) . ' gün kaldı';
                        break;
                    case 'upcoming':
                    case 'warning':
                        $alertClass = 'info';
                        $alertIcon = 'fas fa-clock';
                        $statusText = max(0, $effectiveDaysRemaining) . ' gün kaldı';
                        break;
                    default:
                        $statusText = 'Devam ediyor';
                }
            }
            ?>
            
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card task-card <?= htmlspecialchars($taskType) ?> h-100 shadow-sm" 
                     style="animation-delay: <?= $index * 0.1 ?>s">
                    
                    <!-- Kart Başlığı -->
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="card-title mb-1 text-<?= $alertClass ?>">
                                <i class="<?= $alertIcon ?> me-2"></i>
                                <?= htmlspecialchars($actionTitle ?: 'Başlıksız Görev') ?>
                            </h6>
                            <div class="task-badges">
                                <span class="badge badge-<?= $alertClass ?> badge-pill">
                                    <i class="<?= $alertIcon ?>"></i>
                                    <?= htmlspecialchars($statusText) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kart İçeriği -->
                    <div class="card-body d-flex flex-column">
                        <!-- Açıklama -->
                        <?php if(!empty($actionDesc)): ?>
                            <div class="mb-3">
                                <p class="text-body-secondary mb-0 task-description">
                                    <?= htmlspecialchars(mb_substr($actionDesc, 0, 120)) ?>
                                    <?= mb_strlen($actionDesc) > 120 ? '...' : '' ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Hedef ve Amaç Bilgileri -->
                        <?php if(!empty($objectiveTitle) || !empty($aimTitle)): ?>
                            <div class="mb-3 project-info">
                                <?php if(!empty($aimTitle)): ?>
                                    <div class="mb-1">
                                        <small class="text-body-secondary">
                                            <i class="fas fa-bullseye text-primary me-1"></i>
                                            <strong>Amaç:</strong> <?= htmlspecialchars($aimTitle) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($objectiveTitle)): ?>
                                    <div class="mb-1">
                                        <small class="text-body-secondary">
                                            <i class="fas fa-target text-info me-1"></i>
                                            <strong>Hedef:</strong> <?= htmlspecialchars($objectiveTitle) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Sorumlu Bilgisi -->
                        <?php if(!empty($actionResponsible)): ?>
                            <div class="mb-3">
                                <small class="text-body-secondary">
                                    <i class="fas fa-user text-secondary me-1"></i>
                                    <strong>Sorumlu:</strong> <?= htmlspecialchars($actionResponsible) ?>
                                </small>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Tarih Bilgileri -->
                        <div class="row mb-3 date-info">
                            <div class="col-6">
                                <small class="text-body-secondary">
                                    <i class="fas fa-play-circle text-success me-1"></i>
                                    <strong>Başlangıç:</strong><br>
                                    <?php if($dateStart): ?>
                                        <?= date('d.m.Y', strtotime($dateStart)) ?>
                                    <?php else: ?>
                                        <span class="text-body-secondary">Belirtilmemiş</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="col-6">
                                <small class="text-body-secondary">
                                    <i class="fas fa-flag-checkered text-danger me-1"></i>
                                    <strong>Bitiş:</strong><br>
                                    <?php if($dateEnd): ?>
                                        <?= date('d.m.Y', strtotime($dateEnd)) ?>
                                    <?php else: ?>
                                        <span class="text-body-secondary">Belirtilmemiş</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        
                        <!-- Progress Bar (sadece devam eden görevler için) -->
                        <?php if(!$isCompleted && $dateStart && $dateEnd): ?>
                            <div class="mb-3 flex-grow-1 d-flex flex-column justify-content-end">
                                <div class="progress-section">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-body-secondary"><strong>İlerleme:</strong></small>
                                        <small class="text-<?= $alertClass ?> font-weight-bold"><?= htmlspecialchars($statusText) ?></small>
                                    </div>
                                    
                                    <?php
                                    try {
                                        $startDate = new DateTime($dateStart);
                                        $endDate = new DateTime($dateEnd);
                                        $currentDate = new DateTime();
                                        
                                        $totalDays = $startDate->diff($endDate)->days;
                                        $passedDays = $startDate->diff($currentDate)->days;
                                        
                                        if($currentDate < $startDate) {
                                            $percentage = 0;
                                            $progressClass = 'bg-secondary';
                                        } elseif($currentDate > $endDate) {
                                            $percentage = 100;
                                            $progressClass = 'bg-danger';
                                        } else {
                                            $percentage = $totalDays > 0 ? ($passedDays / $totalDays) * 100 : 0;
                                            if($percentage < 50) $progressClass = 'bg-success';
                                            elseif($percentage < 80) $progressClass = 'bg-warning';
                                            else $progressClass = 'bg-danger';
                                        }
                                        
                                        $safePercentage = min(100, max(0, $percentage));
                                        $safeTotalDays = max(0, $totalDays);
                                        $safePassedDays = min($safeTotalDays, max(0, $passedDays));
                                    } catch (Exception $e) {
                                        $safePercentage = 0;
                                        $safeTotalDays = 0;
                                        $safePassedDays = 0;
                                        $progressClass = 'bg-secondary';
                                    }
                                    ?>
                                    
                                    <div class="progress progress-sm">
                                        <div class="progress-bar <?= $progressClass ?> progress-bar-striped" 
                                             role="progressbar" 
                                             style="width: <?= $safePercentage ?>%" 
                                             aria-valuenow="<?= $safePercentage ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-body-secondary">
                                            <?= number_format($safePercentage, 1) ?>% tamamlandı
                                        </small>
                                        <small class="text-body-secondary">
                                            <?= $safePassedDays ?>/<?= $safeTotalDays ?> gün
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Tamamlanan görevler veya tarih bilgisi olmayan görevler için boşluk -->
                            <div class="flex-grow-1"></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Kart Footer -->
                    <div class="card-footer bg-light border-top-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-body-secondary">
                                <i class="fas fa-calendar-plus me-1"></i>
                                <?php if($createdAt): ?>
                                    <?= date('d.m.Y H:i', strtotime($createdAt)) ?>
                                <?php else: ?>
                                    Tarih yok
                                <?php endif; ?>
                            </small>
                            
                            <?php if(!$isCompleted): ?>
                                <div class="btn-group" role="group">
                                    <a href="index.php?url=action/edit&id=<?= $actionId ?>&objectiveId=<?= $objectiveId ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Görevi Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-trophy"></i> Tamamlandı
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Sayfa Alt Bilgi -->
<?php if(!empty($actions)): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body text-center">
                <small class="text-body-secondary">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong><?= count($actions) ?></strong> görev listelendi
                    <?php if(($type ?? 'all') !== 'all'): ?>
                        • <strong><?= ucfirst($type ?? 'all') ?></strong> kategorisi aktif
                    <?php endif; ?>
                    • Son güncelleme: <?= date('d.m.Y H:i') ?>
                </small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Footer için boşluk -->
<div style="height: 100px;"></div>

<?php
?>

<style>
/* Ana kart stilleri */
.task-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    border-left: 4px solid #007bff;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}

.task-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

/* Görev tiplerine göre renk kodları */
.task-card.overdue {
    border-left-color: #dc3545;
}

.task-card.overdue:hover {
    box-shadow: 0 10px 30px rgba(220,53,69,0.3);
}

.task-card.urgent {
    border-left-color: #fd7e14;
}

.task-card.urgent:hover {
    box-shadow: 0 10px 30px rgba(253,126,20,0.3);
}

.task-card.upcoming,
.task-card.warning {
    border-left-color: #17a2b8;
}

.task-card.upcoming:hover,
.task-card.warning:hover {
    box-shadow: 0 10px 30px rgba(23,162,184,0.3);
}

.task-card.completed {
    border-left-color: #28a745;
    opacity: 0.95;
}

.task-card.completed:hover {
    box-shadow: 0 10px 30px rgba(40,167,69,0.3);
}

/* İstatistik kartları */
.stats-card {
    transition: all 0.3s ease;
    border-top: 3px solid transparent;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stats-card.border-primary:hover { border-top-color: #007bff; }
.stats-card.border-success:hover { border-top-color: #28a745; }
.stats-card.border-danger:hover { border-top-color: #dc3545; }
.stats-card.border-warning:hover { border-top-color: #ffc107; }

.stats-icon {
    opacity: 0.8;
    margin-bottom: 8px;
}

/* Badge'ler */
.task-badges .badge {
    font-size: 0.7rem;
    padding: 6px 10px;
    font-weight: 500;
}

.task-badges .badge i {
    margin-right: 4px;
}

.badge-pill {
    border-radius: 50px;
}

/* Progress bar */
.progress {
    height: 8px;
    border-radius: 10px;
    background-color: rgba(0,0,0,0.1);
}

.progress-sm {
    height: 6px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.progress-bar-striped {
    background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
    background-size: 1rem 1rem;
}

/* Kart içerik alanları */
.task-description {
    font-size: 0.9rem;
    line-height: 1.4;
}

.project-info {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
    border-left: 3px solid #e9ecef;
}

.date-info {
    font-size: 0.85rem;
}

.progress-section {
    margin-top: auto;
}

/* Empty state */
.empty-state {
    padding: 4rem 2rem;
}

.empty-icon {
    opacity: 0.3;
    animation: float 3s ease-in-out infinite;
}

.empty-actions .btn {
    margin: 0 5px;
}

/* Animasyonlar */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.task-card {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

/* Flexbox yardımcıları */
.flex-grow-1 {
    flex-grow: 1 !important;
}

.h-100 {
    height: 100% !important;
}

/* Responsive tasarım */
@media (max-width: 768px) {
    .task-card:hover {
        transform: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .empty-state {
        padding: 2rem 1rem;
    }
    
    .empty-actions .btn {
        display: block;
        width: 100%;
        margin: 5px 0;
    }
    
    .task-badges {
        margin-top: 10px;
    }
    
    .card-title {
        font-size: 0.95rem;
    }
    
    .stats-card .card-body {
        padding: 1rem 0.75rem;
    }
}

/* Tooltip stilleri */
[title] {
    cursor: help;
}

/* Print stilleri */
@media print {
    .task-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .btn, .empty-actions {
        display: none;
    }
}

/* Dark mode desteği */
@media (prefers-color-scheme: dark) {
    .task-card {
        background-color: #2d3748;
        color: #e2e8f0;
    }
    
    .card-header.bg-light {
        background-color: #4a5568 !important;
    }
    
    .project-info {
        background-color: #4a5568;
    }
}

/* Hover efektleri */
.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.card-title:hover {
    color: inherit;
}

/* Loading state */
.task-card.loading {
    opacity: 0.5;
    pointer-events: none;
}

.task-card.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    z-index: 1;
}
</style>

<script>
// Smooth scroll ve diğer etkileşimler
document.addEventListener('DOMContentLoaded', function() {
    // Kart animasyonları
    const cards = document.querySelectorAll('.task-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
        }, index * 100);
    });
    
    // Progress bar animasyonu
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
    
    // Tooltip initialization (Bootstrap 4)
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[title]').tooltip();
    }
});
</script>