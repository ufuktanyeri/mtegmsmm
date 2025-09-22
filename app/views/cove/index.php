<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'SMM Merkezleri';
$page_title = 'SMM Merkezleri';
AssetManager::addBundle('datatables');
ob_start();
?>

<div class="row">
    <div class="card w-100">
        <div class="card-header">
            <h3 class="card-title"><a href="index.php?url=cove/create">Yeni Merkez Ekle</a></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="coves" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                <thead>
                    <tr>
                        <th>Sıra No</th>
                        <th>Merkez Adı</th>
                        <th>Şehir</th>
                        <th>Adres</th>
                        <th>Rapor Al</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lineNumber = 1;
                    foreach ($coves as $cove): ?>
                        <tr>
                            <td><?php echo $lineNumber; ?></td>
                            <td><?php echo htmlspecialchars($cove->getName()); ?></td>
                            <td><?php echo htmlspecialchars($cove->getCity()); ?></td>
                            <td><?php echo htmlspecialchars($cove->getAddress()); ?></td>
                            <td><a class="btn btn-primary" href="index.php?url=aim/getcoveadminreport&id=<?php echo $cove->getId(); ?>">Rapor Al | PDF</a><br>
                            <?php if (preg_match('/Mobile|Android|iP(hone|od|ad)/i', $_SERVER['HTTP_USER_AGENT'])): ?>
                                <a class="btn btn-success mt-2" href="index.php?url=aim/getcoveadminreportwordx&id=<?php echo $cove->getId(); ?>">Rapor Al | WORD</a>
                            <?php else: ?>
                                <a class="btn btn-success mt-2" href="index.php?url=aim/getcoveadminreportword&id=<?php echo $cove->getId(); ?>">Rapor Al | WORD</a>
                            <?php endif; ?>
                    </td>
                            <td>
                                <a href="index.php?url=cove/edit&id=<?php echo $cove->getId(); ?>">Düzenle</a>
                                |
                                <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $cove->getId(); ?>" data-name="<?php echo htmlspecialchars($cove->getName()); ?>">Sil</a>
                            </td>
                        </tr>
                    <?php 
                    $lineNumber++;
                    endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Sıra No</th>
                        <th>Merkez Adı</th>
                        <th>Şehir</th>
                        <th>Adres</th>
                        <th>Rapor Al</th>
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
    // Delete modal handling (DataTable init handled globally)
    $('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var coveId = button.data('id');
    var fieldName = button.data('name');
    var modal = $(this);
    modal.find('#deleteMessage').text('"' + fieldName + '" alanını silmek istediğinizden emin misiniz?');
    modal.find('#confirmDeleteButton').attr('href', 'index.php?url=cove/delete&id=' + coveId);
  });
</script>



