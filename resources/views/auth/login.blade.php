<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="shortcut icon" href="/assets/static/img/icons/icon-mts.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card img {
            height: 100%;
            object-fit: cover;
        }
        .login-form {
            padding: 2rem;
        }
        .brand-title {
            font-weight: 600;
            font-size: 1.8rem;
            color: #0d6efd;
        }
        .page-transition {
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        .page-transition.fade-out {
            opacity: 0;
        }

        /* CSS tambahan untuk tampilan mobile */
        @media (max-width: 767.98px) {
            .logo-mobile {
                display: flex; /* Tampilkan di mobile */
                justify-content: center;
                margin-bottom: 1.5rem; /* Beri sedikit jarak */
            }
            .logo-mobile img {
                width: 150px; /* Sesuaikan ukuran logo di mobile */
                height: 150px;
                object-fit: contain;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card w-100" style="max-width: 900px;">
            <div class="row g-0">
                <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-white">
                    <img src="/assets/static/img/logo-mts-1.png" alt="Login Image" style="width: 225px; height: 225px; object-fit: contain;">
                </div>

                <div class="col-md-6 bg-white">
                    <div class="login-form">
                        <div class="text-center mb-4">
                            <div class="brand-title">Sistem Absensi Siswa</div>
                            <p class="text-dark mb-0">MTs Darul Ishlah</p>
							<div class="logo-mobile d-md-none mt-2">
                                <img src="/assets/static/img/logo-mts-1.png" alt="Login Image">
                            </div>
                        </div>
                        
                        <form action="/login" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
							@error('username')
								<div class="text-danger">
									{{ $message }}
								</div>
							@enderror
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                            </div>
                        </form>
                        
                        <p class="mt-4 text-center text-muted small">© <?= date('Y'); ?> MTs Darul Ishlah</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>