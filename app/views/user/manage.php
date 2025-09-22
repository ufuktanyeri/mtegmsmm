<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
require_once __DIR__ . "/../../../includes/PermissionHelper.php";
use App\Helpers\AssetManager;
$title = 'Kullanıcılar';
$page_title = 'Kullanıcılar';
$breadcrumb = [
    ['title' => 'Sistem Yönetimi'],
    ['title' => 'Kullanıcılar']
];
AssetManager::addBundle('datatables');
ob_start();
?>
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

        <!-- Statistics Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Toplam Kullanıcı</div>
                        </div>
                        <div class="h1 mb-3"><?php echo count($users ?? []); ?></div>
                        <div class="d-flex mb-2">
                            <div>Aktif kullanıcı sayısı</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Roller</div>
                        </div>
                        <div class="h1 mb-3">
                            <?php 
                            $roles = array_unique(array_map(function($user) { 
                                return $user->getRole()->getRoleName(); 
                            }, $users ?? []));
                            echo count($roles); 
                            ?>
                        </div>
                        <div class="d-flex mb-2">
                            <div>Farklı rol türü</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">SMM Merkezleri</div>
                        </div>
                        <div class="h1 mb-3">
                            <?php 
                            $centers = array_unique(array_map(function($user) { 
                                return $user->getCove()->getName(); 
                            }, $users ?? []));
                            echo count($centers); 
                            ?>
                        </div>
                        <div class="d-flex mb-2">
                            <div>Temsil edilen merkez</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Son Eklenen</div>
                        </div>
                        <div class="h1 mb-3">
                            <i class="ti ti-user-plus" style="font-size: 2rem; color: #206bc4;"></i>
                        </div>
                        <div class="d-flex mb-2">
                            <div>
                                <?php if (canCreate('users.manage')): ?>
                                <a href="index.php?url=user/create" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus me-1"></i>
                                    Yeni Kullanıcı
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kullanıcılar Listesi</h3>
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
                        Toplam <?php echo count($users ?? []); ?> kullanıcı
                    </div>
                </div>
            </div>
            <table id="users" class="table table-sm table-bordered" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                <thead>
                    <tr>
                        <th class="col-1">Sıra No</th>
                        <th class="col-2">Gerçek Adı</th>
                        <th class="col-2">Kullanıcı Adı</th>
                        <th class="col-2">E-posta</th>
                        <th class="col-1">Rol</th>
                        <th class="col-2">Merkez</th>
                        <th class="col-2">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lineNumber = 1;
                    foreach ($users as $user): ?>
                        <tr>
                            <td class="align-middle">
                                <div class="text-center text-bold"><?php echo $lineNumber; ?></div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2"><?php echo substr($user->getRealname(), 0, 2); ?></span>
                                    <?php echo htmlspecialchars($user->getRealname()); ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <strong><?php echo htmlspecialchars($user->getUsername()); ?></strong>
                            </td>
                            <td class="align-middle">
                                <a href="mailto:<?php echo htmlspecialchars($user->getEmail()); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($user->getEmail()); ?>
                                </a>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($user->getRole()->getRoleName()); ?></span>
                            </td>
                            <td class="align-middle">
                                <span class="text-muted"><?php echo htmlspecialchars($user->getCove()->getName()); ?></span>
                            </td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    <?php if (canUpdate('users.manage')): ?>
                                    <a href="index.php?url=user/edit&id=<?php echo $user->getId(); ?>" class="btn btn-sm btn-warning">
                                        <i class="ti ti-edit"></i>
                                        Düzenle
                                    </a>
                                    <?php endif; ?>
                                    <?php if (canDelete('users.manage')): ?>
                                    <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                       data-id="<?php echo $user->getId(); ?>" data-name="<?php echo htmlspecialchars($user->getUsername()); ?>" 
                                       data-csrf="<?php echo $csrfToken; ?>">
                                        <i class="ti ti-trash"></i>
                                        Sil
                                    </a>
                                    <?php endif; ?>
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
                        <th>Gerçek Adı</th>
                        <th>Kullanıcı Adı</th>
                        <th>E-posta</th>
                        <th>Rol</th>
                        <th>Merkez</th>
                        <th>İşlemler</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">Silme Onayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
// Delete modal handling
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            const fieldName = button.getAttribute('data-name');
            const csrfToken = button.getAttribute('data-csrf');
            
            const deleteMessage = this.querySelector('#deleteMessage');
            const confirmDeleteButton = this.querySelector('#confirmDeleteButton');
            
            deleteMessage.textContent = '"' + fieldName + '" kullanıcısını silmek istediğinizden emin misiniz?';
            confirmDeleteButton.setAttribute('href', 'index.php?url=user/delete&id=' + userId + '&csrf_token=' + csrfToken);
        });
    }
});
</script>