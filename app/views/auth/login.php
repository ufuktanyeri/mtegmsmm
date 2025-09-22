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

    <style>
        :root {
            --meb-primary: #1e3c72;
            --meb-secondary: #2a5298;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .form-signin {
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }

        .form-signin .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
        }

        .form-signin .card-body {
            padding: 2rem;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="text"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: var(--meb-primary);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .logo i {
            font-size: 2.5rem;
            color: white;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--meb-primary) 0%, var(--meb-secondary) 100%);
            border: none;
            font-weight: 500;
            padding: 0.75rem;
            font-size: 1rem;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--meb-secondary) 0%, var(--meb-primary) 100%);
        }

        .form-control:focus {
            border-color: var(--meb-primary);
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }

        .alert {
            border-radius: 0.5rem;
        }

        .copyright {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .meb-title {
            color: var(--meb-primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .meb-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <main class="form-signin w-100 m-auto">
        <div class="card">
            <div class="card-body">
                <form action="/auth/doLogin" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <div class="logo-container">
                        <div class="logo">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <h1 class="h3 meb-title">MTEGM SMM Portal</h1>
                        <p class="meb-subtitle">Milli Eğitim Bakanlığı<br>Mesleki ve Teknik Eğitim Genel Müdürlüğü</p>
                    </div>

                    <?php if (isset($_SESSION['logout_message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php
                                echo $_SESSION['logout_message'];
                                unset($_SESSION['logout_message']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error) || isset($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger" role="alert">
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

                    <button class="w-100 btn btn-lg btn-primary btn-login" type="submit">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Giriş Yap
                    </button>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="#" class="text-muted small">Şifremi Unuttum</a>
                    </div>
                </form>
            </div>
        </div>

        <p class="mt-4 text-center copyright text-white">
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