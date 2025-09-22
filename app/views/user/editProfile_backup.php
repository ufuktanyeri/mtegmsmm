<?php
$title = 'Profil Düzenle';
$page_title = 'Profil Düzenle';
ob_start();
?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Profil Bilgilerini Güncelle</h3>
            </div>
            <form method="post" action="index.php?url=user/editProfile">
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
                        <label for="realname">Gerçek İsim</label>
                        <input type="text" class="form-control" id="realname" name="realname" value="<?php echo htmlspecialchars($user->getRealname()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Kullanıcı Adı</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user->getUsername()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-posta</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Parola (Değiştirmek istemiyorsanız boş bırakın)</label>
                        <input type="password" class="form-control" id="password" name="password">
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
