<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Views\action\calendarAdmin.php
require_once __DIR__ . '/../../../includes/AssetManager.php';

$title = 'Admin Faaliyet Takvimi';
$page_title = 'Admin Faaliyet Takvimi';
$breadcrumb = [
    ['name' => 'Ana Sayfa', 'url' => 'index.php?url=home'],
    ['name' => 'Admin Panel', 'url' => 'index.php?url=admin'],
    ['name' => 'Faaliyet Takvimi', 'url' => '']
];
ob_start();
use App\Helpers\AssetManager; \App\Helpers\AssetManager::addBundle('fullcalendar');
?>

<!-- Merkez Seçici Kartı (gelişmiş stil) -->
<div class="row mb-3">
    <div class="col-12">
        <div class="gov-card" style="padding:1.1rem 1.25rem;display:flex;flex-direction:column;gap:.9rem;">
            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                <div class="icon-bg" style="width:46px;height:46px;border-radius:14px;background:var(--gov-gradient);display:grid;place-items:center;color:#fff;font-size:1.1rem;box-shadow:0 3px 8px -2px rgba(0,0,0,.25);">
                    <i class="fas fa-filter"></i>
                </div>
                <div style="flex:1 1 auto;min-width:220px;">
                    <h2 style="font-size:1.05rem;font-weight:600;letter-spacing:.4px;margin:0;color:#0b4b84;">Merkez Seçimi</h2>
                    <p style="margin:.25rem 0 0;font-size:.7rem;letter-spacing:.3px;color:#5a6b78;">Belirli bir merkez görevlerini veya tüm merkezleri birlikte görüntüleyin</p>
                </div>
                <form method="GET" action="index.php" style="display:flex;gap:.65rem;flex-wrap:wrap;align-items:end;">
                    <input type="hidden" name="url" value="action/calendarAdmin" />
                    <div>
                        <label for="coveSelect" style="font-size:.62rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#4d6278;margin:0 0 .25rem;">Merkez</label>
                        <select name="coveId" id="coveSelect" class="form-control form-control-sm" style="min-width:260px;">
                            <option value="0" <?= ($selectedCoveId ?? 0) == 0 ? 'selected' : '' ?>>🌐 Tüm Merkezler (<?= count($actions ?? []) ?> görev)</option>
                            <?php if(isset($coves) && is_array($coves)): foreach($coves as $cove): $coveId = is_object($cove)?$cove->getId():($cove['id']??0); $coveName = is_object($cove)?$cove->getName():($cove['name']??'Bilinmeyen'); $coveCity = is_object($cove)?$cove->getCityDistrict():($cove['city_district']??''); ?>
                                <option value="<?= $coveId ?>" <?= ($selectedCoveId ?? 0) == $coveId ? 'selected' : '' ?>>🏢 <?= htmlspecialchars($coveCity.' - '.$coveName) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div style="display:flex;gap:.5rem;align-items:flex-end;">
                        <button type="submit" class="btn btn-primary btn-sm" style="align-self:flex-end;padding:.45rem .9rem;font-weight:600;letter-spacing:.4px;display:inline-flex;gap:.4rem;align-items:center;">
                            <i class="fas fa-search"></i> Filtrele
                        </button>
                        <?php if(($selectedCoveId ?? 0) > 0): ?>
                            <a href="index.php?url=action/calendarAdmin" class="btn btn-outline-secondary btn-sm" style="padding:.45rem .9rem;display:inline-flex;gap:.4rem;align-items:center;">
                                <i class="fas fa-times"></i> Temizle
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- İstatistik Kartları (tıklanabilir) -->
<div class="row mb-3">
    <?php
    // filtre anahtarı eşlemesi
    $statDefs = [
        ['key'=>'overdue','label'=>'Geciken','val'=>$overdueCount ?? 0,'icon'=>'fa-exclamation-triangle','color'=>'#dc3545','bg'=>'linear-gradient(135deg,#ffe5e8,#ffd1d6)'],
        ['key'=>'urgent','label'=>'Acil','val'=>$urgentCount ?? 0,'icon'=>'fa-bolt','color'=>'#fd7e14','bg'=>'linear-gradient(135deg,#ffe9d6,#ffd4ad)'],
        ['key'=>'upcoming','label'=>'Yaklaşan','val'=>$upcomingCount ?? 0,'icon'=>'fa-hourglass-half','color'=>'#17a2b8','bg'=>'linear-gradient(135deg,#d6f4ff,#b9ecff)'],
        ['key'=>'completed','label'=>'Tamamlanan','val'=>$completedCount ?? 0,'icon'=>'fa-check-circle','color'=>'#28a745','bg'=>'linear-gradient(135deg,#d4f8e2,#baf5d2)'],
    ];
    foreach($statDefs as $s):
        $link = 'index.php?url=action/adminTaskList&filter='.$s['key'];
        if(($selectedCoveId ?? 0) > 0) { $link .= '&coveId='.(int)$selectedCoveId; }
    ?>
        <div class="col-sm-6 col-md-3 mb-2">
            <a href="<?= $link ?>" class="stat-link" title="<?= htmlspecialchars($s['label']) ?> görevleri listele">
                <div class="gov-card stat-card-click" style="padding:.85rem .9rem;display:flex;flex-direction:column;gap:.4rem;position:relative;overflow:hidden;">
                    <div style="position:absolute;inset:0;background:<?= $s['bg'] ?>;opacity:.9;"></div>
                    <div style="position:relative;display:flex;align-items:center;gap:.6rem;">
                        <div style="width:40px;height:40px;border-radius:12px;background:<?= $s['color'] ?>;display:grid;place-items:center;color:#fff;font-size:1rem;box-shadow:0 3px 6px -2px rgba(0,0,0,.3);"><i class="fas <?= $s['icon'] ?>"></i></div>
                        <div style="flex:1 1 auto;">
                            <div style="font-size:.58rem;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:#334b61;"><?= htmlspecialchars($s['label']) ?></div>
                            <div style="font-size:1.45rem;font-weight:700;letter-spacing:-1px;color:<?= $s['color'] ?>;line-height:1;"><?= (int)$s['val'] ?></div>
                        </div>
                        <div class="d-none d-md-block" style="color:<?= $s['color'] ?>;opacity:.8;font-size:.85rem;font-weight:600;letter-spacing:.5px;">&rsaquo;</div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Görünüm Anahtarı -->
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:.75rem;">
    <div class="btn-group btn-group-sm" role="group" aria-label="Görünüm seçim">
        <button type="button" class="btn btn-primary" id="btnShowCalendar"><i class="far fa-calendar-alt"></i> Takvim</button>
        <button type="button" class="btn btn-outline-primary" id="btnShowCards"><i class="fas fa-th"></i> Kartlar</button>
    </div>
    <div class="small text-body-secondary"><i class="fas fa-info-circle"></i> Kart görünümünde görevler ızgara halinde listelenir.</div>
