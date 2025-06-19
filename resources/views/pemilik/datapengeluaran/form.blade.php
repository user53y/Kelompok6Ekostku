<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">Jenis Pengeluaran</label>
        <select class="form-select" name="id_jenis" required>
            <option value="">Pilih Jenis</option>
            @foreach($jenisPengeluaran as $jenis)
                <option value="{{ $jenis->id }}">
                    {{ $jenis->kategori_pengeluaran }} - {{ $jenis->nama_pengeluaran }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Jumlah</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" class="form-control" name="jumlah_pengeluaran" required
                   oninput="this.value = formatRupiah(this.value)">
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="date" class="form-control" name="tanggal_pengeluaran" required
               value="{{ date('Y-m-d') }}">
    </div>
</div>

<script>
function formatRupiah(angka) {
    // Remove non-numeric characters
    angka = angka.replace(/\D/g, '');
    // Format to rupiah
    return new Intl.NumberFormat('id-ID').format(angka);
}

$(document).ready(function() {
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const jumlahInput = form.find('input[name="jumlah_pengeluaran"]');

        // Remove formatting before sending
        const originalValue = jumlahInput.val();
        jumlahInput.val(jumlahInput.val().replace(/\D/g, ''));

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    $('#addModal').modal('hide');
                    location.reload();
                } else {
                    alert('Gagal menambah data');
                    jumlahInput.val(originalValue);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Gagal menambah data'));
                jumlahInput.val(originalValue);
            }
        });
    });

    // Format on input
    $('input[name="jumlah_pengeluaran"]').on('input', function() {
        this.value = formatRupiah(this.value);
    });
});
</script>
