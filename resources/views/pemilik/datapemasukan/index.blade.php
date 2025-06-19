@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <!-- Tools Section -->
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari pembayaran...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i>
                            <span>Export</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel</a></li>
                            <li><a class="dropdown-item" href="{{ route('pemasukan.pdf') }}">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="pemasukanTable">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th width="50">No</th>
                            <th>Nama Penghuni</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Denda</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datapemasukan as $index => $pemasukan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $pemasukan->tagihan->penghuni->nama_lengkap }}</div>
                                        <small class="text-muted">Kamar {{ $pemasukan->tagihan->penghuni->datakamar->no_kamar }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $pemasukan->tanggal_pembayaran->format('d/m/Y') }}</td>
                            <td>
                                <span class="text-dark">
                                    Rp {{ number_format($pemasukan->jumlah_pembayaran, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="text-danger">
                                    Rp {{ number_format($pemasukan->denda ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">
                                    Rp {{ number_format(($pemasukan->jumlah_pembayaran + ($pemasukan->denda ?? 0)), 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @if($pemasukan->status == 'pending')
                                    <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                @elseif($pemasukan->denda > 0)
                                    <span class="badge bg-danger">Belum Lunas</span>
                                @else
                                    <span class="badge bg-success">Lunas</span>
                                @endif
                            </td>
                            <td>
                                @if($pemasukan->bukti_pembayaran)
                                    <button class="btn btn-sm btn-light" onclick="viewBukti('{{ asset('images/payments/'.$pemasukan->bukti_pembayaran) }}')">
                                        <i class="bi bi-image text-primary"></i>
                                    </button>
                                @else
                                    <span class="badge bg-secondary">No File</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Footer -->
        <div class="card-footer bg-white border-top">
            <div class="d-flex align-items-center">
                <div class="text-muted small me-3">
                    Total Pembayaran: <span class="badge bg-dark">{{ $datapemasukan->count() }}</span>
                </div>
                <div class="text-muted small">
                    Total Pemasukan: <span class="text-success fw-bold">Rp {{ number_format($datapemasukan->sum('jumlah_pembayaran'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modal -->
@include('components.modals.image-modal')

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with new table ID
    const table = $('#pemasukanTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            search: "",
            searchPlaceholder: "Cari data pemasukan...",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data yang tersedia",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>'
            }
        }
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
});

function viewBukti(url) {
    $('#imageModal img').attr('src', url);
    $('#imageModal').modal('show');
}
</script>
@endpush
@endsection
