<?php
// Konfigurasi dasar
$site_title = "Kos Bu Tik - Tata Cara & Tata Tertib";
$kos_name = "Kos Bu Tik";
$kos_phone = "+62 858-1532-0313";
$kos_email = "kosbutik12@gmail.com";
$kos_address = "Jl. Margo Tani, Sukorame, Kec. Mojoroto, Kota Kediri, Jawa Timur 64114";
$kos_lat = -7.8058928;
$kos_lng = 111.98148;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Inter:700,600,400" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .header-custom {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            text-align: center;
            padding: 4rem 2rem 3rem 2rem;
        }

        .header-custom h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .header-custom p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .container-custom {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .card-custom {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
        }

        .card-custom h2 {
            color: #667eea;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
        }

        .steps {
            display: grid;
            gap: 1.5rem;
        }

        .step {
            display: flex;
            gap: 1rem;
            padding: 1.5rem;
            background: #f8f9ff;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .step-number {
            background: #667eea;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .step-content h4 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .highlight {
            background: linear-gradient(120deg, #a8edea 0%, #fed6e3 100%);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Rules */
        .rules-list {
            list-style: none;
            counter-reset: rule-counter;
        }

        .rules-list li {
            counter-increment: rule-counter;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9ff;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            position: relative;
        }

        .rules-list li:before {
            content: counter(rule-counter);
            position: absolute;
            left: -15px;
            top: 15px;
            background: #dc3545;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .sub-rules {
            list-style: none;
            margin-top: 0.5rem;
            padding-left: 1rem;
        }

        .sub-rules li {
            background: #fff;
            border-left: 2px solid #ffc107;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            font-size: 0.95rem;
        }

        .sub-rules li:before {
            content: "→";
            color: #ffc107;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        /* Alert */
        .alert-custom {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            border-left: 4px solid #ffc107;
        }

        .alert-custom h4 {
            color: #856404;
            margin-bottom: 0.5rem;
        }

        .alert-custom p {
            color: #856404;
            margin: 0;
        }

        /* Map */
        #map {
            height: 300px;
            width: 100%;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .contact-info {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 1.5rem;
            border-left: 4px solid #28a745;
        }

        .contact-info h5 {
            color: #28a745;
            margin-bottom: 1rem;
        }

        .contact-info p {
            margin-bottom: 0.5rem;
        }

        .contact-info a {
            color: #667eea;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .social-links a {
            display: inline-block;
            margin-right: 10px;
            padding: 8px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .social-links a:hover {
            background: #5a67d8;
            color: white;
        }

        /* Footer */
        .footer-custom {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            margin-top: 3rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-custom h1 {
                font-size: 2rem;
            }

            .container-custom {
                padding: 1rem 0.5rem;
            }

            .step {
                flex-direction: column;
                text-align: center;
            }

            #map {
                height: 250px;
            }
        }

        /* Icon SVG */
        .icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        /* Loading overlay untuk map */
        .map-loading {
            position: relative;
        }

        .map-loading::before {
            content: "Loading Map...";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: rgba(255,255,255,0.9);
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <header class="header-custom">
        <div class="text-start mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
            &larr; Kembali
            </a>
        </div>
        <h1>Selamat Datang di <?php echo $kos_name; ?></h1>
        <p>Kos eksklusif dengan fasilitas lengkap dan lokasi strategis</p>
    </header>



    <!-- Main Content -->
    <div class="container-custom">
        <!-- Tata Cara Section -->
        <section class="card-custom" id="tata-cara">
            <h2>
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tata Cara Pemesanan Kamar Kost
            </h2>
            <div class="alert-custom">
                <h4>📋 Informasi Penting</h4>
                <p>Ikuti langkah-langkah berikut untuk melakukan pemesanan kamar kos dengan mudah dan aman.</p>
            </div>
            <div class="steps">
                <?php
                $steps = [
                    [
                        'title' => 'Pendaftaran Calon Penghuni',
                        'content' => 'Calon penghuni melakukan <span class="highlight">pendaftaran akun</span> dengan mengisi data pribadi seperti nama lengkap, nomor telepon, email, dan alamat asal.'
                    ],
                    [
                        'title' => 'Login ke Sistem',
                        'content' => 'Setelah mendaftar, calon penghuni dapat <span class="highlight">login menggunakan email dan password</span> yang telah didaftarkan.'
                    ],
                    [
                        'title' => 'Pemilihan Kamar',
                        'content' => 'Setelah login, penghuni dapat <span class="highlight">melihat dan memilih kamar</span> yang tersedia beserta fasilitas, harga, dan foto kamar.'
                    ],
                    [
                        'title' => 'Pengisian Formulir Kelengkapan Data',
                        'content' => 'Untuk menyelesaikan pemesanan, penghuni wajib <span class="highlight">mengisi formulir kelengkapan data</span> dengan lengkap dan benar, termasuk:',
                        'list' => [
                            'Data identitas lengkap (KTP/Buku Nikah)',
                            'Nomor kontak darurat',
                            'Pekerjaan/Status',
                            'Alamat asal lengkap'
                        ]
                    ],
                    [
                        'title' => 'Proses Pembayaran',
                        'content' => 'Setelah formulir terisi, penghuni akan <span class="highlight">diarahkan ke halaman pembayaran</span> yang menampilkan total biaya sewa dan detail rekening tujuan transfer.'
                    ],
                    [
                        'title' => 'Upload Bukti Transfer',
                        'content' => 'Penghuni <span class="highlight">mengupload bukti transfer pembayaran</span> berupa gambar (screenshot/foto) yang jelas dan dapat dibaca.'
                    ],
                    [
                        'title' => 'Menunggu Konfirmasi Pemilik',
                        'content' => 'Setelah upload bukti transfer, penghuni <span class="highlight">menunggu konfirmasi dari pemilik kos</span>. Proses konfirmasi maksimal 1x24 jam hari kerja.'
                    ],
                    [
                        'title' => 'Konfirmasi & Check-in',
                        'content' => 'Setelah pembayaran dikonfirmasi, penghuni akan mendapat notifikasi dan dapat melakukan <span class="highlight">check-in sesuai jadwal yang disepakati</span>.'
                    ]
                ];

                foreach ($steps as $index => $step) {
                    $stepNumber = $index + 1;
                    echo "<div class='step'>";
                    echo "<div class='step-number'>{$stepNumber}</div>";
                    echo "<div class='step-content'>";
                    echo "<h4>{$step['title']}</h4>";
                    echo "<p>{$step['content']}</p>";

                    if (isset($step['list'])) {
                        echo "<ul style='margin-top: 0.5rem; padding-left: 1.5rem;'>";
                        foreach ($step['list'] as $listItem) {
                            echo "<li>{$listItem}</li>";
                        }
                        echo "</ul>";
                    }

                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </section>

        <!-- Tata Tertib Section -->
        <section class="card-custom" id="peraturan">
            <h2>
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l4 4v11a2 2 0 01-2 2z"/>
                </svg>
                Tata Tertib Rumah Kost/Kontrakan
            </h2>
            <div class="alert-custom">
                <h4>⚠️ Penting untuk Dibaca</h4>
                <p>Semua penghuni wajib mematuhi tata tertib berikut. Pelanggaran dapat berakibat pada teguran hingga penghentian kontrak sewa.</p>
            </div>
            <ol class="rules-list">
                <?php
                $rules = [
                    [
                        'title' => 'Kewajiban Identitas:',
                        'content' => 'Penghuni kost diwajibkan menyerahkan identitas (FC KTP/Buku nikah yang sudah berumah tangga).'
                    ],
                    [
                        'title' => 'Batasan Waktu Tamu:',
                        'content' => 'Penghuni kost dilarang menerima tamu lawan jenis diatas jam 21.00 WIB.'
                    ],
                    [
                        'title' => 'Aturan Tamu:',
                        'content' => 'Tamu yang tidak berkepentingan dilarang masuk ke kamar kost dan dilarang menginap tanpa seijin pemilik kost.'
                    ],
                    [
                        'title' => 'Sanksi Pelanggaran:',
                        'content' => 'Penghuni kost apabila kedapatan didalam kamar yang bukan muhrimnya siap menerima segala resikonya sanksi oleh pihak terkait.'
                    ],
                    [
                        'title' => 'Tanggung Jawab Barang:',
                        'content' => 'Penghuni kost wajib menjaga barang miliknya masing-masing seperti (sepeda motor) dan lain-lain, apabila ada kehilangan tanggung jawab dari penghuni kost.'
                    ],
                    [
                        'title' => 'Keamanan Bersama:',
                        'content' => 'Penghuni kost bila keluar masuk kost/kontrakan harap menutup pagar demi keamanan bersama.'
                    ],
                    [
                        'title' => 'Larangan Mutlak:',
                        'content' => '',
                        'sub_rules' => [
                            'Membawa, menjual, memakai, menyimpan dan mendistribusikan segala jenis narkoba dan obat-obatan terlarang',
                            'Berjudi, mabuk-mabukan dengan mengkonsumsi segala jenis minuman keras siap menerima segala resikonya'
                        ]
                    ],
                    [
                        'title' => 'Larangan Merusak Properti:',
                        'content' => '',
                        'sub_rules' => [
                            'Dilarang memaku dan mencoret lemari, dinding, pintu, jendela, dll',
                            'Dilarang menempel stiker pada lemari, dinding, pintu, jendela, dll',
                            'Dilarang menambah korden/selambu pada jendela, apabila terjadi kerusakan penghuni kost harus membayar ganti rugi'
                        ]
                    ],
                    [
                        'title' => 'Etika dan Kebersihan:',
                        'content' => 'Penghuni kost diharapkan menjaga kesopanan dalam berpakaian dan bicara, menjaga ketenangan dan kerukunan selama berada dalam lingkungan kost/kontrakan serta menjaga kebersihan lingkungan bersama.'
                    ],
                    [
                        'title' => 'Hemat Energi dan Pemberhentian Sewa:',
                        'content' => 'Penghuni kost wajib mematikan listrik, lampu dan alat elektronik bila tidak digunakan dan terakhir pemberhentian sewa dapat diajukan setelah tagihan lunas bulan tersebut dan pengajuan pemberhentian harus dilakukan sebelum tanggal 25 untuk perpanjangan sewa akan secara otomatis diperpanjang setiap bulannya.'
                    ]
                ];

                foreach ($rules as $rule) {
                    echo "<li>";
                    echo "<strong>{$rule['title']}</strong> {$rule['content']}";

                    if (isset($rule['sub_rules'])) {
                        echo "<ul class='sub-rules'>";
                        foreach ($rule['sub_rules'] as $subRule) {
                            echo "<li>{$subRule}</li>";
                        }
                        echo "</ul>";
                    }

                    echo "</li>";
                }
                ?>
            </ol>
        </section>

        <!-- Contact Section -->
        <section class="card-custom" id="kontak">
            <h2>
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Informasi Kontak
            </h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="contact-info">
                        <h5><?php echo $kos_name; ?></h5>
                        <p>Rumah kedua yang nyaman untuk mahasiswa dan pekerja</p>
                        <div class="social-links mt-3">
                            <a href="mailto:<?php echo $kos_email; ?>" title="Email">✉</a>
                            <a href="#" title="Facebook">f</a>
                            <a href="#" title="LinkedIn">in</a>
                            <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', $kos_phone); ?>" title="WhatsApp">📱</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info">
                        <h5>Hubungi Kami</h5>
                        <p><strong>Alamat:</strong><br><?php echo $kos_address; ?></p>
                        <p><strong>No. Telp:</strong><br><a href="tel:<?php echo $kos_phone; ?>"><?php echo $kos_phone; ?></a></p>
                        <p><strong>Email:</strong><br><a href="mailto:<?php echo $kos_email; ?>"><?php echo $kos_email; ?></a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info">
                        <h5>Lokasi <?php echo $kos_name; ?></h5>
                        <div id="map" class="map-loading"></div>
                        <div class="mt-2">
                            <small class="text-muted">Klik peta untuk membuka di Google Maps</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer-custom">
        <p>&copy; <?php echo date('Y'); ?> kelompok_6 E-Kost All rights reserved.</p>
        <p>Hunian nyaman untuk kehidupan yang lebih baik</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <script>
        // Inisialisasi peta dengan error handling
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Koordinat dari PHP
                const kosLat = <?php echo $kos_lat; ?>;
                const kosLng = <?php echo $kos_lng; ?>;
                const kosName = "<?php echo $kos_name; ?>";
                const kosAddress = "<?php echo addslashes($kos_address); ?>";

                // Tunggu sedikit untuk memastikan DOM siap
                setTimeout(function() {
                    // Hapus loading indicator
                    const mapElement = document.getElementById('map');
                    mapElement.classList.remove('map-loading');

                    // Inisialisasi peta
                    const map = L.map('map', {
                        center: [kosLat, kosLng],
                        zoom: 16,
                        zoomControl: true,
                        scrollWheelZoom: true
                    });

                    // Tambahkan tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    // Tambahkan marker
                    const marker = L.marker([kosLat, kosLng]).addTo(map);

                    // Popup content
                    const popupContent = `
                        <div style="text-align: center;">
                            <h6 style="margin-bottom: 5px;"><strong>${kosName}</strong></h6>
                            <p style="margin-bottom: 5px; font-size: 12px;">${kosAddress}</p>
                            <a href="https://www.google.com/maps?q=${kosLat},${kosLng}" target="_blank"
                               style="color: #667eea; text-decoration: none; font-size: 12px;">
                                📍 Buka di Google Maps
                            </a>
                        </div>
                    `;

                    marker.bindPopup(popupContent);

                    // Auto open popup
                    marker.openPopup();

                    // Click handler untuk membuka Google Maps
                    map.on('click', function() {
                        window.open(`https://www.google.com/maps?q=${kosLat},${kosLng}`, '_blank');
                    });

                    // Resize map setelah tab/modal dibuka (jika diperlukan)
                    setTimeout(function() {
                        map.invalidateSize();
                    }, 100);

                }, 500);

            } catch (error) {
                console.error('Error initializing map:', error);
                // Fallback jika peta gagal load
                const mapElement = document.getElementById('map');
                mapElement.innerHTML = `
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 10px;">
                        <div style="text-align: center; color: #6c757d;">
                            <p>📍 Peta tidak dapat dimuat</p>
                            <a href="https://www.google.com/maps?q=<?php echo $kos_lat; ?>,<?php echo $kos_lng; ?>"
                               target="_blank" class="btn btn-sm btn-primary">
                                Buka di Google Maps
                            </a>
                        </div>
                    </div>
                `;
            }
        });

        // Smooth scroll untuk navigasi internal (jika ada)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
