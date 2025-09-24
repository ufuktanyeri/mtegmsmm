<?php
$title = 'SMM Haritası - Türkiye Sektörel Mükemmeliyet Merkezleri';
$description = 'Türkiye genelindeki Sektörel Mükemmeliyet Merkezlerinin haritası ve bilgileri';
$bodyClass = 'hold-transition layout-fixed smm-network-page';

// Load header component
include __DIR__ . '/../components/header.php';
?>

<div class="wrapper">
    <?php include __DIR__ . '/../components/navbar.php'; ?>

<style>
    #map {
        height: 500px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .center-card {
        transition: transform 0.2s ease;
    }
    .center-card:hover {
        transform: translateY(-2px);
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"></script>

    <?php
    // Hero section configuration
    $heroConfig = [
        'title' => 'Türkiye Cumhuriyeti Milli Eğitim Bakanlığı',
        'subtitle' => 'Mesleki ve Teknik Eğitim Genel Müdürlüğü<br>Sektörel Mükemmeliyet Merkezleri',
        'icon' => 'fas fa-map-marked-alt',
        'gradient' => true,
        'type' => 'section'  // Changed from card to section for consistency
    ];
    include __DIR__ . '/../components/hero.php';
    ?>

<div class="container py-4">

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <label for="areaSelect" class="form-label fw-semibold">
                        <i class="fas fa-filter me-2 text-primary"></i>
                        Alan Seçin:
                    </label>
                    <select id="areaSelect" class="form-select">
                        <option value="">Tüm Alanlar</option>   
                        <option value="Bilişim Teknolojileri">Bilişim Teknolojileri</option>
                        <option value="Çocuk Gelişimi ve Eğitimi">Çocuk Gelişimi ve Eğitimi</option>
                        <option value="Denizcilik">Denizcilik</option>					  
                        <option value="Elektrik Elektronik Teknolojileri">Elektrik Elektronik Teknolojileri</option>
                        <option value="Endüstriyel Otomasyon Teknolojileri">Endüstriyel Otomasyon Teknolojileri</option>
                        <option value="Gemi Yapım">Gemi Yapım</option>
                        <option value="Gıda Teknolojileri">Gıda Teknolojileri</option>
                        <option value="Güzellik Hizmetleri">Güzellik Hizmetleri</option>				   
                        <option value="İnşaat Teknolojileri">İnşaat Teknolojileri</option>    
                        <option value="Konaklama ve Seyahat Hizmetleri">Konaklama ve Seyahat Hizmetleri</option>
                        <option value="Kimya Teknolojileri">Kimya Teknolojileri</option>
                        <option value="Makine ve Tasarım Teknolojileri">Makine ve Tasarım Teknolojileri</option>
                        <option value="Matbaa Teknolojileri">Matbaa Teknolojileri</option>                 
                        <option value="Mesleki Yabancı Dil">Mesleki Yabancı Dil</option>
                        <option value="Mesleki Matematik">Mesleki Matematik</option>
                        <option value="Mesleki Fen Bilimleri">Mesleki Fen Bilimleri</option>
                        <option value="Metal Teknolojileri">Metal Teknolojileri</option>
                        <option value="Metalürji Teknolojileri">Metalürji Teknolojileri</option>
                        <option value="Mobilya ve İç Mekan Tasarımı">Mobilya ve İç Mekan Tasarımı</option>
                        <option value="Moda Tasarım Teknolojileri">Moda Tasarım Teknolojileri</option>
                        <option value="Motorlu Araçlar Teknolojileri">Motorlu Araçlar Teknolojileri</option>    					
                        <option value="Plastik Teknolojileri">Plastik Teknolojileri</option>
                        <option value="Raylı Sistemler Teknolojileri">Raylı Sistemler Teknolojileri</option>
                        <option value="Tekstil Teknolojileri">Tekstil Teknolojileri</option>
                        <option value="Tesisat Teknolojileri ve İklimlendirme">Tesisat Teknolojileri ve İklimlendirme</option>                  
                        <option value="Uçak Bakım Alanı">Uçak Bakım Alanı</option>					 
                        <option value="Yenilenebilir Enerji Teknolojileri">Yenilenebilir Enerji Teknolojileri</option>
                        <option value="Yiyecek İçecek Hizmetleri">Yiyecek İçecek Hizmetleri</option>   
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4 class="text-primary mb-0">15</h4>
                            <small class="text-muted">SMM Merkezi</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-success mb-0">6</h4>
                            <small class="text-muted">Şehir</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-warning mb-0">28</h4>
                            <small class="text-muted">Alan</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-info mb-0">3000+</h4>
                            <small class="text-muted">Öğretmen</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div id="map" class="shadow-sm"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h3 class="mb-3">
                <i class="fas fa-building me-2 text-primary"></i>
                Seçilen Alan İçin Merkezler
            </h3>
            <div id="centerList" class="row"></div>
        </div>
    </div>
</div>

<script>
    const smmData = {
        "Ankara": [
            {
                name: "Cezeri Yeşil Teknoloji Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Yenilenebilir Enerji Teknolojileri", "Elektrik Elektronik Teknolojileri"],
                phone: "+90 312 283 10 61",
                address: "Tunahan Mah.211. Cad.No:12 Etimesgut ANKARA",
                web: "https://cezeri.meb.k12.tr/",
                instagram: "https://www.instagram.com/cezeriyesilteknolojismm",
            },
            {
                name: "Gazi Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Bilişim Teknolojileri", "Raylı Sistemler Teknolojileri"],
                phone: "+90 312 212 62 46",
                address: "Emniyet Mahallesi Milas Sokak No88 Teknikokullar ANKARA",
                web: "https://gatemgazi.meb.k12.tr/",
                instagram: "https://www.instagram.com/gazi_smm",
            },
            {
                name: "Mimar Sinan Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["İnşaat Teknolojileri", "Tesisat Teknolojileri ve İklimlendirme"],
                phone: "+90 312 212 67 90",
                address: "Emniyet Mah. Milas Sok.No:82 Teknikokullar/Beşevler ANKARA",
                web: "https://mimarsinanmtal.meb.k12.tr/",
                instagram: "https://www.instagram.com/smm_mimarsinan",
            }
        ],
        "İstanbul": [
            {
                name: "Sultanbeyli Sabiha Gökçen Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Elektrik Elektronik Teknolojileri", "Uçak Bakım Alanı"],
                phone: "+90 216 669 10 02",
                address: "Battal Gazi Mah. Bosna Blv. Revani Sk. No:10 Sultanbeyli İSTANBUL",
                web: "https://isgokcen.meb.k12.tr/",
                instagram: "https://www.instagram.com/sabihagokcen_smm",
            },
            {
                name: "İHKİB Kâğıthane Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Moda Tasarım Teknolojileri", "Tekstil Teknolojileri"],
                phone: "+90 212 221 76 26",
                address: "Gürsel Mah. Görgülü Sok. No 26/1 Kağıthane İSTANBUL",
                web: "https://kagithaneihkib.meb.k12.tr/",
                instagram: "https://www.instagram.com/smm_kagithane",
            },
            {
                name: "Pendik Barbaros Hayrettin Paşa Denizcilik Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Denizcilik", "Gemi Yapım"],
                phone: "+90 216 493 06 67",
                address: "Esenyalı Mah. Necmettin Erbakan Cad. Metehan Sk. No:5 Pendik İSTANBUL",
                web: "https://pbhpmtal.meb.k12.tr/",
                instagram: "https://www.instagram.com/smmpendik",
            },
            {
                name: "Borsa İstanbul Başakşehir Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Mesleki Yabancı Dil", "Mesleki Matematik", "Mesleki Fen Bilimleri"],
                phone: "+90 212 488 00 64",
                address: "Başak Mh. Yaşar Doğu Bulvarı No:16 Başakşehir İSTANBUL",
                web: "https://bistbasaksehirmtal.meb.k12.tr/",
                instagram: "https://www.instagram.com/smmbaşaksehir",
            }
        ],
        "Kocaeli": [
            {
                name: "Darıca Aslan Çimento Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Matbaa Teknolojileri", "Gıda Teknolojileri", "Kimya Teknolojileri"],
                phone: "+90 262 655 58 87",
                address: "Fevzi Çakmak Mahallesi Dr. Zeki Acar Cad. No:74 Darıca KOCAELİ",
                web: "https://datem.meb.k12.tr/",
                instagram: "https://www.instagram.com/aslancimento_smm",
            },
            {
                name: "Denizyıldızları Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Bilişim Teknolojileri"],
                phone: "+90 262 745 62 16",
                address: "Nene Hatun Mah. Turgut Reis Cad. Bostan Sk. No:25, 41700 Darıca KOCAELİ",
                web: "https://denizyildizlari2.meb.k12.tr/icerikler/denizyildizlari-sektorel-mukemmeliyet-merkezi_15776703.html",
                instagram: "https://www.instagram.com/denizyildizlarismm",
            }
        ],
        "Bursa": [
            {
                name: "Tophane Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Makine ve Tasarım Teknolojileri", "Plastik Teknolojileri", "Metalürji Teknolojileri"],
                phone: "+90 224 220 44 04",
                address: "Osmangazi Mah. Hastayurdu Cad. No:2 Osmangazi BURSA",
                web: "https://tophanemtal.meb.k12.tr/",
                instagram: "https://www.instagram.com/tophane_smm",
            },
            {
                name: "Hacı Sevim Yıldız Mobilya ve İç Mekan Tasarımı Teknolojileri Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Mobilya ve İç Mekan Tasarımı"],
                phone: "+90 224 714 21 24",
                address: "Karalar Mahallesi Çavuşköy Yolu Caddesi No12/5A Hacı Sevim Yıldız Mesleki Eğitim Külliyesi İnegöl BURSA",
                web: "https://mobilyaihtisas.meb.k12.tr/",
                instagram: "https://www.instagram.com/inegolsmm",
                linked: "https://linktr.ee/inegolsmm",
            },
            {
                name: "Otomotiv Endüstrisi İhracatçıları Birliği Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Motorlu Araçlar Teknolojileri", "Endüstriyel Otomasyon Teknolojileri"],
                phone: "+90 224 483 28 50",
                address: "Dumlupınar Mh. Gelibolu Cd. No 78 Görükle / Nilüfer BURSA",
                web: "https://oibatl.meb.k12.tr/",
                instagram: "https://www.instagram.com/otomotivlisesismm",
            },
            {
                name: "Bursa Mimar Sinan Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Metal Teknolojileri"],
                phone: "+90 224 366 26 72",
                address: "Mimar Sinan Mahallesi Meral Sok. No: 3 Yıldırım BURSA",
                web: "https://bursamseml.meb.k12.tr/",
                instagram: "https://www.instagram.com/bursamimarsinansmm",
            }
        ],
        "İzmir": [
            {
                name: "Karşıyaka Suzan Divrik Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Çocuk Gelişimi ve Eğitimi", "Güzellik Hizmetleri"],
                phone: "+90 232 369 29 16",
                address: "Donanmacı Mh. 1737 Sok. No:68/A 35530 İZMİR",
                web: "https://suzandivrikktml.meb.k12.tr/",
                instagram: "https://www.instagram.com/smmsuzandivrik",
            }
        ],
        "Antalya": [
            {
                name: "Falez Turizm Mesleki ve Teknik Anadolu Lisesi SMM",
                fields: ["Yiyecek İçecek Hizmetleri", "Konaklama ve Seyahat Hizmetleri"],
                phone: "+90 242 238 51 30",
                address: "Varlık Mahallesi 100. Yıl Caddesi No: 135 ANTALYA",
                web: "https://falezmtal.meb.k12.tr/",
                instagram: "https://www.instagram.com/falezsmm",
            }
        ]
    };

    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('map').setView([39.92077, 32.85411], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        const markers = {};

        const cityCoordinates = {
            "Ankara": [39.92077, 32.85411],
            "İstanbul": [41.00824, 28.97836],
            "Kocaeli": [40.76694, 29.91667],
            "Bursa": [40.18257, 29.06687],
            "İzmir": [38.423733, 27.142826],
            "Antalya": [36.884804, 30.704044],
        };

        for (const city in cityCoordinates) {
            markers[city] = L.marker(cityCoordinates[city]).addTo(map);
        }

        $('#areaSelect').on('change', function () {
            const selectedArea = $(this).val();
            $('#centerList').empty();

            for (const city in smmData) {
                const centers = smmData[city].filter(center => center.fields.includes(selectedArea));
                if (centers.length > 0) {
                    markers[city].setOpacity(1);
                   
                    centers.forEach(center => {
                        const cardHtml = `
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-0 shadow-sm h-100 center-card">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">${center.name}</h6>
                                        <small>${city}</small>
                                    </div>
                                    <div class="card-body">   
                                        <p class="mb-2">
                                            <i class="fas fa-phone text-primary me-2"></i>
                                            <a href="tel:${center.phone}" class="text-decoration-none">${center.phone}</a>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                                            <small>${center.address}</small>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-globe text-info me-2"></i>
                                            <a href="${center.web}" target="_blank" class="text-decoration-none">Web Sitesi</a>
                                        </p>
                                        <p class="mb-2">
                                            <i class="fab fa-instagram text-danger me-2"></i>
                                            <a href="${center.instagram}" target="_blank" class="text-decoration-none">Instagram</a>
                                        </p>
                                        ${center.linked ? `
                                        <p class="mb-0">
                                            <i class="fab fa-linkedin text-primary me-2"></i>
                                            <a href="${center.linked}" target="_blank" class="text-decoration-none">LinkedIn</a>
                                        </p>` : ''}
                                    </div>
                                </div>
                            </div>`;
                        $('#centerList').append(cardHtml);
                    });
                } else {
                    markers[city].setOpacity(0.3);
                }
            }

            if (!selectedArea) {
                for (const city in markers) {
                    markers[city].setOpacity(1);
                }
            }
        });
    });
</script>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</div>

<?php
// Load scripts component
include __DIR__ . '/../components/scripts.php';
?>