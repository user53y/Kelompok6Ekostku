<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Penghuni') - Kos Bu Tik</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn-uicons.flaticon.com/2.6.0/uicons-bold-rounded/css/uicons-bold-rounded.css" rel="stylesheet">
    <link href="{{ asset('template/css/components/penghuni1.css') }}" rel="stylesheet">
</head>
<body>
        <!-- Top Header/Navbar -->
    <header class="top-header d-flex align-items-center">
        <div class="header-left d-flex align-items-center">
            <button class="mobile-menu-toggle me-3 d-lg-none" id="toggleNav">
                <i class="bi bi-list"></i>
            </button>

            <!-- Brand Logo -->
            <a href="#" class="brand-logo d-flex align-items-center">
                <i class="bi bi-house me-2"></i>
                <span>E-Kostku</span>
            </a>
        </div>

        <!-- Center Section - Desktop Navigation -->
        <nav class="header-nav d-none d-lg-flex">
            <ul class="nav-list">
                <li>
                    <a href="{{ route('dashboard.penghuni') }}" class="nav-link {{ request()->routeIs('dashboard.penghuni') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ isset($penghuni) && $penghuni ? route('kamar-detail', $penghuni->id) : route('kamar-tersedia') }}"
                    class="nav-link {{ request()->routeIs(isset($penghuni) && $penghuni ? 'kamar-detail' : 'kamar-tersedia') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>
                        <span>{{ isset($penghuni) && $penghuni ? 'Kamar Saya' : 'Kamar' }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('cek-pembayaran') }}" class="nav-link {{ request()->routeIs('cek-pembayaran') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i>
                        <span>Pembayaran</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Right Section - Profile -->
        <div class="header-right d-flex align-items-center gap-3">
            <!-- Profil (tanpa dropdown) -->
            <div class="profile-menu bg-light rounded-3 d-flex align-items-center px-2 py-1">
                <img src="{{ asset(Auth::user()->foto ? 'images/photoprofile/' . Auth::user()->foto : 'images/Fotoprofile/profile.png') }}"
                    alt="Profile" class="profile-pic rounded-circle" width="32" height="32">
                <div class="profile-info d-none d-sm-block ms-2 text-start">
                    <p class="profile-name text-black mb-0 fw-semibold">{{ Auth::user()->username }}</p>
                    <small class="profile-role text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                </div>
            </div>
        </div>

        <!-- Logout form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </header>

    <!-- Mobile Navigation -->
    <nav class="mobile-nav d-lg-none" id="mobileNav">
        <div class="mobile-nav-content">
            <ul class="nav-list">
                <li>
                    <a href="{{ route('dashboard.penghuni') }}" class="mobile-nav-link {{ request()->routeIs('dashboard.penghuni') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ isset($penghuni) && $penghuni ? route('kamar-detail', $penghuni->id) : route('kamar-tersedia') }}"
                    class="mobile-nav-link {{ request()->routeIs(isset($penghuni) && $penghuni ? 'kamar-detail' : 'kamar-tersedia') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i>
                        <span>{{ isset($penghuni) && $penghuni ? 'Kamar Saya' : 'Kamar' }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('cek-pembayaran') }}" class="mobile-nav-link {{ request()->routeIs('cek-pembayaran') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i>
                        <span>Pembayaran</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="mobile-nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                        <i class="bi bi-file-text"></i>
                        <span>Riwayat</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        @if(request()->routeIs('kamar-detail'))
            @yield('content')
        @else
            <!-- Improved Breadcrumb -->
            <div class="breadcrumb-container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.penghuni') }}">
                            <i class="bi bi-house-door"></i>
                            Dashboard
                        </a>
                    </li>
                    @if(request()->routeIs('kamar-tersedia'))
                        <li class="breadcrumb-item active">Kamar Tersedia</li>
                    @elseif(request()->routeIs('cek-pembayaran'))
                        <li class="breadcrumb-item active">Pembayaran</li>
                    @elseif($penghuni)
                        <li class="breadcrumb-item active">Kamar {{ $penghuni->datakamar->no_kamar }}</li>
                    @endif
                </ol>
            </div>

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Dashboard Penghuni</h1>
                <div class="d-flex gap-2">
                <a href="{{ route('informasi-kost') }}" class="btn btn-outline-primary">
                <i class="bi bi-info-circle me-1"></i>
                Informasi Kost
                </a>
                <a href="https://wa.me/{{ App\Models\User::where('role', 'pemilik')->first()->no_telepon }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-whatsapp me-1"></i>
                Hubungi Admin
                </a>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="card welcome-card mb-4">
                <div class="welcome-content">
                    <h2>Selamat Datang di Rumah Kost Bu Tik, {{ auth()->user()->username }}!</h2>
                    <p class="mb-4">Nikmati fasilitas lengkap dan suasana yang nyaman di kost kami. </p>
                    <div class="d-flex gap-2">
        @if($penghuni)
            <a href="{{ route('kamar-detail', $penghuni->id) }}" class="btn welcome-btn">
                <i class="bi bi-house-door me-1"></i>
                Lihat Kamar
            </a>
            <a href="{{ route('cek-pembayaran') }}" class="btn btn-outline-light">
                <i class="bi bi-cash me-1"></i>
                Cek Tagihan
            </a>
        @else
            <a href="{{ route('kamar-tersedia') }}" class="btn welcome-btn">
                <i class="bi bi-house-door me-1"></i>
                Pesan Kamar
            </a>
        @endif
    </div>
                </div>
            </div>

            <!-- Stats Container with conditional content -->
            @if(request()->routeIs('kamar-tersedia') || request()->routeIs('cek-pembayaran'))
                @yield('content')
            @elseif(isset($penghuni) && $penghuni)
                <div class="stats-container row g-4">
                    <!-- Kamar Info -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100 p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-primary-subtle">
                                    <i class="bi bi-house-door"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stat-label text-muted">Kamar Saya</div>
                                    <h3 class="stat-value mb-0">No. {{ $penghuni->datakamar->no_kamar }}</h3>
                                </div>
                            </div>
                            <div class="text-muted">
                                @php
                                    $latestTagihan = $penghuni->tagihan->first();
                                @endphp
                                <i class="bi bi-circle-fill me-1 {{ $latestTagihan && $latestTagihan->status_tagihan == 'Lunas' ? 'text-success' : 'text-warning' }}"></i>
                                {{ $latestTagihan ? $latestTagihan->status_tagihan : 'Belum Bayar' }}
                            </div>
                        </div>
                    </div>

                    <!-- Periode Sewa -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100 p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-info-subtle">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stat-label text-muted">Periode Sewa</div>
                                    <h3 class="stat-value mb-0">{{ \Carbon\Carbon::parse($penghuni->periode_mulai)->format('d M Y') }}</h3>
                                </div>
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-arrow-right me-1"></i>
                                Sampai: {{ \Carbon\Carbon::parse($penghuni->tanggal_keluar)->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Tagihan Info -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100 p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-warning-subtle">
                                    <i class="bi bi-cash"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stat-label text-muted">Tagihan Bulanan</div>
                                    <h3 class="stat-value mb-0">Rp {{ number_format($penghuni->datakamar->harga_bulanan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="text-danger">
                                <i class="bi bi-clock-history me-1"></i>
                                Jatuh tempo: {{ \Carbon\Carbon::now()->addDays(5)->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Status Pembayaran -->
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card h-100 p-4">
                            @php
                                $latestTagihan = $penghuni->tagihan->first();
                                $totalDenda = $penghuni->tagihan->sum('denda');
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon {{ $latestTagihan && $latestTagihan->status_tagihan == 'Lunas' ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stat-label text-muted">Status Pembayaran</div>
                                    <h3 class="stat-value mb-0">
                                        @if($latestTagihan)
                                            {{ $latestTagihan->status_tagihan }}
                                        @else
                                            Belum Ada Tagihan
                                        @endif
                                    </h3>
                                </div>
                            </div>

                            @if($latestTagihan)
                                @if($latestTagihan->status_tagihan == 'Belum Lunas')
                                    <div class="payment-details mb-3">
                                        <div class="d-flex justify-content-between text-muted mb-2">
                                            <span>Total Tagihan:</span>
                                            <span>Rp {{ number_format($latestTagihan->jumlah_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                        @if($totalDenda > 0)
                                        <div class="d-flex justify-content-between text-danger mb-2">
                                            <span>Denda:</span>
                                            <span>Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('cek-pembayaran') }}" class="btn btn-warning w-100">
                                        <i class="bi bi-credit-card me-1"></i>
                                        Bayar Sekarang
                                    </a>
                                @elseif($latestTagihan->status_tagihan == 'Menunggu Konfirmasi')
                                    <div class="alert alert-info mb-0 d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <span>Pembayaran sedang diproses</span>
                                    </div>
                                @else
                                    <div class="alert alert-success mb-0 d-flex align-items-center">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <span>Pembayaran Lunas</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Room Details -->
                <div class="row mt-4">
                    <!-- Main Room Details Card -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 fw-bold">
                                        Detail Kamar -> {{ $penghuni->datakamar->no_kamar }}
                                    </h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Room Image Gallery -->
                                    <div class="col-md-5">
                                        @php
                                            $images = explode(',', $penghuni->datakamar->gambar);
                                            $mainImage = count($images) > 0 ? $images[0] : 'no-image.jpg';
                                        @endphp
                                        <div class="position-relative">
                                            <img src="{{ asset('images/'.$mainImage) }}" alt="Kamar"
                                                 class="img-fluid rounded-4 shadow-sm"
                                                 style="width: 100%; height: 300px; object-fit: cover;">
                                            @if(count($images) > 1)
                                                <div class="mt-3 d-flex gap-2">
                                                    @foreach(array_slice($images, 1, 3) as $img)
                                                        <img src="{{ asset('images/'.$img) }}"
                                                             alt="Room thumbnail"
                                                             class="rounded-3 shadow-sm"
                                                             style="width: 80px; height: 60px; object-fit: cover;">
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Room Information -->
                                    <div class="col-md-7">
                                        <div class="room-details p-3 bg-light rounded-4">
                                            <div class="detail-item d-flex justify-content-between py-3 border-bottom">
                                                <span class="text-muted">Tipe Kamar : </span>
                                                <span class="fw-semibold">{{ $penghuni->datakamar->tipe }}</span>
                                            </div>
                                            <div class="detail-item d-flex justify-content-between py-3 border-bottom">
                                                <span class="text-muted">Luas : </span>
                                                <span class="fw-semibold">{{ $penghuni->datakamar->luas }}</span>
                                            </div>
                                            <div class="detail-item d-flex justify-content-between py-3 border-bottom">
                                                <span class="text-muted">Lantai : </span>
                                                <span class="fw-semibold">{{ $penghuni->datakamar->lantai }}</span>
                                            </div>
                                            <div class="detail-item d-flex justify-content-between py-3 border-bottom">
                                                <span class="text-muted">Kapasitas : </span>
                                                <span class="fw-semibold">{{ $penghuni->datakamar->kapasitas }} orang</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Move Out Section -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4">Pengajuan Pemberhentian Sewa</h5>

                                @if($penghuni->status_hunian == 'Menunggu Persetujuan Berhenti')
                                    <div class="alert alert-warning mb-4">
                                        <i class="bi bi-hourglass-split me-2"></i>
                                        <span>Pengajuan pemberhentian sewa sedang diproses</span>
                                    </div>
                                @else
                                    <div class="alert alert-info mb-4">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <small>Pengajuan pemberhentian sewa harus dilakukan sebelum tanggal 25 setiap bulannya</small>
                                    </div>

                                    @if($penghuni->tagihan && $penghuni->tagihan->where('status_tagihan', 'Lunas')->count() > 0)
                                        <button type="button"
                                                class="btn btn-danger w-100 py-3 rounded-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#berhentiModal"
                                                {{ now()->day >= 25 ? 'disabled' : '' }}>
                                            <i class="bi bi-box-arrow-right me-2"></i>
                                            Ajukan Pemberhentian Sewa
                                        </button>
                                        @if(now()->day >= 25)
                                            <small class="text-danger d-block mt-2">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                Pengajuan tidak dapat dilakukan setelah tanggal 25
                                            </small>
                                        @endif
                                    @else
                                        <button type="button"
                                                class="btn btn-danger w-100 py-3 rounded-3"
                                                disabled>
                                            <i class="bi bi-box-arrow-right me-2"></i>
                                            Lunasi Tagihan Terlebih Dahulu
                                        </button>
                                        <small class="text-danger d-block mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            Pemberhentian sewa hanya dapat diajukan setelah tagihan lunas
                                        </small>
                                    @endif
                                @endif

                                <div class="mt-4">
                                    <small class="text-muted">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        Dengan mengajukan pemberhentian sewa, tagihan bulan berikutnya akan dihentikan setelah semua kewajiban pembayaran dipenuhi.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="stats-container">
                    <div class="stat-card text-center">
                        <div class="stat-icon">
                            <i class="bi bi-house-add"></i>
                        </div>
                        <div class="stat-title">Belum Memiliki Kamar</div>
                        <p class="text-muted mb-3">Anda belum memesan kamar</p>
                        <a href="{{ route('kamar-tersedia') }}" class="btn btn-primary">Pesan Kamar</a>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Modal Perpanjang Sewa -->
    <div class="modal fade" id="perpanjangModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perpanjang Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('perpanjang-sewa') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="penghuni_id" value="{{ isset($penghuni) && $penghuni ? $penghuni->id : '' }}">
                        <div class="mb-3">
                            <label class="form-label">Durasi Perpanjangan (Bulan)</label>
                            <input type="number" class="form-control" name="durasi" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Perpanjang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pemberhentian Sewa -->
    <div class="modal fade" id="berhentiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pemberhentian Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('pemberhentian-sewa') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Penting!</strong>
                            <ul class="mb-0 mt-2">
                                <li>Pengajuan akan diproses setelah disetujui oleh pemilik kost</li>
                                <li>Pastikan semua tagihan telah dilunasi</li>
                                <li>Pengajuan hanya dapat dilakukan sebelum tanggal 25</li>
                            </ul>
                        </div>
                        <input type="hidden" name="penghuni_id" value="{{ isset($penghuni) && $penghuni ? $penghuni->id : '' }}">
                        <p>Apakah Anda yakin ingin mengajukan pemberhentian sewa?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Ajukan Pemberhentian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')

    @endpush

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/js/notification.js') }}"></script>
    <script src="{{ asset('template/js/penghuni.js') }}"></script>
    @push('scripts')
    <script>
        // Tampilkan SweetAlert untuk notifikasi
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif

        // Konfirmasi sebelum submit form
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.getAttribute('data-confirm') !== 'false') {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Tindakan ini tidak dapat dibatalkan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</body>
</html>
