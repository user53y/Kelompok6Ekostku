<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title', 'Authentication')</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-wrapper {
            width: 100%;
            padding: 2rem 0;
        }
        .auth-card {
            max-width: 450px;
            margin: 0 auto;
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-logo img {
            max-width: 120px;
            margin-bottom: 1rem;
        }
        .brand-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffc107;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255,193,7,0.25);
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        .btn-warning:hover {
            background-color: #ffca2c;
            border-color: #ffc720;
            color: #000;
        }
        .text-warning {
            color: #ffc107 !important;
        }
        .form-floating > .form-control:focus ~ label {
            color: #ffc107;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="container">
            <div class="brand-logo">
                <img src="{{ asset('template/img/logo.png') }}" alt="Logo">
                <div class="brand-text">Kos Bu Tik</div>
            </div>

            <div class="auth-card">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
