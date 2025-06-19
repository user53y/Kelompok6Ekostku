<div class="mb-3">
    <label for="id_tagihan" class="form-label">Tagihan</label>
    <select class="form-select" id="id_tagihan" name="id_tagihan" required>
        <option value="">Pilih Tagihan</option>
        @foreach($tagihan as $item)
            <option value="{{ $item->id }}">
                {{ $item->penghuni->nama }} - {{ date('d/m/Y', strtotime($item->tanggal_tagihan)) }} - Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="jumlah_pembayaran" class="form-label">Jumlah Pembayaran</label>
    <div class="input-group">
        <span class="input-group-text">Rp</span>
        <input type="number" class="form-control" id="jumlah_pembayaran" name="jumlah_pembayaran"
               placeholder="Masukkan jumlah pembayaran" required>
    </div>
</div>

<div class="mb-3">
    <label for="tanggal_pembayaran" class="form-label">Tanggal Pembayaran</label>
    <input type="date" class="form-control" id="tanggal_pembayaran" name="tanggal_pembayaran" required>
</div>

<div class="mb-3">
    <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
    <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*">
</div>
