<?php
require_once __DIR__ . '/../../../includes/AssetManager.php';
require_once __DIR__ . '/../../../includes/PermissionHelper.php';

$pageTitle = 'SMM Portal - Yönetim Paneli';
\App\Helpers\AssetManager::addBundle('home');

// Check permissions
$hasUsersManage = hasPermission('users.manage');
$hasAimsManage = hasPermission('aims.manage');
$hasIndicatorsManage = hasPermission('indicators.manage');
$hasActionsManage = hasPermission('actions.manage');

?>

<!-- Dashboard Welcome Section -->
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <h1 class="h3 mb-3 text-white">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Kullanıcı'); ?>!
                    </h1>
                    <p class="mb-0 opacity-90">
                        Sektörel Mükemmeliyet Merkezleri Yönetim Portalına hoş geldiniz. 
                        Bu panel üzerinden tüm işlemlerinizi gerçekleştirebilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-building text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-body-secondary mb-1">SMM Merkezleri</h6>
                            <h3 class="mb-0">15</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-chart-line text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-body-secondary mb-1">Göstergeler</h6>
                            <h3 class="mb-0">48</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-tasks text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-body-secondary mb-1">Aktif Görevler</h6>
                            <h3 class="mb-0">23</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-body-secondary mb-1">Kullanıcılar</h6>
                            <h3 class="mb-0">127</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-bolt me-2"></i>
                Hızlı İşlemler
            </h5>
            <div class="row g-3">
                <?php if (hasPermission('coves.manage')): ?>
                <div class="col-md-2 col-6">
                    <a href="index.php?url=cove/index" class="btn btn-primary w-100">
                        <i class="fas fa-building me-2"></i>
                        Merkezler
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if (hasPermission('indicators.manage')): ?>
                <div class="col-md-2 col-6">
                    <a href="index.php?url=indicator/index" class="btn btn-outline-primary w-100">
                        <i class="fas fa-chart-bar me-2"></i>
                        Göstergeler
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if (hasPermission('aims.manage')): ?>
                <div class="col-md-2 col-6">
                    <a href="index.php?url=aim/index" class="btn btn-outline-primary w-100">
                        <i class="fas fa-bullseye me-2"></i>
                        Amaçlar
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if (hasPermission('actions.manage')): ?>
                <div class="col-md-2 col-6">
                    <a href="index.php?url=action/taskList" class="btn btn-outline-primary w-100">
                        <i class="fas fa-tasks me-2"></i>
                        Görevler
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="index.php?url=action/calendar" class="btn btn-outline-primary w-100">
                        <i class="fas fa-calendar me-2"></i>
                        Takvim
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="col-md-2 col-6">
                    <a href="index.php?url=help/index" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-question-circle me-2"></i>
                        Yardım
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Sections -->
    <div class="row g-3">
        <!-- Strategic Management -->
        <?php if ($hasAimsManage || $hasIndicatorsManage || $hasActionsManage): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chess me-2 text-primary"></i>
                        Stratejik Yönetim
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php if ($hasAimsManage): ?>
                        <a href="index.php?url=aim/index" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-bullseye me-2 text-primary"></i>
                            Amaç ve Hedefler
                        </a>
                        <?php endif; ?>
                        <?php if ($hasIndicatorsManage): ?>
                        <a href="index.php?url=indicator/index" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-chart-line me-2 text-success"></i>
                            Performans Göstergeleri
                        </a>
                        <?php endif; ?>
                        <?php if ($hasActionsManage): ?>
                        <a href="index.php?url=action/index" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-tasks me-2 text-warning"></i>
                            Eylem ve Faaliyetler
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Reports & Documents -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2 text-success"></i>
                        Raporlar ve Belgeler
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php if ($hasIndicatorsManage): ?>
                        <a href="index.php?url=indicator/adminReport" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-chart-pie me-2 text-info"></i>
                            Performans Raporları
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermission('documentstrategies.manage')): ?>
                        <a href="index.php?url=documentstrategy/index" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>
                            Stratejik Belgeler
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermission('regulations.manage')): ?>
                        <a href="index.php?url=regulation/index" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-gavel me-2 text-secondary"></i>
                            Mevzuat
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Management -->
        <?php if ($hasUsersManage): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2 text-warning"></i>
                        Sistem Yönetimi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="index.php?url=user/manage" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Kullanıcı Yönetimi
                        </a>
                        <a href="index.php?url=user/roles" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-shield-alt me-2 text-warning"></i>
                            Roller ve İzinler
                        </a>
                        <a href="index.php?url=log/index" class="list-group-item list-group-item-action border-0">
                            <i class="fas fa-history me-2 text-secondary"></i>
                            Sistem Logları
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2 text-info"></i>
                        Son Aktiviteler
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 p-2 rounded">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-body-secondary">5 dakika önce</small>
                                <p class="mb-0">Yeni kullanıcı eklendi</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 p-2 rounded">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-body-secondary">1 saat önce</small>
                                <p class="mb-0">Görev tamamlandı</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 p-2 rounded">
                                    <i class="fas fa-file text-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <small class="text-body-secondary">2 saat önce</small>
                                <p class="mb-0">Yeni rapor oluşturuldu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
?>