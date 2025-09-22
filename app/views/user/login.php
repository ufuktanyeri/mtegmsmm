<?php
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: frame-ancestors 'self'");

// Page configuration
$title = 'Sektörel Mükemmeliyet Merkezleri | Giriş';
$description = 'SMM Portal Giriş - Milli Eğitim Bakanlığı Mesleki ve Teknik Eğitim Genel Müdürlüğü';
$bodyClass = 'hold-transition layout-fixed login-page';

// Additional page-specific styles
$additionalCss = '
    /* Login Page Styles */
    .login-page {
        background: linear-gradient(135deg, #f0f2f5 0%, #e4e6ea 100%);
        min-height: 100vh;
    }

    .login-container {
        max-width: 1200px;
        width: 100%;
        display: flex;
        gap: 3rem;
        align-items: center;
        margin: 0 auto;
        padding: 2rem 20px;
        margin-top: 100px;
    }

    .brand-section {
        flex: 1;
        text-align: left;
    }

    .brand-logo {
        max-width: 120px;
        height: auto;
        margin-bottom: 1.5rem;
    }

    .brand-title {
        font-size: 3.5rem;
        font-weight: 700;
        color: #1c1e21;
        line-height: 1.2;
        margin-bottom: 1rem;
    }

    .brand-subtitle {
        font-size: 1.75rem;
        font-weight: 400;
        color: #606770;
        line-height: 1.34;
        margin-bottom: 2rem;
    }

    .brand-description {
        font-size: 1.1rem;
        color: #606770;
        line-height: 1.5;
    }

    .login-form-section {
        flex: 0 0 400px;
    }

    .login-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
        padding: 2rem;
        border: none;
    }

    .login-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .login-header h3 {
        color: #1c1e21;
        font-weight: 600;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .login-header p {
        color: #606770;
        font-size: 1rem;
        margin-bottom: 0;
    }

    .form-control {
        background-color: #f5f6f7;
        border: 1px solid #dddfe2;
        border-radius: 8px;
        color: #1c1e21;
        font-size: 17px;
        padding: 14px 16px;
        line-height: 20px;
        transition: border-color 0.2s ease;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: #1877f2;
        box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.2);
        outline: none;
    }

    .form-control::placeholder {
        color: #8a8d91;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .btn-login {
        background: linear-gradient(135deg, #1877f2 0%, #0d4f8c 100%);
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 17px;
        font-weight: 600;
        padding: 12px;
        width: 100%;
        transition: all 0.2s ease;
        margin-top: 0.5rem;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #166fe5 0%, #0a4275 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3);
        color: white;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .alert {
        border-radius: 8px;
        border: none;
        font-size: 14px;
        margin-bottom: 1rem;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .recaptcha-container {
        display: flex;
        justify-content: center;
        margin: 1rem 0;
    }

    .security-note {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #dadde1;
    }

    .security-note small {
        color: #8a8d91;
        font-size: 13px;
    }

    /* Password Toggle */
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #8a8d91;
        cursor: pointer;
        padding: 8px;
        border-radius: 4px;
        transition: color 0.2s ease;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #1877f2;
        background-color: rgba(24, 119, 242, 0.1);
    }

    .password-toggle:focus {
        outline: none;
        color: #1877f2;
    }

    /* reCAPTCHA styling fixes */
    .g-recaptcha {
        transform: scale(0.9);
        transform-origin: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
            text-align: center;
            gap: 2rem;
        }

        .brand-title {
            font-size: 2.5rem;
        }

        .brand-subtitle {
            font-size: 1.5rem;
        }

        .login-form-section {
            flex: none;
            width: 100%;
            max-width: 400px;
        }
    }

    @media (max-width: 480px) {
        .g-recaptcha {
            transform: scale(0.8);
        }
    }
';

// Additional JavaScript
$additionalJs = '
    // Password toggle functionality
    const pwdInput = document.getElementById("password");
    const toggleBtn = document.getElementById("togglePassword");
    if (pwdInput && toggleBtn) {
        const icon = toggleBtn.querySelector("i");
        toggleBtn.addEventListener("click", function() {
            const isPassword = pwdInput.type === "password";
            pwdInput.type = isPassword ? "text" : "password";

            // Toggle icon
            icon.classList.toggle("fa-eye", !isPassword);
            icon.classList.toggle("fa-eye-slash", isPassword);

            // Update title
            toggleBtn.title = isPassword ? "Parolayı gizle" : "Parolayı göster";
        });
    }

    // Form validation - reCAPTCHA v2 doğrulaması
    const loginForm = document.querySelector("form");
    if (loginForm) {
        loginForm.addEventListener("submit", function(e) {
            if (typeof grecaptcha !== "undefined") {
                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    e.preventDefault();
                    alert("Lütfen reCAPTCHA doğrulamasını tamamlayın.");
                    return false;
                }
            }
        });
    }
';

// Load header component
include __DIR__ . '/../components/header.php';
?>

<!-- Google reCAPTCHA v2 -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div class="wrapper">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="login-container">
        <!-- Brand Section -->
        <div class="brand-section">
            <img src="<?php echo BASE_URL; ?>img/MEB_Logo.svg" alt="MEB Logo" class="brand-logo">
            <h1 class="brand-title">SMM Portal</h1>
            <p class="brand-subtitle">Sektörel Mükemmeliyet Merkezi ile geleceği şekillendirin</p>
            <div class="brand-description">
                <p>Milli Eğitim Bakanlığı Mesleki ve Teknik Eğitim Genel Müdürlüğü bünyesindeki
                    Sektörel Mükemmeliyet Merkezleri ile nitelikli iş gücü yetiştirme misyonuna
                    katılın.</p>
            </div>
        </div>

        <!-- Login Form Section -->
        <div class="login-form-section">
            <div class="login-card">
                <div class="login-header">
                    <h3>Sisteme Giriş</h3>
                    <p>Hesabınızla oturum açın</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken ?? ''); ?>">

                    <!-- Kullanıcı Adı -->
                    <div class="form-group">
                        <input
                            id="username"
                            type="text"
                            name="username"
                            class="form-control"
                            placeholder="Kullanıcı adı"
                            required
                            autocomplete="username" />
                    </div>

                    <!-- Parola -->
                    <div class="form-group position-relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Parola"
                            required
                            autocomplete="current-password"
                            style="padding-right: 50px;" />
                        <button
                            type="button"
                            id="togglePassword"
                            class="password-toggle"
                            title="Parolayı göster/gizle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Google reCAPTCHA v2 -->
                    <div class="recaptcha-container">
                        <!-- reCAPTCHA Test Key Warning -->
                    <?php if (strpos(Recaptcha::getSiteKey(), '6LeIxAcTAAAAA') !== false): ?>
                    <div class="recaptcha-test-warning text-danger small mt-1">
                        <i class="fas fa-exclamation-triangle"></i> Test key kullanılıyor - Kırmızı uyarı normaldir
                    </div>
                    <?php endif; ?>

                    <div class="g-recaptcha"
                            data-sitekey="<?php echo htmlspecialchars(Recaptcha::getSiteKey()); ?>"
                            data-theme="light"></div>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Oturum Aç
                    </button>
                </form>

                <!-- Security Note -->
                <div class="security-note">
                    <small>
                        <i class="fas fa-shield-alt me-1"></i>
                        Güvenli Giriş - MTEGM
                    </small>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</div>

<?php
// Load scripts component
include __DIR__ . '/../components/scripts.php';
?>