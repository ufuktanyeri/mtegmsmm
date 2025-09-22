<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$title = 'Detaylı Logs';
$page_title = 'Detaylı Logs';
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
                <table id="detailedLogsTable" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                    <thead>
                        <tr>
                            <th></th>
                            <th>İşlemi Yapan</th>
                            <th>İşlem Tipi</th>
                            <th>Kayıt Türü</th>
                            <th>Kayıt Başlığı</th>
                            <th>İşlem Tarihi</th>
                            <th>İşlem Yapılan IP Adresi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                         $linenumber=1;
                        foreach ($detailedLogs as $detailedLog): ?>
                            <tr>
                                <td><?php echo htmlspecialchars( $linenumber); ?></td>
                                <td><?php echo htmlspecialchars($detailedLog->getUsername()); ?></td>
                                <td><?php echo htmlspecialchars($detailedLog->getLogType()); ?></td>
                                <td><?php echo htmlspecialchars($detailedLog->getEntityType()); ?></td>
                                <td><?php echo htmlspecialchars($detailedLog->getEntityTitle()); ?></td>
                                <td><?php echo htmlspecialchars($detailedLog->getDateTime()); ?></td>
                                <td><?php echo htmlspecialchars($detailedLog->getIpAddress()); ?></td>
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
