<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Amaçlar';
$page_title = 'Amaçlar';
$hidePageHeader = true;
AssetManager::addBundle('datatables');

// Check if user is superadmin for CRUD operations
$isSuperAdmin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'superadmin';

ob_start();
?>

<!-- Page Actions -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Stratejik Yönetim</div>
                <h2 class="page-title">
                    <i class="ti ti-bullseye me-2"></i>
                    Amaçlar
                </h2>
            </div>
            <?php if ($isSuperAdmin): ?>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="<?php echo BASE_URL; ?>index.php?url=aim/create" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        Yeni Amaç Ekle
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Page Body -->
<div class="page-body">
    <div class="container-xl">
        <?php if ($error <> ""): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="ti ti-alert-circle me-2"></i>
                    </div>
                    <div>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Amaçlar Listesi</h3>
                <div class="card-actions">
                    <a href="#" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="ti ti-printer me-1"></i>
                        Yazdır
                    </a>
                </div>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="d-flex">
                    <div class="text-muted">
                        Toplam <?php echo count($aims ?? []); ?> amaç
                    </div>
                </div>
            </div>
            <table id="aims" class="table table-sm table-bordered" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                <thead>
                    <tr>
                        <th class="col-1">Sıra No</th>
                        <th class="col-11">Amaç</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lineNumber = 1;
                    foreach ($aims as $aim): ?>
                        <tr>
                            <td class="align-middle">
                                 <div class="text-center text-bold"><?php echo $lineNumber; ?>
                    </div>
                                </td>
                            <td>
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($aim->getAimTitle()); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($aim->getAimDesc()); ?></p>
                                           
                                            </small></p>
                                                                                 
                                             <div class="row mt-5">
                                                  <div class="mb-3 col-6 text-start">
                                                   <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#regulationsModal" data-aim-id="<?php echo $aim->getId(); ?>">Dayanaklar</button>
                                                  </div>
                                                <div class="mb-3 col-6 text-end">
                                                    <?php if ($isSuperAdmin): ?>
                                                    <a href="<?php echo BASE_URL; ?>index.php?url=aim/edit&id=<?php echo $aim->getId(); ?>" class="btn btn-warning">Düzenle</a>
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" class="btn btn-danger" data-id="<?php echo $aim->getId(); ?>" data-name="<?php echo htmlspecialchars($aim->getAimTitle()); ?>">Sil</a>
                                                    <?php endif; ?>
                                                </div>
                                             </div>
                                        </div>
                                    </div>
                                  
                            </td>
                        </tr>
                    <?php 
                    $lineNumber++;
                    endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Sıra No</th>
                        <th>Amaç</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Silme Onayı
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger">Sil</a>
            </div>
        </div>
    </div>
</div>

<!-- Regulations Modal -->
<div class="modal fade" id="regulationsModal" tabindex="-1" aria-labelledby="regulationsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regulationsModalLabel">İlgili Mevzuatlar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="regulationsList" class="list-unstyled">
                    <li class="text-center text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Yükleniyor...
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<?php
// Additional JavaScript for modals
$additionalJs = '
    // Regulations modal ajax
    document.getElementById("regulationsModal").addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget;
        const aimId = button.getAttribute("data-aim-id");
        const modal = this;
        const list = modal.querySelector("#regulationsList");
        
        // Show loading
        list.innerHTML = `<li class="text-center text-muted">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Yükleniyor...
        </li>`;
        
        // Fetch regulations
        fetch("' . BASE_URL . 'index.php?url=aim/getRegulations&aimId=" + aimId)
            .then(response => response.json())
            .then(data => {
                list.innerHTML = "";
                if (data && data.length > 0) {
                    data.forEach(function(r) {
                        const li = document.createElement("li");
                        li.className = "mb-2";
                        li.innerHTML = `<i class="fas fa-file-text me-2 text-muted"></i>${r.regulationSourceNo} - ${r.regulationDesc}`;
                        list.appendChild(li);
                    });
                } else {
                    list.innerHTML = `<li class="text-center text-muted">Henüz dayanak mevzuat eklenmemiş.</li>`;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                list.innerHTML = `<li class="text-center text-danger">Veriler yüklenirken hata oluştu.</li>`;
            });
    });

    // Delete modal
    document.getElementById("deleteModal").addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget;
        const fieldId = button.getAttribute("data-id");
        const fieldName = button.getAttribute("data-name");
        const modal = this;
        
        modal.querySelector("#deleteMessage").textContent = `"${fieldName}" amacını silmek istediğinizden emin misiniz?`;
        modal.querySelector("#confirmDeleteButton").href = "' . BASE_URL . 'index.php?url=aim/delete&id=" + fieldId;
    });
';

$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