</div>

<!-- Takvim Alanı -->
<div id="calendarPane" class="gov-card" style="padding:1rem 1.1rem;">
    <div style="display:flex;align-items:center;gap:.55rem;margin-bottom:.75rem;">
        <div class="icon-bg" style="width:40px;height:40px;border-radius:12px;background:var(--gov-gradient);display:grid;place-items:center;color:#fff;font-size:1rem;box-shadow:0 3px 6px -2px rgba(0,0,0,.25);"><i class="far fa-calendar-alt"></i></div>
        <h2 style="font-size:1rem;font-weight:600;letter-spacing:.4px;margin:0;color:#0b4b84;">Faaliyet Takvimi</h2>
    </div>
    <div id="calendar" style="background:#fff;border:1px solid var(--gov-border);border-radius:12px;padding:.5rem;"></div>
</div>

<!-- Kart (Grid) Görünümü -->
<div id="cardsPane" class="gov-card" style="display:none;padding:1rem 1.1rem;">
    <div style="display:flex;align-items:center;gap:.55rem;margin-bottom:.75rem;">
        <div class="icon-bg" style="width:40px;height:40px;border-radius:12px;background:var(--gov-gradient);display:grid;place-items:center;color:#fff;font-size:1rem;box-shadow:0 3px 6px -2px rgba(0,0,0,.25);"><i class="fas fa-th"></i></div>
        <h2 style="font-size:1rem;font-weight:600;letter-spacing:.4px;margin:0;color:#0b4b84;">Görev Kartları</h2>
    </div>
    <div class="row" id="cardsContainer">
        <?php if(!empty($actions)): $idx=0; foreach($actions as $action):
            $o=is_object($action);
            $actionId = $o?($action->id??0):($action['id']??0);
            $actionTitle = $o?($action->actionTitle??''):($action['actionTitle']??'');
            $actionDesc = $o?($action->actionDesc??''):($action['actionDesc']??'');
            $actionStatus = (int)($o?($action->actionStatus??0):($action['actionStatus']??0));
            $alertType = $o?($action->alert_type??'normal'):($action['alert_type']??'normal');
            $dateStart = $o?($action->dateStart??''):($action['dateStart']??'');
            $dateEnd = $o?($action->dateEnd??''):($action['dateEnd']??'');
            $objectiveId = $o?($action->objectiveId??0):($action['objectiveId']??0);
            $daysRemaining = (int)($o?($action->days_remaining??0):($action['days_remaining']??0));
            $daysOverdue = (int)($o?($action->days_overdue??0):($action['days_overdue']??0));
            $statusText='';$cls='primary';$icon='fas fa-tasks';
            if($actionStatus==1){$statusText='Tamamlandı';$cls='success';$icon='fas fa-check-circle';}
            else {
                switch($alertType){
                    case 'overdue': $cls='danger';$icon='fas fa-exclamation-triangle';$statusText=($daysOverdue>0?$daysOverdue:abs($daysRemaining)).' gün gecikmiş';break;
                    case 'urgent': $cls='warning';$icon='fas fa-fire';$statusText=max(0,$daysRemaining).' gün kaldı';break;
                    case 'warning': case 'upcoming': $cls='info';$icon='fas fa-clock';$statusText=max(0,$daysRemaining).' gün kaldı';break;
                    default: $statusText='Devam ediyor';
                }
            }
        ?>
        <div class="col-lg-6 col-xl-4 mb-3">
            <div class="card task-card-mini h-100 border-left-4 border-left-<?= $cls ?>" style="animation-delay:<?= $idx*0.05 ?>s">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-start">
                    <h6 class="mb-0 text-<?= $cls ?>" style="font-size:.8rem;line-height:1.2"><i class="<?= $icon ?> me-1"></i><?= htmlspecialchars($actionTitle) ?></h6>
                    <span class="badge badge-<?= $cls ?> badge-pill" style="font-size:.55rem;"><?= htmlspecialchars($statusText) ?></span>
                </div>
                <div class="card-body p-2 d-flex flex-column">
                    <?php if($actionDesc): ?><p class="text-body-secondary mb-2" style="font-size:.65rem;line-height:1.2;max-height:3.1em;overflow:hidden;"><?= htmlspecialchars(mb_substr($actionDesc,0,160)) ?><?= mb_strlen($actionDesc)>160?'...':'' ?></p><?php endif; ?>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between" style="font-size:.6rem;color:#666;">
                            <div><i class="fas fa-play text-success"></i> <?= $dateStart?date('d.m',strtotime($dateStart)):'-' ?></div>
                            <div><i class="fas fa-flag-checkered text-danger"></i> <?= $dateEnd?date('d.m',strtotime($dateEnd)):'-' ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-1 text-end bg-white">
                    <a class="btn btn-xs btn-outline-primary" style="font-size:.55rem;padding:.2rem .4rem;" href="index.php?url=action/edit&id=<?= $actionId ?>&objectiveId=<?= $objectiveId ?>"><i class="fas fa-edit"></i> Düzenle</a>
                </div>
            </div>
        </div>
        <?php $idx++; endforeach; else: ?>
            <div class="col-12 text-center text-body-secondary py-5"><i class="fas fa-info-circle"></i> Görev bulunamadı.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Detay Modal -->
