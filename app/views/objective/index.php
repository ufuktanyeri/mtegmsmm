<?php
// filepath: c:\xampp\htdocs\mtegmsmm\app\Views\objective\index.php
$title = 'Hedefler';
$page_title = $title;
$hidePageHeader = true; // Sayfa içi özel header kullanılıyor
ob_start();

// Yardımcı: Amaç başlığı çek
function _findAimTitle($aims,$aimId){
        if (empty($aims)) return 'Bilinmiyor';
        foreach($aims as $a){
                if ((is_object($a) && (int)$a->id === (int)$aimId) || (is_array($a) && (int)$a['id']===(int)$aimId)){
                        return is_object($a)? $a->aimTitle : $a['aimTitle'];
                }
        }
        return 'Bilinmiyor';
}

$totalObjectives = is_array($objectives)? count($objectives):0;
?>

<div class="objective-page p-3">
    <div class="gov-section-header" style="margin-top:.5rem;">
        <h2><i class="fas fa-bullseye"></i> <?= htmlspecialchars($title) ?> <span style="font-size:.7rem;font-weight:500;color:#5d7487;">(<?= $totalObjectives ?>)</span></h2>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
            <a href="index.php?url=objective/create" class="btn btn-sm btn-primary focus-ring" style="background:var(--gov-primary);border:none;display:inline-flex;align-items:center;gap:.4rem;">
                <i class="fas fa-plus"></i> Yeni Hedef
            </a>
            <?php if(!empty($aims)): ?>
                <select id="aimFilter" class="form-control form-control-sm" style="min-width:190px;" onchange="onAimFilterChange(this.value)">
                    <option value="0">Tüm Amaçlar</option>
                    <?php foreach($aims as $a): $id = is_object($a)? $a->id : $a['id']; $nm = is_object($a)? $a->aimTitle : $a['aimTitle']; ?>
                        <option value="<?= (int)$id ?>" <?= (isset($currentAimId) && (int)$currentAimId === (int)$id)?'selected':'';?>><?= htmlspecialchars($nm) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <div class="input-group input-group-sm" style="width:230px;">
                <input type="text" id="objectiveSearch" class="form-control" placeholder="Ara..." oninput="filterObjectives()" aria-label="Hedeflerde ara">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>

    <?php if ($totalObjectives === 0): ?>
        <div class="gov-card" style="border-left:4px solid var(--gov-primary);">
            <h3 style="margin:0;font-size:.95rem;display:flex;align-items:center;gap:.45rem;color:#0b4a7e;"><i class="fas fa-info-circle"></i> Henüz hedef tanımlanmamış</h3>
            <p style="font-size:.8rem;margin:0;color:#4d6278;">Stratejik planlamaya başlamak için ilk hedefi ekleyin.</p>
            <a href="index.php?url=objective/create" class="primary-link" style="margin-top:.6rem;">Hedef Oluştur <i class="fas fa-arrow-right"></i></a>
        </div>
    <?php else: ?>
        <div id="objectivesGrid" class="gov-grid" style="margin-top:.6rem;">
            <?php foreach($objectives as $objective): ?>
                <?php $aimTitle = _findAimTitle($aims,$objective->aimId); ?>
                <div class="gov-card objective-card" 
                         data-aim="<?= (int)$objective->aimId ?>" 
                         data-title="<?= htmlspecialchars(mb_strtolower($objective->objectiveTitle,'UTF-8')) ?>" 
                         data-desc="<?= htmlspecialchars(mb_strtolower($objective->objectiveDesc,'UTF-8')) ?>">
                    <div style="display:flex;align-items:flex-start;gap:.65rem;">
                        <div class="gov-icon" aria-hidden="true" style="width:46px;height:46px;display:grid;place-items:center;border-radius:12px;background:var(--gov-gradient);color:#fff;font-size:1.1rem;"><i class="fas fa-bullseye"></i></div>
                        <div style="flex:1;min-width:0;">
                            <h3 style="margin:0;font-size:.9rem;line-height:1.25;"><?= htmlspecialchars($objective->objectiveTitle) ?></h3>
                            <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.35rem;">
                                <span style="background:#e3effa;color:#0b5cab;font-size:.6rem;font-weight:600;padding:.25rem .45rem;border-radius:6px;letter-spacing:.4px;">AMAÇ: <?= htmlspecialchars($aimTitle) ?></span>
                                <span style="background:#f1f5f9;color:#385268;font-size:.6rem;font-weight:600;padding:.25rem .45rem;border-radius:6px;letter-spacing:.4px;">ID #<?= (int)$objective->id ?></span>
                            </div>
                        </div>
                    </div>
                    <p style="font-size:.72rem;margin:.65rem 0 .2rem;color:#4a5f73;max-height:3.4em;overflow:hidden;"><?= htmlspecialchars(mb_strimwidth($objective->objectiveDesc,0,180,'…','UTF-8')) ?></p>
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:auto;">
                        <a href="index.php?url=action/index&aimid=<?= (int)$objective->aimId ?>&objectiveId=<?= (int)$objective->id ?>" class="primary-link" style="font-size:.6rem;">Faaliyetler <i class="fas fa-tasks"></i></a>
                        <a href="index.php?url=objective/edit&id=<?= (int)$objective->id ?>" class="primary-link" style="background:var(--gov-accent);font-size:.6rem;">Düzenle <i class="fas fa-edit"></i></a>
                        <a href="index.php?url=objective/delete&id=<?= (int)$objective->id ?>" class="primary-link" onclick="return confirm('Bu hedefi silmek istediğinizden emin misiniz?')" style="background:#444; font-size:.6rem;">Sil <i class="fas fa-trash"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
 </div>

<script>
function onAimFilterChange(val){
    const aim = parseInt(val,10)||0;
    const cards = document.querySelectorAll('.objective-card');
    cards.forEach(c=>{
        const cardAim = parseInt(c.getAttribute('data-aim'),10)||0;
        c.style.display = (aim===0 || cardAim===aim)?'flex':'none';
    });
    filterObjectives();
}
function filterObjectives(){
    const q = (document.getElementById('objectiveSearch').value||'').trim().toLowerCase();
    const aim = parseInt(document.getElementById('aimFilter')?document.getElementById('aimFilter').value:0,10)||0;
    let visible=0; const cards = document.querySelectorAll('.objective-card');
    cards.forEach(c=>{
        const t = c.getAttribute('data-title');
        const d = c.getAttribute('data-desc');
        const cardAim = parseInt(c.getAttribute('data-aim'),10)||0;
        const aimMatch = (aim===0|| cardAim===aim);
        const textMatch = (!q || (t && t.indexOf(q)>-1) || (d && d.indexOf(q)>-1));
        if(aimMatch && textMatch){ c.style.display='flex'; visible++; } else { c.style.display='none'; }
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>