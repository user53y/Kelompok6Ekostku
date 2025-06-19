@extends('layouts.auth')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Lupa Password</h4>
            <p class="text-muted mb-0">Masukkan email Anda untuk reset password</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" placeholder="nama@contoh.com" required>
                <label for="email">Alamat Email</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold mb-3">
                Kirim Link Reset
            </button>

            <div class="text-center">
                <a href="{{ route('auth') }}?tab=login" class="text-warning text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</div>

@if(session('status'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Email Terkirim!',
        text: "{{ session('status') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif
@endsection
