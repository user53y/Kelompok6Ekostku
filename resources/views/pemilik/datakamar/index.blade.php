@extends('layouts.dashboard')

@push('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari kamar...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-funnel"></i>
                            <span>Filter</span>
                        </button>
                        <div class="dropdown-menu filter-dropdown p-3">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select form-select-sm" id="statusFilter">
                                    <option value="all">Semua Status</option>
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Disewa">Disewa</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipe Kamar</label>
                                <select class="form-select form-select-sm" id="tipeFilter">
                                    <option value="all">Semua Tipe</option>
                                    <option value="Kamar Standard">Kamar Standard</option>
                                    <option value="Kamar Keluarga">Kamar Keluarga</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm w-50" id="applyFilter">Terapkan</button>
                                <button class="btn btn-outline-secondary btn-sm w-50" id="resetFilter">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i>
                            <span>Export</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('kamar.excel') }}"><i class="bi bi-file-earmark-excel"></i> Export Excel</a></li>
                            <li><a class="dropdown-item" href="{{ route('kamar.pdf') }}"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Kamar</span>
                    </button>
                </div>
            </div>
            <!-- Add Bulk Action Bar here -->
            <div id="bulkActionBar" class="row mt-2 d-none">
                <div class="col-12">
                    <div class="bg-light p-2 rounded d-flex align-items-center">
                        <span class="me-3">
                            <i class="bi bi-check-square"></i>
                            <span id="selectedCount">0</span> item selected
                        </span>
                        <button class="btn btn-sm btn-danger me-2" id="bulkDeleteBtn">
                            <i class="bi bi-trash"></i> Delete Selected
                        </button>
                        <button class="btn btn-sm btn-light" id="cancelSelection">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th width="10" class="p-2"> <!-- Added p-2 for smaller padding -->
                                <input type="checkbox" id="selectAll"> <!-- Removed form-check div -->
                            </th>

                            <th width="80">No. Kamar</th>
                            <th width="80">Tipe</th>
                            <th width="100">Luas</th>
                            <th width="80">Lantai</th>
                            <th width="100">Kapasitas</th>
                            <th width="300">Harga</th>
                            <th width="100">Status</th>
                            <th width="50">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datakamar as $key => $room)
                        <tr>
                            <td class="text-center p-2"> <!-- Added p-2 for smaller padding -->
                                <input type="checkbox" class="room-checkbox" value="{{ $room->id }}"> <!-- Removed form-check div -->
                            </td>
                            <td>
                                <strong class="text-primary">{{ $room->no_kamar }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $room->tipe }}
                                </span>
                            </td>
                            <td>{{ $room->luas }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    Lantai {{ $room->lantai }}
                                </span>
                            </td>
                            <td>
                                <i class="bi bi-person-fill text-muted"></i>
                                {{ $room->kapasitas }} orang
                            </td>
                            <td>
                                <strong class="text-dark">
                                    Rp {{ number_format($room->harga_bulanan, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                @if($room->status == 'Tersedia')
                                    <span class="badge rounded-pill bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger">
                                        <i class="bi bi-x-circle-fill me-1"></i>
                                        Disewa
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light btn-sm view-btn"
                                            onclick="viewRoom({{ $room->id }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewModal">
                                        <i class="bi bi-eye text-primary"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm edit-btn"
                                            onclick="editRoom({{ $room->id }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm delete-btn"
                                            onclick="deleteRoom({{ $room->id }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
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

        <!-- Table Footer with Stats -->
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small">
                            <strong>Total Kamar:</strong>
                            <span class="badge bg-secondary">{{ $datakamar->count() }}</span>
                        </div>
                        <div class="text-muted small">
                            <strong>Tersedia:</strong>
                            <span class="badge bg-success">
                                {{ $datakamar->where('status', 'Tersedia')->count() }}
                            </span>
                        </div>
                        <div class="text-muted small">
                            <strong>Disewa:</strong>
                            <span class="badge bg-danger">
                                {{ $datakamar->where('status', 'Disewa')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <nav aria-label="Page navigation" class="d-inline-block">
                        <div id="datatable-pagination"></div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Kamar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('datakamar.store') }}" method="POST" enctype="multipart/form-data" id="addForm">
                @csrf
                <div class="modal-body">
                    @include('pemilik.datakamar.form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Kamar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @include('pemilik.datakamar.form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- lihat -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle-fill me-2"></i>Detail Informasi Kamar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="room-detail-view">
                    <div class="room-image-section">
                        <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner" id="roomImages"></div>
                            <div class="carousel-indicators" id="carouselIndicators"></div>
                            <button class="carousel-nav prev" data-bs-target="#roomCarousel" data-bs-slide="prev">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="carousel-nav next" data-bs-target="#roomCarousel" data-bs-slide="next">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="room-info-section">
                        <div class="room-info-content">
                            <div class="info-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 id="roomNumber" class="mb-0"></h4>
                                    <div id="roomStatus"></div>
                                </div>
                                <div id="roomType" class="text-muted"></div>
                            </div>

                            <div class="info-section">
                                <div class="info-grid">
                                    <div class="info-card">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                        <div>
                                            <div class="text-dark" id="roomSize"></div>
                                            <small class="text-muted">Luas Kamar</small>
                                        </div>
                                    </div>
                                    <div class="info-card">
                                        <i class="bi bi-people"></i>
                                        <div>
                                            <div class="text-dark" id="roomCapacity"></div>
                                            <small class="text-muted">Kapasitas</small>
                                        </div>
                                    </div>
                                    <div class="info-card">
                                        <i class="bi bi-building"></i>
                                        <div>
                                            <div class="text-dark" id="roomFloor"></div>
                                            <small class="text-muted">Lantai</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-section mb-0">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <small class="text-muted d-block mb-1">Harga Sewa Per Bulan</small>
                                        <h4 class="text-primary mb-0" id="roomPrice"></h4>
                                    </div>
                                    <div id="bookingButton"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-trash-fill me-2"></i>Hapus Kamar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kamar ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();

    const table = $('#dataTable').DataTable({
        pageLength: 10,
        responsive: true,
        dom: "<'row mb-3'<'col-md-6'l><'col-md-6'>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            search: "", searchPlaceholder: "Cari nomor kamar...",
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
            { orderable: false, searchable: false, targets: [0, -1] }
        ],
        order: [[8, 'asc']]  // Changed from 'desc' to 'asc' to show newer rooms first
    });

    // Checkbox
    $('#selectAll').on('change', function() {
        $('.room-checkbox').prop('checked', this.checked);
        updateBulkActionBar();
    });

    $(document).on('change', '.room-checkbox', function() {
        $('#selectAll').prop('checked', $('.room-checkbox').length === $('.room-checkbox:checked').length);
        updateBulkActionBar();
    });

    function updateBulkActionBar() {
        const selected = $('.room-checkbox:checked').length;
        $('#selectedCount').text(selected);
        $('#bulkActionBar').toggleClass('d-none', selected === 0);
    }

    // Search kolom ke-1
    $('#searchInput').on('keyup', function() {
        table.column(1).search(this.value).draw();
    });

    // Custom Filter
    $.fn.dataTable.ext.search.push(function(settings, data) {
        const status = $('#statusFilter').val(), tipe = $('#tipeFilter').val();
        return (status === 'all' || data[7].includes(status)) &&
               (tipe === 'all' || data[2].includes(tipe));
    });

    $('#applyFilter').click(function() {
        table.draw();
        const s = $('#statusFilter').val(), t = $('#tipeFilter').val();
        const label = (s !== 'all' || t !== 'all') ? `Filter (${[s, t].filter(v => v !== 'all').length})` : 'Filter';
        $('.dropdown-toggle span').text(label);
    });

    $('#resetFilter').click(function() {
        $('#statusFilter, #tipeFilter').val('all');
        table.draw();
        $('.dropdown-toggle span').text('Filter');
    });

    $('#table-length').on('change', function() {
        table.page.len($(this).val()).draw();
    });
});
// ========================= ROOM ACTION ========================= //
function viewRoom(id) {
    $.get(`/datakamar/${id}`, function(res) {
        if (res.gambar) {
            const gambarArray = res.gambar.split(',');
            const items = gambarArray.map((img, i) => `
            <div class="carousel-item ${i === 0 ? 'active' : ''}">
                <img src="{{ asset('images') }}/${img.trim()}"
                 class="d-block w-100"
                 onerror="this.src='{{ asset('images/kamar.png') }}'"
                 alt="Foto Kamar ${i + 1}">
            </div>`).join('');

            const indicators = gambarArray.map((_, i) => `
            <button type="button" data-bs-target="#roomCarousel" data-bs-slide-to="${i}"
                ${i === 0 ? 'class="active"' : ''} aria-label="Slide ${i + 1}"></button>`).join('');

            $('#roomImages').html(items);
            $('#carouselIndicators').html(indicators);
            $('.carousel-nav, #carouselIndicators').toggle(res.gambar.length > 1);
        } else {
            $('#roomImages').html(`<div class="carousel-item active"><img src="/images/no-image.jpg" class="d-block w-100" alt="No Image"></div>`);
            $('.carousel-nav, #carouselIndicators').hide();
        }

        $('#roomNumber').text(res.no_kamar);
        $('#roomType').text(res.tipe);
        $('#roomStatus').html(`<span class="status-badge ${res.status === 'Tersedia' ? 'available' : 'occupied'}">${res.status}</span>`);
        $('#roomSize').text(res.luas);
        $('#roomFloor').text(`Lantai ${res.lantai}`);
        $('#roomCapacity').text(`${res.kapasitas} Orang`);
        $('#roomPrice').text(`Rp ${new Intl.NumberFormat('id-ID').format(res.harga_bulanan)}`);
        $('#viewModal').modal('show');
    }).fail(() => {
        Swal.fire('Error', 'Gagal memuat data kamar', 'error');
    });
}

function editRoom(id) {
    $.get(`/datakamar/${id}`, function(res) {
        $('#editForm').attr('action', `/datakamar/${id}`).find('.is-invalid, .invalid-feedback, .alert-danger').remove();
        $('#editForm #no_kamar').val(res.no_kamar).prop('disabled', true);
        $('#editForm #tipe').val(res.tipe);
        $('#editForm #luas').val(res.luas);
        $('#editForm #lantai').val(res.lantai);
        $('#editForm #kapasitas').val(res.kapasitas);
        $('#editForm #status').val(res.status);
        $('#editForm #harga_bulanan').val(new Intl.NumberFormat('id-ID').format(res.harga_bulanan));

        const container = $('#editForm #imagePreviewContainer').empty();
        if (res.gambar) {
            res.gambar.split(',').forEach((img, i) => {
                container.append(`<div class="col-4"><div class="preview-image-container">
                    <img src="/images/${img.trim()}" alt="Preview">
                    <button type="button" class="btn btn-sm btn-danger preview-delete-btn" onclick="removeImage(${i})">
                        <i class="bi bi-x"></i></button></div></div>`);
            });
        }

        $('#editModal').modal('show');
    }).fail(() => {
        Swal.fire('Error', 'Gagal memuat data kamar', 'error');
    });
}

$('#editForm').submit(function(e) {
    e.preventDefault();
    const form = $(this);
    form.find('.is-invalid, .invalid-feedback, .alert-danger').remove();

    const hargaInput = $('#editForm #harga_bulanan');
    const originalHarga = hargaInput.val();
    hargaInput.val(originalHarga.replace(/\D/g, ''));

    Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: form.attr('action'), method: 'POST',
        data: new FormData(this), processData: false, contentType: false,
        success: res => {
            Swal.close();
            hargaInput.val(originalHarga);
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message || 'Data berhasil diperbarui', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal memperbarui data' });
            }
        },
        error: xhr => {
            Swal.close(); hargaInput.val(originalHarga);
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let html = '<ul class="mb-0">';
                Object.keys(errors).forEach(field => {
                    const el = $(`#editForm #${field}`);
                    el.addClass('is-invalid');
                    if (!el.next('.invalid-feedback').length) {
                        el.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    }
                    html += `<li>${errors[field][0]}</li>`;
                });
                html += '</ul>';
                $('#editForm .modal-body').prepend(`<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert"><strong>Gagal memperbarui data!</strong>${html}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`);
            } else {
                Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data', 'error');
            }
        }
    });
});

function deleteRoom(id) {
    $('#deleteForm').attr('action', `/datakamar/${id}`);
}

$('#bulkDeleteBtn').click(function() {
    const ids = $('.room-checkbox:checked').map(function() { return $(this).val(); }).get();
    if (!ids.length) return Swal.fire('Peringatan', 'Pilih minimal satu kamar untuk dihapus', 'warning');

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Yakin ingin menghapus ${ids.length} kamar?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('{{ route("datakamar.bulk-delete") }}', {
                ids: ids,
                _token: '{{ csrf_token() }}'
            }).done(() => location.reload())
              .fail(() => Swal.fire('Error', 'Gagal menghapus data', 'error'));
        }
    });
});

</script>
@endpush
@endsection



