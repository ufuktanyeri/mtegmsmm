<?php
$pageTitle = 'Destek Oturumları';
?>
<div class="support-admin" id="supportAdminApp">
  <div class="row">
    <div class="col-md-4">
      <div class="gov-card" style="min-height:400px;">
        <h4>Bekleyen Oturumlar</h4>
        <ul id="pendingList" class="list-unstyled small"></ul>
        <hr>
        <h4>Aktif Oturumlar</h4>
        <ul id="activeList" class="list-unstyled small"></ul>
      </div>
    </div>
    <div class="col-md-8">
      <div class="gov-card" style="min-height:400px;display:flex;flex-direction:column;">
        <div><strong>Seçili Oturum:</strong> <span id="currentSessionLabel">-</span></div>
        <div id="agentMessages" style="flex:1;overflow:auto;margin-top:.7rem;padding:.6rem;background:#f5f9fc;border:1px solid #dbe7ef;border-radius:8px;"></div>
        <form id="agentSendForm" style="margin-top:.6rem;display:flex;gap:.5rem;">
          <input type="text" id="agentMsg" class="form-control" placeholder="Mesaj yaz..." autocomplete="off">
          <button class="btn btn-primary" type="submit">Gönder</button>
          <button class="btn btn-outline-danger" type="button" id="btnCloseSession">Kapat</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
include __DIR__.'/../layouts/unified.php';
?>
<script>
(function(){
  const pendingList = document.getElementById('pendingList');
  const activeList = document.getElementById('activeList');
  const agentMessages = document.getElementById('agentMessages');
  const currentLabel = document.getElementById('currentSessionLabel');
  const form = document.getElementById('agentSendForm');
  const input = document.getElementById('agentMsg');
  const btnClose = document.getElementById('btnCloseSession');
  let currentSession = 0; let lastId = 0; let pollTimer;

  function fetchSessions(){
    fetch('index.php?url=help/agentSessions').then(r=>r.json()).then(d=>{
      if(!d.success) return;
      renderSessionLists(d.pending, d.active);
    });
  }
  function renderSessionLists(pending, active){
    pendingList.innerHTML = pending.map(s=>`<li><button class="btn btn-sm btn-outline-secondary" data-act="act" data-id="${s.id}">#${s.id} Kullanıcı:${s.user_id}</button></li>`).join('') || '<li>Yok</li>';
    activeList.innerHTML = active.map(s=>`<li><button class="btn btn-sm btn-outline-primary" data-act="open" data-id="${s.id}">#${s.id} Kullanıcı:${s.user_id}</button></li>`).join('') || '<li>Yok</li>';
  }
  pendingList.addEventListener('click',e=>{
    const b=e.target.closest('button[data-act="act"]'); if(!b) return;
    activateSession(b.dataset.id);
  });
  activeList.addEventListener('click',e=>{
    const b=e.target.closest('button[data-act="open"]'); if(!b) return;
    openSession(b.dataset.id);
  });
  function activateSession(id){
    fetch('index.php?url=help/agentActivate',{method:'POST',body:new URLSearchParams({session_id:id})})
      .then(r=>r.json()).then(()=>{fetchSessions(); openSession(id);});
  }
  function openSession(id){ currentSession=id; lastId=0; currentLabel.textContent='#'+id; agentMessages.innerHTML='Yükleniyor...'; poll(); }
  function poll(){
    if(!currentSession) return;
    fetch('index.php?url=help/poll&session_id='+currentSession+'&since='+lastId).then(r=>r.json()).then(d=>{
      if(d.success){
        appendMessages(d.messages);
        lastId = d.last_id || lastId;
        if(d.session_status==='closed'){ addSystem('Oturum kapatıldı.'); currentSession=0; }
      }
    });
  }
  function appendMessages(msgs){
    if(!msgs || !msgs.length){ if(agentMessages.innerHTML==='Yükleniyor...') agentMessages.innerHTML=''; return; }
    if(agentMessages.innerHTML==='Yükleniyor...') agentMessages.innerHTML='';
    msgs.forEach(m=>{
      const div=document.createElement('div');
      div.className='am-msg am-'+m.sender;
      div.innerHTML='<span class="tag">'+m.sender+'</span> '+escapeHtml(m.message);
      agentMessages.appendChild(div);
      agentMessages.scrollTop=agentMessages.scrollHeight;
    });
  }
  function addSystem(t){
    const div=document.createElement('div'); div.className='am-msg am-system'; div.textContent=t; agentMessages.appendChild(div);
  }
  form.addEventListener('submit',e=>{
    e.preventDefault(); if(!currentSession) return; const txt=input.value.trim(); if(!txt) return;
    fetch('index.php?url=help/sendMessage',{method:'POST',body:new URLSearchParams({session_id:currentSession,text:txt})})
      .then(r=>r.json()).then(d=>{ if(d.success){ input.value=''; poll(); }});
  });
  btnClose.addEventListener('click',()=>{ if(!currentSession) return; fetch('index.php?url=help/agentClose',{method:'POST',body:new URLSearchParams({session_id:currentSession})}).then(()=>{ addSystem('Oturum kapatıldı.'); currentSession=0; fetchSessions(); }); });
  function escapeHtml(s){return s.replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));}
  fetchSessions(); pollTimer=setInterval(()=>{ fetchSessions(); poll(); },3000);
})();
</script>
<style>
.am-msg{font-size:.7rem;padding:.3rem .4rem;border-bottom:1px dashed #d0dde5;}
.am-msg:last-child{border-bottom:none;}
.am-msg .tag{display:inline-block;font-weight:600;margin-right:.35rem;text-transform:uppercase;font-size:.6rem;letter-spacing:.5px;color:#0b4b84;}
.am-user{background:#eef7ff;}
.am-agent{background:#e8f5e9;}
.am-system{background:#fff3cd;}
button.btn.btn-sm{margin:.15rem 0;width:100%;text-align:left;}
</style>