<div class="modal fade" id="actionDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-info-circle"></i> Görev Detayı</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="actionDetailContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
        <button type="button" id="editActionBtn" class="btn btn-primary" style="display:none;">
            <i class="fas fa-edit"></i> Düzenle
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// === Yardımcı Fonksiyonlar ===
function getAlertMessage(alertType, daysRemaining, status) {
    if (status == 1) return '✅ Bu görev tamamlandı.';
    switch (alertType) {
        case 'overdue': return '⏰ Bu görev gecikmiş durumda!';
        case 'urgent': return '⚠️ Bu görev acil!';
        case 'warning': return '🔔 Yaklaşan görev. Kalan gün: ' + daysRemaining;
        case 'upcoming': return '📅 Yaklaşan tarihte tamamlanmalı.';
        default: return 'ℹ️ Normal görev.';
    }
}
function getBootstrapAlertClass(alertType) {
    switch (alertType) {
        case 'overdue': return 'danger';
        case 'urgent': return 'warning';
        case 'warning': return 'info';
        case 'completed': return 'success';
        default: return 'primary';
    }
}
function getEventColor(alertType, status) {
    if (status == 1) return '#28a745';
    switch (alertType) {
        case 'overdue': return '#dc3545';
        case 'urgent': return '#ffc107';
        case 'warning': return '#17a2b8';
        case 'upcoming': return '#6c757d';
        default: return '#007bff';
    }
}
function getTextColor(alertType) {
    switch (alertType) {
        case 'urgent': return '#222';
        case 'warning': return '#062635';
        default: return '#fff';
    }
}

