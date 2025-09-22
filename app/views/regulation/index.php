<?php
require_once __DIR__ . '/../../../includes/AssetManager.php';
use App\Helpers\AssetManager;
$title = 'Yasal Dayanaklar';
$page_title = 'Yasal Dayanaklar';
$hidePageHeader = true; // İçerikte başlık kartı ile gösteriliyor, duplication engelle
AssetManager::addBundle('datatables');
ob_start();
?>

<div class="row">
    <div class="card w-100">
        <div class="card-header">
            <h3 class="card-title"><a href="index.php?url=regulation/create">Yeni Regülasyon Ekle</a></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php 
            if ($error<>""): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <table id="regulations" class="table table-sm table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                <thead>
                    <tr>
                        <th class="col-1">Sıra No</th>
                        <th class="col-4">Mevzaut Açıklaması</th>
                        <?php if ($isAdmin) {?><th class="col-2">Mevzuat Kaynağı</th><?php }
                         else { ?> 
                         <th class="col-4">Mevzuat Kaynağı</th><?php } ?>
                        
                        <th class="col-1">Mevzuat Kaynak Numarası</th>
                        <?php if ($isAdmin) {?>
                        <th class="col-2">Ekleyen</th>
                       <?php }?>
                        <th class="col-2">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lineNumber = 1;
                    foreach ($regulations as $regulation): ?>
                        <tr>
                            <td><?php echo $lineNumber; ?></td>
                            <td><?php echo htmlspecialchars($regulation->getRegulationDesc()); ?></td>
                            <td><?php echo htmlspecialchars($regulation->getRegulationSource()); ?></td>
                            <td><?php echo htmlspecialchars($regulation->getRegulationSourceNo()); ?></td>
                            <?php if ($isAdmin) {?>
                                <td><?php echo htmlspecialchars($regulation->getOwnerCoveName() ?: ''); ?></td>
                        <?php }?>
                            <td class="text-center">
                            <?php if ($isAdmin) {                               
                                ?>
                                    <a href="index.php?url=regulation/edit&id=<?php echo $regulation->getId(); ?>" class="btn btn-warning">Düzenle</a>
                                    
                                    <a href="#" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" data-id="<?php echo $regulation->getId(); ?>" data-name="<?php echo htmlspecialchars($regulation->getRegulationSourceNo()); ?>">Sil</a>
                                                         
                           <?php 
                            }
                            else {
                                if ($userCoveId == $regulation->getOwnerCoveId()){ ?>
                                    <a href="index.php?url=regulation/edit&id=<?php echo $regulation->getId(); ?>" class="btn btn-warning">Düzenle</a>
                                    
                                    <a href="#" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" data-id="<?php echo $regulation->getId(); ?>" data-name="<?php echo htmlspecialchars($regulation->getRegulationSourceNo()); ?>">Sil</a>
                                                         
                           <?php }
                            }
                            ?>
                            </td>
                        </tr>
                    <?php 
                    $lineNumber++;
                    endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                    <th>Sıra No</th>
                        <th>Mevzaut Açıklaması</th>
                        <th>Mevzuat Kaynağı</th>
                        <th>Mevzuat Kaynak Numarası</th>
                        <?php if ($isAdmin) {?>
                        <th>Ekleyen</th>
                       <?php }?>
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
$content = ob_get_clean();

include __DIR__ . '/../layouts/unified.php';
?>
<script>
// Delete modal handling (DataTable handled globally)
$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var fieldId = button.data('id');
    var fieldName = button.data('name');
    var modal = $(this);
    modal.find('#deleteMessage').text('"' + fieldName + '" regülasyonunu silmek istediğinizden emin misiniz?');
    modal.find('#confirmDeleteButton').attr('href', 'index.php?url=regulation/delete&id=' + fieldId);
});
</script>
