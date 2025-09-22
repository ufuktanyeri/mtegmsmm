<?php
$title = 'Yeni Kullanıcı Ekle';
$page_title = 'Yeni Kullanıcı Ekle';
$hidePageHeader = true;
$breadcrumb = [
    [
        'url' => 'index.php?url=user/manage',
        'title' => 'Kullanıcılar'
    ],
];
ob_start();
?>

<!-- Page Actions -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="index.php?url=user/manage">Kullanıcılar</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Yeni Kullanıcı</li>
                    </ol>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-user-plus me-2"></i>
                    Yeni Kullanıcı Ekle
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="index.php?url=user/manage" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page Body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kullanıcı Bilgileri</h3>
                    </div>
                    <form action="" method="post" data-auto-save="user-create">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <div class="d-flex">
                                        <div>
                                            <i class="ti ti-alert-circle me-2"></i>
                                        </div>
                                        <div>
                                            <h4>Hata!</h4>
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $error): ?>
                                                    <li><?php echo htmlspecialchars($error); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="realname" class="form-label required">Ad Soyad</label>
                                <input type="text" class="form-control" name="realname" id="realname" 
                                       placeholder="Ad Soyad giriniz" required>
                                <div class="form-hint">Kullanıcının gerçek adı ve soyadını giriniz</div>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label required">Kullanıcı Adı</label>
                                <input type="text" class="form-control" name="username" id="username" 
                                       placeholder="Kullanıcı adını giriniz" required>
                                <div class="form-hint">Sisteme giriş için kullanılacak benzersiz kullanıcı adı</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label required">Parola</label>
                                <div class="input-group input-group-flat">
                                    <input type="password" class="form-control" name="password" id="password" 
                                           placeholder="Parola giriniz" autocomplete="new-password" required>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" id="togglePassword" 
                                           title="Parolayı göster / gizle" data-bs-toggle="tooltip">
                                            <i class="ti ti-eye" id="togglePasswordIcon"></i>
                                        </a>
                                    </span>
                                </div>
                                <div class="form-hint">En az 8 karakter, büyük/küçük harf ve rakam içermelidir</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label required">E-posta</label>
                                <input type="email" class="form-control" name="email" id="email" 
                                       placeholder="ornek@domain.com" required>
                                <div class="form-hint">Geçerli bir e-posta adresi giriniz</div>
                            </div>

                            <div class="mb-3">
                                <label for="cove" class="form-label required">Merkez</label>
                                <select id="cove" class="form-select" name="cove" required>
                                    <option value="">Merkez Seçiniz</option>
                                    <?php foreach ($coves as $cove): ?>
                                        <option value="<?php echo $cove->getId(); ?>">
                                            <?php echo htmlspecialchars($cove->getName()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-hint">Kullanıcının bağlı olacağı merkezi seçiniz</div>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label required">Rol</label>
                                <select id="role" class="form-select" name="role" required>
                                    <option value="">Rol Seçiniz</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role->getId(); ?>">
                                            <?php echo htmlspecialchars($role->getRoleName()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-hint">Kullanıcının sistem içindeki yetkilerini belirler</div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <a href="index.php?url=user/manage" class="btn btn-link me-auto">İptal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i>
                                    Kullanıcı Ekle
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const pwd = document.getElementById('password');
    const btn = document.getElementById('togglePassword');
    const icon = document.getElementById('togglePasswordIcon');
    
    if (pwd && btn && icon) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const showing = pwd.type === 'text';
            pwd.type = showing ? 'password' : 'text';
            icon.classList.toggle('ti-eye', showing);
            icon.classList.toggle('ti-eye-off', !showing);
            btn.setAttribute('aria-pressed', String(!showing));
        });
    }
    
    // Form validation
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Username validation
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const value = this.value;
            const isValid = /^[a-zA-Z0-9_]{3,20}$/.test(value);
            
            if (value && !isValid) {
                this.setCustomValidity('Kullanıcı adı 3-20 karakter arasında olmalı ve özel karakterler içermemelidir');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const value = this.value;
            const strength = calculatePasswordStrength(value);
            updatePasswordStrengthUI(strength);
        });
    }
});

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    return strength;
}

function updatePasswordStrengthUI(strength) {
    // This could be enhanced with a visual indicator
    const passwordInput = document.getElementById('password');
    const colors = ['danger', 'warning', 'info', 'success', 'success'];
    const messages = ['Çok zayıf', 'Zayıf', 'Orta', 'Güçlü', 'Çok güçlü'];
    
    passwordInput.classList.remove('is-invalid', 'is-valid');
    
    if (strength < 2) {
        passwordInput.classList.add('is-invalid');
    } else if (strength >= 3) {
        passwordInput.classList.add('is-valid');
    }
}
</script>