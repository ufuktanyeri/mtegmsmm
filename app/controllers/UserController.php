<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../../includes/Pepper.php';
require_once __DIR__ . '/../models/UserModel.php'; // Model for user-related database operations
require_once __DIR__ . '/../models/CoveModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/LogModel.php'; // Include LogModel
require_once __DIR__ . '/../models/DetailedLogModel.php'; // Include DetailedLogModel
require_once __DIR__ . '/../validators/LoginValidator.php';
require_once __DIR__ . '/../validators/RegisterValidator.php';
require_once __DIR__ . '/../validators/UserValidator.php';
require_once __DIR__ . '/../validators/UserManageUpdateValidator.php';
require_once __DIR__ . '/../validators/ProfileUpdateValidator.php';
require_once __DIR__ . '/../entities/Permission.php';
require_once __DIR__ . '/../models/PermissionModel.php';
require_once __DIR__ . '/../models/NewsModel.php';
require_once __DIR__ . '/../models/GalleryModel.php';
require_once __DIR__ . '/../../includes/Recaptcha.php';
require_once __DIR__ . '/../../includes/Security.php';
// New security classes
require_once __DIR__ . '/../../includes/SecurityLogger.php';
require_once __DIR__ . '/../../includes/AccountSecurity.php';
require_once __DIR__ . '/../../includes/SessionManager.php';
require_once __DIR__ . '/../../includes/PasswordPolicy.php';

class UserController extends BaseController
{
    protected function checkUserPermission(string $perm = 'users.manage'): void
    {
        $permissions = isset($_SESSION['permissions']) ? array_map('unserialize', $_SESSION['permissions']) : [];
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {
                if ($permission->getPermissionName() === htmlspecialchars($perm)) {
                    $hasPermission = true;
                    break;
                }
            }
        }

