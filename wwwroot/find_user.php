<?php
/**
 * Belirli kullanıcıları bul - admin_gazi ve adindar_ankara
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

    echo "<h1>🔍 Specific User Search</h1>\n";
    echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

    // admin_gazi kullanıcısını ara
    echo "<h2>👤 Looking for: admin_gazi</h2>\n";
    $adminGazi = $pdo->query("
        SELECT u.*, ur.roleId, r.roleName
        FROM users u
        LEFT JOIN user_roles ur ON u.id = ur.userId
        LEFT JOIN roles r ON ur.roleId = r.id
        WHERE u.username = 'admin_gazi'
    ")->fetch();

    if ($adminGazi) {
        echo "<div style='border: 2px solid #28a745; padding: 15px; margin: 10px 0; background: #f8fff9;'>\n";
        echo "<h3>✅ Found: admin_gazi</h3>\n";
        echo "<p><strong>🆔 User ID:</strong> " . $adminGazi['id'] . "</p>\n";
        echo "<p><strong>📛 Real Name:</strong> " . htmlspecialchars($adminGazi['realname']) . "</p>\n";
        echo "<p><strong>👤 Username:</strong> <code>" . htmlspecialchars($adminGazi['username']) . "</code></p>\n";
        echo "<p><strong>📧 Email:</strong> " . htmlspecialchars($adminGazi['email']) . "</p>\n";
        echo "<p><strong>🎭 Role:</strong> " . ($adminGazi['roleName'] ?? 'No Role') . " (ID: " . ($adminGazi['roleId'] ?? 'None') . ")</p>\n";
        echo "<p><strong>✅ Active:</strong> " . ($adminGazi['is_active'] ? 'Yes' : 'No') . "</p>\n";
        echo "</div>\n";
    } else {
        echo "<p>❌ admin_gazi user not found</p>\n";
    }

    // adindar_ankara kullanıcısını ara
    echo "<h2>👤 Looking for: adindar_ankara</h2>\n";
    $adindarAnkara = $pdo->query("
        SELECT u.*, ur.roleId, r.roleName
        FROM users u
        LEFT JOIN user_roles ur ON u.id = ur.userId
        LEFT JOIN roles r ON ur.roleId = r.id
        WHERE u.username = 'adindar_ankara'
    ")->fetch();

    if ($adindarAnkara) {
        echo "<div style='border: 2px solid #28a745; padding: 15px; margin: 10px 0; background: #f8fff9;'>\n";
        echo "<h3>✅ Found: adindar_ankara</h3>\n";
        echo "<p><strong>🆔 User ID:</strong> " . $adindarAnkara['id'] . "</p>\n";
        echo "<p><strong>📛 Real Name:</strong> " . htmlspecialchars($adindarAnkara['realname']) . "</p>\n";
        echo "<p><strong>👤 Username:</strong> <code>" . htmlspecialchars($adindarAnkara['username']) . "</code></p>\n";
        echo "<p><strong>📧 Email:</strong> " . htmlspecialchars($adindarAnkara['email']) . "</p>\n";
        echo "<p><strong>🎭 Role:</strong> " . ($adindarAnkara['roleName'] ?? 'No Role') . " (ID: " . ($adindarAnkara['roleId'] ?? 'None') . ")</p>\n";
        echo "<p><strong>✅ Active:</strong> " . ($adindarAnkara['is_active'] ? 'Yes' : 'No') . "</p>\n";
        echo "</div>\n";
    } else {
        echo "<p>❌ adindar_ankara user not found</p>\n";

        // Benzer isimleri ara
        echo "<h3>🔍 Searching for similar usernames...</h3>\n";
        $similar = $pdo->query("
            SELECT username, realname, id, email
            FROM users
            WHERE username LIKE '%adindar%' OR username LIKE '%ankara%' OR realname LIKE '%adindar%' OR realname LIKE '%ankara%'
        ")->fetchAll();

        if ($similar) {
            echo "<table border='1' cellpadding='5'>\n";
            echo "<tr><th>ID</th><th>Username</th><th>Real Name</th><th>Email</th></tr>\n";
            foreach ($similar as $user) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td><code>" . htmlspecialchars($user['username']) . "</code></td>";
                echo "<td>" . htmlspecialchars($user['realname']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
    }

    // Tüm username'leri içeren pattern ara
    echo "<h2>🔍 All usernames containing 'admin' or 'ankara'</h2>\n";
    $patterns = $pdo->query("
        SELECT u.id, u.username, u.realname, u.email, ur.roleId
        FROM users u
        LEFT JOIN user_roles ur ON u.id = ur.userId
        WHERE u.username LIKE '%admin%' OR u.username LIKE '%ankara%'
        ORDER BY u.username
    ")->fetchAll();

    if ($patterns) {
        echo "<table border='1' cellpadding='5'>\n";
        echo "<tr><th>ID</th><th>Username</th><th>Real Name</th><th>Email</th><th>Role ID</th></tr>\n";
        foreach ($patterns as $user) {
            $highlight = ($user['roleId'] >= 4) ? " style='background: #ffffcc;'" : '';
            echo "<tr$highlight>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($user['username']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($user['realname']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . ($user['roleId'] ?? 'None') . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px;'>\n";
    echo "<h2>❌ Error</h2>\n";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}
?>

<p><em>User search completed at: <?php echo date('Y-m-d H:i:s'); ?></em></p>