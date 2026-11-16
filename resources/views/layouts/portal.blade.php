<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Layanan PBB Online') - Pemerintah Kampung Sidomulyo Punggur</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-dark: #133329;
            --primary-theme: #1b4d3e;
            --primary-emerald: #10b981;
            --primary-emerald-hover: #059669;
            --accent-gold: #c89650;
            --accent-gold-hover: #b07d3b;
            --bg-canvas: #f8faf9;
            --card-shadow: 0 10px 30px -5px rgba(19, 51, 41, 0.08);
            --card-hover-shadow: 0 20px 40px -10px rgba(19, 51, 41, 0.16);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Glassmorphic Navbar */
        .navbar-portal {
            background: rgba(19, 51, 41, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 2px solid rgba(200, 150, 80, 0.4);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .navbar-brand .brand-logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #10b981 0%, #0d261e 100%);
            border: 2px solid var(--accent-gold);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);
        }

        .btn-portal-primary {
            background: linear-gradient(135deg, var(--primary-emerald) 0%, var(--primary-theme) 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.25s ease;
        }

        .btn-portal-primary:hover {
            background: linear-gradient(135deg, var(--primary-emerald-hover) 0%, var(--primary-dark) 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
        }

        .btn-login-header {
            border: 1.5px solid rgba(200, 150, 80, 0.8);
            background: rgba(200, 150, 80, 0.1);
            color: #f1f5f9;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-login-header:hover {
            background: var(--accent-gold);
            color: #133329;
            border-color: var(--accent-gold);
            transform: translateY(-1px);
        }

        /* 3D Floating Animations */
        @keyframes subtleFloat {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .floating-3d {
            animation: subtleFloat 5s ease-in-out infinite;
        }

        /* 3D Card Hover Effects */
        .card-3d {
            border: 1px solid rgba(19, 51, 41, 0.06);
            border-radius: 18px;
            background: #ffffff;
            box-shadow: var(--card-shadow);
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .card-3d:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-hover-shadow);
            border-color: rgba(16, 185, 129, 0.3);
        }

        /* Footer */
        .footer-portal {
            background: linear-gradient(180deg, #133329 0%, #0a1a15 100%);
            border-top: 3px solid var(--accent-gold);
            color: #94a3b8;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navbar Portal -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-portal sticky-top py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('portal.home') }}">
                <div class="brand-logo-icon rounded-circle d-flex align-items-center justify-content-center me-2 text-warning">
                    <i class="bi bi-shield-fill-check fs-4"></i>
                </div>
                <div>
                    <span class="fw-bold tracking-wide text-white d-block" style="letter-spacing: 0.5px; font-size: 1.15rem;">SIM PBB SIDOMULYO</span>
                    <div style="font-size: 0.72rem; color: #a7f3d0; font-weight: 500;">
                        <i class="bi bi-geo-alt-fill text-warning me-1"></i>Pemerintah Kampung Sidomulyo, Punggur
                    </div>
                </div>
            </a>
            
            <div class="ms-auto d-flex align-items-center">
                <a href="{{ route('login') }}" class="btn btn-login-header btn-sm px-3 py-2 rounded-pill d-flex align-items-center">
                    <i class="bi bi-person-badge-fill me-1 text-warning"></i>
                    <span>Portal Petugas</span>
                </a>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Professional Government Footer -->
    <footer class="footer-portal py-5 mt-5">
        <div class="container">
            <div class="row g-4 justify-content-between align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-logo-icon rounded-circle d-flex align-items-center justify-content-center me-2 text-warning" style="width: 38px; height: 38px;">
                            <i class="bi bi-shield-fill-check fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-white mb-0">Sistem Informasi PBB Kampung Sidomulyo</h6>
                    </div>
                    <p class="small text-white-50 mb-1">
                        Pelayanan digital resmi pengelolaan Pajak Bumi dan Bangunan Perdesaan dan Perkotaan (PBB-P2) Kampung Sidomulyo, Kecamatan Punggur, Kabupaten Lampung Tengah.
                    </p>
                    <p class="small text-white-50 mb-0">
                        Mewujudkan transparansi penerimaan pajak desa yang akuntabel, modern, dan memudahkan seluruh wajib pajak.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="fw-bold text-light small mb-1"><i class="bi bi-building me-1 text-warning"></i> Balai Kampung Sidomulyo</div>
                        <div class="small text-white-50">Kecamatan Punggur, Kabupaten Lampung Tengah, Kode Pos 34152</div>
                        <div class="small text-emerald text-success mt-2">
                            <i class="bi bi-clock-history me-1"></i> Layanan Pengecekan Mandiri Warga Aktif 24 Jam
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary border-opacity-25 mt-4 pt-3 d-flex flex-column flex-md-row justify-content-between align-items-center small text-white-50">
                <div>&copy; {{ date('Y') }} SIM PBB Kampung Sidomulyo. Hak Cipta Dilindungi Undang-Undang.</div>
                <div class="mt-2 mt-md-0">
                    <span class="text-warning"><i class="bi bi-shield-lock-fill me-1"></i> Aman & Terintegrasi DHKP</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
