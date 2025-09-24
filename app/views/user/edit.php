<?php
$title = 'Kullanıcı Güncelle';
$page_title = 'Kullanıcı Güncelle';
$hidePageHeader = true;
$breadcrumb = [
    ['url' => 'index.php?url=user/manage', 'title' => 'Kullanıcılar'],
];
ob_start();
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
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="realname">Ad Soyad</label>
                        <select class="form-control" name="realname" id="realname" required>
                            <option value="">Seçiniz</option>
                            <?php if (isset($allUsers) && is_array($allUsers)) foreach ($allUsers as $u): $rn = method_exists($u, 'getRealname') ? $u->getRealname() : ($u['realname'] ?? '');
                                if (!$rn) continue; ?>
                                <option value="<?php echo htmlspecialchars($rn); ?>" <?php echo (strcasecmp($rn, $user->getRealname()) === 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($rn); ?></option>
                            <?php endforeach; ?>
                            <?php if (!isset($allUsers) || empty($allUsers)): ?>
                                <option value="<?php echo htmlspecialchars($user->getRealname()); ?>" selected><?php echo htmlspecialchars($user->getRealname()); ?> (Mevcut)</option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-body-secondary">Listede yoksa önce kullanıcı oluşturma ekranından ekleyin.</small>
                    </div>

                    <div class="form-group">
                        <label for="username">Kullanıcı Adı</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($user->getUsername()); ?>" placeholder="Kullanıcı adını giriniz">
                    </div>

                    <div class="form-group">
                        <label for="password">Parola</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Sadece Parola değiştirmek istediğinizde yeni parolayı yazın!">
                    </div>

                    <div class="form-group">
                        <label for="email">Eposta</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" placeholder="Eposta giriniz">
                    </div>

                    <div class="form-group">
                        <label for="cove">Merkez</label>
                        <select id="cove" name="cove" class="form-control" required>
                            <option value="0">SMM Seç</option>
                            <?php foreach ($coves as $cove): ?>
                                <option value="<?php echo $cove->getId(); ?>" <?php echo ($user->getCove() && $user->getCove()->getId() == $cove->getId()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cove->getName()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select id="role" name="role" class="form-control" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role->getId(); ?>" <?php echo ($user->getRole() && $user->getRole()->getId() == $role->getId()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role->getRoleName()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Kullanıcı Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>