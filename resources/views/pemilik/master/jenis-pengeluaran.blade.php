@extends('layouts.dashboard')
@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <!-- Header: Search kiri, Tambah kanan -->
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search" class="form-control border-0 bg-light"
                               id="searchInput"
                               placeholder="Cari jenis pengeluaran...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJenisModal">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Jenis</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="jenisTable">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th width="40">No</th>
                            <th>Kategori</th>
                            <th>Nama Pengeluaran</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jenisPengeluaran as $index => $jenis)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <span class="text-center">
                                    {{ $jenis->kategori_pengeluaran }}
                                </span>
                            </td>
                            <td>{{ $jenis->nama_pengeluaran }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light btn-sm delete-btn"
                                            onclick="deleteJenis({{ $jenis->id }})"
                                            title="Hapus">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Footer with Stats -->
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small">
                            <strong>Total Jenis:</strong>
                            <span class="badge bg-secondary">{{ $jenisPengeluaran->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addJenisModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Jenis Pengeluaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="jenisForm" action="{{ route('jenis-pengeluaran.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" name="kategori_pengeluaran" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Pengeluaran</label>
                            <input type="text" class="form-control" name="nama_pengeluaran" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    const table = $('#jenisTable').DataTable({
        pageLength: 10,
        responsive: true,
        dom: "<'row mb-3'<'col-md-6'l><'col-md-6'>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            search: "",
            searchPlaceholder: "Cari jenis pengeluaran...",
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
        },
        columnDefs: [
            { orderable: false, targets: [-1] },
            { searchable: false, targets: [0, -1] }
        ],
        order: [[1, 'asc']]
    });

    // Kolom search manual ke kolom kategori
    $('#searchInput').on('keyup', function() {
        table.column(1).search(this.value).draw();
    });

    // Handle form submission with SweetAlert2
    $('#jenisForm').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil disimpan',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal menambah jenis pengeluaran'
                });
            }
        });
    });
});

// Delete function with SweetAlert2
window.deleteJenis = function(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Yakin ingin menghapus jenis pengeluaran ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/jenis-pengeluaran/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil dihapus',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal menghapus data'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Gagal menghapus data'
                    });
                }
            });
        }
    });
}
</script>
@endpush

@push('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@endsection
