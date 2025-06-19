@extends('dashboard.penghuni')
@section('title', 'Kamar Tersedia')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Kamar Tersedia</h2>
        <div class="filter-controls d-flex gap-2">
            <select class="form-select form-select-sm" id="tipeFilter">
                <option value="">Semua Tipe</option>
                <option value="Kamar Standard">Kamar Standard</option>
                <option value="Kamar Keluarga">Kamar Keluarga</option>
            </select>
            <select class="form-select form-select-sm" id="sortPrice">
                <option value="asc">Harga: Rendah ke Tinggi</option>
                <option value="desc">Harga: Tinggi ke Rendah</option>
            </select>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-3" id="roomContainer">
        @forelse($kamar ?? [] as $room)
            <div class="col-lg-3 col-md-4 col-sm-6" data-tipe="{{ $room->tipe }}" data-harga="{{ $room->harga_bulanan }}">
                <div class="card">
                    <div class="card-img-wrapper">
                        @php
                            $images = explode(',', $room->gambar);
                            $mainImage = count($images) > 0 ? $images[0] : 'no-image.jpg';
                        @endphp
                        <img src="{{ asset('images/'.$mainImage) }}" class="card-img-top" alt="Kamar {{ $room->no_kamar }}">
                    </div>
                    <div class="card-body p-3">
                        <div class="card-content">
                            <h5 class="card-title mb-2"><strong>{{ $room->no_kamar }}</strong></h5>
                            <div class="badges-wrapper">
                                <span class="badge border border-dark bg-white text-dark">
                                    <i class="bi bi-people"></i> {{ $room->kapasitas }}
                                </span>
                                <span class="badge border border-dark bg-white text-dark">
                                    <i class="bi bi-rulers"></i> {{ $room->luas }}
                                </span>
                                <span class="badge border border-dark bg-white text-dark">
                                    <i class="bi bi-building"></i> Lt. {{ $room->lantai }}
                                </span>
                            </div>
                            <div class="price-section mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">Harga Sewa</small>
                                        <strong class="price">Rp {{ number_format($room?->harga_bulanan ?? 0, 0, ',', '.') }}</strong>
                                    </div>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewRoom{{ $room->id }}">
                                        Detail & Pesan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Detail Modal -->
            <div class="modal fade" id="viewRoom{{ $room->id }}" tabindex="-1">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-info-circle me-2"></i>Detail Kamar {{ $room->no_kamar }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="room-detail-view">
                                <div id="roomCarousel{{ $room->id }}" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach(explode(',', $room->gambar) as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <div class="modal-img-wrapper">
                                                    <img src="{{ asset('images/'.$image) }}"
                                                         class="d-block w-100"
                                                         style="height: 250px; object-fit: cover;">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if(count(explode(',', $room->gambar)) > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel{{ $room->id }}" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel{{ $room->id }}" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    @endif
                                </div>
                                <div class="room-detail-wrapper p-3">
                                    <h5><strong>{{ $room->no_kamar }}</strong></h5>
                                    <div class="badges-wrapper mb-3">
                                        <span class="badge border border-dark bg-white text-dark">
                                            <i class="bi bi-people"></i> {{ $room->kapasitas }} Orang
                                        </span>
                                        <span class="badge border border-dark bg-white text-dark">
                                            <i class="bi bi-rulers"></i> {{ $room->luas }}
                                        </span>
                                        <span class="badge border border-dark bg-white text-dark">
                                            <i class="bi bi-building"></i> Lantai {{ $room->lantai }}
                                        </span>
                                        <span class="badge border border-dark bg-white text-dark">
                                            <i class="bi bi-door-open"></i> {{ $room->tipe }}
                                        </span>
                                    </div>
                                    <div class="price-section mt-4 pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">Harga Sewa per Bulan</small>
                                                <h5 class="mb-0">Rp {{ number_format($room->harga_bulanan, 0, ',', '.') }}</h5>
                                            </div>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookRoom{{ $room->id }}">
                                                Pesan Kamar Ini
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Booking Modal -->
            <div class="modal fade" id="bookRoom{{ $room->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-house-add me-2"></i>Pesan Kamar : {{ $room->no_kamar }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('book-room') }}" method="POST" enctype="multipart/form-data" id="bookingForm{{ $room->id }}">
                            @csrf
                            <div class="modal-body">
                                <!-- Summary Section -->
                                <div class="alert alert-info mb-4">
                                    <h6 class="alert-heading">Ringkasan Pemesanan</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Nomor Kamar:</strong> {{ $room->no_kamar }}</p>
                                            <p class="mb-1"><strong>Tipe Kamar:</strong> {{ $room->tipe }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Harga Bulanan:</strong> Rp {{ number_format($room->harga_bulanan, 0, ',', '.') }}</p>
                                            {{-- total pembayaran --}}
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="id_user" value="{{ Auth::id() }}">
                                <input type="hidden" name="id_datakamar" value="{{ $room->id }}">
                                <input type="hidden" name="status_hunian" value="Menghuni">

                                <!-- Form fields -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="{{ Auth::user()->name }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="nik" class="form-label">NIK</label>
                                            <input type="text" class="form-control" id="nik" name="nik" required maxlength="16">
                                        </div>
                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="no_telepon" class="form-label">No. Telepon</label>
                                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                            <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="foto_ktp" class="form-label">Foto KTP</label>
                                            <input type="file" class="form-control" id="foto_ktp" name="foto_ktp" accept="image/*" required>
                                            <div class="preview mt-2"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div>
                                        <div class="alert alert-warning">
                                            <small>
                                                <i class="bi bi-info-circle"></i>
                                                Harap isi Formulir data diri anda dengan benar, Untuk lanjut ke proses pembayaran
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="w-100 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agree{{ $room->id }}" required>
                                        <label class="form-check-label" for="agree{{ $room->id }}">
                                            <small class="text-muted">
                                                Saya menyetujui <a href="{{ route('informasi-kost') }}" target="_blank">syarat dan ketentuan</a> yang berlaku
                                            </small>
                                        </label>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Pesan & Lanjut ke Pembayaran
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-house-slash fs-1 text-muted"></i>
                <h4 class="mt-3">Tidak ada kamar yang tersedia</h4>
                <p class="text-muted">Maaf, saat ini semua kamar sedang terisi.</p>
            </div>
        @endforelse
    </div>

    @if($kamar && $kamar->hasPages())
        <div class="d-flex flex-column align-items-center mt-4 pt-3 border-top">
            <div class="text-muted mb-3">
                Menampilkan {{ $kamar->count() }} kamar tersedia
            </div>
            <nav aria-label="Page navigation">
                {{ $kamar->onEachSide(1)->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function alternativeFilter() {
        const tipeFilter = document.getElementById('tipeFilter').value;
        const sortPrice = document.getElementById('sortPrice').value;
        const roomContainer = document.getElementById('roomContainer');
        const roomCards = Array.from(roomContainer.children);

        // Filter berdasarkan tipe
        roomCards.forEach(card => {
            const cardTipe = card.getAttribute('data-tipe');
            if (!tipeFilter || cardTipe === tipeFilter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Get visible cards untuk sorting
        const visibleCards = roomCards.filter(card => card.style.display !== 'none');

        // Sort berdasarkan harga
        visibleCards.sort((a, b) => {
            const priceA = parseInt(a.getAttribute('data-harga'));
            const priceB = parseInt(b.getAttribute('data-harga'));
            return sortPrice === 'asc' ? priceA - priceB : priceB - priceA;
        });

        // Clear container dan masukkan ulang dengan urutan baru
        roomContainer.innerHTML = '';

        // Masukkan visible cards yang sudah disort
        visibleCards.forEach(card => {
            roomContainer.appendChild(card);
        });

        // Masukkan hidden cards
        const hiddenCards = roomCards.filter(card => card.style.display === 'none');
        hiddenCards.forEach(card => {
            roomContainer.appendChild(card);
        });
    }

    $('#tipeFilter, #sortPrice').on('change', alternativeFilter);
});
</script>
@endpush

@push('styles')
<style>
    .room-card {
        transition: transform 0.2s;
        border-radius: 12px;
        overflow: hidden;
    }
    .room-card:hover {
        transform: translateY(-5px);
    }
    .card-img-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #f8f9fa;
    }
    .card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }
    .room-card:hover .card-img-wrapper img {
        transform: scale(1.05);
    }
    .room-features .badge {
        padding: 0.5rem 0.8rem;
        font-weight: normal;
    }
    .empty-state {
        padding: 3rem;
        background: #f8f9fa;
        border-radius: 1rem;
    }
    .filter-controls select {
        min-width: 200px;
    }
    .page-link {
        color: var(--primary-color);
        border-radius: 0.5rem;
        margin: 0 0.2rem;
    }
    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .page-item.disabled .page-link {
        color: #6c757d;
    }
    .feature-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
    }
    .feature-item i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .room-info {
        padding: 2rem;
    }
    #roomCarousel {
        border-bottom: 1px solid #eee;
    }
    .booking-section {
        padding-top: 1rem;
        border-top: 1px solid #eee;
        margin-top: 1rem;
    }
    .modal-img-wrapper {
        width: 100%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .modal-dialog.modal-md {
        max-width: 500px;
    }
</style>
@endpush

