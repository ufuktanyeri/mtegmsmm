<?php
$title = 'İzin Düzenle';
$page_title = 'İzin Düzenle';
$breadcrumb = [    
    [
        'url' => 'index.php?url=user/permissions',
        'title' => 'İzinler'
    ],
];
ob_start();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
            </div>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="permissionName">İzin Adı</label>
                        <input type="text" class="form-control" id="permissionName" name="permissionName" value="<?php echo htmlspecialchars($permission->getPermissionName()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($permission->getDescription()); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="roleId">İlgili Rol</label>
                        <select class="form-control" id="roleId" name="roleId" required>
                            <option value="">Rol Seç</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role->getId(); ?>" <?php echo ($assignedRoleId == $role->getId()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role->getRoleName()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