        if (!$hasPermission) {
            header('Location: index.php?url=home/error');
            exit();
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $passwordPolicy = new PasswordPolicy();
            $securityLogger = new SecurityLogger();
            
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $errors[] = 'Geçersiz CSRF token';
                $this->render('user/register', ['title' => htmlspecialchars('Kayıt Ol'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            // Validate form input
            $validator = new RegisterValidator($_POST);
            $validationErrors = $validator->validateForm();
            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }

            // Sanitize and validate input
            $realname = filter_input(INPUT_POST, 'realname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            // Validate password against policy
            $passwordErrors = $passwordPolicy->validatePassword($password);
            if (!empty($passwordErrors)) {
                $errors = array_merge($errors, $passwordErrors);
            }

            // Check if username or email already exists
            $userModel = new UserModel();
            if ($userModel->usernameExists($username)) {
                $errors[] = 'Kullanıcı adı zaten mevcut';
            }
            if ($userModel->emailExists($email)) {
                $errors[] = 'E-posta zaten mevcut';
            }

            // If there are validation errors, show them
            if (!empty($errors)) {
                $csrfToken = $this->getCSRFToken();
                $policySettings = $passwordPolicy->getPolicySettings();
                $this->render('user/register', [
                    'title' => htmlspecialchars('Kayıt Ol'), 
                    'csrfToken' => $csrfToken, 
                    'errors' => $errors,
                    'passwordPolicy' => $policySettings
                ]);
                return;
            }

            // Hash the password using Pepper
            $pepper = new Pepper();
            $hashedPassword = $pepper->hashPassword($password);

            // Insert user into the database using the UserModel
            $userId = $userModel->createUser($realname, $username, $hashedPassword, $email);

            if ($userId) {
                // Initialize password history with first password
                $passwordPolicy->addToPasswordHistory($userId, $hashedPassword);
                
                // Assign the default 'guest' role to the new user
                $userModel->assignRole($userId, 'guest');
                
                // Log user registration
                $securityLogger->logSecurityEvent(
                    $userId,
                    $username,
                    'user_registered',
                    null,
                    null,
                    null,
                    ['registration_method' => 'web_form'],
                    true
                );
                
                // Redirect to login page with success message
                $_SESSION['registration_success'] = 'Kayıt işlemi başarılı! Şimdi giriş yapabilirsiniz.';
                header('Location: index.php?url=user/login');
                exit();
            } else {
                $errors[] = 'Kayıt işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.';
                $csrfToken = $this->getCSRFToken();
                $policySettings = $passwordPolicy->getPolicySettings();
                $this->render('user/register', [
                    'title' => htmlspecialchars('Kayıt Ol'), 
                    'csrfToken' => $csrfToken, 
                    'errors' => $errors,
                    'passwordPolicy' => $policySettings
                ]);
            }
        } else {
            // Generate a CSRF token
            $csrfToken = $this->getCSRFToken();
            $passwordPolicy = new PasswordPolicy();
            $policySettings = $passwordPolicy->getPolicySettings();
            
            $this->render('user/register', [
                'title' => htmlspecialchars('Kayıt Ol'), 
                'csrfToken' => $csrfToken,
                'passwordPolicy' => $policySettings
            ]);
        }
    }

    public function login()
    {
        // Check if user is already logged in
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?url=home');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $securityLogger = new SecurityLogger();
            $accountSecurity = new AccountSecurity();
            $sessionManager = new SessionManager();
            
            // Rate limiting check - 5 attempts per 15 minutes per IP
            $userIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            if (!Security::checkRateLimit('login_' . $userIP, 5, 900)) {
                array_push($errors, 'Çok fazla giriş denemesi yapıldı. 15 dakika sonra tekrar deneyin.');
                $csrfToken = $this->getCSRFToken();
                $this->render('user/login', ['title' => htmlspecialchars('Giriş Yap'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }
            
            // Validate CSRF token using BaseController method
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->validateCSRFToken($csrfToken)) {
                array_push($errors, 'Güvenlik token\'ı geçersiz veya süresi dolmuş. Lütfen tekrar deneyin.');
                $csrfToken = $this->getCSRFToken();
                $this->render('user/login', ['title' => htmlspecialchars('Giriş Yap'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }
            
            // Google reCAPTCHA v2 kontrolü
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
            
            if (empty($recaptchaResponse)) {
                array_push($errors, 'reCAPTCHA doğrulaması gerekli. Lütfen doğrulamayı tamamlayın.');
            } else {
                $recaptchaResult = Recaptcha::verifyV2($recaptchaResponse);
                
                if (!$recaptchaResult['success']) {
                    error_log('reCAPTCHA verification failed: ' . implode(', ', $recaptchaResult['error-codes'] ?? []));
                    array_push($errors, 'reCAPTCHA doğrulaması başarısız. Lütfen tekrar deneyin.');
                } else {
                    error_log('reCAPTCHA verification successful');
                }
            }

            // Validate form input
            $validator = new LoginValidator($_POST);
            $validationErrors = $validator->validateForm();
            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }

            if (!empty($errors)) {
                $csrfToken = $this->getCSRFToken();
                $this->render('user/login', ['title' => htmlspecialchars('Giriş Yap'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            // Sanitize and validate input
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Get user by username
            $userModel = new UserModel();
            $user = $userModel->getUserByUsername($username);

            // Check if user exists and account is active
            if (!$user) {
                // Log failed login for non-existent user
                $securityLogger->logFailedLogin($username, ['reason' => 'user_not_found']);
                array_push($errors, 'Geçersiz kullanıcı adı veya parola');
                $csrfToken = $this->getCSRFToken();
                $this->render('user/login', ['title' => htmlspecialchars('Giriş Yap'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            $userId = $user->getId();

            // Check if account is active
            if (!$accountSecurity->isAccountActive($userId)) {
                $securityLogger->logFailedLogin($username, ['reason' => 'account_inactive']);
                array_push($errors, 'Hesabınız deaktive edilmiştir. Lütfen yönetici ile iletişime geçin.');
                $csrfToken = $this->getCSRFToken();
                $this->render('user/login', ['title' => htmlspecialchars('Giriş Yap'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            // Check if account is locked
            if ($accountSecurity->isAccountLocked($userId)) {
                $lockInfo = $accountSecurity->getAccountLockInfo($userId);
                $lockUntil = $lockInfo['locked_until'] ? date('H:i', strtotime($lockInfo['locked_until'])) : 'belirsiz';
                
                $securityLogger->logFailedLogin($username, [
                    'reason' => 'account_locked',
                    'locked_until' => $lockInfo['locked_until']
                ]);
                
                array_push($errors, "Hesabınız çok fazla başarısız giriş denemesi nedeniyle kilitlenmiştir. Kilit saat {$lockUntil}'de kalkacaktır.");
                $csrfToken = $this->getCSRFToken();
                $this->render('user/login', ['title' => htmlspecialchars('Giriş Yap'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }

            // Verify password
            $pepper = new Pepper();
            if ($pepper->verifyPassword($password, $user->getPassword())) {
                // Password is correct - proceed with login
                
                // Record successful login (resets failed attempts)
                $accountSecurity->recordSuccessfulLogin($userId);
                
                // Set user session variables
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['realname'] = $user->getRealname();
                $_SESSION['profile_photo'] = $user->getProfilePhoto();

                // Store roles and permissions in session
                $_SESSION['role'] = $user->getRole()->getRoleName();
                $_SESSION['permissions'] = array_map(function ($permission) {
                    return serialize($permission);
                }, $user->getRole()->getPermissions());
                
                // Store cove information if available
                $coveId = null;
                if ($user->getCove()) {
                    $_SESSION['cove_name'] = $user->getCove()->getCoveName();
                    $_SESSION['cove_id'] = $user->getCove()->getId();
                    $coveId = $user->getCove()->getId();
                }

                // Create session record in database
                $sessionManager->createSession($userId, $coveId);

                // Log successful login
                $securityLogger->logSuccessfulLogin($userId, $username, [
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    'cove_id' => $coveId
                ]);

                // Legacy log for compatibility
                $logModel = new LogModel();
                $logModel->createLog($user->getId(), $_SERVER['REMOTE_ADDR']);

                // Check if password needs to be changed
                $passwordPolicy = new PasswordPolicy();
                if ($passwordPolicy->isPasswordExpired($userId)) {
                    $_SESSION['password_expired'] = 'Şifrenizin süresi dolmuştur. Lütfen yeni bir şifre belirleyin.';
                    header('Location: index.php?url=user/changePassword');
                    exit();
                }

                // Check if password will expire soon (within 7 days)
                $daysUntilExpiry = $passwordPolicy->getDaysUntilExpiry($userId);
                if ($daysUntilExpiry !== null && $daysUntilExpiry <= 7 && $daysUntilExpiry > 0) {
                    $_SESSION['password_warning'] = "Şifrenizin süresi {$daysUntilExpiry} gün içinde dolacaktır. Şifrenizi değiştirmenizi öneririz.";
                }

                // Redirect to the homepage
                header('Location: index.php?url=home');
                exit();
                
            } else {
                // Password is incorrect - record failed login
                $accountSecurity->recordFailedLogin($username);
                
                // Check if this attempt caused account lockout
                $lockInfo = $accountSecurity->getAccountLockInfo($userId);
                
                if ($lockInfo['is_locked']) {
                    $lockUntil = $lockInfo['locked_until'] ? date('H:i', strtotime($lockInfo['locked_until'])) : 'belirsiz';
                    array_push($errors, "Çok fazla başarısız giriş denemesi. Hesabınız kilitlenmiştir. Kilit saat {$lockUntil}'de kalkacaktır.");
                } else {
                    $remainingAttempts = 5 - $lockInfo['failed_attempts']; // TODO: Get from config
                    if ($remainingAttempts <= 2) {
                        array_push($errors, "Geçersiz kullanıcı adı veya parola. Kalan deneme hakkı: {$remainingAttempts}");
                    } else {
                        array_push($errors, 'Geçersiz kullanıcı adı veya parola');
                    }
                }
                
                $csrfToken = $this->getCSRFToken();
                $this->render('user/login', ['title' => htmlspecialchars('Giriş Yap'), 'csrfToken' => $csrfToken, 'errors' => $errors]);
                return;
            }
        } else {
            // Generate a CSRF token for GET request
            $csrfToken = $this->getCSRFToken();
            $errors = [];

            // Check for login errors from AuthController
            if (isset($_SESSION['login_error'])) {
                $errors[] = $_SESSION['login_error'];
                unset($_SESSION['login_error']);
            }

            $this->render('user/login', [
                'title' => htmlspecialchars('Giriş Yap'),
                'csrfToken' => $csrfToken,
                'errors' => $errors
            ]);
        }
    }

    public function captcha()
    {



        // Rastgele 5 karakterli Captcha kodu oluştur
        $captchaCode = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);

        // Kodun doğrulanması için oturuma kaydedelim
        $_SESSION["captcha"] = $captchaCode;

        // Görsel oluşturmak için GD kütüphanesi ile boş bir resim yapısı oluştur
        $width = 150;
        $height = 50;
        $image = imagecreate($width, $height);

        // Arkaplan rengi (Beyaz)
        $bgColor = imagecolorallocate($image, 255, 255, 255);

        // Yazı rengi (Siyah)
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // Gürültü eklemek için gri çizgiler
        $noiseColor = imagecolorallocate($image, 180, 180, 180);
        for ($i = 0; $i < 50; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $noiseColor);
        }

        // Yazıyı ekleyelim
        $font = __DIR__ . '/../fonts/arial.ttf'; // Arial.ttf dosyasını aynı klasöre koymalısınız!
        imagettftext($image, 22, rand(-10, 10), 30, 35, $textColor, $font, $captchaCode);

        // Görseli çıktı al ve hafızayı temizle
        header("Content-Type: image/png");
        imagepng($image);
        imagedestroy($image);

    }

    public function manage()
    {
        $this->checkUserPermission();
        $userModel = new UserModel();
        $users = $userModel->getAllUsers();
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;
        $this->render('user/manage', ['title' => 'Kullanıcılar', 'users' => $users, 'csrfToken' => $csrfToken]);
    }

    public function create()
    {
        $this->checkUserPermission();
        $coveModel = new CoveModel();
        $roleModel = new RoleModel();
        $coves = $coveModel->getAllCoves();
        $roles = $roleModel->getAllRoles();
      

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('user/create', ['title' => 'Kullanıcı Ekle', 'coves' => $coves, 'roles' => $roles, 'error' => $error]);
                return;
            }

            $validator = new UserValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('user/create', ['title' => 'Kullanıcı Ekle', 'coves' => $coves, 'roles' => $roles, 'errors' => $errors, 'csrfToken' => $csrfToken]);
                return;
            }

            $realname = filter_input(INPUT_POST, 'realname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $coveId = filter_input(INPUT_POST, 'cove', FILTER_SANITIZE_NUMBER_INT);
            $roleId = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);


            if ($realname && $username && $password && $email && $coveId && $roleId) {
                $pepper = new Pepper();
                $hashedPassword = $pepper->hashPassword($password);

                $userModel = new UserModel();
                $db = $userModel->getDb();

                try {
                    // Transaction başlat
                    $db->beginTransaction();


                    $userId = $userModel->createUser($realname, $username, $hashedPassword, $email);
                    $userModel->assignRoleByRoleId($userId, $roleId);
                    $userModel->assignCove($userId, $coveId);
                    // İşlemler sorunsuz tamamlandığında commit yap
                    $db->commit();

                    header('Location: index.php?url=user/manage');
                    exit();
                } catch (Exception $e) {
                    // Hata alınırsa rollback yap
                    $db->rollback();
                    header('Location: index.php?url=home/error');
                    exit();
                }
            } else {
                $error = 'Tüm alanlar gerekli';
                $this->render('user/create', ['title' => 'Kullanıcı Ekle', 'coves' => $coves, 'roles' => $roles, 'error' => $error, 'csrfToken' => $csrfToken]);
            }
        } else {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            $this->render('user/create', ['title' => 'Kullanıcı Ekle', 'coves' => $coves, 'roles' => $roles, 'csrfToken' => $csrfToken]);
        }
    }

    public function edit($params)
    {
        $this->checkUserPermission();
        $id = htmlspecialchars($params['id']);
        $userModel = new UserModel();
        $coveModel = new CoveModel();
        $roleModel = new RoleModel();
    $user = $userModel->getUserById($id);
    $coves = $coveModel->getAllCoves();
    $roles = $roleModel->getAllRoles();
    // Ad Soyad select'i için tüm kullanıcı adlarını getir (gerekirse cache yapılabilir)
    $allUsers = $userModel->getAllUsers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('user/edit', ['title' => 'Kullanıcı Güncelle', 'user' => $user, 'coves' => $coves, 'roles' => $roles, 'allUsers' => $allUsers, 'error' => $error]);
                return;
            }
            $validator = new UserManageUpdateValidator($_POST);
            $errors = $validator->validateForm();
            if (!empty($errors)) {
                $this->render('user/edit', ['title' => 'Kullanıcı Güncelle', 'user' => $user, 'coves' => $coves, 'roles' => $roles, 'allUsers' => $allUsers, 'errors' => $errors, 'csrfToken' => $csrfToken]);
                return;
            }
            $password = '';
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $realname = filter_input(INPUT_POST, 'realname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($_POST['password'] != '') {
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $coveId = filter_input(INPUT_POST, 'cove', FILTER_SANITIZE_NUMBER_INT);
            $roleId = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);

            if ($realname && $username && $email && $coveId && $roleId) {
                $db = $userModel->getDb();

                try {
                    // Transaction başlat
                    $db->beginTransaction();

                    if ($password != '') {
                        $pepper = new Pepper();
                        $hashedPassword = $pepper->hashPassword($password);
                        $userModel->updateUser($id, $realname, $username, $hashedPassword, $email);
                    } else {
                        $userModel->updateUserWithoutPassword($id, $realname, $username, $email);
                    }

                    // Check if user role exists, then update or insert
                    if ($userModel->userRoleExists($id)) {
                        $userModel->updateUserRole($id, $roleId);
                    } else {
                        $userModel->assignRoleByRoleId($id, $roleId);
                    }

                    // Check if user cove exists, then update or insert
                    if ($userModel->userCoveExists($id)) {
                        $userModel->updateUserCove($id, $coveId);
                    } else {
                        $userModel->assignCove($id, $coveId);
                    }

                    $db->commit();

                    // Log the admin edit event
                    $detailedLogModel = new DetailedLogModel();
                    $detailedLogModel->createDetailedLog($_SESSION['user_id'], 'adminedit', 'User', "id:" . $id . " - " . $username, $_SERVER['REMOTE_ADDR']);

                    header('Location: index.php?url=user/manage');
                    exit();
                } catch (Exception $e) {
                    // Hata alınırsa rollback yap
                    $db->rollback();
                    header('Location: index.php?url=home/error');
                    exit();
                }
            } else {
                 
                $error = 'Tüm alanlar gerekli';
                $this->render('user/edit', ['title' => 'Kullanıcı Güncelle', 'user' => $user, 'coves' => $coves, 'roles' => $roles, 'allUsers' => $allUsers, 'error' => $error, 'csrfToken' => $csrfToken]);
            }
        } else {
            // Generate a CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            $this->render('user/edit', ['title' => 'Kullanıcı Güncelle', 'user' => $user, 'coves' => $coves, 'roles' => $roles, 'allUsers' => $allUsers, 'csrfToken' => $csrfToken]);
        }
    }

    public function delete($params)
    {
        $this->checkUserPermission();
        $id = htmlspecialchars($params['id']);

        // Validate CSRF token
        $csrfToken = $_GET['csrf_token'];
        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            header('Location: index.php?url=user/manage&error=Geçersiz CSRF token');
            exit();
        }

        $userModel = new UserModel();
        $user = $userModel->getUserById($id);

        if ($user) {
            $userModel->deleteUser($id);
            header('Location: index.php?url=user/manage');
        } else {
            header('Location: index.php?url=user/manage&error=Kullanıcı bulunamadı');
        }
        exit();
    }

    public function logout()
    {
        // Initialize security components
        $securityLogger = new SecurityLogger();
        $sessionManager = new SessionManager();
        
        // Get user info before destroying session
        $userId = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? null;
        
        if ($userId && $username) {
            // Log successful logout
            $securityLogger->logLogout($userId, $username);
            
            // Destroy session in database
            $sessionManager->destroySession();
        }
        
        // Destroy PHP session
        session_destroy();
        
        // Clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Redirect to login page
        header('Location: index.php?url=user/login');
        exit();
    }
    
    public function changePassword()
    {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=user/login');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $passwordPolicy = new PasswordPolicy();
        $securityLogger = new SecurityLogger();
        
        // Check if password change is forced
        $isForced = $passwordPolicy->isPasswordExpired($userId);
        $daysUntilExpiry = $passwordPolicy->getDaysUntilExpiry($userId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->validateCSRFToken($csrfToken)) {
                $errors[] = 'Güvenlik token\'ı geçersiz veya süresi dolmuş.';
            }
            
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Basic validation
            if (empty($currentPassword)) {
                $errors[] = 'Mevcut şifre gerekli.';
            }
            
            if (empty($newPassword)) {
                $errors[] = 'Yeni şifre gerekli.';
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Yeni şifre ve onay şifresi eşleşmiyor.';
            }
            
            if (empty($errors)) {
                // Use PasswordPolicy to update password
                $result = $passwordPolicy->updatePassword($userId, $newPassword, $currentPassword);
                
                if ($result['success']) {
                    $_SESSION['password_change_success'] = 'Şifreniz başarıyla güncellendi.';
                    
                    // If this was a forced password change, redirect to main page
                    if ($isForced) {
                        header('Location: index.php');
                    } else {
                        header('Location: index.php?url=user/editProfile');
                    }
                    exit();
                } else {
                    $errors = array_merge($errors, $result['errors']);
                }
            }
            
            // Render form with errors
            $csrfToken = $this->getCSRFToken();
            $policySettings = $passwordPolicy->getPolicySettings();
            
            $this->render('user/changePassword', [
                'title' => 'Şifre Değiştir',
                'csrfToken' => $csrfToken,
                'errors' => $errors,
                'isForced' => $isForced,
                'daysUntilExpiry' => $daysUntilExpiry,
                'passwordPolicy' => $policySettings
            ]);
            
        } else {
            // GET request - show form
            $csrfToken = $this->getCSRFToken();
            $policySettings = $passwordPolicy->getPolicySettings();
            
            $this->render('user/changePassword', [
                'title' => 'Şifre Değiştir',
                'csrfToken' => $csrfToken,
                'isForced' => $isForced,
                'daysUntilExpiry' => $daysUntilExpiry,
                'passwordPolicy' => $policySettings
            ]);
        }
    }

    public function editProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=user/login');
            exit();
        }

        // Users can edit their own profile without additional permissions

        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //  print_r($_POST);

            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'error' => $error]);
                return;
            }

            $validator = new ProfileUpdateValidator($_POST);
            $errors = $validator->validateForm();
            //var_dump($errors);
            if (!empty($errors)) {
                $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'errors' => $errors, 'csrfToken' => $csrfToken]);
                return;
            }

            $realname = filter_input(INPUT_POST, 'realname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if ($userModel->usernameExists($username, $user->getId())) {
                $error = 'Kullanıcı adı zaten mevcut';
                $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'error' => $error, 'csrfToken' => $csrfToken]);
                return;
            }
            if ($userModel->emailExists($email, $user->getId())) {
                $error = 'E-posta zaten mevcut';
                $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'error' => $error, 'csrfToken' => $csrfToken]);
                return;
            }

            // Handle profile photo upload
            $profilePhoto = $user->getProfilePhoto(); // Keep existing photo by default
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $uploadedFile = $_FILES['profile_photo'];
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($uploadedFile['type'], $allowedTypes)) {
                    $error = 'Sadece JPG, JPEG ve PNG dosyaları yüklenebilir';
                    $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'error' => $error, 'csrfToken' => $csrfToken]);
                    return;
                }
                
                // Validate file size (5MB max)
                if ($uploadedFile['size'] > 5 * 1024 * 1024) {
                    $error = 'Dosya boyutu çok büyük. Maksimum 5MB olmalıdır';
                    $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'error' => $error, 'csrfToken' => $csrfToken]);
                    return;
                }
                
                // Generate unique filename
                $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . $user->getId() . '_' . time() . '.' . $extension;
                $uploadPath = __DIR__ . '/../../wwwroot/uploads/profiles/' . $filename;
                
                // Create directory if it doesn't exist
                $uploadDir = dirname($uploadPath);
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Move uploaded file
                if (move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
                    // Delete old profile photo if exists
                    if ($user->getProfilePhoto() && file_exists(__DIR__ . '/../../wwwroot/uploads/profiles/' . $user->getProfilePhoto())) {
                        unlink(__DIR__ . '/../../wwwroot/uploads/profiles/' . $user->getProfilePhoto());
                    }
                    $profilePhoto = $filename;
                } else {
                    $error = 'Dosya yüklenirken hata oluştu';
                    $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'error' => $error, 'csrfToken' => $csrfToken]);
                    return;
                }
            }

            // Update user with or without photo
            if ($password) {
                $pepper = new Pepper();
                $hashedPassword = $pepper->hashPassword($password);
                if ($profilePhoto !== $user->getProfilePhoto()) {
                    $userModel->updateUserWithPhoto($user->getId(), $realname, $username, $hashedPassword, $email, $profilePhoto);
                } else {
                    $userModel->updateUser($user->getId(), $realname, $username, $hashedPassword, $email);
                }
            } else {
                if ($profilePhoto !== $user->getProfilePhoto()) {
                    $userModel->updateUserWithoutPasswordButWithPhoto($user->getId(), $realname, $username, $email, $profilePhoto);
                } else {
                    $userModel->updateUserWithoutPassword($user->getId(), $realname, $username, $email);
                }
            }

            // Update session data
            $_SESSION['realname'] = $realname;
            $_SESSION['username'] = $username;
            $_SESSION['profile_photo'] = $profilePhoto;

            // Log the profile edit event
            $detailedLogModel = new DetailedLogModel();
            $detailedLogModel->createDetailedLog($user->getId(), 'profileedit', 'user', $user->getUsername(), $_SERVER['REMOTE_ADDR']);

            // Redirect with success message
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $userModel->getUserById($_SESSION['user_id']), 'success' => true, 'csrfToken' => $csrfToken]);
            return;
        } else {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            //print_r($csrfToken);
            $this->render('user/editProfile', ['title' => 'Profil Düzenle', 'user' => $user, 'csrfToken' => $csrfToken]);
        }
    }

    public function main()
    {
       
$newsModel = new NewsModel();
$headlineNews = $newsModel->getHeadlineNewsOrderedLimited(5);
$this->render('user/main', [
    // ...existing data...
    'headlineNews' => $headlineNews
]);
// ...existing code...
      //  $this->render('user/main', ['title' => 'Main Page']);
    }

    public function roles()
    {
        $this->checkUserPermission('roles.manage'); // Ensure the user has permission to manage roles
        $roleModel = new RoleModel();
        $roles = $roleModel->getAllRoles();
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;
        $this->render('user/roles', ['title' => 'Roller', 'roles' => $roles, 'csrfToken' => $csrfToken]);
    }

    public function editRole($params)
    {
        $this->checkUserPermission('roles.manage');
        $roleId = htmlspecialchars($params['id']);
        $roleModel = new RoleModel();
        $role = $roleModel->getRoleById($roleId);
        $roles = $roleModel->getAllRoles();

        if (!$role) {
            header('Location: index.php?url=user/roles&error=Role not found');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('user/editRole', ['title' => 'Edit Role', 'role' => $role, 'roles' => $roles, 'error' => $error]);
                return;
            }

            $roleName = filter_input(INPUT_POST, 'roleName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $parentRoleId = filter_input(INPUT_POST, 'parentRoleId', FILTER_VALIDATE_INT);

            if ($parentRoleId && !$roleModel->roleExists($parentRoleId)) {
                $error = 'Invalid parent role selected.';
                $this->render('user/editRole', ['title' => 'Edit Role', 'role' => $role, 'roles' => $roles, 'error' => $error]);
                return;
            }

            if ($roleName && $description) {
                $roleModel->updateRole($roleId, $roleName, $description, $parentRoleId);
                header('Location: index.php?url=user/roles');
                exit();
            } else {
                $error = 'All fields are required.';
                $this->render('user/editRole', ['title' => 'Edit Role', 'role' => $role, 'roles' => $roles, 'error' => $error]);
            }
        } else {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            $this->render('user/editRole', ['title' => 'Edit Role', 'role' => $role, 'roles' => $roles, 'csrfToken' => $csrfToken]);
        }
    }

    public function deleteRole($params)
    {
        $this->checkUserPermission('roles.manage');
        $roleId = htmlspecialchars($params['id']);

        // Validate CSRF token
        $csrfToken = $_GET['csrf_token'];
        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            header('Location: index.php?url=user/roles&error=Geçersiz CSRF token');
            exit();
        }

        $roleModel = new RoleModel();
        $role = $roleModel->getRoleById($roleId);

        if ($role) {
            $roleModel->deleteRole($roleId);
            header('Location: index.php?url=user/roles');
        } else {
            header('Location: index.php?url=user/roles&error=Rol bulunamadı');
        }
        exit();
    }

    public function createRole()
    {
        $this->checkUserPermission('roles.manage'); // Ensure the user has permission to manage roles
        $roleModel = new RoleModel();
        $roles = $roleModel->getAllRoles(); // Fetch all roles for the parent dropdown

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('user/createRole', ['title' => 'Create Role', 'roles' => $roles, 'error' => $error]);
                return;
            }

            $roleName = filter_input(INPUT_POST, 'roleName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $parentRoleId = filter_input(INPUT_POST, 'parentRoleId', FILTER_VALIDATE_INT);

            // Validate parentRoleId
            if (!$parentRoleId && !$roleModel->roleExists($parentRoleId)) {
                $error = 'Ebeveyn Rol Seçiniz.';
                $this->render('user/createRole', ['title' => 'Create Role', 'roles' => $roles, 'error' => $error]);
                return;
            }

            if ($roleName && $description) {
                $roleModel->createRole($roleName, $description, $parentRoleId);
                header('Location: index.php?url=user/roles');
                exit();
            } else {
                $error = 'All fields are required.';
                $this->render('user/createRole', ['title' => 'Create Role', 'roles' => $roles, 'error' => $error]);
            }
        } else {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            $this->render('user/createRole', ['title' => 'Create Role', 'roles' => $roles, 'csrfToken' => $csrfToken]);
        }
    }

    public function permissions()
    {
        $this->checkUserPermission('permissions.manage'); // Ensure the user has permission to manage permissions
        $permissionModel = new PermissionModel();
        $permissions = $permissionModel->getAllPermissions();
        $this->render('user/permissions', ['title' => 'İzinler', 'permissions' => $permissions]);
    }

    public function createPermission()
    {
        $this->checkUserPermission('permissions.manage');
        $roleModel = new RoleModel();
        $roles = $roleModel->getAllRoles();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $error = 'Geçersiz CSRF token';
                $this->render('user/createPermission', ['title' => 'Yeni İzin Ekle', 'roles' => $roles, 'error' => $error]);
                return;
            }

            $permissionName = filter_input(INPUT_POST, 'permissionName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $roleId = filter_input(INPUT_POST, 'roleId', FILTER_VALIDATE_INT);

            if ($permissionName && $description && $roleId) {
                $permissionModel = new PermissionModel();
                $permissionId = $permissionModel->createPermission($permissionName, $description);

                // Assign the permission to the selected role
                $permissionModel->assignPermissionToRole($permissionId, $roleId);

                header('Location: index.php?url=user/permissions');
                exit();
            } else {
                $error = 'Tüm alanlar gerekli';
                $csrfToken = bin2hex(random_bytes(32));
                $_SESSION['csrf_token'] = $csrfToken;
                $this->render('user/createPermission', ['title' => 'Yeni İzin Ekle', 'roles' => $roles, 'error' => $error, 'csrfToken' => $csrfToken]);
            }
        } else {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            $this->render('user/createPermission', ['title' => 'Yeni İzin Ekle', 'roles' => $roles, 'csrfToken' => $csrfToken]);
        }
    }

    public function editPermission($params)
    {
        $this->checkUserPermission('permissions.manage');
        $id = htmlspecialchars($params['id']);
        $permissionModel = new PermissionModel();
        $roleModel = new RoleModel();
        $permission = $permissionModel->getPermissionById($id);
        $roles = $roleModel->getAllRoles();
        $assignedRoleId = $permissionModel->getRoleIdByPermissionId($id);

        if (!$permission) {
            header('Location: index.php?url=user/permissions&error=İzin bulunamadı');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'];
            if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
                $csrfToken = bin2hex(random_bytes(32));
                $_SESSION['csrf_token'] = $csrfToken;
                $error = 'Geçersiz CSRF token';
                $this->render('user/editPermission', ['title' => 'İzin Düzenle', 'permission' => $permission, 'roles' => $roles, 'assignedRoleId' => $assignedRoleId, 'error' => $error, 'csrfToken' => $csrfToken]);
                return;
            }

            $permissionName = filter_input(INPUT_POST, 'permissionName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $roleId = filter_input(INPUT_POST, 'roleId', FILTER_VALIDATE_INT);

            if ($permissionName && $description && $roleId) {
                // Prevent assigning the same permission to multiple roles
                if ($permissionModel->rolePermissionExists($id)) {
                    $permissionModel->updateRolePermission($id, $roleId);
                } else {
                    $permissionModel->assignPermissionToRole($id, $roleId);
                }

                $permissionModel->updatePermission($id, $permissionName, $description);

                // Update the role-permission relationship
                //$permissionModel->assignPermissionToRole($id, $roleId);

                // Check if user cove exists, then update or insert
               


                header('Location: index.php?url=user/permissions');
                exit();
            } else {
                $csrfToken = bin2hex(random_bytes(32));
                $_SESSION['csrf_token'] = $csrfToken;
                $error = 'Tüm alanlar gerekli';
                $this->render('user/editPermission', ['title' => 'İzin Düzenle', 'permission' => $permission, 'roles' => $roles, 'assignedRoleId' => $assignedRoleId, 'error' => $error, 'csrfToken' => $csrfToken]);
            }
        } else {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            $this->render('user/editPermission', ['title' => 'İzin Düzenle', 'permission' => $permission, 'roles' => $roles, 'assignedRoleId' => $assignedRoleId, 'csrfToken' => $csrfToken]);
        }
    }

    public function deletePermission($params)
    {
        $this->checkUserPermission();
        $permissionModel = new PermissionModel();
        $id = htmlspecialchars($params['id']);
        $csrfToken = $_GET['csrf_token'];

        if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
            header('Location: index.php?url=user/permissions&error=Geçersiz CSRF token');
            exit();
        }

        $permission = $permissionModel->getPermissionById($id);

        if ($permission) {
            $permissionModel->deletePermission($id);
            header('Location: index.php?url=user/permissions');
        } else {
            header('Location: index.php?url=user/permissions&error=İzin bulunamadı');
        }
        exit();
    }

    public function haberler($params) {
        // id parametresi kontrolü
        $id = htmlspecialchars($params['id']);
        $news = null;
        $gallery = [];
        $title = 'Single Page';
        $page_title = 'Single Page';

        if ($id > 0) {
            $newsModel = new NewsModel();
            $galleryModel = new GalleryModel();
            $news = $newsModel->getNewsPublicById($id); // user_id null ile tüm haberler için
            if ($news) {
                $gallery = $galleryModel->getGalleryByNewsId($id);
                $title = $news->getTitle();
                $page_title = $news->getTitle();
            }
        }

        $this->render('user/haberler', [
            'title' => $title,
            'page_title' => $page_title,
            'news' => $news,
            'gallery' => $gallery
        ]);
    }

    public function haberlist($params)
    {
        $newsModel = new NewsModel();
        $perPage = 20;
        $currentPage = isset($params['page']) ? (int)$params['page'] : 1;
        $offset = ($currentPage - 1) * $perPage;

        // Toplam haber sayısı
        $totalNews = $newsModel->countAllActiveNews();
        // Sadece aktif haberleri ve sıralamaya göre getir (limitli)
        $pagedNews = $newsModel->getAllActiveNewsOrderedPaged($perPage, $offset);

        $this->render('user/haberlist', [
            'title' => 'Haberler',
            'newsList' => $pagedNews,
            'totalNews' => $totalNews,
            'perPage' => $perPage,
            'currentPage' => $currentPage
        ]);
    }

    // Tema fonksiyonları kaldırıldı (setTheme / resetTheme)
}
?>