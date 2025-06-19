@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <!-- Tools Section -->
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-6 text-end ms-auto">
                    <div class="d-flex justify-content-end gap-2 align-items-center">
                        <div class="input-group me-2" style="min-width:220px;">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control form-control-sm" id="searchTagihanInput" placeholder="Cari Tagihan...">
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-funnel"></i>
                                <span>Filter</span>
                            </button>
                            <div class="dropdown-menu filter-dropdown p-3">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select form-select-sm" id="statusFilter">
                                        <option value="all">Semua Status</option>
                                        <option value="Lunas">Lunas</option>
                                        <option value="Belum Lunas">Belum Lunas</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary btn-sm w-100" id="applyFilter">Terapkan Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Bulk Action Bar -->
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
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                Tagihan dibuat otomatis setiap awal bulan untuk semua penghuni aktif.
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="dataTable">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th width="10">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>No</th>
                            <th>Nama Penghuni</th>
                            <th>Periode</th>
                            <th>Tanggal Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($tagihan as $item)
                        @if($item->status_tagihan !== 'Berhenti')
                        <tr>
                            <td>
                                <input type="checkbox" class="tagihan-checkbox" value="{{ $item->id }}">
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong class="text-primary">{{ $item->penghuni->nama_lengkap }}</strong>
                                <br>
                                <small class="text-muted">Kamar: {{ $item->penghuni->datakamar->no_kamar }}</small>
                            </td>
                            <td>{{ $item->periode }}</td>
                            <td>{{ $item->tanggal_tagihan->format('d/m/Y') }}</td>
                            <td>{{ $item->jatuh_tempo->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $item->status_tagihan === 'Lunas' ? 'success' : 'danger' }}">
                                    {{ $item->status_tagihan }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light btn-sm pemberhentian-btn"
                                            data-id="{{ $item->id }}">
                                        <i class="bi bi-x-circle text-danger"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm view-btn"
                                            data-id="{{ $item->id }}"
                                            onclick="viewTagihan({{ $item->id }})">
                                        <i class="bi bi-info-circle text-primary"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Tagihan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @include('pemilik.tagihan.form')
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle me-2"></i>Detail Tagihan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Penghuni</label>
                            <div class="fw-bold" id="viewNamaPenghuni">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Nomor Kamar</label>
                            <div class="fw-bold" id="viewNoKamar">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Periode</label>
                            <div class="fw-bold" id="viewPeriode">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Tanggal Tagihan</label>
                            <div class="fw-bold" id="viewTanggalTagihan">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Jatuh Tempo</label>
                            <div class="fw-bold text-danger" id="viewJatuhTempo">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Jumlah Tagihan</label>
                            <div class="fw-bold" id="viewJumlahTagihan">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Denda Keterlambatan</label>
                            <div class="fw-bold text-danger" id="viewDenda">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Pembayaran</label>
                            <div class="fw-bold text-primary" id="viewTotalPembayaran">-</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">Status Pembayaran:</span>
                                    <span id="viewStatus" class="ms-2">-</span>
                                </div>
                                <form id="updateStatusForm" class="d-inline" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="form-label">Upload Bukti Pembayaran/Kuitansi</label>
                                                <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*" required>
                                                <small class="text-muted">Upload bukti pembayaran atau foto kuitansi manual</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-success w-100" id="btnUpdateLunas">
                                                <i class="bi bi-check-circle me-2"></i>Tandai Lunas
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
let table;

function initializeDataTable() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    // Initialize DataTable
    table = $('#dataTable').DataTable({
        retrieve: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        responsive: true,
        dom: "<'row'<'col-sm-12'l>>" + // Hilangkan search dari dom
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            search: "Cari:",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang ditemukan",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>'
            }
        },
        columnDefs: [
            { orderable: false, targets: [0, -1] },
            { searchable: false, targets: [0, -1] }
        ],
        order: [[1, 'asc']]
    });

    return table;
}

