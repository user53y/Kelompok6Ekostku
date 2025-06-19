<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf
            <div class="form-floating mb-3">
                <input type="text" class="form-control @error('username') is-invalid @enderror"
                       id="signup-username" name="username" value="{{ old('username') }}"
                       placeholder="Username" required pattern="^\S*$">
                <label for="signup-username">Username</label>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Username tidak boleh mengandung spasi dan minimal 3 karakter</small>
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="signup-email" name="email" value="{{ old('email') }}"
                       placeholder="nama@contoh.com" required>
                <label for="signup-email">Email</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="signup-password" name="password" placeholder="Password" required minlength="8">
                        <label for="signup-password">Password</label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="passwordError" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="signup-password_confirmation"
                               name="password_confirmation" placeholder="Konfirmasi Password" required>
                        <label for="signup-password_confirmation">Konfirmasi Password</label>
                        <div id="confirmPasswordError" class="text-danger small mt-1" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold mb-3">
                Daftar
            </button>
        </form>
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
    }).then(() => {
        window.location.href = "{{ route('login') }}";
    });
@endif

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ $errors->first() }}"
    });
@endif

document.getElementById('signup-password').addEventListener('input', validatePassword);
document.getElementById('signup-password_confirmation').addEventListener('input', validatePasswordConfirmation);

function validatePassword() {
    const password = document.getElementById('signup-password').value;
    const errorDiv = document.getElementById('passwordError');

    // Reset error
    errorDiv.style.display = 'none';
    document.getElementById('signup-password').classList.remove('is-invalid');

    // Validate password
    if (password.length < 8) {
        showError(errorDiv, 'Password minimal 8 karakter');
        return false;
    }

    if (!/[A-Z]/.test(password)) {
        showError(errorDiv, 'Password harus mengandung huruf besar');
        return false;
    }

    if (!/[a-z]/.test(password)) {
        showError(errorDiv, 'Password harus mengandung huruf kecil');
        return false;
    }

    if (!/[0-9]/.test(password)) {
        showError(errorDiv, 'Password harus mengandung angka');
        return false;
    }

    validatePasswordConfirmation();
    return true;
}

function validatePasswordConfirmation() {
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-password_confirmation').value;
    const errorDiv = document.getElementById('confirmPasswordError');

    // Reset error
    errorDiv.style.display = 'none';
    document.getElementById('signup-password_confirmation').classList.remove('is-invalid');

    if (confirmPassword && password !== confirmPassword) {
        showError(errorDiv, 'Password tidak cocok');
        return false;
    }

    return true;
}

function showError(element, message) {
    element.textContent = message;
    element.style.display = 'block';
    element.previousElementSibling.classList.add('is-invalid');
}

// Validate form before submit
document.getElementById('registerForm').addEventListener('submit', function(e) {
    if (!validatePassword() || !validatePasswordConfirmation()) {
        e.preventDefault();
    }
});
</script>
