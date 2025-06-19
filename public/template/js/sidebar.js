document.addEventListener("DOMContentLoaded", function () {
    // Menandai link sidebar yang sesuai dengan halaman saat ini
    const sidebarLinks = document.querySelectorAll(".sidebar-link");
    const currentUrl = window.location.pathname;

    sidebarLinks.forEach(link => {
        if (link.pathname === currentUrl) {
            link.classList.add("active");
        }
    });

    // Menentukan judul halaman berdasarkan URL saat ini
    const pageTitle = document.getElementById("pageTitle");
    if (pageTitle) {
        const pageTitles = {
            "/tampil-kamar": "Data Kamar",
            "/tampil-penghuni": "Data Penghuni",
            "/jenis-pengeluaran": "Data Jenis Pengeluaran",
            "/tampil-tagihan": "Data Tagihan",
            "/tampil-pemasukan": "Data Pemasukan",
            "/tampil-pengeluaran": "Data Pengeluaran",
            "/tampil-laporan": "Cetak Laporan",
            "/kamar-tersedia": "Pesan Kamar",
            "/cek-pembayaran": "Cek Pembayaran"
        };
        pageTitle.textContent = pageTitles[currentUrl] || "Dashboard";
    }

    // Add active class to current dropdown parent
    const currentPath = window.location.pathname;
    const dropdownLinks = document.querySelectorAll('.sidebar-link.sub-link');

    dropdownLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            link.closest('.collapse').classList.add('show');
            link.closest('.sidebar-group')
                .querySelector('.has-dropdown')
                .setAttribute('aria-expanded', 'true');
        }
    });
});

// Animate chevron on dropdown toggle
document.querySelectorAll('.sidebar-link.has-dropdown').forEach(dropdown => {
    dropdown.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        const chevron = this.querySelector('.bi-chevron-down');
        chevron.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(-180deg)';
    });
});



$(document).ready(function () {
    // Menampilkan dan memperbarui waktu secara real-time
    function updateDateTime() {
        const now = new Date();
        const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $("#current-date").text(`${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`);
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Fungsi toggle sidebar
    $("#toggleButton").click(function () {
        if ($(window).width() > 768) {
            $("#sidebar").toggleClass("mini");
            $("#mainContent, #topNavbar").toggleClass("expanded");
        } else {
            $("#sidebar").toggleClass("active");
            $("#mainContent, #topNavbar").toggleClass("shifted");
        }
    });

    // Menyesuaikan sidebar berdasarkan ukuran layar
    function handleScreenResize() {
        if ($(window).width() <= 768) {
            $("#sidebar").removeClass("mini").addClass("active");
            $("#mainContent, #topNavbar").removeClass("expanded").addClass("shifted");
        } else {
            $("#sidebar").removeClass("active").addClass("mini");
            $("#mainContent, #topNavbar").removeClass("shifted").addClass("expanded");
        }
    }
    $(window).on("load resize", handleScreenResize);


    // Inisialisasi DataTable dengan konfigurasi khusus
    $("#dataTable").DataTable({
        pageLength: 10,
        responsive: true,
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            paginate: {
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>'
            }
        },
        searching: false
    });

    // Fungsi untuk menampilkan/menghilangkan action bar berdasarkan jumlah checkbox yang dipilih
    function updateBulkActionBar() {
        const selectedCount = $(".row-checkbox:checked").length;
        $("#selectedCount").text(selectedCount);
        $("#bulkActionBar").toggleClass("show", selectedCount > 0);
    }

    // Mengontrol pemilihan checkbox "Select All"
    $("#selectAll").change(function () {
        $(".row-checkbox").prop("checked", $(this).prop("checked"));
        updateBulkActionBar();
    });

    // Mengontrol pemilihan checkbox individu
    $(document).on("change", ".row-checkbox", function () {
        updateBulkActionBar();
        $("#selectAll").prop("checked", $(".row-checkbox:checked").length === $(".row-checkbox").length);
    });

    // Membatalkan semua pilihan checkbox
    $("#cancelSelection").click(function () {
        $("#selectAll, .row-checkbox").prop("checked", false);
        updateBulkActionBar();
    });

    // Aksi hapus banyak data
    $("#bulkDeleteBtn").click(function () {
        const selectedIds = $(".row-checkbox:checked")
            .map(function () {
                return $(this).closest("tr").find("button[data-id]").data("id");
            })
            .get();

        if (selectedIds.length === 0) {
            alert("Pilih setidaknya satu data untuk dihapus!");
            return;
        }

        if (confirm("Apakah Anda yakin ingin menghapus data terpilih?")) {
            $.post(
                "/datakamar/bulk-delete",
                {
                    ids: selectedIds,
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                function () {
                    location.reload();
                }
            );
        }
    });
});
