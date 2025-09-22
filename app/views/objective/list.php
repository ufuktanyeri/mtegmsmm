<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Hedefler';
$page_title = 'Hedefler';
$breadcrumb = [
    [
        'url' => 'index.php?url=aim/index',
        'title' => 'Amaçlar'
    ],
];
AssetManager::addBundle('datatables');
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link " href="index.php?url=aim/edit&id=<?php echo $aimid; ?>" role="tab" aria-controls="custom-tabs-one-home" aria-selected="false">Amaç Düzenle</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active" href="#"  role="tab" aria-controls="custom-tabs-one-profile" aria-selected="true">Hedefler</a>
                  </li>
                     <li class="nav-item">
                    <a class="nav-link" href="index.php?url=action/index&aimid=<?php echo $aimid; ?>" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Faaliyetler</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="index.php?url=indicator/index&aimid=<?php echo $aimid; ?>"  role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Performans Göstergeleri</a>
                  </li>
               
                </ul>
              </div>
            <div class="card-body">
           <div class="row mb-3"> <h3 class="card-title"><a href="index.php?url=objective/create&aimid=<?php echo $aimid?>">Yeni Hedef Ekle</a></h3>
</div>
                <table id="objectivesTable" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hedef Başlığı</th>
                            <th>Hedef Açıklaması</th>
                            <th>Oluşturulma Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $linenumber=1;
                        foreach ($objectives as $objective): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($linenumber); ?></td>
                                <td><?php echo htmlspecialchars($objective->getObjectiveTitle()); ?></td>
                                <td><?php echo htmlspecialchars($objective->getObjectiveDesc()); ?></td>
                                <td><?php echo htmlspecialchars($objective->getCreatedAt()); ?></td>
                                <td>
                                    <a href="index.php?url=objective/edit&id=<?php echo $objective->getId(); ?>" class="btn btn-warning">Düzenle</a>
                                    <a href="#" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $objective->getId(); ?>" data-title="<?php echo htmlspecialchars($objective->getObjectiveTitle()); ?>" class="btn btn-danger">Sil</a>
                               </td>
                            </tr>
                        <?php 
                    $linenumber++;
                    endforeach; ?>
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
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
    // Delete modal handling (DataTable init handled globally)
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var fieldId = button.data('id');
        var fieldTitle = button.data('title');
        var modal = $(this);
        modal.find('#deleteMessage').text('"' + fieldTitle + '" başlıklı amacı silmek istediğinizden emin misiniz?');
        modal.find('#confirmDeleteButton').attr('href', 'index.php?url=objective/delete&id=' + fieldId);
    });
</script>
