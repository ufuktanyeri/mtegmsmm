<?php
$pageTitle = 'Hesap Ayarları';
$breadcrumb = [
    ['url' => 'index.php', 'title' => 'Ana Sayfa'],
    ['url' => '#', 'title' => 'Hesap Ayarları']
];

// Get user data (mock data for now - replace with actual user data)
$user = $_SESSION['user'] ?? [
    'name' => $_SESSION['username'] ?? '',
    'email' => 'kullanici@meb.gov.tr',
    'phone' => '+90 555 123 4567',
    'cove' => 'Gazi SMM',
    'role' => $_SESSION['role'] ?? 'User',
    'bio' => 'Mesleki ve teknik eğitimde kaliteyi artırmak için çalışıyoruz.'
];
?>

<style>
/* Account Settings Styles */
.account-settings-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.settings-header {
    margin-bottom: 2rem;
}

.settings-header h1 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.settings-header p {
    color: #64748b;
    font-size: 0.95rem;
}

.settings-content {
    display: flex;
    gap: 2rem;
}

/* Sidebar Navigation */
.settings-sidebar {
    width: 280px;
    flex-shrink: 0;
}

.settings-nav {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.nav-tabs {
    flex-direction: column;
    border: none;
}

.nav-tabs .nav-link {
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    color: #475569;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s;
}

.nav-tabs .nav-link:hover {
    background: #f8fafc;
    color: #1e293b;
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.nav-tabs .nav-link i {
    width: 20px;
    text-align: center;
}

/* Main Content Area */
.settings-main {
    flex: 1;
    min-width: 0;
}

.tab-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.tab-pane {
    padding: 2rem;
}

/* Profile Section */
.profile-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 2rem;
}

.profile-avatar-section {
    position: relative;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid #e2e8f0;
}

.avatar-upload-btn {
    position: absolute;
    bottom: -10px;
    right: -10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: 3px solid white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s;
}

.avatar-upload-btn:hover {
    transform: scale(1.1);
}

.profile-info h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.profile-info p {
    color: #64748b;
    margin-bottom: 0.5rem;
}

.profile-badges {
    display: flex;
    gap: 0.5rem;
}

.badge-custom {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.badge-role {
    background: #dbeafe;
    color: #1e40af;
}

.badge-cove {
    background: #dcfce7;
    color: #166534;
}

/* Form Styles */
.form-section {
    margin-bottom: 2rem;
}

.form-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e2e8f0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #475569;
}

.form-label .required {
    color: #ef4444;
    margin-left: 2px;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-control:disabled {
    background: #f8fafc;
    cursor: not-allowed;
}

.form-text {
    font-size: 0.875rem;
    color: #64748b;
    margin-top: 0.25rem;
}

/* Action Buttons */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;
}

.btn-save {
    padding: 0.75rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
}

.btn-cancel {
    padding: 0.75rem 2rem;
    background: white;
    color: #475569;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* Security Section */
.security-item {
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.security-info h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.security-info p {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
}

.btn-action {
    padding: 0.5rem 1rem;
    background: white;
    color: #6366f1;
    border: 1.5px solid #6366f1;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-action:hover {
    background: #6366f1;
    color: white;
}

/* Notifications Section */
.notification-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-info h4 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.notification-info p {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
}

.form-switch {
    position: relative;
}

.form-switch .form-check-input {
    width: 3rem;
    height: 1.5rem;
    background-color: #cbd5e1;
    border: none;
    cursor: pointer;
}

.form-switch .form-check-input:checked {
    background-color: #6366f1;
}

/* Alert Messages */
.alert-custom {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Responsive */
@media (max-width: 768px) {
    .settings-content {
        flex-direction: column;
    }

    .settings-sidebar {
        width: 100%;
    }

    .profile-header {
        flex-direction: column;
        text-align: center;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-save, .btn-cancel {
        width: 100%;
    }
}
</style>

<div class="account-settings-container">
    <!-- Header -->
    <div class="settings-header">
        <h1>Hesap Ayarları</h1>
        <p>Profil bilgilerinizi ve güvenlik ayarlarınızı yönetin</p>
    </div>

    <!-- Settings Content -->
    <div class="settings-content">
        <!-- Sidebar -->
        <aside class="settings-sidebar">
            <nav class="settings-nav">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                            <i class="fas fa-user"></i>
                            Profil Bilgileri
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                            <i class="fas fa-lock"></i>
                            Güvenlik
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                            <i class="fas fa-bell"></i>
                            Bildirimler
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab">
                            <i class="fas fa-cog"></i>
                            Tercihler
                        </button>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="settings-main">
            <div class="tab-content">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?url=user/updateProfile" enctype="multipart/form-data">
                        <!-- Profile Header -->
                        <div class="profile-header">
                            <div class="profile-avatar-section">
                                <img src="<?php echo BASE_URL; ?>wwwroot/img/default-avatar.svg" alt="Profile" class="profile-avatar">
                                <label for="avatar-upload" class="avatar-upload-btn">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;">
                            </div>
                            <div class="profile-info">
                                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                                <p><?php echo htmlspecialchars($user['email']); ?></p>
                                <div class="profile-badges">
                                    <span class="badge-custom badge-role"><?php echo htmlspecialchars($user['role']); ?></span>
                                    <span class="badge-custom badge-cove"><?php echo htmlspecialchars($user['cove']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Kişisel Bilgiler</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Ad Soyad <span class="required">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               name="name"
                                               value="<?php echo htmlspecialchars($user['name']); ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Kullanıcı Adı <span class="required">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>"
                                               disabled>
                                        <small class="form-text">Kullanıcı adı değiştirilemez</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            E-posta <span class="required">*</span>
                                        </label>
                                        <input type="email"
                                               class="form-control"
                                               name="email"
                                               value="<?php echo htmlspecialchars($user['email']); ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Telefon</label>
                                        <input type="tel"
                                               class="form-control"
                                               name="phone"
                                               value="<?php echo htmlspecialchars($user['phone']); ?>"
                                               placeholder="+90 5XX XXX XX XX">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Hakkımda</label>
                                <textarea class="form-control"
                                          name="bio"
                                          rows="4"
                                          placeholder="Kendiniz hakkında birkaç cümle yazın..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                                <small class="form-text">Maksimum 500 karakter</small>
                            </div>
                        </div>

                        <!-- Organization Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Kurum Bilgileri</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">SMM</label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?php echo htmlspecialchars($user['cove']); ?>"
                                               disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Rol</label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?php echo htmlspecialchars($user['role']); ?>"
                                               disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn-cancel">İptal</button>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save me-2"></i>Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="form-section">
                        <h3 class="form-section-title">Güvenlik Ayarları</h3>

                        <div class="security-item">
                            <div class="security-info">
                                <h4>Şifre Değiştir</h4>
                                <p>Son değişiklik: 30 gün önce</p>
                            </div>
                            <button class="btn-action">Şifreyi Değiştir</button>
                        </div>

                        <div class="security-item">
                            <div class="security-info">
                                <h4>İki Faktörlü Doğrulama</h4>
                                <p>Hesabınızı daha güvenli hale getirin</p>
                            </div>
                            <button class="btn-action">Aktifleştir</button>
                        </div>

                        <div class="security-item">
                            <div class="security-info">
                                <h4>Aktif Oturumlar</h4>
                                <p>Hesabınıza bağlı cihazları yönetin</p>
                            </div>
                            <button class="btn-action">Görüntüle</button>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="form-section">
                        <h3 class="form-section-title">Bildirim Tercihleri</h3>

                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>E-posta Bildirimleri</h4>
                                <p>Önemli güncellemeler ve duyurular için</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>Sistem Bildirimleri</h4>
                                <p>Görev ve eylem hatırlatmaları</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <h4>Haftalık Özet</h4>
                                <p>Haftalık performans ve ilerleme raporu</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences Tab -->
                <div class="tab-pane fade" id="preferences" role="tabpanel">
                    <div class="form-section">
                        <h3 class="form-section-title">Uygulama Tercihleri</h3>

                        <div class="form-group">
                            <label class="form-label">Dil</label>
                            <select class="form-control">
                                <option selected>Türkçe</option>
                                <option>English</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Saat Dilimi</label>
                            <select class="form-control">
                                <option selected>İstanbul (GMT+3)</option>
                                <option>Londra (GMT)</option>
                                <option>New York (GMT-5)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tarih Formatı</label>
                            <select class="form-control">
                                <option selected>GG/AA/YYYY</option>
                                <option>AA/GG/YYYY</option>
                                <option>YYYY-AA-GG</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel">İptal</button>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save me-2"></i>Tercihleri Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Avatar upload preview
document.getElementById('avatar-upload').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.profile-avatar').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

// Form validation
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Add your form submission logic here
        alert('Değişiklikler kaydedildi!');
    });
});
</script>