<?php
// PHP tarafında veri hazırlığı (daha güvenli JSON çıkışı)
$eventPayload = [];
if (!empty($actions)) {
    foreach($actions as $action) {
        $o = is_object($action);
        $actionId = $o ? ($action->id ?? 0) : ($action['id'] ?? 0);
        $actionTitle = $o ? ($action->actionTitle ?? '') : ($action['actionTitle'] ?? '');
        $dateStart = $o ? ($action->dateStart ?? date('Y-m-d')) : ($action['dateStart'] ?? date('Y-m-d'));
        $dateEnd = $o ? ($action->dateEnd ?? date('Y-m-d')) : ($action['dateEnd'] ?? date('Y-m-d'));
        $actionDesc = $o ? ($action->actionDesc ?? '') : ($action['actionDesc'] ?? '');
        $actionResponsible = $o ? ($action->actionResponsible ?? '') : ($action['actionResponsible'] ?? '');
        $actionStatus = (int)($o ? ($action->actionStatus ?? 0) : ($action['actionStatus'] ?? 0));
        $alertType = $o ? ($action->alert_type ?? 'normal') : ($action['alert_type'] ?? 'normal');
        $daysRemaining = (int)($o ? ($action->days_remaining ?? 0) : ($action['days_remaining'] ?? 0));
        $coveName = $o ? ($action->cove_name ?? '') : ($action['cove_name'] ?? '');
        $cityDistrict = $o ? ($action->city_district ?? '') : ($action['city_district'] ?? '');
        $objectiveTitle = $o ? ($action->objectiveTitle ?? '') : ($action['objectiveTitle'] ?? '');
        $aimTitle = $o ? ($action->aimTitle ?? '') : ($action['aimTitle'] ?? '');
        $periodic = (string)($o ? ($action->periodic ?? 0) : ($action['periodic'] ?? 0));
        $periodType = (string)($o ? ($action->periodType ?? '') : ($action['periodType'] ?? ''));
        $periodTime = (string)($o ? ($action->periodTime ?? '') : ($action['periodTime'] ?? ''));
        $objectiveId = (string)($o ? ($action->objectiveId ?? 0) : ($action['objectiveId'] ?? 0));
        $bg = '';// JS tarafında hesaplayacağız
        $eventPayload[] = [
            'id'=>$actionId,
            'title'=>$actionTitle,
            'start'=>$dateStart,
            'end'=>date('Y-m-d', strtotime($dateEnd.' +1 day')),
            'description'=>$actionDesc,
            'responsible'=>$actionResponsible,
            'status'=>$actionStatus,
            'alertType'=>$alertType,
            'daysRemaining'=>$daysRemaining,
            'coveName'=>$coveName,
            'cityDistrict'=>$cityDistrict,
            'objectiveTitle'=>$objectiveTitle,
            'aimTitle'=>$aimTitle,
            'periodic'=>$periodic,
            'periodType'=>$periodType,
            'periodTime'=>$periodTime,
            'objectiveId'=>$objectiveId
        ];
    }
}
?>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
    var baseEvents = <?php echo json_encode($eventPayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>.map(function(ev){
        ev.backgroundColor = getEventColor(ev.alertType, ev.status);
        ev.borderColor = ev.backgroundColor;
        ev.textColor = getTextColor(ev.alertType);
        return ev;
    });

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'tr',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        themeSystem: 'bootstrap',
        firstDay: 1,
        weekNumbers: true,
        editable: false,
        events: function(fetchInfo, success) {
            var list = [];
            baseEvents.forEach(function(ev){
                if (ev.periodic == '1' && ev.periodType == '1') { // haftalık
                    var start = moment(fetchInfo.start);
                    var end = moment(fetchInfo.end);
                    var dow = parseInt(ev.periodTime || '1');
                    var current = start.clone().day(dow);
                    if (current.isBefore(start)) current.add(1,'week');
                    var guard = 0;
                    while(current.isBefore(end) && guard < 52) {
                        var clone = Object.assign({}, ev, {
                            id: ev.id + '_p_' + guard,
                            start: current.format('YYYY-MM-DD'),
                            end: current.clone().add(1,'hours').format(),
                            isPeriodic: true,
                            originalId: ev.id
                        });
                        list.push(clone);
                        current.add(1,'week');
                        guard++;
                    }
                } else {
                    list.push(ev);
                }
            });
            success(list);
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            var p = info.event.extendedProps;
            var statusText = p.status == 1 ? 'Tamamlandı' : 'Devam Ediyor';
            var statusClass = p.status == 1 ? 'success' : 'secondary';
            var alertClass = getBootstrapAlertClass(p.alertType);
            var modalContent = `
                <div class="alert alert-${alertClass} border-0" role="alert">
                    <i class="fas fa-info-circle"></i> <strong>${getAlertMessage(p.alertType, p.daysRemaining, p.status)}</strong>
                </div>
                <div class="row">
                  <div class="col-md-8"><h5 class="text-primary"><i class="fas fa-tasks"></i> ${info.event.title}</h5></div>
                  <div class="col-md-4 text-end">
                    <span class="badge badge-${statusClass} badge-lg"><i class="fas fa-${p.status==1?'check-circle':'clock'}"></i> ${statusText}</span>
                  </div>
                </div>
                <hr/>
                <div class="row">
                  <div class="col-md-6"><div class="info-item"><i class="fas fa-calendar-plus text-success"></i><strong> Başlangıç:</strong><br><span class="text-body-secondary">${moment(info.event.start).format('DD MMMM YYYY, dddd')}</span></div></div>
                  <div class="col-md-6"><div class="info-item"><i class="fas fa-calendar-check text-danger"></i><strong> Bitiş:</strong><br><span class="text-body-secondary">${moment(info.event.end).format('DD MMMM YYYY, dddd')}</span></div></div>
                </div>
                <div class="row mt-3">
                  <div class="col-md-6"><div class="info-item"><i class="fas fa-building text-primary"></i><strong> Merkez:</strong><br><span class="text-body-secondary">${p.cityDistrict||'Belirtilmemiş'} - ${p.coveName||'Bilinmeyen'}</span></div></div>
                  <div class="col-md-6"><div class="info-item"><i class="fas fa-user text-info"></i><strong> Sorumlu:</strong><br><span class="text-body-secondary">${p.responsible||'Belirtilmemiş'}</span></div></div>
                </div>
                ${p.description?`<div class='row mt-3'><div class='col-12'><div class='info-item'><i class='fas fa-align-left text-secondary'></i><strong> Açıklama:</strong><br><span class='text-body-secondary'>${p.description}</span></div></div></div>`:''}
                ${p.aimTitle?`<div class='row mt-3'><div class='col-12'><div class='info-item'><i class='fas fa-bullseye text-warning'></i><strong> Amaç:</strong><br><span class='text-body-secondary'>${p.aimTitle}</span></div></div></div>`:''}
                ${p.objectiveTitle?`<div class='row mt-3'><div class='col-12'><div class='info-item'><i class='fas fa-target text-success'></i><strong> Hedef:</strong><br><span class='text-body-secondary'>${p.objectiveTitle}</span></div></div></div>`:''}
                ${p.isPeriodic?`<div class='row mt-3'><div class='col-12'><div class='alert alert-info'><i class='fas fa-sync-alt'></i> Periyodik Görev (haftalık)</div></div></div>`:''}
            `;
            document.getElementById('actionDetailContent').innerHTML = modalContent;
            var actionId = p.originalId || info.event.id;
            var editBtn = document.getElementById('editActionBtn');
            editBtn.style.display = 'inline-block';
            editBtn.onclick = function(){ window.open('index.php?url=action/edit&id='+actionId+'&objectiveId='+(p.objectiveId||'0'),'_blank'); };
            $('#actionDetailModal').modal('show');
        },
        eventDidMount: function(info){
            var p = info.event.extendedProps;
            if (p.alertType === 'urgent') info.el.style.animation = 'pulse 2s infinite';
            if (p.alertType === 'overdue') info.el.style.animation = 'shake 1s infinite';
            if (p.isPeriodic) info.el.style.border = '2px dashed ' + info.event.borderColor;
        }
    });

    calendar.render();

    if (baseEvents.length === 0) {
        setTimeout(function(){
            var harness = document.querySelector('#calendar .fc-view-harness');
            if(!harness) return;
            harness.insertAdjacentHTML('beforeend', `<div class="text-center p-5"><i class="fas fa-calendar-times fa-4x text-body-secondary mb-3"></i><h5 class="text-body-secondary">Görev Bulunamadı</h5><p class="text-body-secondary"><?php echo (($selectedCoveId ?? 0) > 0) ? 'Seçili merkez için' : 'Sistemde'; ?> henüz tanımlanmış bir görev bulunmuyor.</p></div>`);
        }, 800);
    }

    // Görünüm düğmeleri
    var btnCal = document.getElementById('btnShowCalendar');
    var btnCards = document.getElementById('btnShowCards');
    var paneCal = document.getElementById('calendarPane');
    var paneCards = document.getElementById('cardsPane');
    if(btnCal && btnCards){
        btnCal.addEventListener('click', function(){
            paneCal.style.display='block';
            paneCards.style.display='none';
            btnCal.classList.add('btn-primary');btnCal.classList.remove('btn-outline-primary');
            btnCards.classList.remove('btn-primary');btnCards.classList.add('btn-outline-primary');
            setTimeout(function(){calendar.updateSize();},150);
        });
        btnCards.addEventListener('click', function(){
            paneCal.style.display='none';
            paneCards.style.display='block';
            btnCards.classList.add('btn-primary');btnCards.classList.remove('btn-outline-primary');
            btnCal.classList.remove('btn-primary');btnCal.classList.add('btn-outline-primary');
        });
    }
});
</script>

