<?php
$title = $title ?? 'Yardım ve Destek';
$page_title = $page_title ?? 'Yardım Merkezi';
$hidePageHeader = true; // Tekrarlı başlığı gizle
ob_start();
?>
<div class="help-chat-wrapper">
  <div class="help-chat-layout">
    <aside class="help-sidebar">
      <h2 class="hs-title"><i class="fas fa-life-ring"></i> Yardım Merkezi</h2>
      <p class="hs-desc">Sorunuzu aşağıdaki sohbet kutusuna yazın. SSS veritabanında eşleşme bulunamazsa canlı desteğe aktarabilirsiniz.</p>
      <div class="quick-links">
        <h3>Hızlı Bağlantılar</h3>
        <ul>
          <li><a href="index.php?url=action/calendar"><i class="fas fa-calendar-alt"></i> Takvim</a></li>
          <li><a href="index.php?url=action/taskList&type=all"><i class="fas fa-tasks"></i> Görevler</a></li>
          <li><a href="index.php?url=indicator/index&aimid=1"><i class="fas fa-chart-line"></i> İndikatörler</a></li>
          <li><a href="index.php?url=action/upload"><i class="fas fa-upload"></i> Dosya Yükleme</a></li>
        </ul>
      </div>
      <div class="starter-info" style="margin-top:1rem;font-size:.62rem;line-height:.95rem;color:#3a5563;">Aşağıdaki hazır konulardan birini seçebilir veya kendi sorunuzu yazabilirsiniz.</div>
    </aside>
    <main class="chat-panel">
      <div class="chat-header">
        <div class="ch-left">
          <i class="fas fa-robot bot-icon"></i>
          <div>
            <h3>Destek Asistanı</h3>
            <span class="status"><span class="dot"></span> Çevrimiçi</span>
          </div>
        </div>
        <div class="ch-right">
          <button id="btnHandover" class="btn btn-outline-warning btn-sm" title="Canlı destek temsilcisine aktar"><i class="fas fa-user-headset"></i> Canlı Destek</button>
        </div>
      </div>
      <div id="chatMessages" class="chat-messages">
        <div class="msg bot">
          <div class="bubble" id="initialBubble">
            <p>Merhaba, ben yardım asistanıyım. Aşağıdan bir konu seçin ya da sorunuzu yazın.</p>
          </div>
        </div>
      </div>
      <form id="chatForm" class="chat-input-bar" autocomplete="off">
        <input type="text" id="chatQuestion" class="form-control" placeholder="Sorunuzu yazın ve Enter'a basın..." required />
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
      </form>
      <div class="handover-banner" id="handoverBanner" style="display:none;">
        <i class="fas fa-user-clock"></i> Canlı destek isteğiniz alındı. Bağlanılıyor...
      </div>
    </main>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/unified.php';
