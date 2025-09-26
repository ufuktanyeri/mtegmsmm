<?php
$pageTitle = 'Görev Dosyası Yükle';
// Layout başlığını gizle, içerikte h2 var
if (session_status() === PHP_SESSION_NONE) session_start();
// CSRF token üret
if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<div class="container mt-4 upload-wrap">
    <h2 class="mb-3"><i class="fas fa-cloud-upload-alt"></i> Görev Dosyası Yükle</h2>
    <p class="text-body-secondary small mb-4">Dosyalar <code>wwwroot/uploads/tasks</code> dizinine kaydedilir. İzin verilen türler: pdf, doc(x), xls(x), ppt(x), jpg, jpeg, png, gif, txt. Maksimum boyut 10MB.</p>
    <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form id="uploadForm" method="post" enctype="multipart/form-data" class="card p-3 shadow-sm border-0 bg-light">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form-group mb-3">
            <label class="font-weight-semibold">Dosya Seçin (tek dosya):</label>
            <input type="file" name="file" id="fileInput" class="form-control" required>
        </div>
        <div id="dropZone" class="drop-zone mb-3" tabindex="0" aria-label="Sürükle bırak yükleme alanı">
            <div class="dz-inner">
                <i class="fas fa-upload"></i>
                <span>Dosyayı buraya bırakın veya tıklayın</span>
            </div>
        </div>
        <div class="progress mb-2 d-none" id="uploadProgressWrap" style="height:8px;">
            <div class="progress-bar" id="uploadProgress" role="progressbar" style="width:0%"></div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-cloud-upload-alt"></i> Yükle</button>
            <a href="index.php?url=action/listUploads" class="btn btn-outline-secondary"><i class="fas fa-list"></i> Yüklenenleri Gör</a>
        </div>
        <div id="uploadMsg" class="mt-3" role="status" aria-live="polite"></div>
    </form>
</div>
<style>
.upload-wrap code {background:#f1f4f6;padding:2px 6px;border-radius:4px;}
.drop-zone {border:2px dashed #8aa7b7;border-radius:14px;padding:1.75rem;text-align:center;cursor:pointer;background:linear-gradient(145deg,#f5f8fa,#eef3f6);position:relative;transition:.25s;}
.drop-zone:focus {outline:3px solid #1786c5;outline-offset:2px;}
.drop-zone.dragover {background:#e3f4ff;border-color:#1786c5;}
.drop-zone .dz-inner {display:flex;flex-direction:column;align-items:center;gap:.6rem;color:#4a6675;font-weight:500;font-size:.95rem;letter-spacing:.3px;}
.drop-zone .dz-inner i {font-size:2rem;color:#1786c5;}
.progress {background:#dbe6ec;border-radius:20px;overflow:hidden;}
.progress-bar {background:linear-gradient(90deg,#0b4b84,#1786c5);transition:width .25s;}
#uploadMsg .alert {margin-bottom:0;}
</style>
<script>
(function(){
    const form=document.getElementById('uploadForm');
    const fileInput=document.getElementById('fileInput');
    const dropZone=document.getElementById('dropZone');
    const progWrap=document.getElementById('uploadProgressWrap');
    const prog=document.getElementById('uploadProgress');
    const msg=document.getElementById('uploadMsg');
    function showMessage(type,text){msg.innerHTML='<div class="alert alert-'+type+'">'+text+'</div>'}
    dropZone.addEventListener('click',()=>fileInput.click());
    dropZone.addEventListener('dragover',e=>{e.preventDefault();dropZone.classList.add('dragover');});
    dropZone.addEventListener('dragleave',()=>dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop',e=>{e.preventDefault();dropZone.classList.remove('dragover'); if(e.dataTransfer.files.length){ fileInput.files=e.dataTransfer.files; }});
    form.addEventListener('submit',e=>{e.preventDefault(); if(!fileInput.files.length){showMessage('warning','Lütfen bir dosya seçin');return;} const fd=new FormData(form); fd.append('file',fileInput.files[0]); progWrap.classList.remove('d-none'); prog.style.width='0%'; showMessage('info','Yükleme başlatılıyor...'); fetch(window.location.href,{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(data=>{ if(data.success){ showMessage('success',data.message); form.reset(); prog.style.width='100%'; } else { showMessage('danger', data.error||'Yükleme başarısız'); }}).catch(err=>{ showMessage('danger','İstek hatası: '+err); }); });
    // Progress simulation (XHR olmadan gerçek ilerleme alınamaz; istenirse XHR ile değiştirilebilir)
    let simInterval; form.addEventListener('submit',()=>{clearInterval(simInterval); let p=0; simInterval=setInterval(()=>{p=Math.min(95,p+Math.random()*15); prog.style.width=p+'%'; if(p>=95) clearInterval(simInterval);},300);});
})();
</script>
<?php
?>