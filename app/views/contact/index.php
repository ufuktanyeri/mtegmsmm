<?php
$title='İletişim';
$page_title='İletişim';
$hidePageHeader = true; // Tam ekran tasarım
ob_start();
?>
<section class="contact-hero">
  <div class="contact-hero__overlay"></div>
  <div class="contact-hero__content">
    <h1><i class="fas fa-envelope-open-text"></i> İletişim</h1>
    <p>Görüş, öneri ve işbirliği talepleriniz için resmi kanallarımız.</p>
  </div>
</section>
<section class="contact-wrapper">
  <div class="contact-grid">
    <div class="contact-panel contact-info">
      <h2 class="panel-title"><span class="icon"><i class="fas fa-building"></i></span>Adres</h2>
      <address>
        Milli Eğitim Bakanlığı Merkez Bina<br>
        Atatürk Bulvarı No: 98, 1. Kat, A Blok<br>
        Bakanlıklar / ANKARA
      </address>
      <div class="contact-divider"></div>
      <h3 class="sub-title"><i class="fas fa-phone-alt"></i> Telefon / Faks</h3>
      <ul class="contact-list">
        <li><i class="fas fa-phone"></i> +90 (312) 413 26 80</li>
        <li><i class="fas fa-phone"></i> +90 (312) 413 26 81</li>
        <li><i class="fas fa-fax"></i> +90 (312) 413 18 38</li>
      </ul>
      <div class="contact-divider"></div>
      <h3 class="sub-title"><i class="fas fa-share-alt"></i> Sosyal Medya</h3>
      <div class="social-deck">
        <a href="https://www.facebook.com/meslegimhayatim" class="social-tile facebook" target="_blank" rel="noopener" aria-label="Facebook">
          <div class="ring"></div><i class="fab fa-facebook-f"></i><span>Facebook</span>
        </a>
        <a href="https://x.com/tcmeb_mtegm" class="social-tile twitter" target="_blank" rel="noopener" aria-label="X (Twitter)">
          <div class="ring"></div><i class="fab fa-x-twitter"></i><span>X</span>
        </a>
        <a href="https://www.instagram.com/meslegimhayatim/" class="social-tile instagram" target="_blank" rel="noopener" aria-label="Instagram">
          <div class="ring"></div><i class="fab fa-instagram"></i><span>Instagram</span>
        </a>
      </div>
      <div class="contact-footer-links">
        <a href="mailto:info@meb.gov.tr" class="link-inline"><i class="fas fa-paper-plane"></i> E-posta Gönder</a>
        <a href="#harita" class="link-inline"><i class="fas fa-map-marker-alt"></i> Haritada Gör</a>
      </div>
    </div>
    <div class="contact-panel map-panel" id="harita">
      <h2 class="panel-title"><span class="icon"><i class="fas fa-map-marked-alt"></i></span>Konum</h2>
      <p class="map-desc">Merkez binamızın konumu (Google Haritalar)</p>
      <div class="map-embed-wrapper">
        <iframe title="MEB Merkez Bina Harita" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3067.196130062378!2d32.85288517699487!3d39.92304017152667!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d34f2f1d5f5d2b%3A0x97b789a501d0689c!2sMilli%20E%C4%9Fitim%20Bakanl%C4%B1%C4%9F%C4%B1!5e0!3m2!1str!2str!4v<?= time() ?>"></iframe>
      </div>
      <div class="map-actions">
        <a target="_blank" rel="noopener" href="https://maps.google.com/?q=Milli+Egitim+Bakanligi+Ankara" class="btn btn-outline-primary btn-sm"><i class="fas fa-external-link-alt"></i> Google Haritalar'da Aç</a>
      </div>
    </div>
  </div>
  <div class="contact-bottom-actions">
    <a class="btn btn-primary" href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
  </div>
