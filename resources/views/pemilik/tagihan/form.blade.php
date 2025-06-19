<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="id_penghuni" class="form-label">Nama Penghuni</label>
            <select class="form-select" id="id_penghuni" name="id_penghuni" required {{ count($penghuni) == 0 ? 'disabled' : '' }}>
                <option value="">Pilih Penghuni</option>
                {{-- Hanya tampilkan penghuni yang belum ada di tagihan --}}
                @forelse($penghuni as $p)
                    <option value="{{ $p->id }}"
                            @if(isset($tagihan) && $tagihan instanceof \App\Models\Tagihan && $tagihan->id_penghuni == $p->id) selected @endif>
                        {{ $p->nama_lengkap }} - Kamar {{ $p->datakamar->no_kamar }}
                    </option>
                @empty
                    <option value="">Semua penghuni sudah memiliki tagihan</option>
                @endforelse
            </select>
            @if(count($penghuni) == 0)
                <div class="text-danger mt-2">Semua penghuni sudah memiliki tagihan untuk periode ini.</div>
            @endif
        </div>
        <div class="mb-3">
            <label for="periode" class="form-label">Periode</label>
            <input type="month" class="form-control" id="periode" name="periode" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Preview Tagihan</label>
            <div class="alert alert-info mb-0">
                <div class="d-flex justify-content-between">
                    <span>Total Tagihan:</span>
                    <strong id="previewJumlahTagihan">Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Jatuh Tempo:</span>
                    <span id="previewJatuhTempo">-</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodeInput = document.getElementById('periode');
    const penghuniSelect = document.getElementById('id_penghuni');

    // Set initial value to current month
    const now = new Date();
    const currentMonth = now.toISOString().slice(0, 7);
    periodeInput.value = currentMonth;

    // Only set the value if tagihan exists and is defined as an object
    @if(isset($tagihan) && $tagihan instanceof \App\Models\Tagihan)
        penghuniSelect.value = '{{ $tagihan->id_penghuni }}';
        periodeInput.value = '{{ date("Y-m", strtotime($tagihan->periode)) }}';
    @endif

    function updateTagihanPreview() {
        const penghuniId = penghuniSelect.value;
        const periode = periodeInput.value;

        if (penghuniId && periode) {
            fetch(`/tagihan/calculate/${penghuniId}/${periode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('previewJumlahTagihan').textContent =
                            'Rp ' + new Intl.NumberFormat('id-ID').format(data.jumlah);
                        // Set jatuh tempo +37 hari dari hari ini
                        const jatuhTempo = new Date();
                        jatuhTempo.setDate(jatuhTempo.getDate() + 37);
                        const options = { day: '2-digit', month: 'long', year: 'numeric' };
                        document.getElementById('previewJatuhTempo').textContent =
                            jatuhTempo.toLocaleDateString('id-ID', options);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }

    // AJAX: Update penghuni list when periode changes
    periodeInput.addEventListener('change', function() {
        fetch(`/tagihan/available-penghuni?periode=${periodeInput.value}`)
            .then(response => response.json())
            .then(data => {
                penghuniSelect.innerHTML = '';
                if (data.penghuni.length > 0) {
                    penghuniSelect.disabled = false;
                    penghuniSelect.innerHTML = '<option value="">Pilih Penghuni</option>';
                    data.penghuni.forEach(function(p) {
                        penghuniSelect.innerHTML += `<option value="${p.id}">${p.nama_lengkap} - Kamar ${p.no_kamar}</option>`;
                    });
                    // Remove info message if exists
                    if (document.getElementById('noPenghuniMsg')) {
                        document.getElementById('noPenghuniMsg').remove();
                    }
                } else {
                    penghuniSelect.disabled = true;
                    penghuniSelect.innerHTML = '<option value="">Semua penghuni sudah memiliki tagihan</option>';
                    // Show info message
                    if (!document.getElementById('noPenghuniMsg')) {
                        const msg = document.createElement('div');
                        msg.className = 'text-danger mt-2';
                        msg.id = 'noPenghuniMsg';
                        msg.innerText = 'Semua penghuni sudah memiliki tagihan untuk periode ini.';
                        penghuniSelect.parentNode.appendChild(msg);
                    }
                }
                updateTagihanPreview();
            });
    });

    // Add event listeners for changes
    penghuniSelect.addEventListener('change', updateTagihanPreview);
    periodeInput.addEventListener('change', updateTagihanPreview);

    // Initial calculation if values are present
    if (penghuniSelect.value && periodeInput.value) {
        updateTagihanPreview();
    }
});
</script>
