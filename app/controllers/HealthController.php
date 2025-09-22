<?php
// filepath: app/Controllers/HealthController.php
require_once 'BaseController.php';

class HealthController extends BaseController {
    private function respond($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function check($params = []) {
        $providedKey = $_GET['key'] ?? null;
    // Use constant() to avoid static analysis undefined constant warning if not defined
    $expectedKey = defined('HEALTH_CHECK_KEY') ? constant('HEALTH_CHECK_KEY') : (getenv('HEALTH_CHECK_KEY') ?: null);
        if ($expectedKey && $providedKey !== $expectedKey) {
            $this->respond(['status' => 'forbidden', 'error' => 'Invalid key'], 403);
            return;
        }
        $started = microtime(true);
        $results = [ 'status' => 'ok', 'timestamp' => date('c'), 'php_version' => PHP_VERSION, 'checks' => [] ];
        // DB bağlantısı
        $dbCheck = [ 'ok' => false ];
        try {
            require_once __DIR__ . '/../../config/config.php';
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $t0 = microtime(true);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);
            $dbCheck['ok'] = true; $dbCheck['latency_ms'] = round((microtime(true) - $t0) * 1000,2);
        } catch (Exception $e) { $dbCheck['error'] = $e->getMessage(); $results['status'] = 'degraded'; }
        $results['checks']['database'] = $dbCheck;
        // Tablolar
        $requiredTables = ['users','aims','objectives','actions','indicators'];
        $tablesCheck = [];
        if (!empty($dbCheck['ok'])) {
            try { $stmt = $pdo->query('SHOW TABLES'); $existing = array_map('current',$stmt->fetchAll(PDO::FETCH_NUM));
                foreach($requiredTables as $tbl){ $present = in_array($tbl,$existing,true); $tablesCheck[$tbl] = $present? 'present':'missing'; if(!$present) $results['status']='degraded'; }
            } catch (Exception $e){ $tablesCheck['error']=$e->getMessage(); $results['status']='degraded'; }
        }
        $results['checks']['tables'] = $tablesCheck;
        // Sayım
        $modelChecks = [];
        if (!empty($dbCheck['ok'])) {
            $simpleCounts = [ 'users'=>'SELECT COUNT(*) FROM users','aims'=>'SELECT COUNT(*) FROM aims','objectives'=>'SELECT COUNT(*) FROM objectives','actions'=>'SELECT COUNT(*) FROM actions','indicators'=>'SELECT COUNT(*) FROM indicators' ];
            foreach($simpleCounts as $name=>$sql){ if(isset($tablesCheck[$name]) && $tablesCheck[$name] !== 'present'){ $modelChecks[$name]='skipped'; continue; }
                try { $c0=microtime(true); $val=(int)$pdo->query($sql)->fetchColumn(); $modelChecks[$name]=['count'=>$val,'latency_ms'=>round((microtime(true)-$c0)*1000,2)]; }
                catch(Exception $e){ $modelChecks[$name]=['error'=>$e->getMessage()]; $results['status']='degraded'; }
            }
        }
        $results['checks']['counts'] = $modelChecks;
        // Dizin yazılabilirlik kontrolleri
        $dirChecks = [];
        $importantDirs = [
            realpath(__DIR__ . '/../../wwwroot/uploads/tasks'), // aktif kullanılan
            realpath(__DIR__ . '/../../wwwroot/distribution of tasks'), // eski/legacy
            realpath(sys_get_temp_dir()),
        ];
        foreach ($importantDirs as $d) {
            if ($d === false) continue;
            $ok = is_dir($d) && is_writable($d);
            $dirChecks[$d] = [ 'writable' => $ok ];
            if (!$ok) $results['status'] = 'degraded';
        }
        $results['checks']['directories'] = $dirChecks;
        // PHP ayarları
        $results['checks']['php_ini'] = [
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'error_reporting' => error_reporting(),
            'display_errors' => ini_get('display_errors'),
        ];
        // Genişletmeler
        $requiredExtensions = ['pdo_mysql','mbstring'];
        $extStatus = [];
        foreach ($requiredExtensions as $ext) {
            $loaded = extension_loaded($ext);
            $extStatus[$ext] = $loaded ? 'loaded' : 'missing';
            if (!$loaded) $results['status'] = 'degraded';
        }
        $results['checks']['extensions'] = $extStatus;
        // Disk alanı (root path)
        $rootPath = dirname(__DIR__, 2); // app/Controllers/ -> project root approx.
        $free = @disk_free_space($rootPath);
        $total = @disk_total_space($rootPath);
        if ($free !== false && $total !== false) {
            $percentFree = $total > 0 ? round(($free / $total) * 100, 2) : null;
            $results['checks']['disk'] = [
                'root' => $rootPath,
                'free_bytes' => $free,
                'total_bytes' => $total,
                'free_percent' => $percentFree,
            ];
            if ($percentFree !== null && $percentFree < 5) {
                $results['status'] = 'degraded';
            }
        }
        // Controller metodları
        $controllerDir = __DIR__;
        $controllerFiles = glob($controllerDir.'/*Controller.php');
        $controllersStatus = []; $coreMethods=['index','create','edit','delete','report','adminreport'];
        foreach($controllerFiles as $file){ $base=basename($file,'.php'); if(in_array($base,['BaseController','HealthController'],true)) continue;
            try { require_once $file; if(!class_exists($base)){ $controllersStatus[$base]=['error'=>'class_not_found']; continue; }
                $ref=new ReflectionClass($base); $existing=array_map(fn($m)=>$m->getName(),$ref->getMethods(ReflectionMethod::IS_PUBLIC)); $map=[]; foreach($coreMethods as $m){ $map[$m]=in_array($m,$existing,true); }
                $controllersStatus[$base]=$map; }
            catch(Throwable $t){ $controllersStatus[$base]=['error'=>$t->getMessage()]; $results['status']='degraded'; }
        }
        $results['checks']['controllers']=$controllersStatus; $results['duration_ms']=round((microtime(true)-$started)*1000,2);
        $this->respond($results);
    }
}
?>
