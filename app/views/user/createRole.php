<?php
$pageTitle = 'Rol Oluştur';
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
                <h3 class="card-title">Rol Oluştur</h3>
            </div>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="roleName">Role Adı</label>
                        <input type="text" class="form-control" id="roleName" name="roleName" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklana</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="parentRoleId">Parent Role</label>
                        <select class="form-control" id="parentRoleId" name="parentRoleId">
                            
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role->getId(); ?>"><?php echo htmlspecialchars($role->getRoleName()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
?>
