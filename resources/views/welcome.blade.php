<!DOCTYPE html>
<html lang="en" class="scroll-padding">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kos Bu Tik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Inter:700,600,400" rel="stylesheet">
    <link href=template/css/landingpage.css rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
          <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="https://c.animaapp.com/dFX5qZAh/img/svg@2x.png" alt="E-Kostku Logo" width="24" height="24" class="me-2">
            E-Kostku
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
          </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#hero">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#rooms">Kamar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Kontak</a>
                </li>
            </ul>
            <div class="ms-auto">
                <a href="{{ route('auth') }}" class="btn btn-primary">Masuk</a>
            </div>
        </div>
        </div>
      </nav>
    </header>

    <main>
      <!-- Hero Section -->
      <section id="hero" class="hero d-flex align-items-center">
        <div class="container position-relative">
          <div class="row">
            <div class="col-md-6">
              <h1 class="display-4 fw-bold">Selamat Datang di Website Kost Bu Tik</h1>
              <p class="lead">Kos eksklusif dengan fasilitas lengkap dan lokasi strategis</p>
              <a href="{{ route('auth') }}?tab=register" class="btn btn-primary px-5">Daftar Sekarang</a>
            </div>
          </div>
        </div>
      </section>

      <!-- Rooms Section -->
      <section id="rooms" class="py-5">
        <div class="container">
          <h2 class="text-white mb-4 section-header fw-bold">Pilihan Kamar Kos Terbaik Kami</h2>
          <p class="text-white mb-4">Berbagai tipe kamar untuk memenuhi kebutuhan Anda</p>

          <div class="row g-4">
            <div class="col-md-4">
              <div class="card room-card">
                <img src="https://c.animaapp.com/dFX5qZAh/img/background@2x.png" class="card-img-top" alt="Kamar Tipe A" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <h5 class="card-title">Kamar Tipe A</h5>
                  <p class="card-text">Kamar kos ekonomis dengan kamar mandi luar</p>
                  <div class="price">Rp500.000/bulan</div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card room-card">
                <img src="https://c.animaapp.com/dFX5qZAh/img/kamar-kos--5-@2x.png" class="card-img-top" alt="Kamar Tipe B" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <h5 class="card-title">Kamar Tipe B</h5>
                  <p class="card-text">Kamar kos standard dengan kamar mandi dalam</p>
                  <div class="price">Rp600.000/bulan</div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card room-card">
                <img src="https://c.animaapp.com/dFX5qZAh/img/kamar-kos-7@2x.png" class="card-img-top" alt="Kamar Tipe C" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <h5 class="card-title">Kamar Tipe C</h5>
                  <p class="card-text">Kamar kos yang luas dengan kamar mandi dalam</p>
                  <div class="price">Rp800.000/bulan</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section id="features" class="py-5 bg-white">
        <div class="container">
          <h2 class="text-center mb-4 text-bold">Mengapa Memilih Kos Bu Tik?</h2>
          <p class="text-center mb-5">Keunggulan yang membuat kami berbeda</p>

          <div class="row g-4">
            <div class="col-md-3">
              <div class="text-center">
                <div class="feature-icon">🔍</div>
                <h5>Mudah Ditemukan</h5>
                <p>Lokasi strategis dekat dengan kampus PSDKU Polinema</p>
              </div>
            </div>

            <div class="col-md-3">
              <div class="text-center">
                <div class="feature-icon">💰</div>
                <h5>Harga Terjangkau</h5>
                <p>Harga sewa yang sesuai dengan fasilitas yang didapatkan</p>
              </div>
            </div>

            <div class="col-md-3">
              <div class="text-center">
                <div class="feature-icon">🌟</div>
                <h5>Kualitas Terjamin</h5>
                <p>Kamar dan fasilitas selalu dijaga kebersihannya</p>
              </div>
            </div>

            <div class="col-md-3">
              <div class="text-center">
                <div class="feature-icon">😊</div>
                <h5>Penghuni Puas</h5>
                <p>Banyak penghuni yang setia tinggal bertahun-tahun</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Services Section -->
      <section id="services" class="py-5">
        <div class="container">
          <h2 class="text-white mb-4 text-bold">Layanan Kami</h2>
          <p class="text-white mb-4">Berbagai fasilitas untuk kenyamanan Anda</p>

          <div class="row g-4">
            <div class="col-md-4">
              <div class="card h-100">
                <img src="https://c.animaapp.com/dFX5qZAh/img/image-1.png" class="card-img-top" alt="Cleaning Service" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <h5 class="card-title">Cleaning Service</h5>
                  <p class="card-text">Layanan pembersihan area umum setiap hari</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card h-100">
                <img src="https://c.animaapp.com/dFX5qZAh/img/image-3.png" class="card-img-top" alt="Keamanan 24 Jam" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <h5 class="card-title">Keamanan 24 Jam</h5>
                  <p class="card-text">Sistem keamanan dan CCTV 24 jam</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card h-100">
                <img src="https://c.animaapp.com/dFX5qZAh/img/image-5.png" class="card-img-top" alt="Maintenance Cepat" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                  <h5 class="card-title">Maintenance Cepat</h5>
                  <p class="card-text">Layanan perbaikan cepat untuk masalah di kamar</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="py-5">
        <div class="container">
          <div class="cta-section text-center text-white p-5">
            <h2>Mari Mulai Tinggal di Kos Bu Tik</h2>
            <p class="mb-4">Hubungi kami sekarang untuk informasi ketersediaan kamar</p>
            <a href="" class="btn btn-light">Hubungi Kami</a>
          </div>
        </div>
      </section>

      <!-- Contact Section -->
      <section id="contact" class="py-5">
        <div class="container">
          <div class="row g-4">
            <div class="col-md-4">
              <h5 class="text-success mb-3">Kos Bu Tik</h5>
              <p class="text-white">Rumah kedua yang nyaman untuk mahasiswa dan pekerja</p>
              <div class="mt-3">
                <a href="#" class="social-icon">
                  <img src="https://c.animaapp.com/dFX5qZAh/img/envelope-1@2x.png" alt="Email" width="16" height="16">
                </a>
                <a href="#" class="social-icon">
                  <img src="https://c.animaapp.com/dFX5qZAh/img/facebook-1@2x.png" alt="Facebook" width="16" height="16">
                </a>
                <a href="#" class="social-icon">
                  <img src="https://c.animaapp.com/dFX5qZAh/img/link@2x.png" alt="LinkedIn" width="16" height="16">
                </a>
                <a href="#" class="social-icon">
                  <img src="https://c.animaapp.com/dFX5qZAh/img/whatsapp-1@2x.png" alt="WhatsApp" width="16" height="16">
                </a>
              </div>
            </div>

            <div class="col-md-4">
              <h5 class="text-success mb-3">Hubungi Kami</h5>
              <p class = "text-white">Alamat: Jl. Margo Tani, Sukorame, Kec. Mojoroto, Kota Kediri, Jawa Timur 64114</p>
              <p class="text-white">No. Telp: <a href="tel:+6285815320313" class="text-white">+62 858-1532-0313</a></p>
              <p class ="text-white">Email: kosbutik12@gmail.com</p>
            </div>

            <div class="col-md-4">
              <h5 class="text-success mb-3">Lokasi kost Bu Tik</h5>
              <div id="map" class="rounded" style="height: 200px;"></div>
            </div>
          </div>

          <div class="text-center mt-5 pt-3 border-top border-secondary">
            <p class="copyright">&copy; 2025 kelompok_6 E-Kost All rights reserved.</p>
          </div>
        </div>
      </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
    <script>
      // Add active class to navigation items based on scroll position
      document.addEventListener('DOMContentLoaded', function() {
          const sections = document.querySelectorAll('section');
          const navLinks = document.querySelectorAll('.nav-link');

          window.addEventListener('scroll', function() {
              let current = '';
              sections.forEach(section => {
                  const sectionTop = section.offsetTop;
                  const sectionHeight = section.clientHeight;
                  if (scrollY >= (sectionTop - 150)) {
                      current = section.getAttribute('id');
                  }
              });

              navLinks.forEach(link => {
                  link.classList.remove('active');
                  if (link.getAttribute('href').slice(1) === current) {
                      link.classList.add('active');
                  }
              });
          });

          // Map implementation
          const map = L.map('map').setView([-7.8041774, 111.9799067], 16);

          L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
              maxZoom: 18,
              attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          }).addTo(map);

          L.marker([-7.8058928, 111.98148]).addTo(map)
              .bindPopup('<b>Kos Bu Tik</b><br>Kos Nyaman').openPopup();
      });
    </script>
  </body>
</html>


