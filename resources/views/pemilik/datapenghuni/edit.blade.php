@php
    $datapenghuni = $datapenghuni ?? null;
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="id_user_display" class="form-label">Nama Penghuni</label>
            <input type="text" class="form-control" id="id_user_display" value="{{ $datapenghuni->user->username ?? '-' }}" readonly>
            <input type="hidden" id="id_user" name="id_user" value="{{ $datapenghuni->id_user ?? '' }}">
        </div>

        <div class="mb-3">
            <label for="id_datakamar_display" class="form-label">No. Kamar</label>
            <input type="text" class="form-control" id="id_datakamar_display" value="{{ $datapenghuni->datakamar->no_kamar ?? '-' }}" readonly>
            <input type="hidden" id="id_datakamar" name="id_datakamar" value="{{ $datapenghuni->id_datakamar ?? '' }}">
        </div>

        <div class="mb-3">
            <label for="tanggal_masuk_display" class="form-label">Tanggal Masuk</label>
            <input type="text" class="form-control" id="tanggal_masuk_display" value="{{ isset($datapenghuni->tanggal_masuk) ? \Carbon\Carbon::parse($datapenghuni->tanggal_masuk)->format('d-m-Y') : '-' }}" readonly>
            <input type="hidden" id="tanggal_masuk" name="tanggal_masuk" value="{{ $datapenghuni->tanggal_masuk ?? '' }}">
        </div>

        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik"
                value="{{ $datapenghuni->nik ?? '' }}"
                maxlength="16"
                pattern="[0-9]{16}"
                title="NIK harus terdiri dari 16 digit angka"
                required>
            <small class="text-muted">Masukkan 16 digit NIK</small>
        </div>

        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                value="{{ $datapenghuni->nama_lengkap ?? '' }}"
                required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ $datapenghuni->alamat ?? '' }}</textarea>
        </div>

        <div class="mb-3">
            <label for="no_telepon" class="form-label">No. Telepon</label>
            <input type="tel" class="form-control" id="no_telepon" name="no_telepon"
                value="{{ $datapenghuni->no_telepon ?? '' }}"
                pattern="[0-9+\-\s]+"
                title="Masukkan nomor telepon yang valid"
                required>
        </div>

        <div class="mb-3">
            <label for="pekerjaan" class="form-label">Pekerjaan</label>
            <input type="text" class="form-control" id="pekerjaan" name="pekerjaan"
                value="{{ $datapenghuni->pekerjaan ?? '' }}"
                required>
        </div>

        <div class="mb-3">
            <label for="foto_ktp" class="form-label">Foto KTP</label>
            <input type="file" class="form-control" id="foto_ktp" name="foto_ktp" accept="image/*">
            @if(isset($datapenghuni) && $datapenghuni->foto_ktp)
                <div class="mt-2">
                    <small class="text-muted d-block mb-2">Foto KTP saat ini:</small>
                    <img src="{{ asset('images/ktp/' . $datapenghuni->foto_ktp) }}"
                         class="img-fluid rounded border"
                         style="max-height: 150px; max-width: 200px;">
                </div>
            @endif
            <div class="mt-2 preview"></div>
            <small class="text-muted">Format: JPG, JPEG, PNG (Max. 2MB). Kosongkan jika tidak ingin mengubah foto.</small>
        </div>

        <div class="mb-3">
            <label for="status_hunian" class="form-label">Status Hunian</label>
            <select class="form-select" id="status_hunian" name="status_hunian" required>
                <option value="">Pilih Status</option>
                <option value="Menghuni" {{ (isset($datapenghuni) && $datapenghuni->status_hunian == 'Menghuni') ? 'selected' : '' }}>Menghuni</option>
                <option value="Tidak Menghuni" {{ (isset($datapenghuni) && $datapenghuni->status_hunian == 'Tidak Menghuni') ? 'selected' : '' }}>Tidak Menghuni</option>
            </select>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview image functionality
        const fotoKtpInput = document.getElementById('foto_ktp');
        const previewContainer = fotoKtpInput.closest('.mb-3').querySelector('.preview');

        fotoKtpInput.addEventListener('change', function(e) {
            previewContainer.innerHTML = '';

            const existingPreview = document.getElementById('ktp-existing');
            if (existingPreview) {
                existingPreview.style.display = 'none'; // Sembunyikan foto lama
            }

            if (this.files && this.files[0]) {
                const file = this.files[0];

                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <small class="text-muted d-block mb-2">Preview foto baru:</small>
                        <img src="${e.target.result}"
                             class="img-fluid rounded border"
                             style="max-height: 150px; max-width: 200px;">
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

        // NIK validation
        const nikInput = document.getElementById('nik');
        nikInput.addEventListener('input', function() {
            // Hanya angka
            this.value = this.value.replace(/[^0-9]/g, '');

            // Maksimal 16 digit
            if (this.value.length > 16) {
                this.value = this.value.substring(0, 16);
            }
        });

        // Phone number validation
        const phoneInput = document.getElementById('no_telepon');
        phoneInput.addEventListener('input', function() {
            // Hanya angka, +, -, dan spasi
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });
    });
</script>
