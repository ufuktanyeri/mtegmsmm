<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Mesleki Eğitim Politika Belgesi';
$page_title = 'Mesleki Eğitim Politika Belgesi Stratejileri';
AssetManager::addBundle('datatables');
ob_start();
?>

<div class="row">
    <div class="card w-100">
        <div class="card-header">
            <h3 class="card-title"><a href="index.php?url=documentStrategy/create">Yeni Strateji Ekle</a></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="strategies" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                <thead>
                    <tr>
                        <th>Sıra No</th>
                        <th>Strareji Açıklaması</th>
                        <th>Strareji Belge Numarası</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lineNumber = 1;
                    foreach ($strategies as $strategy): ?>
                        <tr>
                            <td><?php echo $lineNumber; ?></td>
                            <td><?php echo htmlspecialchars($strategy->getStrategyDesc()); ?></td>
                            <td><?php echo htmlspecialchars($strategy->getStrategyNo()); ?></td>
                            <td>
                            <a href="index.php?url=documentStrategy/edit&id=<?php echo $strategy->getId(); ?>" >Düzenle</a>
                                |
                                <a href="#" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $strategy->getId(); ?>" data-name="<?php echo htmlspecialchars($strategy->getStrategyNo()); ?>">Sil</a>
                            </td>
                        </tr>
                    <?php 
                    $lineNumber++;
                    endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Sıra No</th>
                        <th>Strareji Açıklaması</th>
                        <th>Strareji Belge Numarası</th>
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
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
    // Delete modal handling (DataTable init handled globally)
    $('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var fieldId = button.data('id');
    var fieldName = button.data('name');
    var modal = $(this);
    modal.find('#deleteMessage').text('"' + fieldName + '" stratejisini silmek istediğinizden emin misiniz?');
    modal.find('#confirmDeleteButton').attr('href', 'index.php?url=documentStrategy/delete&id=' + fieldId);
  });
</script>