$(document).ready(function() {
    // Initialize DataTable
    table = initializeDataTable();

    // Hilangkan search default DataTables
    $('#dataTable_filter').hide();

    // Hubungkan search custom ke DataTables
    $('#searchTagihanInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.tagihan-checkbox').prop('checked', this.checked);
        updateBulkActionBar();
    });

    // Individual checkbox functionality
    $(document).on('change', '.tagihan-checkbox', function() {
        const totalCheckboxes = $('.tagihan-checkbox').length;
        const selectedCheckboxes = $('.tagihan-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === selectedCheckboxes);
        updateBulkActionBar();
    });

    function updateBulkActionBar() {
        const selectedCount = $('.tagihan-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        $('#bulkActionBar').toggleClass('d-none', selectedCount === 0);
    }

    // Cancel Selection
    $('#cancelSelection').on('click', function() {
        $('#selectAll').prop('checked', false);
        $('.tagihan-checkbox').prop('checked', false);
        updateBulkActionBar();
    });

    // Bulk Delete functionality
    $('#bulkDeleteBtn').on('click', function() {
        const selectedIds = $('.tagihan-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal satu tagihan untuk dihapus', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus ${selectedIds.length} tagihan yang dipilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("tagihan.bulk-delete") }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus tagihan', 'error');
                    }
                });
            }
        });
    });

    // Status filter handler with fix for reinitialization
    $('#applyFilter').on('click', function() {
        const status = $('#statusFilter').val();
        table.column(7).search(status === 'all' ? '' : status).draw();
        $('.dropdown-toggle').dropdown('hide');
    });

    // Handle Periode Input
    $('#periode').on('change', function() {
        updateTagihanPreview();
    });

    // Handle Penghuni Selection
    $('#id_penghuni').on('change', function() {
        updateTagihanPreview();
    });

    function updateTagihanPreview() {
        const penghuniId = $('#id_penghuni').val();
        const periode = $('#periode').val();

        if (penghuniId && periode) {
            // Calculate due date (7 days from current date)
            const jatuhTempo = moment().add(7, 'days').format('DD MMMM YYYY');
            $('#previewJatuhTempo').text(jatuhTempo);

            // Calculate tagihan using AJAX
            $.get(`/tagihan/calculate/${penghuniId}/${periode}`, function(response) {
                $('#previewJumlahTagihan').text(
                    'Rp ' + new Intl.NumberFormat('id-ID').format(response.jumlah)
                );
            });
        }
    }

    // Edit Tagihan
    window.editTagihan = function(id) {
        $.get(`/tagihan/${id}`, function(data) {
            $('#editForm').attr('action', `/tagihan/${id}`);
            $('#editForm #id_penghuni').val(data.id_penghuni);
            $('#editForm #periode').val(moment(data.periode, 'MMMM YYYY').format('YYYY-MM'));
            $('#editForm #status_tagihan').val(data.status_tagihan);
            updateTagihanPreview();
            $('#editModal').modal('show');
        });
    }

    // View Tagihan
    window.viewTagihan = function(id) {
        $.get(`/tagihan/${id}`, function(response) {
            if (response.success) {
                const data = response.data;

                // Update modal content
                $('#viewNamaPenghuni').text(data.penghuni.nama_lengkap);
                $('#viewNoKamar').text(data.penghuni.kamar.no_kamar);
                $('#viewPeriode').text(data.periode);
                $('#viewTanggalTagihan').text(moment(data.tanggal_tagihan).format('DD MMMM YYYY'));
                $('#viewJatuhTempo').text(moment(data.jatuh_tempo).format('DD MMMM YYYY'));
                $('#viewJumlahTagihan').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.jumlah_tagihan));
                $('#viewDenda').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.denda));
                $('#viewTotalPembayaran').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total_pembayaran));

                // Update status
                const statusBadgeClass = data.status_tagihan === 'Lunas' ? 'bg-success' : 'bg-danger';
                $('#viewStatus').html(`<span class="badge ${statusBadgeClass}">${data.status_tagihan}</span>`);

                // Update form action and show/hide button
                $('#updateStatusForm').attr('action', `/tagihan/${data.id}/update-status`);
                $('#btnUpdateLunas').toggle(data.status_tagihan !== 'Lunas');
                $('#viewModal').modal('show');
            }
        });
    }

    // Handle status update
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    $('#viewModal').modal('hide');
                    Swal.fire('Berhasil', 'Status tagihan berhasil diperbarui', 'success')
                        .then(() => location.reload());
                }
            },
            error: function(xhr) {
                let message = 'Gagal memperbarui status tagihan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error', message, 'error');
            }
        });
    });

    // Delete Tagihan
    $('.pemberhentian-btn').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Konfirmasi Pemberhentian Sewa',
            text: 'Apakah Anda yakin ingin memberhentikan sewa untuk tagihan ini? Data penghuni dan tagihan akan dihapus jika sudah lunas.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Berhentikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/pemberhentian-sewa/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Data penghuni dan tagihan berhasil dihapus.', 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        let message = 'Gagal memberhentikan sewa';
                        if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                            Swal.fire('Peringatan', message, 'warning');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                            Swal.fire('Error', message, 'error');
                        } else {
                            Swal.fire('Error', message, 'error');
                        }
                    }
                });
            }
        });
    });
});

</script>

<!-- Move these script tags before the main script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
@endpush
@endsection