<style>
:root { --grad-red:linear-gradient(135deg,#ffe5e8,#ffd1d6); --grad-orange:linear-gradient(135deg,#ffe9d6,#ffd4ad); --grad-blue:linear-gradient(135deg,#d6f4ff,#b9ecff); --grad-green:linear-gradient(135deg,#d4f8e2,#baf5d2); }
.gov-card.gradient-frame { background:linear-gradient(145deg,#f6f8fa,#e3eef9); padding:2px; border-radius:14px; }
.gradient-inner { background:#fff; border-radius:12px; padding:1rem 1.1rem; }
body.dark-mode .gradient-inner { background:#1f2933; }
.stat-link { text-decoration:none; display:block; }
.stat-card-click { transition:transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s; cursor:pointer; }
.stat-card-click:hover { transform:translateY(-4px); box-shadow:0 8px 24px -6px rgba(0,0,0,.25); }
.task-card-mini { border:1px solid var(--gov-border,#dee2e6); border-left:4px solid #007bff; border-radius:10px; transition:all .35s ease; position:relative; overflow:hidden; }
.task-card-mini:hover { transform:translateY(-3px); box-shadow:0 8px 22px -6px rgba(0,0,0,.2); }
.border-left-danger{border-left-color:#dc3545!important}.border-left-warning{border-left-color:#fd7e14!important}.border-left-info{border-left-color:#17a2b8!important}.border-left-success{border-left-color:#28a745!important}.border-left-primary{border-left-color:#007bff!important}
@keyframes fadeInSmall{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.task-card-mini{animation:fadeInSmall .5s ease forwards;opacity:0}
@media (max-width:768px){#cardsPane .task-card-mini{font-size:12px}}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
return; // güvenlik

