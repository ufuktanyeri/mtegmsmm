<?php
$pageTitle = 'Haber Detayı';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($news->getTitle()); ?></h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <strong>Oluşturulma Tarihi:</strong> <?php echo $news->getCreatedDate(); ?><br>
                        <strong>Durum:</strong> <?php echo $news->getState() ? 'Aktif' : 'Pasif'; ?><br>
                        <strong>Manşet:</strong> <?php echo $news->getHeadline() ? 'Evet' : 'Hayır'; ?><br>
                        <strong>Ön Sayfa:</strong> <?php echo $news->getFront() ? 'Evet' : 'Hayır'; ?><br>
                        <strong>Sıralama:</strong> <?php echo $news->getOrderNo(); ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>İçerik</h4>
                        <div class="border p-3">
                            <?php echo nl2br(htmlspecialchars($news->getDetails())); ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($gallery)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Görseller</h4>
                            <div class="row">
                                <?php foreach ($gallery as $item): ?>
                                    <div class="col-md-3 mb-3">
                                        <img src="<?php echo $item->getPath(); ?>" class="img-fluid">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="index.php?url=news/edit&id=<?php echo $news->getId(); ?>" class="btn btn-warning">Düzenle</a>
                <a href="index.php?url=news/index" class="btn btn-secondary">Geri Dön</a>
            </div>
        </div>
    </div>
</div>

<?php
?>
