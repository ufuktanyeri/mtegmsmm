<?php
/**
 * Role Debug Tool - Kullanıcı rollerini ve izinlerini kontrol et
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

    echo "<h1>🔍 Role & Permissions Debug</h1>\n";
    echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

    // 1. Roles tablosu
    echo "<h2>👑 Available Roles</h2>\n";
    $roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
    echo "<table border='1' cellpadding='5'>\n";
    echo "<tr><th>ID</th><th>Name</th><th>Display Name</th><th>Description</th></tr>\n";
    foreach ($roles as $role) {
        echo "<tr>";
        echo "<td>" . $role['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($role['name'] ?? '') . "</strong></td>";
        echo "<td>" . htmlspecialchars($role['display_name'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($role['description'] ?? '') . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";

    // 2. User-Role bağlantısı için user_roles tablosunu ara
    $userRoleTables = ['user_roles', 'users_roles', 'role_users'];
    $userRoleTable = null;

    foreach ($userRoleTables as $table) {
        try {
            $pdo->query("SELECT * FROM $table LIMIT 1");
            $userRoleTable = $table;
            echo "<p>✅ User-Role table found: <strong>$table</strong></p>\n";
            break;
        } catch (Exception $e) {
            echo "<p>❌ Table '$table' not found</p>\n";
        }
    }

    if ($userRoleTable) {
        // 3. Kullanıcı-Rol atamaları
        echo "<h2>🔗 User-Role Assignments</h2>\n";
        $userRoles = $pdo->query("
            SELECT ur.*, u.realname, u.username, r.name as role_name, r.display_name
            FROM $userRoleTable ur
            JOIN users u ON ur.user_id = u.id
            JOIN roles r ON ur.role_id = r.id
            ORDER BY u.realname, r.name
        ")->fetchAll();

        if ($userRoles) {
            echo "<table border='1' cellpadding='5'>\n";
            echo "<tr><th>User ID</th><th>Real Name</th><th>Username</th><th>Role Name</th><th>Display Name</th></tr>\n";
            foreach ($userRoles as $ur) {
                $highlight = (strpos(strtolower($ur['role_name']), 'admin') !== false) ? " style='background: #ffffcc;'" : '';
                echo "<tr$highlight>";
                echo "<td>" . $ur['user_id'] . "</td>";
                echo "<td>" . htmlspecialchars($ur['realname']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($ur['username']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($ur['role_name']) . "</td>";
                echo "<td>" . htmlspecialchars($ur['display_name']) . "</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";

            // 4. Admin kullanıcılar özellikle
            echo "<h2>👑 Admin Users - Login Details</h2>\n";
            $adminUsers = $pdo->query("
                SELECT u.id, u.realname, u.username, u.email, r.name as role_name
                FROM users u
                JOIN $userRoleTable ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE r.name LIKE '%admin%'
                ORDER BY u.id
            ")->fetchAll();

            foreach ($adminUsers as $admin) {
                echo "<div style='border: 2px solid #28a745; padding: 15px; margin: 10px 0; background: #f8fff9;'>\n";
                echo "<h3>🔑 " . htmlspecialchars($admin['realname']) . " (ID: " . $admin['id'] . ")</h3>\n";
                echo "<p><strong>👤 Username:</strong> <code style='background: #e9ecef; padding: 3px 6px; font-size: 14px;'>" . htmlspecialchars($admin['username']) . "</code></p>\n";
                echo "<p><strong>📧 Email:</strong> " . htmlspecialchars($admin['email']) . "</p>\n";
                echo "<p><strong>🎭 Role:</strong> <span style='background: #dc3545; color: white; padding: 2px 8px; border-radius: 3px;'>" . htmlspecialchars($admin['role_name']) . "</span></p>\n";

                echo "<div style='background: #fff3cd; padding: 10px; border-radius: 5px; margin-top: 10px;'>\n";
                echo "<h4>🔐 Test Login Info:</h4>\n";
                echo "<p><strong>Username:</strong> <code>" . htmlspecialchars($admin['username']) . "</code></p>\n";
                echo "<p><strong>Possible Passwords to try:</strong></p>\n";
                echo "<ul>\n";
                echo "<li><code>SecurePass123!</code> (from conversations)</li>\n";
                echo "<li><code>admin123</code> (common test password)</li>\n";
                echo "<li><code>Test123!</code> (common test password)</li>\n";
                echo "<li><strong>Your own password</strong> (if this is your real account)</li>\n";
                echo "</ul>\n";
                echo "</div>\n";
                echo "</div>\n";
            }

        } else {
            echo "<p>❌ No user-role assignments found</p>\n";
        }
    } else {
        echo "<p>❌ No user-role relationship table found</p>\n";

        // Alternative: users tablosunda role_id var mı?
        try {
            $usersWithRoleId = $pdo->query("SELECT id, realname, username, role_id FROM users WHERE role_id IS NOT NULL LIMIT 5")->fetchAll();
            if ($usersWithRoleId) {
                echo "<h3>📋 Users with role_id</h3>\n";
                echo "<table border='1' cellpadding='5'>\n";
                echo "<tr><th>ID</th><th>Name</th><th>Username</th><th>Role ID</th></tr>\n";
                foreach ($usersWithRoleId as $user) {
                    echo "<tr>";
                    echo "<td>" . $user['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($user['realname']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . $user['role_id'] . "</td>";
                    echo "</tr>\n";
                }
                echo "</table>\n";
            }
        } catch (Exception $e) {
            echo "<p>❌ No role_id column in users table</p>\n";
        }
    }

    // 5. Permissions
    echo "<h2>🛡️ Available Permissions</h2>\n";
    $permissions = $pdo->query("SELECT * FROM permissions ORDER BY name LIMIT 10")->fetchAll();
    echo "<table border='1' cellpadding='5'>\n";
    echo "<tr><th>ID</th><th>Name</th><th>Display Name</th><th>Category</th></tr>\n";
    foreach ($permissions as $perm) {
        echo "<tr>";
        echo "<td>" . $perm['id'] . "</td>";
        echo "<td>" . htmlspecialchars($perm['name'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($perm['display_name'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($perm['category'] ?? '') . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px;'>\n";
    echo "<h2>❌ Error</h2>\n";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}
?>

<p><em>Role debug completed at: <?php echo date('Y-m-d H:i:s'); ?></em></p>