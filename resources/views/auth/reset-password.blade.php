@extends('layouts.auth')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Reset Password</h4>
            <p class="text-muted mb-0">Silakan masukkan password baru Anda</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <input type="hidden" name="email" value="{{ request()->email }}">

            <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password" placeholder="Password Baru" required>
                <label for="password">Password Baru</label>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control"
                       id="password_confirmation" name="password_confirmation"
                       placeholder="Konfirmasi Password" required>
                <label for="password_confirmation">Konfirmasi Password</label>
            </div>

            <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold mb-3">
                Reset Password
            </button>
        </form>
    </div>
</div>

@if(session('status'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('status') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif
@endsection
