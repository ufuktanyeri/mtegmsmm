<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MTEGM SMM Portal Giriş">
    <title>Giriş Yap - MTEGM SMM Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

</head>
<body class="d-flex align-items-center py-5 bg-gradient" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <main class="w-100 m-auto" style="max-width: 400px;">
        <div class="card border-0 rounded-3 shadow-lg">
            <div class="card-body p-4">
                <form action="/auth/doLogin" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-mortarboard-fill text-white fs-1"></i>
                        </div>
                        <h1 class="h3 text-primary fw-bold mb-2">MTEGM SMM Portal</h1>
                        <p class="text-secondary small mb-4">Milli Eğitim Bakanlığı<br>Mesleki ve Teknik Eğitim Genel Müdürlüğü</p>
                    </div>

                    <?php if (isset($_SESSION['logout_message'])): ?>
                        <div class="alert alert-success rounded-3" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php
                                echo $_SESSION['logout_message'];
                                unset($_SESSION['logout_message']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error) || isset($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php
                                echo $error ?? $_SESSION['login_error'] ?? 'Giriş hatası';
                                unset($_SESSION['login_error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Kullanıcı Adı" required autofocus>
                        <label for="username">
                            <i class="bi bi-person me-2"></i>Kullanıcı Adı
                        </label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Şifre" required>
                        <label for="password">
                            <i class="bi bi-lock me-2"></i>Şifre
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="remember" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Beni hatırla
                        </label>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary py-3 fw-medium" type="submit">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Giriş Yap
                    </button>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="#" class="text-body-secondary small">Şifremi Unuttum</a>
                    </div>
                </form>
            </div>
        </div>

        <p class="mt-4 text-center text-white-50 small">
            &copy; <?php echo date('Y'); ?> T.C. Milli Eğitim Bakanlığı
        </p>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-focus on username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.focus();
            }
        });

        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>