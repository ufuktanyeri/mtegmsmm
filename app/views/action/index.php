<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;

// Initialize variables that might come from controller
$aimid = $aimid ?? 0;
$objectives = $objectives ?? [];
$actions = $actions ?? [];

$title = 'Faaliyetler';
$page_title = 'Faaliyetler';
$hidePageHeader = true; // Sekmeli yapı kendi başlığını gösteriyor
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
                    <a class="nav-link" href="index.php?url=objective/list&aimid=<?php echo $aimid; ?>"  role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Hedefler</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active" href="#"  role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Faaliyetler</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="index.php?url=indicator/index&aimid=<?php echo $aimid; ?>"  role="tab" aria-controls="custom-tabs-one-profile" aria-selected="true">Performans Göstergeleri</a>
                  </li>
                  
                </ul>
              </div>
               
            <div class="card-body">
                <?php if (isset($error) && $error !== ""): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="row mb-3">
                        <div class="col-md-6 text-end">
                            <h3 class="card-title"><a href="index.php?url=action/create&aimid=<?php echo $aimid; ?>">Yeni Faaliyet  Ekle</a></h3>
                        </div>
                        <div class="col-md-6">
                        <div class="col-md-12 text-end">                                
                                <select id="objectiveFilter" name="objectiveFilter" class="form-control">
                                    <option value="">Tüm Hedefler</option>
                                    <?php foreach ($objectives as $objective): ?>
                                        <?php if ($objective->getAimId() == $aimid): ?>
                                            <option value="<?php echo $objective->getId(); ?>"><?php echo htmlspecialchars($objective->getObjectiveTitle()); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>                            
                        </div>                        
                    </div>                
                </form>               

                <table id="actionsTable" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                    <thead>
                        <tr>
                            <th class="col-1"></th>
                            <th class="col-7">Faaliyet Başlığı ve Açıklama</th>                           
                            <th class="col-1">Başlangıç Tarihi</th>
                            <th class="col-1">Bitiş Tarihi</th>
                            <th class="col-2">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $lineNumber=1;
                        foreach ($actions as $action): ?>
                            <tr data-objective-id="<?php echo $action->getObjectiveId(); ?>">
                                <td><?php echo htmlspecialchars((string)$lineNumber); ?></td>
                                <td>
                                    <p class="card-text"><?php echo htmlspecialchars($action->getActionTitle()); ?></p>
                                    <p class="card-text"><?php echo htmlspecialchars($action->getActionDesc()); ?></p>
                                    <p class="card-text"><?php echo htmlspecialchars($action->getActionResponsible()); ?></p>
                                    <p class="card-text"><?php echo ($action->getActionStatus() == 1 ? '<p class="card-text text-success"> Tamamlandı</p>' : '<p class="card-text text-warning"> Devam Ediyor</p>'); ?></p>
                                </td>
                                <td><?php echo htmlspecialchars($action->getDateStart()); ?></td>
                                <td><?php echo htmlspecialchars($action->getDateEnd()); ?></td>
                                <td>
                                    <a href="index.php?url=action/edit&id=<?php echo $action->getId(); ?>&objectiveId=<?php echo $action->getobjectiveId(); ?>&aimid=<?php echo $aimid; ?>" class="btn btn-warning">Düzenle</a>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" data-aimid="<?php echo $aimid; ?>" data-id="<?php echo $action->getId(); ?>" data-title="<?php echo htmlspecialchars($action->getActionTitle()); ?>" class="btn btn-danger">Sil</a>
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
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
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
    // Delete modal handling and filtering (DataTable init handled globally)
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var actionId = button.data('id');
        var aimId = button.data('aimid');
        var actionTitle = button.data('title');
        var modal = $(this);
        modal.find('#deleteMessage').text('"' + actionTitle + '" başlıklı faaliyeti silmek istediğinizden emin misiniz?');
        modal.find('#confirmDeleteButton').attr('href', 'index.php?url=action/delete&id=' + actionId + '&aimid=' + aimId);
    });
    $('#objectiveFilter').on('change', function () {
        var selectedObjectiveId = $(this).val();
        $('#actionsTable tbody tr').each(function () {
            var rowObjectiveId = $(this).data('objective-id');
            if (selectedObjectiveId === "" || rowObjectiveId == selectedObjectiveId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
</script>