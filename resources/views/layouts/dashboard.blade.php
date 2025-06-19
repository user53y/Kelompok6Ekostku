<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Kos Bu Tik</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn-uicons.flaticon.com/2.6.0/uicons-bold-rounded/css/uicons-bold-rounded.css" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- Custom Styles -->
    <link href="{{ asset('template/css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/profile.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/components/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/components/forms.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/components/tables.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/components/modals.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/components/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/components/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('template/css/components/booking.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .notification-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            margin-top: 10px;
        }
        .notification-dropdown.show {
            display: block;
            animation: fadeIn 0.2s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .notification-actions {
            display: flex !important;
            gap: 8px;
            margin-top: 8px;
        }
        .notification-actions .btn {
            min-width: 80px;
            font-size: 0.95rem;
        }
    </style>
    
    <script>
    // Vanilla JavaScript version - works without jQuery
    (function() {
        // Override XMLHttpRequest
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
            if (typeof url === 'string' && url.indexOf('http://') === 0) {
                url = url.replace('http://', 'https://');
            }
            return originalOpen.call(this, method, url, async, user, password);
        };
        
        // Override fetch if available
        if (window.fetch) {
            const originalFetch = window.fetch;
            window.fetch = function(url, options) {
                if (typeof url === 'string' && url.indexOf('http://') === 0) {
                    url = url.replace('http://', 'https://');
                }
                return originalFetch.call(this, url, options);
            };
        }
        
        // jQuery ajaxPrefilter when jQuery is loaded
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery) {
                jQuery.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                    if (options.url && options.url.indexOf('http://') === 0) {
                        options.url = options.url.replace('http://', 'https://');
                    }
                });
            }
        });
    })();
    </script>
</head>

<body>
    @php
        $user = auth()->user();
    @endphp
    <div class="wrapper">
        <!-- Sidebar -->
        <div id="sidebar" class="left-sidebar">
            <div class="brand-logo">
                <h4>E-KOSTKU</h4>
                <span>SISTEM MANAJEMEN KOS</span>
                <div class="brand-logo-mini">
                    <i class="bi bi-house"></i>
                </div>
            </div>
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <a href="{{ route('dashboard.pemilik') }}" class="sidebar-link">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Data Master Group -->
                    <div class="sidebar-group">
                        <div class="sidebar-link has-dropdown" data-bs-toggle="collapse" data-bs-target="#masterData">
                            <i class="bi bi-database-fill"></i>
                            <span>Data Master</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </div>
                        <div class="collapse" id="masterData">
                            <a href="{{ route('tampil-kamar') }}" class="sidebar-link sub-link">
                                <i class="bi bi-house-door-fill"></i>
                                <span>Data Kamar</span>
                            </a>
                            <a href="{{ route('tampil-penghuni') }}" class="sidebar-link sub-link">
                                <i class="bi bi-people-fill"></i>
                                <span>Data Penghuni</span>
                            </a>
                            <a href="{{ route('jenis-pengeluaran.index') }}" class="sidebar-link sub-link">
                                <i class="bi bi-tag-fill"></i>
                                <span>Jenis Pengeluaran</span>
                            </a>
                        </div>
                    </div>

                    <!-- Keuangan Group -->
                    <div class="sidebar-group">
                        <div class="sidebar-link has-dropdown" data-bs-toggle="collapse" data-bs-target="#keuangan">
                            <i class="bi bi-cash-coin"></i>
                            <span>Keuangan</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </div>
                        <div class="collapse" id="keuangan">
                            <a href="{{ route('tampil-tagihan') }}" class="sidebar-link sub-link">
                                <i class="bi bi-receipt"></i>
                                <span>Tagihan</span>
                            </a>
                            <a href="{{ route('tampil-pemasukan') }}" class="sidebar-link sub-link">
                                <i class="bi bi-arrow-down-circle-fill"></i>
                                <span>Pemasukan</span>
                            </a>
                            <a href="{{ route('tampil-pengeluaran') }}" class="sidebar-link sub-link">
                                <i class="bi bi-arrow-up-circle-fill"></i>
                                <span>Pengeluaran</span>
                            </a>
                        </div>
                    </div>

                    <!-- Single Items -->
                    <a href="{{ route('tampil-laporan') }}" class="sidebar-link">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Laporan</span>
                    </a>

                    <a href="{{ route('logout') }}" class="sidebar-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fi fi-br-exit"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </nav>
            </div>
        </div>

        <!-- Top Navbar -->
        <div id="topNavbar" class="top-navbar">
            <div class="d-flex align-items-center">
                <button id="toggleButton" class="toggle-btn">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h5 id="pageTitle" class="page-title mb-0">Dashboard</h5>
                    <small id="current-date" class="text-secondary">{{ date('l, d F Y') }}</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="notification-icon">
                    <i class="bi bi-bell"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="notification-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                    <div class="notification-dropdown">
                        <div class="notification-header">
                            <h6 class="mb-0 fw-semibold">Notifikasi</h6>
                        </div>
                        <div class="notification-body">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="notification-item" data-payment-id="{{ $notification->data['payment_id'] ?? '' }}">
                                    <div class="notification-content">
                                        <p class="mb-1 fs-14">
                                            {{ $notification->data['message'] ?? 'Ada notifikasi baru.' }}
                                        </p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>

                                        @if(isset($notification->data['bukti_pembayaran']))
                                            <div class="mt-2">
                                                <img src="{{ asset('images/payments/' . $notification->data['bukti_pembayaran']) }}"
                                                     alt="Bukti Pembayaran"
                                                     class="img-fluid rounded"
                                                     style="max-height:100px;">
                                            </div>
                                        @endif
                                    </div>

                                    @if(auth()->user()->role === 'pemilik' && $notification->data['type'] === 'payment_pending')
                                        <div class="notification-actions mt-2">
                                            <button type="button"
                                                    class="btn btn-success btn-sm"
                                                    onclick="confirmPayment('{{ $notification->data['payment_id'] }}')">
                                                <i class="bi bi-check-lg"></i> Terima
                                            </button>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="rejectPayment('{{ $notification->data['payment_id'] }}')">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="notification-empty">
                                    <i class="bi bi-bell-slash mb-2"></i>
                                    <p class="mb-0">Tidak ada notifikasi</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="nav-divider"></div>
                <div class="dropdown profile-dropdown">
                    <a href="#" class="profile-menu" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ $user->foto ? asset('images/photoprofile/' . $user->foto) : asset('images/Fotoprofile/profile.png') }}"
                             alt="Profile" class="profile-pic">
                        <div class="profile-info d-none d-sm-block">
                            <p class="profile-name mb-0">{{ auth()->user()->username }}</p>
                            <small class="profile-role">{{ ucfirst($user->role) }}</small>
                        </div>
                        <i class="bi bi-chevron-down ms-2 dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu profile-dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-fill"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="main-content">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="template/js/sidebar.js"></script>
    <script src="{{ asset('template/js/notification.js') }}"></script>
    @stack('scripts')
</body>
</html>
