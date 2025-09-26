<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Views\action\calendar.php
require_once __DIR__ . '/../../../includes/AssetManager.php';
use App\Helpers\AssetManager;

$pageTitle = 'Faaliyet Takvimi';
$breadcrumb = [
    [
        'name' => 'Amaçlar',
        'url' => 'index.php?url=aim/index'
    ],
];
AssetManager::addBundle('fullcalendar');
?>

<div class="row">
    <!-- Sol taraf - İstatistikler -->
    <div class="col-md-3">
        <div class="row">
            <!-- Geciken Görevler -->
            <div class="col-12 mb-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo $overdueCount ?? 0; ?></h3>
                        <p>Geciken Görevler</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <a href="<?php echo (isset($isAdmin) && $isAdmin) ? 'index.php?url=action/adminTaskList&filter=overdue' : 'index.php?url=action/taskList&type=overdue'; ?>" class="small-box-footer">
                        Detayları Gör <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Acil Görevler -->
            <div class="col-12 mb-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $urgentCount ?? 0; ?></h3>
                        <p>Acil Görevler (3 gün)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="<?php echo (isset($isAdmin) && $isAdmin) ? 'index.php?url=action/adminTaskList&filter=urgent' : 'index.php?url=action/taskList&type=urgent'; ?>" class="small-box-footer">
                        Detayları Gör <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Yaklaşan Görevler -->
            <div class="col-12 mb-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $upcomingCount ?? 0; ?></h3>
                        <p>Yaklaşan Görevler (7 gün)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <a href="<?php echo (isset($isAdmin) && $isAdmin) ? 'index.php?url=action/adminTaskList&filter=upcoming' : 'index.php?url=action/taskList&type=upcoming'; ?>" class="small-box-footer">
                        Detayları Gör <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Tamamlanan Görevler -->
            <div class="col-12 mb-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $completedCount ?? 0; ?></h3>
                        <p>Tamamlanan Görevler</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="<?php echo (isset($isAdmin) && $isAdmin) ? 'index.php?url=action/adminTaskList&filter=completed' : 'index.php?url=action/taskList&type=completed'; ?>" class="small-box-footer">
                        Detayları Gör <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sağ taraf - Takvim -->
    <div class="col-md-9">
        <div class="card card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?php echo isset($isAdmin) && $isAdmin ? 'Admin ' : ''; ?>Faaliyet Takvimi
                </h3>
                <div class="d-none d-md-flex align-items-center small text-body-secondary">
                    <span class="me-2"><span class="badge badge-danger">&nbsp;</span> Geciken</span>
                    <span class="me-2"><span class="badge badge-warning">&nbsp;</span> Acil</span>
                    <span class="me-2"><span class="badge badge-info">&nbsp;</span> Yaklaşan</span>
                    <span class="me-2"><span class="badge badge-success">&nbsp;</span> Tamamlanan</span>
                    <span class="me-2"><span class="badge badge-primary">&nbsp;</span> Normal</span>
                </div>
                <?php if (isset($isAdmin) && $isAdmin): ?>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <!-- THE CALENDAR -->
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<?php
?>

<!-- FullCalendar bundle scripts are injected via AssetManager in layout -->

<style>
.small-box.bg-danger {background:linear-gradient(135deg,#ff5f6d,#ffc371)!important;color:#fff;}
.small-box.bg-warning {background:linear-gradient(135deg,#f7971e,#ffd200)!important;color:#222;}
.small-box.bg-info {background:linear-gradient(135deg,#56ccf2,#2f80ed)!important;color:#fff;}
.small-box.bg-success {background:linear-gradient(135deg,#11998e,#38ef7d)!important;color:#fff;}
.small-box .inner h3,.small-box .inner p {text-shadow:0 2px 4px rgba(0,0,0,.2);} 
</style>

<script>
$(function () {
    var Calendar = FullCalendar.Calendar;
    var calendarEl = document.getElementById('calendar');

    // ✅ DÜZELTILMIŞ: Object methods yerine property access
    var events = [
        <?php if (isset($actions) && is_array($actions)): ?>
            <?php foreach ($actions as $action): ?>
                <?php
                // Object veya array olabilir, ikisini de destekle
                $actionTitle = is_object($action) ? ($action->actionTitle ?? '') : ($action['actionTitle'] ?? '');
                $dateStart = is_object($action) ? ($action->dateStart ?? '') : ($action['dateStart'] ?? '');
                $dateEnd = is_object($action) ? ($action->dateEnd ?? '') : ($action['dateEnd'] ?? '');
                $actionId = is_object($action) ? ($action->id ?? 0) : ($action['id'] ?? 0);
                $objectiveId = is_object($action) ? ($action->objectiveId ?? 0) : ($action['objectiveId'] ?? 0);
                $periodic = is_object($action) ? ($action->periodic ?? '0') : ($action['periodic'] ?? '0');
                $periodType = is_object($action) ? ($action->periodType ?? '0') : ($action['periodType'] ?? '0');
                $periodTime = is_object($action) ? ($action->periodTime ?? '0') : ($action['periodTime'] ?? '0');
                $actionStatus = is_object($action) ? ($action->actionStatus ?? 0) : ($action['actionStatus'] ?? 0);
                $alertType = is_object($action) ? ($action->alert_type ?? 'normal') : ($action['alert_type'] ?? 'normal');
                
                // Aim ID'yi objective üzerinden al veya varsayılan
                $aimId = 1; // Varsayılan
                ?>
                ,{
                    title: '<?php echo htmlspecialchars($actionTitle); ?>',
                    start: '<?php echo htmlspecialchars($dateStart); ?>',
                    // FullCalendar all-day event end = exclusive, +1 gün doğru; boşsa aynı gün
                    end: '<?php echo $dateEnd ? date('Y-m-d', strtotime($dateEnd . ' +1 day')) : ($dateStart ? date('Y-m-d', strtotime($dateStart . ' +1 day')) : ''); ?>',
                    url: 'index.php?url=action/edit&id=<?php echo $actionId; ?>&objectiveId=<?php echo $objectiveId; ?>',
                    periodic: '<?php echo htmlspecialchars($periodic); ?>',
                    periodType: '<?php echo htmlspecialchars($periodType); ?>',
                    periodTime: '<?php echo htmlspecialchars($periodTime); ?>',
                    status: <?php echo $actionStatus; ?>,
                    alertType: '<?php echo $alertType; ?>',
                    // Renk belirleme
                    <?php
                    $color = '#007bff'; // Varsayılan mavi
                    switch($alertType) {
                        case 'completed':
                            $color = '#28a745'; // Yeşil
                            break;
                        case 'overdue':
                            $color = '#dc3545'; // Kırmızı
                            break;
                        case 'urgent':
                            $color = '#fd7e14'; // Turuncu
                            break;
                        case 'warning':
                            $color = '#ffc107'; // Sarı
                            break;
                        default:
                            $color = '#007bff'; // Mavi
                    }
                    ?>
                    backgroundColor: '<?php echo $color; ?>',
                    borderColor: '<?php echo $color; ?>',
                    textColor: '<?php echo ($alertType === 'warning') ? '#000000' : '#ffffff'; ?>',
                    daysRemaining: <?php echo isset($action->days_remaining) ? (int)$action->days_remaining : (isset($action['days_remaining']) ? (int)$action['days_remaining'] : 0); ?>
                },
            <?php endforeach; ?>
        <?php endif; ?>
    ];

    var calendar = new Calendar(calendarEl, {
        locale: 'tr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap',
        editable: false,
        droppable: false,
        height: 'auto',
        dayMaxEvents: true,
        events: function(fetchInfo, successCallback, failureCallback) {
            var eventsToShow = [];
            
            events.forEach(function(event) {
                if (event.periodic == '1') {
                    // Periyodik görevler için tekrarlama logic'i
                    if (event.periodType == '1') { // Haftalık
                        var startDate = moment(event.start);
                        var endDate = moment(fetchInfo.end);
                        var dayOfWeek = parseInt(event.periodTime);
                        var current = moment(fetchInfo.start).day(dayOfWeek);
                        
                        if (current.isBefore(moment(fetchInfo.start))) {
                            current.add(1, 'weeks');
                        }
                        
                        while (current.isBefore(endDate)) {
                            eventsToShow.push({
                                title: event.title + ' (Tekrar)',
                                start: current.format('YYYY-MM-DD'),
                                url: event.url,
                                backgroundColor: event.backgroundColor,
                                borderColor: event.borderColor,
                                textColor: event.textColor
                            });
                            current.add(1, 'weeks');
                        }
                    }
                } else {
                    // Normal görevler
                    eventsToShow.push(event);
                }
            });
            
            successCallback(eventsToShow);
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        eventDidMount: function(info) {
            var alertType = info.event.extendedProps.alertType;
            var status = info.event.extendedProps.status;
            var days = info.event.extendedProps.daysRemaining;
            var msg;
            if(status === 1){
                msg = 'Tamamlandı';
            } else {
                switch(alertType){
                    case 'overdue': msg = 'Geciken ('+Math.abs(days)+' gün)'; break;
                    case 'urgent': msg = 'Acil ('+days+' gün)'; break;
                    case 'warning': msg = 'Yaklaşan ('+days+' gün)'; break;
                    default: msg = 'Normal';
                }
            }
            $(info.el).tooltip({
                title: info.event.title + '\n' + msg,
                placement: 'top', trigger: 'hover', container: 'body'
            });
            $(info.el).addClass('calendar-event-' + alertType);
        }
    });

    calendar.render();
    
    // Responsive handling
    $(window).on('resize', function() {
        calendar.updateSize();
    });
});
</script>
<?php if(empty($actions)): ?>
<div class="mt-3 text-center text-body-secondary small">
    <i class="fas fa-info-circle"></i> Henüz tanımlanmış bir görev yok.
</div>
<?php endif; ?>