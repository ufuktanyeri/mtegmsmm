<?php
$title = 'Yeni Hedef Ekle';
$page_title = 'Yeni Hedef Ekle';
$breadcrumb = [
    [
        'url' => 'index.php?url=objective/list&aimid='.$aimid,
        'title' => 'Hedefler'
    ],
];
ob_start();
?>
<!-- Bootstrap 5 Duallistbox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-duallistbox@4.0.2/dist/bootstrap-duallistbox.min.css">

<div class="row">
    <div class="col-md-12">
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
                        <label for="objectiveTitle">Hedef Başlığı</label>
                        <input type="text" class="form-control" id="objectiveTitle" name="objectiveTitle" required maxlength="250">
                    </div>
                    <div class="form-group">
                        <label for="objectiveDesc">Hedef Açıklaması</label>
                        <textarea class="form-control" id="objectiveDesc" name="objectiveDesc" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="objectiveResult">Hedef Sonucu</label>
                        <textarea class="form-control" id="objectiveResult" name="objectiveResult"></textarea>
                    </div>                   
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
