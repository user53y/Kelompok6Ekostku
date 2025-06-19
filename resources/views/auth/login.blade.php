<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="login-email" name="email" value="{{ old('email') }}"
                       placeholder="nama@contoh.com" required>
                <label for="login-email">Email</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="login-password" name="password" placeholder="Password" required>
                <label for="login-password">Password</label>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember-me" name="remember">
                    <label class="form-check-label" for="remember-me">Ingat saya</label>
                </div>
                <a href="{{ route('password.request') }}" class="text-warning text-decoration-none">
                    Lupa password?
                </a>
            </div>

            <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold mb-3">
                Masuk
            </button>
        </form>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <strong>Gagal masuk!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
</div>

<script>
@if(session('status'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('status') }}",
        timer: 2000,
        showConfirmButton: false
    });
@endif

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        html: "{!! implode('<br>', $errors->all()) !!}"
    });
@endif
</script>


