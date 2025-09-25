<?php
/**
 * Table Structure Debug - user_roles ve roles tablolarının yapısını kontrol et
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

    echo "<h1>🔍 Table Structure Debug</h1>\n";
    echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

    // 1. Roles tablosu yapısı
    echo "<h2>🏗️ Roles Table Structure</h2>\n";
    $roleColumns = $pdo->query("DESCRIBE roles")->fetchAll();
    echo "<table border='1' cellpadding='5'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
    foreach ($roleColumns as $col) {
        echo "<tr>";
        echo "<td><strong>" . $col['Field'] . "</strong></td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";

    // 2. Roles tablosu içerik
    echo "<h3>📋 Roles Table Content</h3>\n";
    $roles = $pdo->query("SELECT * FROM roles")->fetchAll();
    if (!empty($roles)) {
        echo "<table border='1' cellpadding='5'>\n";
        $headers = array_keys($roles[0]);
        echo "<tr>";
        foreach ($headers as $header) {
            echo "<th>$header</th>";
        }
        echo "</tr>\n";

        foreach ($roles as $role) {
            echo "<tr>";
            foreach ($headers as $header) {
                $value = $role[$header] ?? '';
                if (empty($value)) {
                    echo "<td style='background: #fff3cd;'><em>empty</em></td>";
                } else {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    }

    // 3. user_roles tablosu yapısı
    echo "<h2>🏗️ User_Roles Table Structure</h2>\n";
    $userRoleColumns = $pdo->query("DESCRIBE user_roles")->fetchAll();
    echo "<table border='1' cellpadding='5'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
    foreach ($userRoleColumns as $col) {
        echo "<tr>";
        echo "<td><strong>" . $col['Field'] . "</strong></td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";

    // 4. user_roles tablosu içerik
    echo "<h3>📋 User_Roles Table Content</h3>\n";
    $userRoles = $pdo->query("SELECT * FROM user_roles")->fetchAll();
    if (!empty($userRoles)) {
        echo "<table border='1' cellpadding='5'>\n";
        $headers = array_keys($userRoles[0]);
        echo "<tr>";
        foreach ($headers as $header) {
            echo "<th>$header</th>";
        }
        echo "</tr>\n";

        foreach ($userRoles as $ur) {
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<td>" . htmlspecialchars($ur[$header] ?? '') . "</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";

        // 5. Kullanıcı-Rol eşleştirme (sadece ID'lerle)
        echo "<h3>🔗 User-Role Matching</h3>\n";
        $userRoleInfo = $pdo->query("
            SELECT ur.user_id, ur.role_id, u.realname, u.username, u.email
            FROM user_roles ur
            JOIN users u ON ur.user_id = u.id
            ORDER BY ur.user_id
        ")->fetchAll();

        if ($userRoleInfo) {
            echo "<table border='1' cellpadding='5'>\n";
            echo "<tr><th>User ID</th><th>Role ID</th><th>Real Name</th><th>Username</th><th>Email</th><th>Role Type</th></tr>\n";
            foreach ($userRoleInfo as $info) {
                $roleType = '';
                switch ($info['role_id']) {
                    case 1: $roleType = 'Guest'; break;
                    case 2: $roleType = 'User'; break;
                    case 3: $roleType = 'Coordinator'; break;
                    case 4: $roleType = 'Admin'; break;
                    case 5: $roleType = 'SuperAdmin'; break;
                    case 6: $roleType = 'News Manager'; break;
                    default: $roleType = 'Unknown';
                }

                $highlight = ($info['role_id'] >= 4) ? " style='background: #ffffcc;'" : '';
                echo "<tr$highlight>";
                echo "<td>" . $info['user_id'] . "</td>";
                echo "<td>" . $info['role_id'] . "</td>";
                echo "<td>" . htmlspecialchars($info['realname']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($info['username']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($info['email']) . "</td>";
                echo "<td><strong>$roleType</strong></td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
    } else {
        echo "<p>❌ No user-role assignments found</p>\n";
    }

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px;'>\n";
    echo "<h2>❌ Error</h2>\n";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}
?>

<p><em>Structure debug completed at: <?php echo date('Y-m-d H:i:s'); ?></em></p>