@if(auth()->user()->role === 'penghuni')
    @php
        $penghuni = auth()->user()->datapenghuni;
    @endphp
@else
    @extends('layouts.dashboard')
@endif

@section('title', 'Profil')

@section('content')
<div class="profile-wrapper">
    <div class="profile-header">
        <div class="profile-avatar-container">
            <img src="{{ $user->foto ? asset('images/photoprofile/' . $user->foto) : asset('images/default-avatar.png') }}"
                 alt="Profile" class="profile-avatar" id="profileImage">
            <label for="fotoInput" class="avatar-upload">
                <i class="bi bi-camera-fill"></i>
            </label>
            <input type="file" id="fotoInput" name="foto" hidden accept="image/*">
        </div>
        <h1 class="profile-name text-light"> Username: {{ $user->username }}</h1>
        <span class="role-badge">{{ ucfirst($user->role) }}</span>
    </div>

    <div class="profile-content">
        <nav class="profile-nav">
            <div class="nav nav-tabs" role="tablist">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info">
                    <i class="bi bi-person-vcard-fill"></i> Informasi Pribadi
                </button>
                @if(auth()->user()->role === 'penghuni')
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#kamar">
                    <i class="bi bi-house-door-fill"></i> Informasi Kamar
                </button>
                @else
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#kost">
                    <i class="bi bi-building-fill"></i> Informasi Kost
                </button>
                @endif
            </div>
        </nav>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="info">
                <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        @if(auth()->user()->role === 'penghuni')
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                       name="nama_lengkap"
                                       value="{{ old('nama_lengkap', $user->datapenghuni ? $user->datapenghuni->nama_lengkap : $user->username) }}"
                                       required>
                                <label><i class="bi bi-person-badge-fill"></i> Nama Lengkap</label>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                <label><i class="bi bi-envelope-paper-fill"></i> Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control @error('no_telepon') is-invalid @enderror"
                                       name="no_telepon"
                                       value="{{ old('no_telepon', $user->datapenghuni ? $user->datapenghuni->no_telepon : '') }}"
                                       required>
                                <label><i class="bi bi-phone-vibrate-fill"></i> No. HP</label>
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                       name="nik"
                                       value="{{ old('nik', $user->datapenghuni ? $user->datapenghuni->nik : '') }}"
                                       required>
                                <label><i class="bi bi-person-badge-fill"></i> NIK</label>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control @error('alamat') is-invalid @enderror"
                                          name="alamat" style="height: 100px"
                                          required>{{ old('alamat', $user->datapenghuni ? $user->datapenghuni->alamat : '') }}</textarea>
                                <label><i class="bi bi-geo-alt-fill"></i> Alamat</label>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror"
                                       name="pekerjaan"
                                       value="{{ old('pekerjaan', $user->datapenghuni ? $user->datapenghuni->pekerjaan : '') }}"
                                       required>
                                <label><i class="bi bi-briefcase-fill"></i> Pekerjaan</label>
                                @error('pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @else
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                       name="nama_lengkap"
                                       value="{{ old('nama_lengkap', $user->username) }}"
                                       required>
                                <label><i class="bi bi-person-badge-fill"></i> Username</label>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                <label><i class="bi bi-envelope-paper-fill"></i> Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="tel" class="form-control @error('no_telepon') is-invalid @enderror"
                                       name="no_telepon"
                                       value="{{ old('no_telepon', $user->no_telepon) }}"
                                       required>
                                <label><i class="bi bi-phone-vibrate-fill"></i> No. HP</label>
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @if(auth()->user()->role === 'penghuni')
            <div class="tab-pane fade" id="kamar">
                <div class="room-info-card">
                    <div class="room-header">
                        <h4>
                            Kamar
                            @if(isset($profile) && $profile && $profile->kamar)
                                #{{ $profile->kamar->no_kamar }}
                            @else
                                <span class="text-muted">(Belum ada kamar)</span>
                            @endif
                        </h4>
                        <span class="badge bg-{{ (isset($profile) && $profile && $profile->status_hunian === 'Aktif') ? 'success' : 'warning' }}">
                            {{ (isset($profile) && $profile) ? ($profile->status_hunian ?? '-') : '-' }}
                        </span>
                    </div>
                    <div class="room-details">
                        <div class="detail-item">
                            <span class="label">Tipe Kamar</span>
                            <span class="value">
                                {{ (isset($profile) && $profile && $profile->kamar) ? $profile->kamar->tipe : '-' }}
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Harga per Bulan</span>
                            <span class="value">
                                @if(isset($profile) && $profile && $profile->kamar)
                                    Rp {{ number_format($profile->kamar->harga_bulanan, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Tanggal Masuk</span>
                            <span class="value">
                                @if(isset($profile) && $profile && $profile->tanggal_masuk)
                                    {{ \Carbon\Carbon::parse($profile->tanggal_masuk)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="tab-pane fade" id="kost">
                <div class="kost-info-card">
                    <h4 class="mb-4">Informasi Kost</h4>
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <th width="200"><i class="bi bi-building"></i> Nama Kost</th>
                                <td>Kost Sejahtera</td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-telephone"></i> Kontak</th>
                                <td>081234567890</td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-geo-alt"></i> Alamat</th>
                                <td>Jl. Contoh No. 123, Kota Malang</td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-info-circle"></i> Deskripsi</th>
                                <td>Kost nyaman dengan fasilitas lengkap, lokasi strategis dekat kampus</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Existing photo upload code
    const fotoInput = document.getElementById('fotoInput');
    const profileImage = document.getElementById('profileImage');

    fotoInput.addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('foto', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route("profile.avatar") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    profileImage.src = data.foto;
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal mengupload foto', 'error');
            }
        }
    });

    // New profile form submission code
    const profileForm = document.getElementById('profileForm');
    profileForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Profil berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            Swal.fire('Error', 'Gagal memperbarui profil', 'error');
        }
    });
</script>
@endpush

@endsection
