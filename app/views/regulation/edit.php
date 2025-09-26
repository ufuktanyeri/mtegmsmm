<?php
$pageTitle = 'Yasal Dayanak Düzenle';
$breadcrumb = [    
    [
        'url' => 'index.php?url=regulation/index',
        'title' => 'Dayanaklar'
    ],
];
?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
            </div>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
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
                        <label for="regulationDesc">Mevzuat Açıklaması</label>
                        <input type="text" class="form-control" id="regulationDesc" name="regulationDesc" value="<?php echo htmlspecialchars($regulation->getRegulationDesc()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="regulationSource">Mevzuat Kaynağı</label>
                        <input type="text" class="form-control" id="regulationSource" name="regulationSource" value="<?php echo htmlspecialchars($regulation->getRegulationSource()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="regulationSourceNo">Mevzuat Kaynak Numarası</label>
                        <input type="text" class="form-control" id="regulationSourceNo" name="regulationSourceNo" value="<?php echo htmlspecialchars($regulation->getRegulationSourceNo()); ?>" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
?>
