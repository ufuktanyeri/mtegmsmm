<?php
/**
 * User Debug Tool - Login bilgilerini kontrol et
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

    echo "<h1>🔍 User Login Debug Tool</h1>\n";
    echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

    // Users tablosu yapısını kontrol et
    echo "<h2>📋 Users Table Structure</h2>\n";
    $columns = $pdo->query("DESCRIBE users")->fetchAll();
    echo "<table border='1' cellpadding='5'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";

    // SuperAdmin kullanıcıları bul
    echo "<h2>👑 SuperAdmin Users</h2>\n";

    // Önce role alanını kontrol et
    $roleFields = ['role', 'user_role', 'user_type', 'authority', 'level'];
    $userTable = null;

    foreach ($roleFields as $field) {
        try {
            $test = $pdo->query("SELECT $field FROM users LIMIT 1")->fetch();
            $userTable = $field;
            echo "<p>✅ Role field found: <strong>$field</strong></p>\n";
            break;
        } catch (Exception $e) {
            echo "<p>❌ Field '$field' not found</p>\n";
        }
    }

    if ($userTable) {
        // Tüm kullanıcıları listele
        echo "<h3>🔍 All Users with Login Info</h3>\n";
        $users = $pdo->query("SELECT id, realname, username, email, $userTable as role FROM users ORDER BY id")->fetchAll();

        echo "<table border='1' cellpadding='5'>\n";
        echo "<tr><th>ID</th><th>Real Name</th><th>Username</th><th>Email</th><th>Role</th></tr>\n";

        foreach ($users as $user) {
            $highlight = (strpos(strtolower($user['role']), 'admin') !== false) ? " style='background: #ffffcc;'" : '';
            echo "<tr$highlight>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . htmlspecialchars($user['realname']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($user['username']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";

        // SuperAdmin kullanıcıları özellikle göster
        $admins = $pdo->query("SELECT * FROM users WHERE $userTable LIKE '%admin%' ORDER BY id")->fetchAll();

        if ($admins) {
            echo "<h3>👑 Admin Users Detail</h3>\n";
            foreach ($admins as $admin) {
                echo "<div style='border: 2px solid #28a745; padding: 15px; margin: 10px 0; background: #f8fff9;'>\n";
                echo "<h4>🔑 " . htmlspecialchars($admin['realname']) . " (ID: " . $admin['id'] . ")</h4>\n";
                echo "<p><strong>Username:</strong> <code>" . htmlspecialchars($admin['username']) . "</code></p>\n";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($admin['email']) . "</p>\n";
                echo "<p><strong>Role:</strong> " . htmlspecialchars($admin[$userTable]) . "</p>\n";

                // Password hash göster (ilk 20 karakter)
                if (isset($admin['password'])) {
                    $passPreview = substr($admin['password'], 0, 20) . "...";
                    echo "<p><strong>Password Hash:</strong> <code>$passPreview</code></p>\n";
                }

                echo "</div>\n";
            }
        }
    } else {
        echo "<p>❌ No role field found in users table</p>\n";

        // Tüm sütunları göster
        echo "<h3>📊 Sample User Data</h3>\n";
        $sample = $pdo->query("SELECT * FROM users ORDER BY id LIMIT 5")->fetchAll();

        if ($sample) {
            $firstUser = $sample[0];
            echo "<p><strong>Available columns:</strong> " . implode(', ', array_keys($firstUser)) . "</p>\n";

            echo "<table border='1' cellpadding='5'>\n";
            $headers = array_keys($firstUser);
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th>$header</th>";
            }
            echo "</tr>\n";

            foreach ($sample as $user) {
                echo "<tr>";
                foreach ($headers as $header) {
                    $value = $user[$header];
                    if ($header === 'password' && strlen($value) > 20) {
                        $value = substr($value, 0, 20) . "...";
                    }
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
    }

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px;'>\n";
    echo "<h2>❌ Error</h2>\n";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}
?>

<p><em>Debug completed at: <?php echo date('Y-m-d H:i:s'); ?></em></p>