<?php
$pageTitle = 'Rol Güncelle';
$breadcrumb = [    
    [
        'url' => 'index.php?url=user/roles',
        'title' => 'Roller'
    ],
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Rol Güncelle</h3>
            </div>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="roleName">Role Name</label>
                        <input type="text" class="form-control" id="roleName" name="roleName" value="<?php echo htmlspecialchars($role->getRoleName()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($role->getDescription()); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="parentRoleId">Parent Role</label>
                        <select class="form-control" id="parentRoleId" name="parentRoleId">
                            <option value="">None</option>
                            <?php foreach ($roles as $parentRole): ?>
                                <option value="<?php echo $parentRole->getId(); ?>" <?php echo $role->getParentRoleId() == $parentRole->getId() ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($parentRole->getRoleName()); ?>
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
?>
