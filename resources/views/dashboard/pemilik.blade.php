@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome & Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Hai, {{ auth()->user()->username }}!</h2>
                    <p>Selamat datang di dashboard sistem manajemen kos. Kelola kos Anda dengan mudah dan efisien.</p>
                    <div class="quick-links mb-4">
                        <a href="{{ route('datapenghuni.index') }}" class="quick-link-card">
                            <div class="quick-link-icon">
                                <i class="bi bi-plus-circle-fill"></i>
                            </div>
                            <div class="quick-link-content">
                                <h4>Tambah Penghuni</h4>
                                <p>Daftarkan penghuni baru</p>
                            </div>
                        </a>
                        <a href="{{ route('datapemasukan.index') }}" class="quick-link-card">
                            <div class="quick-link-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="quick-link-content">
                                <h4>Catat Pembayaran</h4>
                                <p>Kelola transaksi keuangan</p>
                            </div>
                        </a>
                        <a href="{{ route('tampil-laporan') }}" class="quick-link-card">
                            <div class="quick-link-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="quick-link-content">
                                <h4>Laporan</h4>
                                <p>Lihat laporan keuangan</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row g-4">
                @php
                $stats = [
                    [
                        'icon' => 'bi bi-house-door',
                        'label' => 'Kamar Tersedia',
                        'value' => $kamarTersedia,
                        'route' => 'tampil-kamar',
                        'color' => 'primary'
                    ],
                    [
                        'icon' => 'bi bi-exclamation-circle',
                        'label' => 'Belum Lunas',
                        'value' => $belumLunas,
                        'route' => 'tampil-tagihan',
                        'color' => 'danger'
                    ],
                    [
                        'icon' => 'bi bi-people-fill',
                        'label' => 'Total Penghuni',
                        'value' => $penghuni ?? 0,
                        'route' => 'tampil-penghuni',
                        'color' => 'info'
                    ],
                    [
                        'icon' => 'bi bi-cash-coin',
                        'label' => 'Total Pemasukan',
                        'value' => 'Rp ' . number_format($totalPemasukan, 0, ',', '.'),
                        'route' => 'tampil-pemasukan',
                        'color' => 'success'
                    ],
                ];
                @endphp
                @foreach($stats as $stat)
                <div class="col-12 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value">{{ $stat['value'] }}</div>
                            <div class="stat-title">{{ $stat['label'] }}</div>
                        </div>
                        <a href="{{ route($stat['route']) }}" class="stat-link">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Links Section -->


    <!-- Activities Row -->
    <div class="row g-4">
        <!-- Recent Activities -->
        <div class="col-12 col-lg-7">
            <div class="activity-card h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-4">
                        <i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru
                    </h5>
                    <div class="activity-list">
                        @forelse($recentPenghuni as $activity)
                        <div class="activity-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="activity-icon text-success">
                                    <i class="bi bi-person-plus-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-semibold">{{ $activity['name'] }} bergabung di kamar {{ $activity['kamar'] }}</p>
                                    <small class="text-muted">
                                        {{ $activity['date'] ? $activity['date']->diffForHumans() : '-' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-4">Belum ada aktivitas terbaru.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-12 col-lg-5">
            <div class="activity-card h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-4">
                        <i class="bi bi-cash-stack me-2"></i>Pembayaran Terbaru
                    </h5>
                    <div class="activity-list">
                        @forelse($upcomingDueDates as $due)
                        <div class="activity-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="activity-icon text-primary">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-semibold">{{ optional($due->penghuni)->nama_lengkap ?? 'N/A' }} - Kamar {{ optional($due->penghuni->datakamar)->no_kamar ?? 'N/A' }}</p>
                                    <small class="text-muted">Jatuh tempo {{ $due->jatuh_tempo->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-4">Belum ada pembayaran terbaru.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.quick-link-card {
    text-decoration: none;
    color: inherit;
    display: flex;
    padding: 1rem;
    background: white;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.quick-link-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.quick-link-icon {
    margin-right: 1rem;
    font-size: 1.5rem;
    color: #0d6efd;
}

.quick-link-content h4 {
    margin: 0;
    font-size: 1rem;
}

.quick-link-content p {
    margin: 0;
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush
