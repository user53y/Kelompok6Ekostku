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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari penghuni...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-funnel"></i>
                                <span>Filter</span>
                            </button>
                            <div class="dropdown-menu filter-dropdown p-3">
                                <div class="mb-3">
                                    <label class="form-label">Status Hunian</label>
                                    <select class="form-select form-select-sm" id="statusFilter">
                                        <option value="all">Semua Status</option>
                                        <option value="Menghuni">Menghuni</option>
                                        <option value="Tidak Menghuni">Tidak Menghuni</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary btn-sm w-100" id="applyFilter">Terapkan Filter</button>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i>
                                <span>Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" id="exportExcelBtn" href="{{ route('datapenghuni.export.excel') }}">
                                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" id="exportPdfBtn" href="{{ route('datapenghuni.export.pdf') }}">
                                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Penghuni</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Bulk Action Bar after card-header -->
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

        <!-- Table Section -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="dataTable">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th width="10">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>No. Telepon</th>
                            <th>Pekerjaan</th>
                            <th>Tanggal Masuk</th>
                            <th>No. Kamar</th>
                            <th>Status Hunian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penghuni as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="penghuni-checkbox" value="{{ $item->id }}">
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong class="text-primary">{{ $item->nama_lengkap }}</strong>
                                <br>
                                <small class="text-muted">Username: {{ $item->user->username }}</small>
                            </td>
                            <td>{{ $item->no_telepon }}</td>
                            <td>{{ $item->pekerjaan }}</td>
                            <td>{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    {{ $item->datakamar->no_kamar ?? 'Belum ada kamar' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->status_hunian === 'Menghuni' ? 'success' : 'warning' }}">
                                    {{ $item->status_hunian }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light btn-sm view-btn"
                                            data-id="{{ $item->id }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewModal">
                                        <i class="bi bi-eye text-primary"></i>
                                    </button>
                                    <!-- Hapus link edit, ganti dengan tombol modal edit -->
                                    <button type="button" class="btn btn-light btn-sm edit-btn"
                                            data-id="{{ $item->id }}"
                                            onclick="editPenghuni({{ $item->id }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm delete-btn"
                                            data-id="{{ $item->id }}"
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

        <!-- Card Footer with Stats -->
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small">
                            <strong>Total Penghuni:</strong>
                            <span class="badge bg-secondary">{{ $penghuni->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Penghuni
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addForm" action="{{ route('datapenghuni.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @include('pemilik.datapenghuni.tambah')
                    <div class="preview mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Penghuni
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @include('pemilik.datapenghuni.edit')
                    <div class="preview mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white align-items-center">
                <h5 class="modal-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Detail Penghuni
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Left: Profile Card -->
                    <div class="col-md-4 bg-light border-end py-4 px-3 d-flex flex-column align-items-center">
                        <div class="avatar-wrapper mb-3 position-relative">
                            <img src="" id="avatarImage" class="rounded-circle shadow" style="width: 110px; height: 110px; object-fit: cover; border: 4px solid #fff;">
                        </div>
                        <h5 class="fw-bold mb-1 text-center" id="namaPenghuni"></h5>
                        <div id="statusHunian" class="mb-2"></div>
                        <div class="w-100">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-badge me-2 text-muted"></i>
                                <span class="small text-muted">Username:</span>
                                <span class="ms-1 small" id="usernamePenghuni"></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-telephone me-2 text-muted"></i>
                                <span class="small text-muted">No. Telepon:</span>
                                <span class="ms-1 small" id="noTelepon"></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-briefcase me-2 text-muted"></i>
                                <span class="small text-muted">Pekerjaan:</span>
                                <span class="ms-1 small" id="pekerjaan"></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-credit-card-2-front me-2 text-muted"></i>
                                <span class="small text-muted">NIK:</span>
                                <span class="ms-1 small" id="nik"></span>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Detail Info -->
                    <div class="col-md-8 py-4 px-4">
                        <div class="row mb-3">
                            <div class="col-6 mb-2">
                                <div class="text-muted small mb-1">No. Kamar</div>
                                <div id="noKamar" class="fw-semibold"></div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="text-muted small mb-1">Tanggal Masuk</div>
                                <div id="tanggalMasuk" class="fw-semibold"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Alamat</div>
                            <div id="alamatPenghuni" class="fw-normal"></div>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted small mb-1">Foto KTP</div>
                            <div id="fotoKtp" class="border rounded d-flex justify-content-center align-items-center" style="min-height: 180px; background: #f8f9fa;">
                                <!-- KTP image or placeholder will be injected here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
let table;
let statusFilterValue = 'all';

function initializeDataTable() {
    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    // Custom filter for exact match on status hunian
    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(f) {
        // Remove previous custom filter
        return f.name !== 'statusHunianExact';
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'dataTable') return true;
        if (statusFilterValue === 'all') return true;
        // Kolom ke-7 (index 7) adalah Status Hunian
        // Ambil text tanpa HTML
        let statusText = $('<div>').html(data[7]).text().trim();
        return statusText === statusFilterValue;
    });

    table = $('#dataTable').DataTable({
        retrieve: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        responsive: true,
        dom: "<'row'<'col-sm-12 col-md-6'l>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Tidak ada data yang ditemukan",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>'
            }
        },
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 8] },
            { searchable: false, targets: [0, 8] }
        ]
    });

    // Status filter handler
    $('#applyFilter').off('click').on('click', function() {
        statusFilterValue = $('#statusFilter').val();
        table.draw();
        $('.dropdown-toggle').dropdown('hide');
        updateExportLinks();
    });
    // Inisialisasi awal link export
    updateExportLinks();
}

function updateExportLinks() {
    // Ambil status filter yang aktif
    const status = $('#statusFilter').val();
    let excelUrl = `{{ route('datapenghuni.export.excel') }}`;
    let pdfUrl = `{{ route('datapenghuni.export.pdf') }}`;
    // Tambahkan query string jika filter aktif
    if (status && status !== 'all') {
        excelUrl += `?status=${encodeURIComponent(status)}`;
        pdfUrl += `?status=${encodeURIComponent(status)}`;
    }
    $('#exportExcelBtn').attr('href', excelUrl);
    $('#exportPdfBtn').attr('href', pdfUrl);
}

$(document).ready(function() {
    initializeDataTable();

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.penghuni-checkbox').prop('checked', this.checked);
        updateBulkActionBar();
    });

    // Individual checkbox functionality
    $(document).on('change', '.penghuni-checkbox', function() {
        const totalCheckboxes = $('.penghuni-checkbox').length;
        const selectedCheckboxes = $('.penghuni-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === selectedCheckboxes);
        updateBulkActionBar();
    });

    function updateBulkActionBar() {
        const selectedCount = $('.penghuni-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        $('#bulkActionBar').toggleClass('d-none', selectedCount === 0);
    }

    // Cancel Selection
    $('#cancelSelection').on('click', function() {
        $('#selectAll').prop('checked', false);
        $('.penghuni-checkbox').prop('checked', false);
        updateBulkActionBar();
    });

    // Bulk Delete functionality
    $('#bulkDeleteBtn').on('click', function() {
        const selectedIds = $('.penghuni-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal satu penghuni untuk dihapus', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus ${selectedIds.length} penghuni yang dipilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("datapenghuni.bulk-delete") }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            }
        });
    });

    // Delete functionality
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/datapenghuni/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data', 'error');
                    }
                });
            }
        });
    });

    // Add this edit function
    window.editPenghuni = function(id) {
        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.get(`/datapenghuni/${id}`, function(response) {
            if (response.success && response.data) {
                const data = response.data;
                $('#editForm').attr('action', `/datapenghuni/${id}`);
                // Pastikan hidden field tetap ada dan diisi
                $('#editForm #id_user').val(data.id_user);
                $('#editForm #id_user_display').val(data.user ? data.user.username : '-');
                $('#editForm #id_datakamar').val(data.id_datakamar);
                $('#editForm #id_datakamar_display').val(data.datakamar ? data.datakamar.no_kamar : '-');
                $('#editForm #nama_lengkap').val(data.nama_lengkap ?? '');
                $('#editForm #nik').val(data.nik ?? '');
                $('#editForm #alamat').val(data.alamat ?? '');
                $('#editForm #no_telepon').val(data.no_telepon ?? '');
                $('#editForm #pekerjaan').val(data.pekerjaan ?? '');
                $('#editForm #tanggal_masuk').val(data.tanggal_masuk ?? '');
                $('#editForm #tanggal_masuk_display').val(data.tanggal_masuk ? moment(data.tanggal_masuk).format('DD-MM-YYYY') : '-');
                $('#editForm #status_hunian').val(data.status_hunian ?? '');

                // Pastikan field tidak disabled saat edit
                $('#editForm #id_user').prop('disabled', false);
                $('#editForm #id_datakamar').prop('disabled', false);

                if (data.foto_ktp) {
                    $('#editForm .preview').html(`
                        <img src="/images/ktp/${data.foto_ktp}"
                             class="img-fluid rounded"
                             style="max-height: 200px">
                    `);
                } else {
                    $('#editForm .preview').empty();
                }

                $('#editModal').modal('show');
            } else {
                Swal.fire('Error', 'Gagal mengambil data penghuni', 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal mengambil data penghuni', 'error');
        });
    }

    // Handle edit form submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('_method', 'PUT'); // Add this for PUT method

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data penghuni berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        $(`#editForm [name="${key}"]`).addClass('is-invalid')
                            .after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                    });
                }
                Swal.fire('Error', 'Gagal memperbarui data penghuni', 'error');
            }
        });
    });

    // Handle add form submission
    $('#addForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#addModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data penghuni berhasil ditambahkan',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if(errors) {
                    Object.keys(errors).forEach(key => {
                        $(`#addForm [name="${key}"]`).addClass('is-invalid')
                            .after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                    });
                }
                Swal.fire('Error', 'Gagal menambahkan data penghuni', 'error');
            }
        });
    });

    $('#viewModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const modal = $(this);

        // Reset modal content
        modal.find('#namaPenghuni').text('-');
        modal.find('#usernamePenghuni').text('-');
        modal.find('#noTelepon').text('-');
        modal.find('#pekerjaan').text('-');
        modal.find('#nik').text('-');
        modal.find('#tanggalMasuk').text('-');
        modal.find('#alamatPenghuni').text('-');
        modal.find('#statusHunian').html('-');
        modal.find('#noKamar').html('-');
        modal.find('#fotoKtp').empty();
        modal.find('#avatarImage').attr('src', '');

        // AJAX request to fetch data
        $.ajax({
            url: `/datapenghuni/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;

                    // Update modal content with fetched data
                    modal.find('#namaPenghuni').text(data.nama_lengkap ?? '-');
                    modal.find('#usernamePenghuni').text(data.user?.username ?? '-');
                    modal.find('#noTelepon').text(data.no_telepon ?? '-');
                    modal.find('#pekerjaan').text(data.pekerjaan ?? '-');
                    modal.find('#nik').text(data.nik ?? '-');
                    modal.find('#alamatPenghuni').text(data.alamat ?? '-');

                    // Status with badge
                    const statusClass = data.status_hunian === 'Menghuni' ? 'success' : 'warning';
                    modal.find('#statusHunian').html(`
                        <span class="badge bg-${statusClass} px-3 py-2 fs-6">
                            ${data.status_hunian ?? '-'}
                        </span>
                    `);

                    // Format date
                    if (data.tanggal_masuk) {
                        const formattedDate = moment(data.tanggal_masuk).format('DD MMMM YYYY');
                        modal.find('#tanggalMasuk').text(formattedDate);
                    } else {
                        modal.find('#tanggalMasuk').text('-');
                    }

                    // Room number with badge
                    if (data.datakamar && data.datakamar.no_kamar) {
                        modal.find('#noKamar').html(`
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                ${data.datakamar.no_kamar}
                            </span>
                        `);
                    } else {
                        modal.find('#noKamar').html('-');
                    }

                    // KTP Image handling
                    const fotoKtpContainer = modal.find('#fotoKtp');
                    if (data.foto_ktp) {
                        fotoKtpContainer.html(`
                            <img src="/images/ktp/${data.foto_ktp}"
                                 class="img-fluid rounded shadow"
                                 alt="Foto KTP"
                                 style="max-height: 160px; width: auto;">
                        `);
                    } else {
                        fotoKtpContainer.html(`
                            <div class="text-center p-4 w-100">
                                <i class="bi bi-card-image fs-1 text-muted"></i>
                                <p class="text-muted mb-0">Tidak ada foto KTP</p>
                            </div>
                        `);
                    }

                    // Avatar/Profile Image
                    if (data.user && data.user.avatar) {
                        modal.find('#avatarImage').attr('src', `/storage/avatars/${data.user.avatar}`);
                    } else {
                        modal.find('#avatarImage').attr('src', '/images/default-avatar.png');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Tidak dapat memuat data penghuni'
                    });
                    modal.modal('hide');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tidak dapat memuat data penghuni'
                });
                modal.modal('hide');
            }
        });
    });

    // Add preview for foto_ktp in add/edit forms
    function previewImage(input, previewElement) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(previewElement).html(`<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px">`);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Add event listeners for file inputs
    $('input[name="foto_ktp"]').change(function() {
        previewImage(this, $(this).siblings('.preview')[0]);
    });

    // Reset preview when modal is hidden
    $('#addModal, #editModal').on('hidden.bs.modal', function() {
        $(this).find('.preview').empty();
        $(this).find('form')[0].reset();
    });

    // Update export links jika filter diubah manual (tanpa klik Terapkan Filter)
    $('#statusFilter').on('change', function() {
        updateExportLinks();
    });

    // Jika user menekan enter di filter, langsung terapkan
    $('#statusFilter').on('keyup', function(e) {
        if (e.key === 'Enter') {
            $('#applyFilter').click();
        }
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
@endpush

@push('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@endsection

<style>
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }

    .info-item label {
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .avatar-wrapper img {
        border: 3px solid #fff;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    #fotoKtp img {
        max-height: 200px;
        width: 100%;
        object-fit: contain;
    }

    .badge {
        font-weight: 500;
        padding: 0.5rem 1rem;
    }

    /* Tambahan styling untuk modal view detail penghuni */
    #viewModal .modal-content {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }
    #viewModal .avatar-wrapper {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    #viewModal .badge {
        font-size: 1rem;
        font-weight: 500;
        border-radius: 0.5rem;
    }
    #viewModal .border-end {
        border-right: 1px solid #e9ecef !important;
    }
    #viewModal .modal-body {
        background: #fff;
    }
    #viewModal .text-muted {
        color: #6c757d !important;
    }
    #viewModal .fw-semibold {
        font-weight: 600;
    }
    #viewModal .fw-bold {
        font-weight: 700;
    }
    #viewModal .small {
        font-size: 0.95em;
    }
    #viewModal .img-fluid {
        max-width: 100%;
        height: auto;
    }
    #viewModal .rounded {
        border-radius: 0.5rem !important;
    }
    #viewModal .shadow {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    }
    #viewModal .d-flex.align-items-center {
        gap: 0.5rem;
    }
    #viewModal .border {
        border: 1px solid #dee2e6 !important;
    }
    #viewModal .bg-light {
        background: #f8f9fa !important;
    }
    #viewModal .px-3 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    #viewModal .px-4 {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
    }
    #viewModal .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    #viewModal .mb-1 {
        margin-bottom: 0.25rem !important;
    }
    #viewModal .mb-2 {
        margin-bottom: 0.5rem !important;
    }
    #viewModal .mb-3 {
        margin-bottom: 1rem !important;
    }
    #viewModal .w-100 {
        width: 100% !important;
    }
</style>
