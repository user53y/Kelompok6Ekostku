<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="no_kamar" class="form-label">No. Kamar</label>
            <input type="text" class="form-control" id="no_kamar" name="no_kamar" required>
        </div>
        <div class="mb-3">
            <label for="tipe" class="form-label">Tipe</label>
            <select class="form-select" id="tipe" name="tipe" required>
                <option value="">Pilih Tipe</option>
                <option value="Kamar Standard">Kamar Standard</option>
                <option value="Kamar Keluarga">Kamar Keluarga</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="luas" class="form-label">Luas</label>
            <select class="form-select" id="luas" name="luas" required>
                <option value="">Pilih Ukuran</option>
                <option value="3m x 4m">3m x 4m</option>
                <option value="3m x 5m">3m x 5m</option>
                <option value="4m x 5m">4m x 5m</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="lantai" class="form-label">Lantai</label>
            <input type="number" class="form-control" id="lantai" name="lantai" required min="1">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kapasitas" class="form-label">Kapasitas</label>
            <div class="input-group">
                <input type="number" class="form-control" id="kapasitas" name="kapasitas" required min="1">
                <span class="input-group-text">orang</span>
            </div>
        </div>
        <div class="mb-3">
            <label for="harga_bulanan" class="form-label">Harga Bulanan</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control" id="harga_bulanan" name="harga_bulanan" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Tersedia">Tersedia</option>
                <option value="Disewa">Disewa</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Kamar</label>
            <input type="file" class="form-control" id="gambar" name="gambar[]" accept="image/*" multiple>
            <small class="text-muted">Dapat memilih lebih dari 1 gambar</small>
            <div id="preview" class="mt-2">
                <div class="row g-2" id="imagePreviewContainer"></div>
            </div>
        </div>
    </div>
</div>

<style>
.preview-image-container {
    aspect-ratio: 1/1;
    overflow: hidden;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-delete-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 10;
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentFiles = [];

        document.getElementById('gambar').addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            currentFiles = newFiles;
            updatePreview();
        });

        window.removeImage = function(index) {
            currentFiles.splice(index, 1);
            updatePreview();
        };

        function updatePreview() {
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';

            currentFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4';
                    col.innerHTML = `
                        <div class="preview-image-container">
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="btn btn-sm btn-danger preview-delete-btn" onclick="removeImage(${index})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                    container.appendChild(col);
                };
                reader.readAsDataURL(file);
            });

            updateInputFiles();
        }

        function updateInputFiles() {
            const dt = new DataTransfer();
            currentFiles.forEach(file => dt.items.add(file));
            document.getElementById('gambar').files = dt.files;
        }

        // Format harga dengan pemisah ribuan
        $('#harga_bulanan').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(new Intl.NumberFormat('id-ID').format(value));
        });

        // ✅ Form submission dengan validasi yang diperbaiki
        $('#addForm, #editForm').on('submit', function(e) {
            e.preventDefault();

            // Bersihkan validasi lama
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            const hargaInput = $('#harga_bulanan');
            const originalValue = hargaInput.val();
            hargaInput.val(hargaInput.val().replace(/\D/g, ''));

            const formData = new FormData(this);

            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                Swal.close();

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data kamar berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    // Jika response.success === false
                    hargaInput.val(originalValue);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal menyimpan data'
                    });
                }
            },
                error: function(xhr) {
                    Swal.close();
                    hargaInput.val(originalValue);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;

                        Object.keys(errors).forEach(field => {
                            const fieldName = field.replace(/\.\d+$/, ''); // untuk field array seperti gambar.0, gambar.1
                            const input = $(`[name="${fieldName}"]`);

                            if (!input.length) return;

                            input.addClass('is-invalid');

                            // Tambahkan feedback hanya jika belum ada
                            if (input.next('.invalid-feedback').length === 0) {
                                $('<div class="invalid-feedback"></div>')
                                    .text(errors[field][0])
                                    .insertAfter(input);
                            }
                        });
                    }
                }
            });
        });
    });
</script>
