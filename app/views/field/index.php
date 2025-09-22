<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'SMM Alanları';
$page_title = 'SMM Alanları';
AssetManager::addBundle('datatables');
ob_start();
?>

<div class="row">
    <div class="card w-100">
        <div class="card-header">
            <h3 class="card-title"><a href="index.php?url=field/create">Yeni Alan Ekle</a></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="fields" class="table table-bordered table-striped" data-datatable="fields">
                <thead>
                    <tr>
                        <th>Sıra No</th>
                        <th>Mesleki Alan Adı</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lineNumber = 1;
                    foreach ($fields as $field): ?>
                        <tr>
                            <td><?php echo $lineNumber; ?></td>
                            <td><?php echo htmlspecialchars($field->getName()); ?></td>
                            <td>
                                <a href="index.php?url=field/edit&id=<?php echo $field->getId(); ?>">Düzenle</a>
                                |
                                <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $field->getId(); ?>" data-name="<?php echo htmlspecialchars($field->getName()); ?>">Sil</a>
                            </td>
                        </tr>
                    <?php 
                    $lineNumber++;
                    endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Sıra No</th>
                        <th>Mesleki Alan Adı</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Silme Onayı</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
// Modal delete handling (datatable init handled globally)
$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var fieldId = button.data('id');
    var fieldName = button.data('name');
    var modal = $(this);
    modal.find('#deleteMessage').text('"' + fieldName + '" alanını silmek istediğinizden emin misiniz?');
    modal.find('#confirmDeleteButton').attr('href', 'index.php?url=field/delete&id=' + fieldId);
});
</script>



