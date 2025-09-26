<?php
require_once __DIR__ . '/../../../includes/AssetManager.php';
use App\Helpers\AssetManager;
$pageTitle = 'İzinler';
AssetManager::addBundle('datatables');
?>

<div class="row">
    <div class="col-md-12">
        <div class="card w-100">
            <div class="card-header">
                <h3 class="card-title"><a href="index.php?url=user/createPermission">Yeni İzin Ekle</a></h3>
            </div>
            <div class="card-body">
                <table id="permissionsTable" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>İzin Adı</th>
                            <th>Açıklama</th>
                            <th>İlgili Rol</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($permission->getId()); ?></td>
                                <td><?php echo htmlspecialchars($permission->getPermissionName()); ?></td>
                                <td><?php echo htmlspecialchars($permission->getDescription()); ?></td>
                                <td>
                                    <?php echo $permission->getRole() ? htmlspecialchars($permission->getRole()->getRoleName()) : 'Yok'; ?>
                                </td>
                                <td>
                                    <a href="index.php?url=user/editPermission&id=<?php echo $permission->getId(); ?>" class="btn btn-warning">Düzenle</a>
                                    <button class="btn btn-danger" 
                                            data-toggle="modal" 
                                            data-target="#deleteModal" 
                                            data-id="<?php echo htmlspecialchars($permission->getId()); ?>" 
                                            data-name="<?php echo htmlspecialchars($permission->getPermissionName()); ?>">
                                        Sil
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">Silme Onayı</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <a href="#" id="confirmDeleteButton" class="btn btn-danger">Sil</a>
            </div>
        </div>
    </div>
</div>
<?php
?>
<script>
    // Delete modal (DataTable init handled globally)
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var permissionId = button.data('id');
        var permissionName = button.data('name');
        var modal = $(this);
        modal.find('#deleteMessage').text('"' + permissionName + '" adlı izni silmek istediğinizden emin misiniz?');
        modal.find('#confirmDeleteButton').attr('href', 'index.php?url=user/deletePermission&id=' + permissionId + '&csrf_token=<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>');
    });
</script>