?>
<style>
.help-chat-wrapper {padding:1.2rem 1rem 2.5rem;background:linear-gradient(180deg,#f5f9fc,#ffffff 70%);} 
.help-chat-layout {display:grid;gap:1.2rem;grid-template-columns:280px 1fr;max-width:1600px;margin:0 auto;}
@media (max-width:1000px){.help-chat-layout{grid-template-columns:1fr;}}
.help-sidebar {background:#fff;border:1px solid #e1ecf2;border-radius:16px;padding:1.4rem 1.2rem;box-shadow:0 6px 18px -6px rgba(0,60,100,.12);position:relative;overflow:hidden;}
.help-sidebar:before {content:"";position:absolute;inset:0;background:linear-gradient(140deg,rgba(11,75,132,.07),rgba(39,177,214,.05));pointer-events:none;}
.hs-title {font-size:1.05rem;font-weight:600;margin:0 0 .65rem;display:flex;align-items:center;gap:.55rem;color:#0b4b84;}
.hs-desc {font-size:.75rem;line-height:1.2rem;color:#334d5c;margin:0 0 1rem;}
.quick-links h3 {font-size:.7rem;text-transform:uppercase;letter-spacing:.6px;font-weight:700;color:#4b6472;margin:0 0 .5rem;}
.quick-links ul {list-style:none;margin:0;padding:0;display:grid;gap:.4rem;}
.quick-links a {display:flex;align-items:center;gap:.5rem;font-size:.72rem;font-weight:600;padding:.55rem .65rem;border-radius:10px;text-decoration:none;color:#0b4b84;background:#e8f3fa;transition:all .25s;}
.quick-links a:hover {background:#d6e9f4;color:#08365c;}
.starter-info {margin-top:1rem;font-size:.62rem;line-height:.95rem;color:#3a5563;}
.faq-starter-list {display:grid;gap:.45rem;margin-top:.6rem;}
.faq-starter-list .faq-option {text-align:left;background:#eef6fb;border:1px solid #d3e4ee;color:#0b4b84;font-size:.66rem;padding:.5rem .6rem;border-radius:10px;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:.5rem;transition:.25s;}
.faq-starter-list .faq-option.live {background:#ffe9d6;border-color:#f7c7a1;color:#7d3a00;}
.faq-starter-list .faq-option:hover {background:#d9ecf6;}
.faq-starter-list .faq-option.live:hover {background:#ffd9b8;}
.chat-panel {background:#fff;border:1px solid #e1ecf2;border-radius:18px;display:flex;flex-direction:column;position:relative;overflow:hidden;box-shadow:0 8px 22px -8px rgba(0,60,100,.18);} 
.chat-header {padding:.9rem 1rem;border-bottom:1px solid #e1ecf2;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(120deg,#0b4b84,#1786c5);color:#fff;}
.chat-header h3 {font-size:.9rem;margin:0;font-weight:600;letter-spacing:.4px;}
.chat-header .status {font-size:.6rem;display:inline-flex;align-items:center;gap:.3rem;background:rgba(255,255,255,.2);padding:.2rem .5rem;border-radius:20px;letter-spacing:.5px;font-weight:600;}
.chat-header .dot {width:7px;height:7px;border-radius:50%;background:#35ff8a;box-shadow:0 0 6px #35ff8a;display:inline-block;animation:pulseDot 1.8s infinite;} @keyframes pulseDot {0%,100%{transform:scale(1);}50%{transform:scale(1.4);} }
.bot-icon {font-size:1.35rem;margin-right:.7rem;}
.chat-messages {flex:1;overflow-y:auto;padding:1.1rem 1.2rem;display:flex;flex-direction:column;gap:.9rem;background:linear-gradient(180deg,#f0f6fa,#ffffff);scroll-behavior:smooth;}
.chat-messages::-webkit-scrollbar {width:10px;} .chat-messages::-webkit-scrollbar-track {background:transparent;} .chat-messages::-webkit-scrollbar-thumb {background:#c2d5e0;border-radius:6px;} .chat-messages::-webkit-scrollbar-thumb:hover {background:#9fb9c7;}
.msg {max-width:72%;display:flex;}
.msg.user {align-self:flex-end;justify-content:flex-end;}
.msg .bubble {background:#fff;border:1px solid #d9e7ef;padding:.75rem .85rem;border-radius:16px 16px 16px 4px;font-size:.75rem;line-height:1.15rem;color:#1d4050;position:relative;box-shadow:0 4px 16px -6px rgba(0,0,0,.15);} 
.msg.user .bubble {background:#0b78c3;color:#fff;border-color:#0b78c3;border-radius:16px 16px 4px 16px;box-shadow:0 5px 18px -6px rgba(0,90,160,.4);} 
.msg .bubble p {margin:0 0 .5rem;} .msg .bubble p:last-child{margin:0;}
.msg .bubble .hint {opacity:.7;font-size:.65rem;margin-top:.3rem;}
.msg .bubble .answers {margin-top:.4rem;padding-top:.4rem;border-top:1px dashed #c2d5e0;display:grid;gap:.35rem;}
.msg .bubble .answers button {background:#e9f4fb;border:1px solid #d0e5ef;color:#0b4b84;font-size:.65rem;padding:.35rem .55rem;border-radius:8px;cursor:pointer;font-weight:600;letter-spacing:.4px;transition:all .25s;} .msg .bubble .answers button:hover {background:#d6e9f4;}
.msg.system .bubble {background:#fde9c7;border-color:#f6d09a;color:#6b4a11;}

.chat-input-bar {padding:.65rem .8rem;border-top:1px solid #e1ecf2;display:flex;gap:.6rem;background:#f5f9fc;} 
.chat-input-bar input {font-size:.8rem;border-radius:12px;}
.chat-input-bar button {border-radius:12px;padding:.55rem .9rem;font-weight:600;}
.handover-banner {background:#fff3cd;color:#856404;padding:.55rem .9rem;font-size:.7rem;letter-spacing:.4px;text-align:center;border-top:1px solid #f1e0a5;}

@media (max-width:1000px){ .msg {max-width:90%;} }
@media (prefers-color-scheme:dark){
  .help-chat-wrapper{background:linear-gradient(180deg,#0f1f29,#102a36);} .help-sidebar,.chat-panel{background:#1d2e39;border-color:#243c4a;} .help-sidebar:before{background:linear-gradient(140deg,rgba(40,180,255,.07),rgba(40,180,255,.05));}
  .hs-desc,.quick-links a, .faq-hint, .chat-messages, .msg .bubble{color:#d2e6ef;} .quick-links a{background:#17313f;color:#d5eef7;} .quick-links a:hover{background:#1f4355;color:#fff;} .chat-messages{background:linear-gradient(180deg,#16242e,#1d2e39);} .msg .bubble{background:#244254;border-color:#2f566b;color:#cfe9f5;} .msg.user .bubble{background:#0d6aa9;} .chat-input-bar{background:#17313f;border-color:#243c4a;} .contact-divider{background:linear-gradient(90deg,rgba(255,255,255,.05),rgba(255,255,255,.15),rgba(255,255,255,.05));}
}
</style>
<script>
(function(){
  const form = document.getElementById('chatForm');
  const input = document.getElementById('chatQuestion');
  const messages = document.getElementById('chatMessages');
  const btnHandover = document.getElementById('btnHandover');
  const banner = document.getElementById('handoverBanner');
  let sessionId = null; let lastMsgId = 0; let pollTimer = null; let chatActive=true;
  const starterFaq = [
    {q:'Dosya yüklerken hata alıyorum', a:'Dosya boyutu 10MB altında ve izinli uzantılarda olduğundan emin olun. Klasör yazma izni yoksa sistem yöneticisine bildiriniz.'},
    {q:'Faaliyet durumu güncellenmiyor', a:'Oturum süreniz dolmuş olabilir. Sayfayı yenileyip tekrar deneyin. Zorunlu alanları doldurduğunuzdan emin olun.'},
    {q:'Hedef filtrelemesi çalışmıyor', a:'Tarayıcı önbelleğini temizleyip yeniden deneyin. Devam ederse yöneticiye bildiriniz.'},
    {q:'Görevlerim takvimde görünmüyor', a:'Merkez ve tarih aralığını kontrol edin. Periyodik görev gün ayarı doğru mu bakın.'},
    {q:'Periyodik görev nasıl eklenir', a:'Faaliyet formunda periyodik seçeneğini işaretleyip periyot türü ve günü seçerek kaydedin.'},
    {q:'Görev tamamlandı işaretlenmiyor', a:'İzinleriniz yetersiz olabilir veya sayfa önbellekten geliyor. Çıkış yapıp tekrar girin.'}
  ];
  function appendMessage(role, html){
    const wrap = document.createElement('div');
    wrap.className = 'msg '+role;
    wrap.innerHTML = '<div class="bubble">'+html+'</div>';
    messages.appendChild(wrap);
    messages.scrollTop = messages.scrollHeight;
  }

  function faqSearch(q){
    return fetch('index.php?url=help/faqSearch&query='+encodeURIComponent(q))
      .then(r=>r.json()).catch(()=>({results:[]}));
  }

  function ensurePoll(){
    if(pollTimer || !sessionId) return;
    pollTimer = setInterval(pollMessages, 3000);
  }
  async function pollMessages(){
    if(!sessionId) return;
    try {
      const res = await fetch('index.php?url=help/poll&session_id='+sessionId+'&since='+lastMsgId);
      const data = await res.json();
      if(!data.success) return;
      if(data.messages && data.messages.length){
        data.messages.forEach(m=>{
          if(m.id>lastMsgId) lastMsgId=m.id;
          if(m.sender==='agent') appendMessage('bot','<p>'+escapeHtml(m.message)+'</p>');
        });
      }
      if(data.session_status==='closed'){
        appendMessage('system','<p>Oturum kapatıldı.</p>');
        clearInterval(pollTimer); pollTimer=null; sessionId=null;
      }
    } catch(e){}
  }

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    const q = input.value.trim();
    if(!q) return;
  appendMessage('user','<p>'+escapeHtml(q)+'</p>');
    input.value='';
    appendMessage('bot','<p><i class="fas fa-spinner fa-spin"></i> Aranıyor...</p>');
    const loading = messages.lastElementChild;
    const data = await faqSearch(q);
    loading.remove();
  if(data.results && data.results.length){
      const answersHtml = data.results.map(r=>'<div class="answer-item"><strong>'+escapeHtml(r.q)+':</strong><br>'+escapeHtml(r.a)+'</div>').join('<hr style="margin:.5rem 0;opacity:.25;">');
      appendMessage('bot','<p><strong>Bulunan yanıtlar:</strong></p>'+answersHtml+'<div class="answers"><button type="button" data-act="handover">Yanıtlar Yetersiz</button></div>');
    } else {
      appendMessage('bot','<p>Bu soruya ait hazır yanıt bulunamadı.</p><div class="answers"><button type="button" data-act="handover">Canlı Destek İste</button></div>');
    }
  });

  messages.addEventListener('click', function(e){
    const btn = e.target.closest('button[data-act="handover"]');
    if(btn){ startHandover(); }
  });
  btnHandover.addEventListener('click', startHandover);

  function startHandover(){
    appendMessage('system','<p>Canlı destek isteği gönderiliyor...</p>');
    fetch('index.php?url=help/handover',{method:'POST'}).then(r=>r.json()).then(d=>{
      if(!d.success){ appendMessage('system','<p>İstek başarısız: '+escapeHtml(d.error||'hata')+'</p>'); return; }
      sessionId = d.session_id; lastMsgId = 0; banner.style.display='block';
      appendMessage('system','<p>'+escapeHtml(d.message||'Oturum başlatıldı.')+'</p>');
      ensurePoll(); pollMessages();
    }).catch(()=>appendMessage('system','<p>Handover başarısız. Daha sonra tekrar deneyin.</p>'));
  }

  // Kullanıcı mesajlarını canlı destek modunda da ilet
  function sendUserMessageDirect(text){
    if(!sessionId) return;
    fetch('index.php?url=help/sendMessage',{method:'POST', body: new URLSearchParams({session_id:sessionId,text:text})})
      .then(r=>r.json()).then(d=>{ if(d.success){ pollMessages(); }});
  }

  // Form submit sonrası eğer session başlamışsa sadece destek mesajı olarak da kaydet
  form.addEventListener('submit', function(e){
    // Ek listener chaining already handled above; if sessionId active & we already sent FAQ search, also store user msg
    if(sessionId){
      const lastUser = messages.querySelector('.msg.user:last-child .bubble p');
      if(lastUser){ sendUserMessageDirect(lastUser.textContent); }
    }
  }, true);

  function escapeHtml(str){
    if(!str) return '';
    return str.replace(/[&<>"']/g,function(c){
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]);
    });
  }

  (function renderStarter(){
    const ib = document.getElementById('initialBubble'); if(!ib) return;
    let html = '<div class="faq-starter-list">';
    starterFaq.forEach(item=>{ html += '<button type="button" class="faq-option" data-q="'+item.q.replace(/"/g,'&quot;')+'" data-a="'+item.a.replace(/"/g,'&quot;')+'"><i class="fas fa-question-circle"></i> '+item.q+'</button>'; });
    html += '<button type="button" class="faq-option live" data-live="1"><i class="fas fa-headset"></i> Canlı Destek</button></div>';
    ib.insertAdjacentHTML('beforeend', html);
  })();
  document.getElementById('chatMessages').addEventListener('click',function(e){
    const opt = e.target.closest('.faq-option'); if(opt){ if(opt.dataset.live){ startHandover(); return; }
      const q=opt.dataset.q||''; const a=opt.dataset.a||''; if(q){ appendMessage('user','<p>'+q+'</p>'); if(a){ appendMessage('bot','<p>'+a+'</p><div class="answers"><button type="button" data-act="handover">Canlı Destek</button></div>'); } }
      return; }
    const btn=e.target.closest('button[data-act="handover"]'); if(btn){ startHandover(); }
  });
})();
</script>
