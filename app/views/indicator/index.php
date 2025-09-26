<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$pageTitle = 'Göstergeler';
$pageDescription = 'Performans göstergeleri yönetimi';
AssetManager::addBundle('datatables');
?>

<!-- Page Header -->
<div class="page-header-internal">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">
                    <i class="fas fa-chart-line me-3"></i>
                    Performans Göstergeleri
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="index.php?url=aim/index" class="text-white-50">Amaçlar</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Göstergeler</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="index.php?url=indicator/create&aimid=<?php echo $aimid; ?>" class="btn btn-light btn-lg rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>
                    Yeni Gösterge Ekle
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Content Area -->
<div class="container py-4">
    <?php if (!empty($aimid)): ?>
        <!-- Navigation Tabs -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded-pill" href="index.php?url=aim/edit&id=<?php echo (int)$aimid; ?>" role="tab">
                            <i class="fas fa-edit me-2"></i>
                            Amaç Düzenle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill" href="index.php?url=objective/list&aimid=<?php echo (int)$aimid; ?>" role="tab">
                            <i class="fas fa-bullseye me-2"></i>
                            Hedefler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill" href="index.php?url=action/index&aimid=<?php echo (int)$aimid; ?>" role="tab">
                            <i class="fas fa-tasks me-2"></i>
                            Faaliyetler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active rounded-pill" href="#" role="tab">
                            <i class="fas fa-chart-line me-2"></i>
                            Performans Göstergeleri
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Göstergeler Listesi
            </h3>
        </div>
               
        <div class="card-body">
            <?php if ($error <> ""): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle p-2 me-3">
                            <i class="fas fa-chart-bar text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Toplam Göstergeler</h5>
                            <span class="text-body-secondary"><?php echo count($indicators ?? []); ?> kayıt</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <form method="post" action="" class="d-flex justify-content-end">
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-filter"></i>
                            </span>
                            <select id="objectiveFilter" name="objectiveFilter" class="form-select">
                                <option value="">Tüm Hedefler</option>
                                <?php if (!empty($objectives)) foreach ($objectives as $objective): ?>
                                    <?php if ($objective->getAimId() == $aimid): ?>
                                        <option value="<?php echo $objective->getId(); ?>">
                                            <?php echo htmlspecialchars($objective->getObjectiveTitle()); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>                       
                </div>
            </div>

            <div class="table-responsive">
                <table id="indicatorsTable" class="table table-hover" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center" width="5%">#</th>
                            <th width="35%">Gösterge Bilgileri</th>
                            <th width="10%">Tür</th>
                            <th width="15%">Alan</th>
                            <th class="text-center" width="10%">Hedef</th>
                            <th class="text-center" width="10%">Tamamlanan</th>
                            <th class="text-center" width="10%">Durum</th>
                            <th class="text-center" width="15%">İşlemler</th>
                        </tr>
                    </thead>
                            <tbody>
                                <?php 
                                $lineNumber = 1;
                                foreach ($indicators as $indicator): ?>
                                    <tr data-objective-id="<?php echo $indicator->getObjectiveId(); ?>">
                                        <td class="align-middle text-center">
                                            <span class="badge bg-secondary"><?php echo $lineNumber; ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($indicator->getIndicatorTitle()); ?></h6>
                                                <div class="text-body-secondary small">
                                                    <div><strong>Hedef:</strong> <?php echo htmlspecialchars($indicator->getObjectiveTitle()); ?></div>
                                                    <div><strong>Tarih:</strong> <?php echo htmlspecialchars($indicator->getCreatedAt()); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge bg-info"><?php echo htmlspecialchars($indicator->getIndicatorTypeTitle()); ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <small class="text-body-secondary"><?php echo htmlspecialchars($indicator->getFieldName()); ?></small>
                                        </td>
                                        <td class="align-middle text-center">
                                            <strong><?php echo htmlspecialchars($indicator->getTarget()); ?></strong>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-primary fw-bold"><?php echo htmlspecialchars($indicator->getCompleted()); ?></span>
                                        </td>
                                        <td class="align-middle">
                            <?php if ($indicator->getIndicatorStatus() == 1): ?>
                                <span class="badge bg-success rounded-pill">
                                    <i class="fas fa-check me-1"></i>
                                    Tamamlandı
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning rounded-pill">
                                    <i class="fas fa-clock me-1"></i>
                                    Devam Ediyor
                                </span>
                            <?php endif; ?>
                                        </td>
                        <td class="align-middle text-center">
                            <div class="btn-group" role="group">
                                <a href="index.php?url=indicator/edit&aimid=<?php echo $aimid;?>&id=<?php echo $indicator->getId(); ?>" 
                                   class="btn btn-sm btn-outline-primary rounded-pill" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger rounded-pill ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                   data-aimid="<?php echo $aimid; ?>" data-id="<?php echo $indicator->getId(); ?>" 
                                   data-title="<?php echo htmlspecialchars($indicator->getIndicatorTitle()); ?>" title="Sil">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                                    </tr>
                                <?php 
                                $lineNumber++;
                                endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Silme Onayı
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p id="deleteMessage" class="fs-6 text-body-secondary"></p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger rounded-pill px-4">
                    <i class="fas fa-trash me-2"></i>Sil
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete modal handling
    const deleteModal = document.getElementById('deleteModal');
    
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const fieldId = button.getAttribute('data-id');
            const aimId = button.getAttribute('data-aimid');
            const fieldTitle = button.getAttribute('data-title');
            
            const deleteMessage = this.querySelector('#deleteMessage');
            const confirmDeleteButton = this.querySelector('#confirmDeleteButton');
            
            deleteMessage.textContent = '"' + fieldTitle + '" başlıklı göstergeyi silmek istediğinizden emin misiniz?';
            confirmDeleteButton.setAttribute('href', 'index.php?url=indicator/delete&id=' + fieldId + '&aimid=' + aimId);
        });
    }

    // Objective filter functionality
    const objectiveFilter = document.getElementById('objectiveFilter');
    
    if (objectiveFilter) {
        objectiveFilter.addEventListener('change', function () {
            const selectedObjectiveId = this.value;
            const tableRows = document.querySelectorAll('#indicatorsTable tbody tr');
            
            tableRows.forEach(function(row) {
                const rowObjectiveId = row.getAttribute('data-objective-id');
                
                if (selectedObjectiveId === "" || rowObjectiveId == selectedObjectiveId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Progress bar for completed vs target
    const indicators = document.querySelectorAll('#indicatorsTable tbody tr');
    indicators.forEach(function(row) {
        const targetCell = row.cells[4];
        const completedCell = row.cells[5];
        
        if (targetCell && completedCell) {
            const target = parseFloat(targetCell.textContent) || 0;
            const completed = parseFloat(completedCell.textContent) || 0;
            
            if (target > 0) {
                const percentage = Math.min((completed / target) * 100, 100);
                const progressClass = percentage >= 100 ? 'bg-success' : 
                                    percentage >= 75 ? 'bg-info' : 
                                    percentage >= 50 ? 'bg-warning' : 'bg-danger';
                
                completedCell.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <div class="progress progress-sm">
                                <div class="progress-bar ${progressClass}" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                        <div class="ms-2 text-body-secondary small">
                            ${completed}/${target}
                        </div>
                    </div>
                `;
            }
        }
    });
});
</script>
