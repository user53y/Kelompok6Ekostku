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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari pengeluaran...">
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
                                <label class="form-label">Jenis Pengeluaran</label>
                                <select class="form-select form-select-sm" id="jenisFilter">
                                    <option value="all">Semua Jenis</option>
                                    @foreach($jenisPengeluaran as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama_pengeluaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm w-100" id="applyFilter">Terapkan Filter</button>
                        </div>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Pengeluaran</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Bulk Action Bar after the card-header div -->
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
                <table class="table table-hover align-middle" id="dataTable">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th width="10" class="p-2">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Jenis Pengeluaran</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datapengeluaran as $pengeluaran)
                        <tr>
                            <td class="text-center p-2">
                                <input type="checkbox" class="pengeluaran-checkbox" value="{{ $pengeluaran->id }}">
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">
                                    <i class="bi bi-tag-fill me-1"></i>
                                    {{ optional($pengeluaran->jenisPengeluaran)->nama_pengeluaran ?? 'Data Jenis Terhapus' }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark">
                                    Rp {{ number_format($pengeluaran->jumlah_pengeluaran, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>{{ date('d M Y', strtotime($pengeluaran->tanggal_pengeluaran)) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light btn-sm edit-btn"
                                            onclick="editPengeluaran({{ $pengeluaran->id }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                        <i class="bi bi-pencil text-warning"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm delete-btn"
                                            onclick="deletePengeluaran({{ $pengeluaran->id }})"
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

        <!-- Stats Footer -->
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small">
                            <strong>Total Pengeluaran:</strong>
                            <span class="badge bg-secondary">{{ $datapengeluaran->count() }}</span>
                        </div>
                        <div class="text-muted small">
                            <strong>Total Nominal:</strong>
                            <span class="badge bg-success">
                                Rp {{ number_format($datapengeluaran->sum('jumlah_pengeluaran'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit/Delete Modals here -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('datapengeluaran.store') }}" method="POST" id="addForm">
                @csrf
                <div class="modal-body">
                    @include('pemilik.datapengeluaran.form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @include('pemilik.datapengeluaran.form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengeluaran ini?</p>
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
<script>
$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    // Initialize DataTable
    const table = $('#dataTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            search: "",
            searchPlaceholder: "Cari pengeluaran...",
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
            { orderable: false, targets: -1 }, // Disable sorting on action column
        ]
    });

    // Search functionality - use DataTables API
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Filter functionality
    $('#jenisFilter').on('change', function() {
        const jenis = $(this).val();
        table.column(0).search(jenis === 'all' ? '' : $(this).find('option:selected').text()).draw();
    });

    // Edit function
    window.editPengeluaran = function(id) {
        $.get(`/datapengeluaran/${id}/edit`, function(data) {
            $('#editForm').attr('action', `/datapengeluaran/${id}`);
            $('#editForm select[name="id_jenis"]').val(data.id_jenis);
            $('#editForm input[name="jumlah_pengeluaran"]').val(
                new Intl.NumberFormat('id-ID').format(data.jumlah_pengeluaran)
            );
            $('#editForm input[name="tanggal_pengeluaran"]').val(data.tanggal_pengeluaran);
            $('#editModal').modal('show');
        });
    }

    // Delete function
    window.deletePengeluaran = function(id) {
        $('#deleteForm').attr('action', `/datapengeluaran/${id}`);
    }

    // Handle update pengeluaran
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const jumlahInput = form.find('input[name="jumlah_pengeluaran"]');
        jumlahInput.val(jumlahInput.val().replace(/\D/g, ''));

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#editModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal mengupdate data');
                jumlahInput.val(new Intl.NumberFormat('id-ID').format(jumlahInput.val()));
            }
        });
    });

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.pengeluaran-checkbox').prop('checked', this.checked);
        updateBulkActionBar();
    });

    $(document).on('change', '.pengeluaran-checkbox', function() {
        const totalCheckboxes = $('.pengeluaran-checkbox').length;
        const selectedCheckboxes = $('.pengeluaran-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === selectedCheckboxes);
        updateBulkActionBar();
    });

    function updateBulkActionBar() {
        const selectedCount = $('.pengeluaran-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        $('#bulkActionBar').toggleClass('d-none', selectedCount === 0);
    }

    // Bulk Delete functionality
    $('#bulkDeleteBtn').click(function() {
        const selectedIds = $('.pengeluaran-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('Pilih minimal satu pengeluaran untuk dihapus');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} pengeluaran yang dipilih?`)) {
            $.ajax({
                url: '{{ route("datapengeluaran.bulk-delete") }}',
                method: 'POST',
                data: {
                    ids: selectedIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data');
                }
            });
        }
    });

    $('#cancelSelection').click(function() {
        $('.pengeluaran-checkbox, #selectAll').prop('checked', false);
        updateBulkActionBar();
    });
});
</script>
@endpush

@push('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@endsection