</section>
<?php
$content=ob_get_clean();
include __DIR__.'/../layouts/unified.php';
?>
<style>
.contact-hero {position:relative;min-height:240px;display:flex;align-items:center;justify-content:center;text-align:center;background:linear-gradient(135deg,#0b4b84,#1786c5 55%,#27b1d6);color:#fff;overflow:hidden;}
.contact-hero__overlay {position:absolute;inset:0;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,.15),transparent 60%),radial-gradient(circle at 70% 60%,rgba(255,255,255,.12),transparent 65%);mix-blend-mode:overlay;}
.contact-hero__content {position:relative;padding:1.5rem 1rem;max-width:900px;}
.contact-hero__content h1 {font-size:2rem;font-weight:700;margin:0 0 .6rem;letter-spacing:.5px;}
.contact-hero__content p {margin:0;font-size:1rem;font-weight:400;opacity:.9;}
.contact-wrapper {padding:2.2rem 1.25rem 4rem;background:linear-gradient(180deg,#f4f8fb 0%,#ffffff 65%);} @media (min-width:1200px){.contact-wrapper{padding-left:2.5rem;padding-right:2.5rem;}}
.contact-grid {display:grid;gap:2rem;max-width:1500px;margin:0 auto;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));align-items:start;}
.contact-panel {position:relative;background:#ffffff;border-radius:18px;padding:1.75rem 1.6rem;box-shadow:0 4px 20px -6px rgba(0,40,70,.12),0 2px 6px -2px rgba(0,40,70,.08);border:1px solid #e4eef5;overflow:hidden;}
.contact-panel:before {content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(135deg,rgba(11,75,132,.08),rgba(15,144,201,.05));opacity:.85;pointer-events:none;}
.contact-panel.map-panel iframe {width:100%;height:380px;border:0;border-radius:14px;box-shadow:0 3px 14px -4px rgba(0,0,0,.25);} .contact-panel.map-panel .map-embed-wrapper {position:relative;border-radius:16px;overflow:hidden;background:#e6f2f9;}
.panel-title {font-size:1.1rem;margin:0 0 1rem;font-weight:600;display:flex;align-items:center;gap:.6rem;color:#0b4b84;letter-spacing:.3px;} .panel-title .icon {display:inline-flex;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#0b4b84,#2196c9);align-items:center;justify-content:center;color:#fff;font-size:1rem;box-shadow:0 3px 8px -3px rgba(0,0,0,.35);}
.sub-title {font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.7px;margin:1.2rem 0 .4rem;color:#355468;display:flex;align-items:center;gap:.5rem;}
address {font-style:normal;line-height:1.45;font-size:.92rem;color:#2c4a57;margin:0;} .contact-list {list-style:none;margin:.4rem 0 0;padding:0;font-size:.85rem;color:#2c4a57;display:grid;gap:.35rem;} .contact-list i {color:#0b7db3;margin-right:.4rem;}
.contact-divider {height:1px;background:linear-gradient(90deg,rgba(0,0,0,.08),rgba(0,0,0,.25),rgba(0,0,0,.08));margin:1.25rem 0;}
.social-deck {display:flex;flex-wrap:wrap;gap:1rem;margin-top:.6rem;} .social-tile {position:relative;flex:1 1 120px;min-width:140px;max-width:180px;background:#0d2230;color:#fff;text-decoration:none;padding:1rem .75rem;border-radius:16px;display:flex;flex-direction:column;align-items:flex-start;gap:.6rem;overflow:hidden;isolation:isolate;transition:all .45s cubic-bezier(.6,.2,.2,1);font-size:.8rem;font-weight:500;letter-spacing:.4px;} .social-tile .ring {position:absolute;inset:-30%;background:conic-gradient(from 0deg,var(--c1),var(--c2),var(--c3),var(--c1));filter:blur(22px);opacity:.55;transition:opacity .5s;z-index:-1;animation:spin 6s linear infinite;} .social-tile i {font-size:1.35rem;} .social-tile span {font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;font-weight:600;opacity:.95;} .social-tile:hover {transform:translateY(-6px) scale(1.03);box-shadow:0 15px 34px -8px rgba(0,0,0,.4);} .social-tile:hover .ring {opacity:.85;} .social-tile.facebook {--c1:#1877f2;--c2:#45a2ff;--c3:#1d53ff;background:#0a1d33;} .social-tile.twitter {--c1:#1da1f2;--c2:#37d7ff;--c3:#0077b5;background:#071d29;} .social-tile.instagram {--c1:#f09433;--c2:#e6683c;--c3:#bc1888;background:#2a0f27;}
@keyframes spin {to {transform:rotate(360deg);}}
.contact-footer-links {display:flex;flex-wrap:wrap;gap:1rem;margin-top:1.4rem;} .link-inline {font-size:.75rem;text-decoration:none;font-weight:600;letter-spacing:.5px;color:#0b4b84;display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .85rem;background:#e9f4fb;border-radius:30px;transition:all .3s;} .link-inline:hover {background:#d3ecf8;color:#063553;}
.map-desc {font-size:.72rem;letter-spacing:.4px;text-transform:uppercase;margin:-.4rem 0 1rem;color:#48606d;font-weight:600;} .map-actions {margin-top:1rem;} .map-actions .btn {font-size:.7rem;letter-spacing:.4px;font-weight:600;}
.contact-bottom-actions {max-width:1500px;margin:2.5rem auto 0;display:flex;justify-content:center;} .contact-bottom-actions .btn {padding:.75rem 1.4rem;font-weight:600;letter-spacing:.6px;border-radius:12px;}
@media (max-width:768px){ .contact-hero{min-height:200px;} .contact-hero__content h1{font-size:1.55rem;} .contact-panel{padding:1.3rem 1.1rem;} .contact-grid{gap:1.4rem;} .social-tile{min-width:120px;padding:.9rem .7rem;} .contact-wrapper{padding:1.5rem 1rem 3.5rem;} .contact-panel.map-panel iframe{height:300px;} }
@media (prefers-color-scheme:dark){ .contact-wrapper{background:linear-gradient(180deg,#0f1f29,#102a36);} .contact-panel{background:#1d2e39;border-color:#243c4a;} .contact-panel:before{background:linear-gradient(135deg,rgba(40,180,255,.08),rgba(40,180,255,.03));} address,.contact-list li, .sub-title, .panel-title{color:#e1edf5;} .contact-divider{background:linear-gradient(90deg,rgba(255,255,255,.08),rgba(255,255,255,.25),rgba(255,255,255,.08));} .link-inline{background:#17313f;color:#d5eef7;} .link-inline:hover{background:#1f4355;color:#fff;} }
</style>
