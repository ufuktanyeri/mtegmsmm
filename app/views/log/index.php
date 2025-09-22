<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Logs';
$page_title = 'Logs';
$breadcrumb = [
  
];
AssetManager::addBundle('datatables');
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?php echo htmlspecialchars($title); ?></h3>
            </div>
            <div class="card-body">
                <table id="logsTable" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Giriş Yapan Kullanıcı</th>
                            <th>Tarih</th>
                            <th>IP Adresi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $linenumber=1;
                         foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($linenumber); ?></td>
                                <td><?php echo htmlspecialchars($log->getUsername()); ?></td>
                                <td><?php echo htmlspecialchars($log->getDateTime()); ?></td>
                                <td><?php echo htmlspecialchars($log->getIpAddress()); ?></td>
                            </tr>
                        <?php
                    $linenumber++;
                    endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
