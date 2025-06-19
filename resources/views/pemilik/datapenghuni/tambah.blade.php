@php
    $datapenghuni = $datapenghuni ?? null;
@endphp
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="id_user" class="form-label">Nama Penghuni</label>
            <select class="form-select" id="id_user" name="id_user" required>
                <option value="">Pilih Penghuni</option>
                @foreach($users as $user)
                    @if(!$user->datapenghuni || (isset($datapenghuni) && $datapenghuni->id_user == $user->id))
                        <option value="{{ $user->id }}"
                            {{ (isset($datapenghuni) && $datapenghuni->id_user == $user->id) ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="id_datakamar" class="form-label">No. Kamar</label>
            <select class="form-select" id="id_datakamar" name="id_datakamar" required>
                <option value="">Pilih Kamar</option>
                @foreach($kamar as $room)
                    <option value="{{ $room->id }}"
                        {{ (isset($datapenghuni) && $datapenghuni->id_datakamar == $room->id) ? 'selected' : '' }}>
                        {{ $room->no_kamar }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                value="{{ isset($datapenghuni) && $datapenghuni->tanggal_masuk ? \Carbon\Carbon::parse($datapenghuni->tanggal_masuk)->format('Y-m-d') : '' }}"
                required>
        </div>
        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" value="{{ $datapenghuni->nik ?? '' }}" required>
        </div>
        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ $datapenghuni->nama_lengkap ?? '' }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ $datapenghuni->alamat ?? '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="no_telepon" class="form-label">No. Telepon</label>
            <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="{{ $datapenghuni->no_telepon ?? '' }}" required>
        </div>
        <div class="mb-3">
            <label for="pekerjaan" class="form-label">Pekerjaan</label>
            <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" value="{{ $datapenghuni->pekerjaan ?? '' }}" required>
        </div>
        <div class="mb-3">
            <label for="foto_ktp" class="form-label">Foto KTP</label>
            <input type="file" class="form-control" id="foto_ktp" name="foto_ktp" accept="image/*">
            @if(isset($datapenghuni) && $datapenghuni->foto_ktp)
                <div class="mt-2 preview">
                    <img src="/images/ktp/{{ $datapenghuni->foto_ktp }}" class="img-fluid rounded" style="max-height: 200px">
                </div>
            @else
                <div class="mt-2 preview"></div>
            @endif
            <small class="text-muted">Format: JPG, JPEG, PNG (Max. 2MB)</small>
        </div>
        <div class="mb-3">
            <label for="status_hunian" class="form-label">Status Hunian</label>
            <select class="form-select" id="status_hunian" name="status_hunian" required>
                <option value="Menghuni" {{ (isset($datapenghuni) && $datapenghuni->status_hunian == 'Menghuni') ? 'selected' : '' }}>Menghuni</option>
                <option value="Tidak Menghuni" {{ (isset($datapenghuni) && $datapenghuni->status_hunian == 'Tidak Menghuni') ? 'selected' : '' }}>Tidak Menghuni</option>
            </select>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set initial value to today's date only if tambah (tidak ada $datapenghuni)
    @if(!isset($datapenghuni))
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_masuk').value = today;
    @endif
    // Preview image
    document.getElementById('foto_ktp').addEventListener('change', function(e) {
        const preview = this.closest('.mb-3').querySelector('.preview');
        preview.innerHTML = '';
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px">
                `;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
