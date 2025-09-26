<?php
require_once __DIR__ . "/../../../includes/AssetManager.php";
use App\Helpers\AssetManager;
$pageTitle = 'Yönetim Raporu';
AssetManager::addBundle('datatables');
AssetManager::addBundle('chartjs');
?>

<!-- Page Header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Göstergeler</div>
                <h2 class="page-title">
                    <i class="ti ti-chart-bar me-2"></i>
                    Yönetim Raporu
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="index.php?url=indicator/index" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>
                        Göstergeler
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page Body -->
<div class="page-body">
    <div class="container-xl">
        
        <!-- Statistics Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Toplam Hedef</div>
                            <div class="ms-auto">
                                <div class="chart-sparkline chart-sparkline-sm" id="sparkline-activity"></div>
                            </div>
                        </div>
                        <div class="h1 mb-3"><?php echo number_format($totals['target'] ?? 0); ?></div>
                        <div class="d-flex mb-2">
                            <div>Planlanan hedef sayısı</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Tamamlanan</div>
                            <div class="ms-auto">
                                <div class="chart-sparkline chart-sparkline-sm" id="sparkline-bounce-rate"></div>
                            </div>
                        </div>
                        <div class="h1 mb-3"><?php echo number_format($totals['completed'] ?? 0); ?></div>
                        <div class="d-flex mb-2">
                            <div>Gerçekleşen değer</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Başarı Oranı</div>
                        </div>
                        <div class="h1 mb-3">
                            <?php 
                            $successRate = ($totals['target'] ?? 0) > 0 ? round((($totals['completed'] ?? 0) / ($totals['target'] ?? 1)) * 100, 1) : 0;
                            echo $successRate; 
                            ?>%
                        </div>
                        <div class="d-flex mb-2">
                            <div class="progress progress-sm flex-fill">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $successRate; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Aktif Göstergeler</div>
                        </div>
                        <div class="h1 mb-3"><?php echo count($indicators ?? []); ?></div>
                        <div class="d-flex mb-2">
                            <div>Toplam gösterge sayısı</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <?php if (!empty($indicatorTypes)): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtrele</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="index.php?url=indicator/adminReport" class="row g-3">
                            <div class="col-md-8">
                                <label for="indicatorTypeId" class="form-label">Gösterge Türü</label>
                                <select id="indicatorTypeId" name="indicatorTypeId" class="form-select">
                                    <option value="">Tüm Göstergeler</option>
                                    <?php foreach ($indicatorTypes as $type): ?>
                                        <option value="<?php echo $type->getId(); ?>" <?php echo ($selectedTypeId == $type->getId()) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type->getIndicatorTitle()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-filter me-1"></i>
                                    Filtrele
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-table me-2"></i>
                            Detaylı Rapor
                        </h3>
                        <div class="card-actions">
                            <a href="#" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                <i class="ti ti-printer me-1"></i>
                                Yazdır
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($indicators)): ?>
                    <table id="reportTable" class="table table-bordered table-striped" data-datatable data-dt-title="<?php echo htmlspecialchars($title); ?>">
                        <thead>
                            <tr>
                                <th></th>
                                <th>SMM</th>
                                <th>Gösterge Başlığı</th>
                                <th>Hedef</th>
                                <th>Tamamlanan</th>
                                <th>Durum</th>
                               <!-- <th>Alan</th>-->
                                
                                <th>Oluşturulma Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                             $linenumber = 1;
                            foreach ($indicators as $indicator): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($linenumber); ?></td>
                                    <td><?php echo htmlspecialchars($indicator->getCoveName()); ?></td>
                                    <td>
                                    <p class="card-text"><?php echo htmlspecialchars($indicator->getIndicatorTitle()); ?></p>
                                    <p class="card-text"><?php echo htmlspecialchars($indicator->getIndicatorDesc()); ?></p>
                                </td>
                                    <td><?php echo htmlspecialchars($indicator->getTarget()); ?></td>
                                    <td><?php echo htmlspecialchars($indicator->getCompleted()); ?></td>
                                    <td>
                                    <?php echo ($indicator->getIndicatorStatus() == 1 ? '<p class="card-text text-success"> Tamamlandı</p>' : '<p class="card-text text-warning"> Devam Ediyor</p>'); ?>
                                </td>
                                   <!-- <td><?php echo htmlspecialchars($indicator->getFieldName()); ?></td>-->
                                    
                                    <td><?php echo htmlspecialchars($indicator->getCreatedAt()); ?></td>
                                </tr>
                            <?php 
                        $linenumber++;
                        endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Toplam</th>
                                <th><?php echo htmlspecialchars($totals['target']); ?></th>
                                <th><?php echo htmlspecialchars($totals['completed']); ?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <div class="d-flex">
                                    <div>
                                        <i class="ti ti-info-circle me-2"></i>
                                    </div>
                                    <div>
                                        <strong>Bilgi:</strong> Gösterilecek gösterge bulunamadı.
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row row-deck row-cards">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-chart-donut me-2"></i>
                            Genel Durum
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="donutChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-chart-bar me-2"></i>
                            SMM Bazında Performans
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="barChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
?>
<!-- Chart.js bundle yüklendi (AssetManager) -->
<script>
    // Charts (DataTable init handled globally)
    $(function () {
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
        var donutData        = {
            labels: ['Hedef','Tamamlanan'],
            datasets: [{
                    data: [Number('<?php echo (float)$totals['target']; ?>'), Number('<?php echo (float)$totals['completed']; ?>')],
                    backgroundColor : ['#f56954', '#00a65a']
            }]
        }
    var donutOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
    })
   

    //---------------------
    //- STACKED BAR CHART -
    //---------------------

    // Get context with jQuery - using jQuery's .get() method.

    var areaChartData = {
    labels  : [<?php if (!empty($indicators)) { $first=true; foreach ($indicators as $indicator): if(!$first) echo ','; $first=false; ?>'<?php echo htmlspecialchars($indicator->getCoveName()); ?>'<?php endforeach; } ?>],
      datasets: [
        {
          label               : 'Hedef',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [<?php if (!empty($indicators)) { $first=true; foreach ($indicators as $indicator): if(!$first) echo ','; $first=false; echo (float)$indicator->getTarget(); endforeach; } ?>]
        },
        {
          label               : 'Tamamlandı',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [<?php if (!empty($indicators)) { $first=true; foreach ($indicators as $indicator): if(!$first) echo ','; $first=false; echo (float)$indicator->getCompleted(); endforeach; } ?>]
        },
      ]
    }

  
   //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartData = $.extend(true, {}, areaChartData)
    var temp0 = areaChartData.datasets[1]
    var temp1 = areaChartData.datasets[0]
    barChartData.datasets[0] = temp1
    barChartData.datasets[1] = temp0

    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false
    }

    new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })
    });
</script>
