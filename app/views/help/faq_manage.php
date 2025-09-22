<?php
$title='SSS Yönetimi';
$page_title='Sık Sorulan Sorular Yönetimi';
ob_start();
?>
<div class="gov-card" style="margin-bottom:1.5rem;">
  <form id="faqForm" class="form-inline" style="gap:.6rem;flex-wrap:wrap;">
    <input type="hidden" id="faqId" value="">
    <div class="flex-grow-1" style="min-width:260px;">
      <input type="text" id="faqQuestion" class="form-control w-100" placeholder="Soru" required maxlength="255">
    </div>
    <div class="flex-grow-1" style="min-width:300px;">
      <textarea id="faqAnswer" class="form-control w-100" placeholder="Cevap" rows="2" required></textarea>
    </div>
    <div>
      <label style="font-size:.65rem;display:flex;align-items:center;gap:.3rem;">
        <input type="checkbox" id="faqActive" checked> Aktif
      </label>
    </div>
    <div>
      <button type="submit" class="btn btn-primary btn-sm">Kaydet</button>
      <button type="button" id="faqReset" class="btn btn-secondary btn-sm">Temizle</button>
    </div>
  </form>
</div>
<div class="gov-card">
  <h4 style="font-size:.8rem;font-weight:600;">Kayıtlı Sorular</h4>
  <div class="table-responsive">
    <table class="table table-sm table-bordered" id="faqTable" style="font-size:.7rem;">
      <thead class="thead-light"><tr><th>ID</th><th>Soru</th><th>Aktif</th><th>İşlem</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__.'/../layouts/unified.php';
?>
<script>
(function(){
  if(!window.fetch){ return; }
  const form = document.getElementById('faqForm');
  const idEl = document.getElementById('faqId');
  const qEl = document.getElementById('faqQuestion');
  const aEl = document.getElementById('faqAnswer');
  const actEl = document.getElementById('faqActive');
  const tableBody = document.querySelector('#faqTable tbody');
  const resetBtn = document.getElementById('faqReset');

  function load(){
    fetch('index.php?url=help/faqAdminList').then(r=>r.json()).then(d=>{
      if(!d.success) return; render(d.items);
    });
  }
  function render(items){
    tableBody.innerHTML = items.map(r=>`<tr data-id="${r.id}"><td>${r.id}</td><td>${escapeHtml(r.question)}</td><td>${r.is_active?'<span class="badge badge-success">Evet</span>':'<span class="badge badge-secondary">Hayır</span>'}</td><td><button class="btn btn-xs btn-warning" data-act="edit">Düzenle</button> <button class="btn btn-xs btn-danger" data-act="del">Sil</button></td></tr>`).join('');
  }
  tableBody.addEventListener('click', function(e){
    const tr = e.target.closest('tr'); if(!tr) return;
    if(e.target.dataset.act==='edit'){
      const id=tr.dataset.id; fetch('index.php?url=help/faqAdminGet&id='+id).then(r=>r.json()).then(d=>{ if(d.success){ idEl.value=d.item.id; qEl.value=d.item.question; aEl.value=d.item.answer; actEl.checked = d.item.is_active==1; window.scrollTo({top:0,behavior:'smooth'}); } });
    } else if(e.target.dataset.act==='del'){
      if(confirm('Silinsin mi?')){ fetch('index.php?url=help/faqAdminDelete',{method:'POST',body:new URLSearchParams({id:tr.dataset.id})}).then(r=>r.json()).then(d=>{ if(d.success){ load(); } }); }
    }
  });
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new URLSearchParams({
      id: idEl.value.trim(),
      question: qEl.value.trim(),
      answer: aEl.value.trim(),
      active: actEl.checked?1:0
    });
    fetch('index.php?url=help/faqAdminSave',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{ if(d.success){ reset(); load(); } else { alert('Hata: '+(d.error||'kaydedilemedi')); } });
  });
  function reset(){ idEl.value=''; qEl.value=''; aEl.value=''; actEl.checked=true; }
  resetBtn.addEventListener('click', reset);
  function escapeHtml(s){return s.replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));}
  load();
})();
</script>
