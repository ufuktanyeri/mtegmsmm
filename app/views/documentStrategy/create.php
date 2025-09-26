<?php
$pageTitle = 'Strateji Ekle';
$breadcrumb = [    
    [
        'url' => 'index.php?url=documentStrategy/index',
        'title' => 'Politika Belgesi'
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
                        <label for="strategyDesc">Strateji Açıklaması</label>
                        <input type="text" class="form-control" id="strategyDesc" name="strategyDesc" required>
                    </div>
                    <div class="form-group">
                        <label for="strategyNo">Strateji Numarası</label>
                        <input type="text" class="form-control" id="strategyNo" name="strategyNo" required>
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
?>
