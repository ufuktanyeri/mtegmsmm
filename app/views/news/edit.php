<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Haber Düzenle';
$page_title = 'Haber Düzenle';
$hidePageHeader = true; // Üst başlık tekrarını engelle
$breadcrumb = [    
    [
        'url' => 'index.php?url=news/index',
        'title' => 'Haberler'
    ],
];
AssetManager::addBundle('summernote');
ob_start();
?>


<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <!-- İç başlık kaldırıldı: sayfa başlığı layout üzerinden geliyor -->
         
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <div class="card-body">
                  <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
                    <div class="form-group">
                        <label for="title">Başlık</label>
                        <input type="text" class="form-control" id="title" name="title" minlength="3" maxlength="150"
                            value="<?php echo htmlspecialchars($news->getTitle()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="details">Başlık</label>
                        <input type="text" class="form-control" id="details" name="details" minlength="10" maxlength="200"
                            value="<?php echo htmlspecialchars($news->getDetails()); ?>" required >
                    </div>

                    <div class="form-group">
                        <label for="content">İçerik</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required data-summernote data-lang="tr-TR" data-height="350"><?php
                                                                                                        echo htmlspecialchars($news->getContent());
                                                                                                        ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="frontpage_image">Ön Sayfa Görseli</label>
                        <input type="file" class="form-control" id="frontpage_image" name="frontpage_image" accept="image/*" onchange="previewFrontpageImage(this)">
                        <?php if ($news->getFrontpageImage()): ?>
                            <div class="mt-2">
                                <label>Mevcut Ön Sayfa Görseli:</label>
                                <img src="<?php echo $news->getFrontpageImage(); ?>" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>
                        <div id="frontpage-preview" class="mt-2"></div>
                    </div>

                    <div class="form-group">
                        <label for="gallery">Görseller (En fazla 3 resim)</label>
                        <input type="file" class="form-control" id="gallery" name="gallery[]" multiple accept="image/*" onchange="previewImages(this)">
                        <small class="text-body-secondary">Lütfen en fazla 3 resim seçin.</small>

                        <?php if (!empty($gallery)): ?>
                            <div class="mt-2">
                                <label>Mevcut Görseller:</label>
                                <div class="row">
                                    <?php foreach ($gallery as $item): ?>
                                        <div class="col-md-4 mt-2 position-relative gallery-image-container">
                                            <img src="<?php echo $item->getPath(); ?>" class="img-thumbnail w-100" style="max-height: 200px;">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm position-absolute gallery-delete-btn"
                                                    style="top:5px; right:15px; z-index:2;"
                                                    data-id="<?php echo $item->getId(); ?>"
                                                    data-path="<?php echo htmlspecialchars($item->getPath(), ENT_QUOTES); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row mt-2" id="image-preview"></div>
                    </div>

                    <div class="form-group">
                        <label for="order_no">Sıralama</label>
                        <input type="number" class="form-control" id="order_no" name="order_no"
                            value="<?php echo $news->getOrderNo(); ?>">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="headline" name="headline"
                            <?php echo $news->getHeadline() ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="headline">Manşet</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="state" name="state"
                            <?php echo $news->getState() ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="state">Aktif</label>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteGalleryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Galeriden Resim Sil</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteGalleryMessage">Bu resmi silmek istediğinizden emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="confirmGalleryDelete">Sil</button>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
 // Summernote handled globally; remaining image preview & gallery delete logic

    function previewFrontpageImage(input) {
        const preview = document.getElementById('frontpage-preview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.maxHeight = '200px';
                preview.appendChild(img);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        if (input.files.length > 3) {
            alert('En fazla 3 resim seçebilirsiniz!');
            input.value = '';
            return;
        }

        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            if (!file.type.startsWith('image/')) continue;

            const col = document.createElement('div');
            col.className = 'col-md-4 mt-2';

            const img = document.createElement('img');
            img.className = 'img-thumbnail';
            img.style.maxHeight = '200px';
            img.file = file;

            const reader = new FileReader();
            reader.onload = (function(aImg) {
                return function(e) {
                    aImg.src = e.target.result;
                };
            })(img);
            reader.readAsDataURL(file);

            col.appendChild(img);
            preview.appendChild(col);
        }
    };
    



    

    let deleteGalleryId = null;
    let deleteGalleryPath = null;
    let deleteGalleryBtn = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Sil butonuna tıklanınca modal aç
        document.querySelectorAll('.gallery-delete-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                deleteGalleryId = this.getAttribute('data-id');
                deleteGalleryPath = this.getAttribute('data-path');
                deleteGalleryBtn = this;
                $('#deleteGalleryModal').modal('show');
            });
        });

        // Modal onayla butonu
        document.getElementById('confirmGalleryDelete').addEventListener('click', function() {
            if (!deleteGalleryId || !deleteGalleryPath) return;
            // AJAX ile silme isteği gönder
            fetch('index.php?url=news/deleteGalleryImage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(deleteGalleryId) +
                      '&path=' + encodeURIComponent(deleteGalleryPath) +
                      '&csrf_token=' + encodeURIComponent(document.querySelector('input[name="csrf_token"]').value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Görseli arayüzden kaldır
                    if (deleteGalleryBtn) {
                        let container = deleteGalleryBtn.closest('.gallery-image-container');
                        if (container) container.remove();
                    }
                    $('#deleteGalleryModal').modal('hide');
                } else {
                    alert(data.message || 'Silme işlemi başarısız.');
                }
            })
            .catch(() => {
                alert('Silme işlemi sırasında bir hata oluştu.');
            });
        });
    });
</script>

<style>
.gallery-image-container {
    position: relative;
}
.gallery-delete-btn {
    position: absolute;
    top: 5px;
    right: 15px;
    z-index: 2;
}
</style>


