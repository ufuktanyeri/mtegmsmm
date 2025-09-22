<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Haberler';
$page_title = 'Haberler';
$hidePageHeader = true; // Kart başlığı zaten var
AssetManager::addBundle('datatables');
ob_start();
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <a href="index.php?url=news/create" class="btn btn-primary">Yeni Haber Ekle</a>
                </h3>
            </div>
            <div class="card-body">
                <table id="newsTable" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>"
                    data-searching="true"
                    data-paging="true"
                    data-info="true"
                    data-responsive="true"
                    data-buttons='["copy", "csv", "excel", "pdf", "print", "colvis"]'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>Oluşturulma Tarihi</th>
                            <th>Durum</th>
                            <th>Manşet</th>
                            <th>Sıralama</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news as $item): ?>
                            <tr data-id="<?php echo $item->getId(); ?>">
                                <td><?php echo $item->getId(); ?></td>
                                <td><?php echo htmlspecialchars($item->getTitle()); ?></td>
                                <td><?php echo $item->getCreatedDate(); ?></td>
                                <td><?php echo $item->getState() ? 'Aktif' : 'Pasif'; ?></td>
                                <td><?php echo $item->getHeadline() ? 'Evet' : 'Hayır'; ?></td>
                                <td>
                                    <input type="number" class="form-control order-no-input" style="width:80px;display:inline-block;" 
                                        value="<?php echo $item->getOrderNo(); ?>" 
                                        data-id="<?php echo $item->getId(); ?>">
                                </td>
                                <td>                                  
                                    <a href="index.php?url=news/edit&id=<?php echo $item->getId(); ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" 
                                            data-id="<?php echo $item->getId(); ?>" 
                                            data-title="<?php echo htmlspecialchars($item->getTitle()); ?>">Sil</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button id="saveOrderBtn" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Sıralamaları Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Haberi Sil</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Sil</a>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
$(function () {
    // Delete modal handling & order save (DataTable init handled globally)
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var title = button.data('title');
        var modal = $(this);
        modal.find('#deleteMessage').text('"' + title + '" haberini silmek istediğinizden emin misiniz?');
        modal.find('#confirmDelete').attr('href', 'index.php?url=news/delete&id=' + id + '&csrf_token=<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, 'UTF-8'); ?>');
    });
    
    $('#saveOrderBtn').on('click', function() {
        var orderData = [];
        $('.order-no-input').each(function() {
            orderData.push({
                id: $(this).data('id'),
                order_no: $(this).val()
            });
        });
        $.ajax({
            url: 'index.php?url=news/updateOrder',
            type: 'POST',
            data: {
                order: orderData,
                csrf_token: '<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, 'UTF-8'); ?>'
            },
            success: function(resp) {
                try {
                    var data = JSON.parse(resp);
                    if (data.success) {
                        alert('Sıralamalar başarıyla güncellendi.');
                        location.reload();
                    } else {
                        alert(data.message || 'Bir hata oluştu.');
                    }
                } catch(e) {
                    alert('Bir hata oluştu.');
                }
            },
            error: function() {
                alert('Bir hata oluştu.');
            }
        });
    });
});
</script>
