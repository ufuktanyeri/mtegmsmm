<?php
$title = 'Profil Düzenle';
$page_title = 'Profil Düzenle';

// Additional page-specific styles
$additionalCss = '
    .profile-upload-container {
        position: relative;
        display: inline-block;
    }
    
    .profile-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .profile-photo:hover {
        border-color: var(--meb-primary);
    }
    
    .profile-upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
        color: white;
        font-size: 2rem;
    }
    
    .profile-upload-container:hover .profile-upload-overlay {
        opacity: 1;
    }
    
    .profile-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .profile-header {
        background: var(--meb-gradient);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }
    
    .profile-header::before {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: white;
        border-radius: 20px 20px 0 0;
    }
    
    .form-control:focus {
        border-color: var(--meb-primary);
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    
    .btn-update {
        background: var(--meb-gradient);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        color: white;
    }
    
    .btn-logout {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-logout:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        color: white;
    }
    
    .input-group {
        margin-bottom: 1.5rem;
    }
    
    .input-group-text {
        background: rgba(79, 70, 229, 0.1);
        border-color: rgba(79, 70, 229, 0.2);
        color: var(--meb-primary);
    }
    
    .profile-stats {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1.5rem;
        margin: 1rem 0;
    }
    
    .stat-item {
        text-align: center;
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--meb-primary);
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
';

ob_start();
?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="profile-card">
                <!-- Profile Header -->
                <div class="profile-header">
                    <h2 class="mb-0">Profil Düzenle</h2>
                    <p class="opacity-75 mb-0">Hesap bilgilerinizi güncelleyin</p>
                </div>

                <!-- Profile Form -->
                <div class="p-4">
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Hata!</strong> Lütfen aşağıdaki sorunları düzeltin:
                            <ul class="mt-2 mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss="true">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Başarılı!</strong> Profil bilgileriniz güncellendi.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo BASE_URL; ?>index.php?url=user/editprofile" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        
                        <div class="row">
                            <!-- Profile Photo Section -->
                            <div class="col-md-4 text-center mb-4">
                                <div class="profile-upload-container mx-auto">
                                    <img src="<?php echo ($user->getProfilePhoto() && !empty($user->getProfilePhoto())) ? BASE_URL . 'uploads/profiles/' . htmlspecialchars($user->getProfilePhoto()) : BASE_URL . 'img/default-avatar.svg'; ?>" 
                                         alt="Profil Fotoğrafı" class="profile-photo" id="profilePreview">
                                    <div class="profile-upload-overlay" onclick="document.getElementById('profilePhotoInput').click();">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*" style="display: none;" onchange="previewProfilePhoto(this)">
                                <div class="mt-3">
                                    <small class="text-muted">Fotoğrafı değiştirmek için tıklayın</small><br>
                                    <small class="text-muted">JPG, PNG (Max: 5MB)</small>
                                </div>

                                <!-- Profile Stats -->
                                <div class="profile-stats mt-4">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Kullanıcı'); ?></div>
                                                <div class="stat-label">Rol</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo htmlspecialchars($_SESSION['cove_name'] ?? 'Genel'); ?></div>
                                                <div class="stat-label">Birim</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo date('d.m.Y', strtotime($user->getCreatedAt())); ?></div>
                                                <div class="stat-label">Üyelik Tarihi</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="col-md-8">
                                <!-- Real Name -->
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="realname" name="realname" 
                                           value="<?php echo htmlspecialchars($user->getRealname()); ?>" 
                                           placeholder="Gerçek isminiz" required>
                                    <div class="invalid-feedback">Gerçek isim gereklidir.</div>
                                </div>

                                <!-- Username -->
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-at"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($user->getUsername()); ?>" 
                                           placeholder="Kullanıcı adınız" required>
                                    <div class="invalid-feedback">Kullanıcı adı gereklidir.</div>
                                </div>

                                <!-- Email -->
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user->getEmail()); ?>" 
                                           placeholder="E-posta adresiniz" required>
                                    <div class="invalid-feedback">Geçerli bir e-posta adresi gereklidir.</div>
                                </div>

                                <!-- Password -->
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Yeni parola (değiştirmek istemiyorsanız boş bırakın)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>

                                <!-- Confirm Password -->
                                <div class="input-group" id="confirmPasswordGroup" style="display: none;">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Parolayı tekrar girin">
                                    <div class="invalid-feedback">Parolalar eşleşmiyor.</div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row mt-4">
                                    <div class="col-sm-6 mb-3">
                                        <button type="submit" class="btn btn-update w-100" data-loading="Güncelleniyor...">
                                            <i class="fas fa-save me-2"></i>Profili Güncelle
                                        </button>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <a href="<?php echo BASE_URL; ?>index.php?url=user/logout" 
                                           class="btn btn-logout w-100" 
                                           data-confirm="Oturumu kapatmak istediğinizden emin misiniz?">
                                            <i class="fas fa-sign-out-alt me-2"></i>Oturumu Kapat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Profile photo preview
function previewProfilePhoto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Dosya boyutu çok büyük! Maksimum 5MB olmalıdır.');
            input.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
            alert('Sadece JPG, JPEG ve PNG formatları desteklenir!');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}

// Password toggle
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Show/hide confirm password based on password input
document.getElementById('password').addEventListener('input', function() {
    const confirmGroup = document.getElementById('confirmPasswordGroup');
    const confirmInput = document.getElementById('confirm_password');
    
    if (this.value.length > 0) {
        confirmGroup.style.display = 'block';
        confirmInput.required = true;
    } else {
        confirmGroup.style.display = 'none';
        confirmInput.required = false;
        confirmInput.value = '';
    }
});

// Confirm password validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Parolalar eşleşmiyor');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>