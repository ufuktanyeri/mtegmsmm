<?php
$pageTitle = 'Hedef Düzenle';
$breadcrumb = [
    [
        'url' => 'index.php?url=objective/list&aimid='.$objective->getaimId(),
        'title' => 'Hedefler'
    ],
];
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
                        <input type="text" class="form-control" id="objectiveTitle" name="objectiveTitle" value="<?php echo htmlspecialchars($objective->getObjectiveTitle()); ?>" required maxlength="250">
                    </div>
                    <div class="form-group">
                        <label for="objectiveDesc">Hedef Açıklaması</label>
                        <textarea class="form-control" id="objectiveDesc" name="objectiveDesc" required><?php echo htmlspecialchars($objective->getObjectiveDesc()); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="objectiveResult">Hedef Sonucu</label>
                        <textarea class="form-control" id="objectiveResult" name="objectiveResult"><?php echo htmlspecialchars($objective->getObjectiveResult()); ?></textarea>
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
