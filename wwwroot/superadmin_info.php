<?php
/**
 * SuperAdmin Login Info - Giriş için kullanılabilir hesapları göster
 */

require_once __DIR__ . '/../app/config/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
    echo "<title>SuperAdmin Login Info</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .card { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .admin { border-left: 5px solid #dc3545; }
        .superadmin { border-left: 5px solid #28a745; }
        .coordinator { border-left: 5px solid #007bff; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; }
        .login-box { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .test-password { background: #fff3cd; padding: 10px; border-radius: 5px; margin: 5px 0; }
    </style></head><body>";

    echo "<h1>🔑 SuperAdmin Login Information</h1>\n";
    echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

    // SuperAdmin kullanıcıları (role_id = 5)
    $superAdmins = $pdo->query("
        SELECT u.id, u.realname, u.username, u.email, u.is_active, ur.roleId
        FROM users u
        JOIN user_roles ur ON u.id = ur.userId
        WHERE ur.roleId = 5
        ORDER BY u.id
    ")->fetchAll();

    echo "<h2>👑 SuperAdmin Users (Role ID: 5)</h2>\n";
    foreach ($superAdmins as $admin) {
        $activeStatus = $admin['is_active'] ? '✅ Active' : '❌ Inactive';
        echo "<div class='card superadmin'>\n";
        echo "<h3>🔐 " . htmlspecialchars($admin['realname']) . " ($activeStatus)</h3>\n";

        echo "<div class='login-box'>\n";
        echo "<h4>Login Credentials:</h4>\n";
        echo "<p><strong>👤 Username:</strong> <code>" . htmlspecialchars($admin['username']) . "</code></p>\n";
        echo "<p><strong>📧 Email:</strong> " . htmlspecialchars($admin['email']) . "</p>\n";
        echo "<p><strong>🆔 User ID:</strong> " . $admin['id'] . "</p>\n";
        echo "</div>\n";

        echo "<div class='test-password'>\n";
        echo "<h4>🔐 Possible Test Passwords:</h4>\n";
        echo "<ul>\n";
        echo "<li><code>SecurePass123!</code> - Portal guide'da tanımlı</li>\n";
        echo "<li><code>Test123!</code> - Genel test şifresi</li>\n";
        echo "<li><code>admin123</code> - Basit admin şifresi</li>\n";
        echo "<li><strong>Kendi şifreniz</strong> - Eğer bu gerçek hesabınızsa</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
        echo "</div>\n";
    }

    // Admin kullanıcıları (role_id = 4)
    $admins = $pdo->query("
        SELECT u.id, u.realname, u.username, u.email, u.is_active, ur.roleId
        FROM users u
        JOIN user_roles ur ON u.id = ur.userId
        WHERE ur.roleId = 4
        ORDER BY u.id
    ")->fetchAll();

    if ($admins) {
        echo "<h2>🛡️ Admin Users (Role ID: 4)</h2>\n";
        foreach ($admins as $admin) {
            $activeStatus = $admin['is_active'] ? '✅ Active' : '❌ Inactive';
            echo "<div class='card admin'>\n";
            echo "<h3>🔐 " . htmlspecialchars($admin['realname']) . " ($activeStatus)</h3>\n";

            echo "<div class='login-box'>\n";
            echo "<p><strong>👤 Username:</strong> <code>" . htmlspecialchars($admin['username']) . "</code></p>\n";
            echo "<p><strong>📧 Email:</strong> " . htmlspecialchars($admin['email']) . "</p>\n";
            echo "<p><strong>🆔 User ID:</strong> " . $admin['id'] . "</p>\n";
            echo "</div>\n";
            echo "</div>\n";
        }
    }

    // Migration için hazırlanan test kullanıcılar (ID: 52, 53, 54)
    echo "<h2>🎯 Migration Test Users</h2>\n";
    $testUsers = $pdo->query("
        SELECT u.id, u.realname, u.username, u.email, u.is_active, ur.roleId
        FROM users u
        LEFT JOIN user_roles ur ON u.id = ur.userId
        WHERE u.id IN (52, 53, 54)
        ORDER BY u.id
    ")->fetchAll();

    if ($testUsers) {
        echo "<table>\n";
        echo "<tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role ID</th><th>Active</th></tr>\n";
        foreach ($testUsers as $user) {
            $roleText = $user['roleId'] ? $user['roleId'] : 'No Role';
            $activeStatus = $user['is_active'] ? '✅' : '❌';
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . htmlspecialchars($user['realname']) . "</td>";
            echo "<td><code>" . htmlspecialchars($user['username']) . "</code></td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . $roleText . "</td>";
            echo "<td>" . $activeStatus . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p>❌ Migration test users (52, 53, 54) not found</p>\n";
    }

    // Login test formu
    echo "<h2>🧪 Quick Login Test</h2>\n";
    echo "<div class='card'>\n";
    echo "<p>Test etmek istediğiniz hesap bilgilerini girin:</p>\n";
    echo "<form method='post' action='login_test.php'>\n";
    echo "<p><label>Username: <input type='text' name='username' placeholder='erkin_deniz' /></label></p>\n";
    echo "<p><label>Password: <input type='password' name='password' placeholder='SecurePass123!' /></label></p>\n";
    echo "<p><button type='submit'>🔐 Test Login</button></p>\n";
    echo "</form>\n";
    echo "</div>\n";

    echo "</body></html>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px;'>\n";
    echo "<h2>❌ Error</h2>\n";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}
?>