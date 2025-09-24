<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Yeni Haber';
$page_title = 'Yeni Haber';
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
            <div class="card-header">
                <h3 class="card-title">Yeni Haber Ekle</h3>
            </div>
          
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
                    
                    <?php
$form = isset($form) ? $form : [];
?>
<div class="form-group">
    <label for="title">Başlık</label>
    <input type="text" class="form-control" id="title" name="title"
        minlength="3" maxlength="150"
           value="<?php echo isset($form['title']) ? htmlspecialchars($form['title']) : ''; ?>" required>
</div>

<div class="form-group">
    <label for="details">Açıklama</label>
    <input type="text" class="form-control" id="details" name="details"
           minlength="10" maxlength="200"
           value="<?php echo isset($form['details']) ? htmlspecialchars($form['details']) : ''; ?>" required>
</div>
    
<div class="form-group">
    <label for="content">İçerik</label>
    <textarea class="form-control" id="content" name="content" rows="5" required data-summernote data-lang="tr-TR" data-height="350"><?php 
        echo isset($form['content']) ? htmlspecialchars($form['content']) : '';
    ?></textarea>
</div>
                    
                    <div class="form-group">
                        <label for="frontpage_image">Ön Sayfa Görseli</label>
                        <input type="file" class="form-control" id="frontpage_image" name="frontpage_image" accept="image/*" onchange="previewFrontpageImage(this)">
                        <div id="frontpage-preview" class="mt-2"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="gallery">Görseller (En fazla 3 resim)</label>
                        <input type="file" class="form-control" id="gallery" name="gallery[]" multiple accept="image/*" onchange="previewImages(this)">
                        <small class="text-body-secondary">Lütfen en fazla 3 resim seçin.</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="row" id="image-preview"></div>
                    </div>
                    
                    <div class="form-group">
    <label for="order_no">Sıralama</label>
    <input type="number" class="form-control" id="order_no" name="order_no"
           value="<?php echo isset($form['order_no']) ? htmlspecialchars($form['order_no']) : '0'; ?>">
</div>

<div class="form-check mb-3">
    <input type="checkbox" class="form-check-input" id="headline" name="headline"
        <?php echo (isset($form['headline']) && $form['headline']) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="headline">Manşet</label>
</div>

<div class="form-check">
    <input type="checkbox" class="form-check-input" id="state" name="state"
        <?php echo (!isset($form['state']) || $form['state']) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="state">Aktif</label>
</div>
                    
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
    // Image preview helpers (Summernote init handled globally)
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
}

</script>

