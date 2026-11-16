<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Petugas - SIM PBB Sidomulyo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #133329 0%, #1e5a48 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .login-header {
            background: #0f2c23;
            color: #fff;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 4px solid #c89650;
        }
        .btn-theme {
            background-color: #1b4d3e;
            color: #fff;
            font-weight: 600;
        }
        .btn-theme:hover {
            background-color: #153e32;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-bank2 fs-1 text-warning mb-2 d-block"></i>
            <h5 class="fw-bold mb-1">SIM PBB KAMPUNG SIDOMULYO</h5>
            <small class="text-white-50">Kecamatan Punggur, Lampung Tengah</small>
        </div>

        <div class="p-4">
            @if(session('error'))
                <div class="alert alert-danger py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username Akun</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: admin / petugas_karmin" required autofocus>
                    </div>
                    @error('username')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi" required>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-theme w-100 py-2 mt-2 shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                </button>
            </form>

            <div class="alert alert-light border mt-4 p-2 small text-muted text-center">
                <i class="bi bi-info-circle me-1"></i> <strong>Akun Demo:</strong><br>
                Admin: <code>admin</code> | Password: <code>password</code><br>
                Petugas: <code>petugas_karmin</code> | Password: <code>password</code>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('portal.home') }}" class="text-decoration-none small text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Portal Warga
                </a>
            </div>
        </div>
    </div>
</body>
</html>

