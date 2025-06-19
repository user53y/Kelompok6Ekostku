<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Kos Bu Tik</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .bg-image {
            background: url('template/img/login.png') center/cover no-repeat;
        }
        .auth-container {
            padding: 3rem 2rem;
        }
        .auth-card {
            max-width: 450px;
            width: 100%;
            margin: auto;
        }
        .nav-pills {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .nav-pills .nav-link {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link.active {
            background-color: #ffc107;
            color: #000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-pills .nav-link:hover:not(.active) {
            background-color: #fff3cd;
            transform: translateY(-1px);
        }
        .divider {
            margin: 2rem 0;
            position: relative;
        }
        .divider-text {
            background: #fff;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .social-btn {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
            background: #fff;
        }
        .social-btn i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }
        .social-btn.google {
            color: #ea4335;
        }
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-decoration: none;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #ffc107;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255,193,7,0.25);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <div class="col-md-6 d-none d-md-block p-0 bg-dark bg-image">
                <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4">
                    <h1 class="text-center fw-bold">
                        <span class="bg-warning px-2">Selamat Datang</span>
                        <p class="bg-warning px-2">di Kos Bu Tik</p>
                    </h1>
                </div>
            </div>
            <div class="col-md-6">
                <div class="auth-container d-flex align-items-center justify-content-center min-vh-100">
                    <div class="auth-card">
                        <ul class="nav nav-pills nav-fill" id="auth-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="login-tab" data-type="login" type="button">Masuk</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="register-tab" data-type="register" type="button">Daftar</button>
                            </li>
                        </ul>
                        <div id="auth-content" class="mb-4">
                        </div>

                        <div class="divider">
                            <div class="row align-items-center">
                                <div class="col"><hr></div>
                                <div class="col-auto text-center">
                                    <span class="divider-text">atau lanjutkan dengan</span>
                                </div>
                                <div class="col"><hr></div>
                            </div>
                        </div>

                        <div class="social-login">
                            <a href="{{ route('social.login', 'google') }}" class="social-btn google w-100">
                                <i class="fab fa-google"></i>
                                <span>Masuk dengan Google</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Check for URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');

        function loadAuthContent(type) {
            $.ajax({
                url: '/' + type,
                method: 'GET',
                success: function(response) {
                    $('#auth-content').html(response);
                    $('#auth-tab button').removeClass('active');
                    $(`#${type}-tab`).addClass('active');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memuat data. Silakan coba lagi.'
                    });
                }
            });
        }

        $(document).ready(function() {
            // Load initial content based on URL parameter or default to login
            loadAuthContent(tab === 'register' ? 'register' : 'login');

            $('#auth-tab button').on('click', function() {
                const type = $(this).data('type');
                loadAuthContent(type);
            });
        });
    </script>
</body>
</html>


