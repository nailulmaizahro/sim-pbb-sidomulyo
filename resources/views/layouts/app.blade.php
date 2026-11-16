<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi PBB') - Kampung Sidomulyo Punggur</title>
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
            --sidebar-width: 260px;
            --primary-dark: #133329;
            --primary-color: #1b4d3e;
            --primary-hover: #153e32;
            --primary-emerald: #10b981;
            --accent-color: #e0a96d;
            --accent-gold: #c89650;
            --bg-canvas: #f4f7f6;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #133329;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }
        .sidebar .brand {
            padding: 22px 20px;
            background: rgba(0,0,0,0.22);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar .nav-link {
            color: #c9d6d2;
            padding: 12px 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            transition: all 0.2s;
            font-size: 0.92rem;
        }
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.15rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.08);
            border-left-color: var(--primary-emerald);
        }
        .sidebar .nav-section {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8da39b;
            padding: 16px 20px 6px;
            font-weight: 700;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border-bottom: 1px solid #eef2f6;
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .content-body {
            padding: 28px;
            flex: 1;
        }
        .card {
            border: 1px solid #edf2f7;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03);
            margin-bottom: 24px;
            background: #fff;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #edf2f7;
            padding: 16px 20px;
            font-weight: 700;
            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        .badge-lunas {
            background-color: #10b981;
            color: #fff;
        }
        .badge-cicilan {
            background-color: #f59e0b;
            color: #000;
        }
        .badge-belum {
            background-color: #ef4444;
            color: #fff;
        }
        @media print {
            .sidebar, .topbar, .btn-print, .no-print {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
        }
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebarMenu">
        <div class="brand d-flex align-items-center">
            <div class="rounded-circle bg-warning bg-opacity-20 d-flex align-items-center justify-content-center me-2 p-2" style="width: 42px; height: 42px;">
                <i class="bi bi-shield-fill-check fs-4 text-warning"></i>
            </div>
            <div>
                <div class="fw-bold fs-6 tracking-wide">SIM PBB SIDOMULYO</div>
                <small class="text-emerald" style="font-size: 0.72rem; color: #a7f3d0;">Kec. Punggur, Lampung Tengah</small>
            </div>
        </div>

        <div class="py-2">
            <div class="nav-section">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section">Pelayanan Pajak</div>
            <a href="{{ route('pembayaran.index') }}" class="nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Transaksi & Kasir
                @php
                    $pendingCount = \App\Models\TransaksiPembayaran::where('status_verifikasi', 'Menunggu Verifikasi')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-auto rounded-pill">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('sppt.index') }}" class="nav-link {{ request()->routeIs('sppt.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Data SPPT / DHKP
            </a>

            <div class="nav-section">Data Master</div>
            <a href="{{ route('wajib-pajak.index') }}" class="nav-link {{ request()->routeIs('wajib-pajak.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Wajib Pajak (Warga)
            </a>
            <a href="{{ route('objek-pajak.index') }}" class="nav-link {{ request()->routeIs('objek-pajak.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Objek Pajak (NOP)
            </a>

            <div class="nav-section">Pelaporan Resmi</div>
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-printer"></i> Laporan & Rekapitulasi
            </a>

            <div class="nav-section">Layanan Publik</div>
            <a href="{{ route('portal.home') }}" target="_blank" class="nav-link">
                <i class="bi bi-box-arrow-up-right"></i> Buka Portal Warga
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="document.getElementById('sidebarMenu').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="fw-semibold text-secondary small">
                    <i class="bi bi-calendar3 me-1 text-success"></i> {{ date('d F Y') }}
                </div>
            </div>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center py-1 px-3 border rounded-pill" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5 me-2 text-success"></i>
                        <div class="text-start me-2">
                            <div class="fw-bold small">{{ Auth::user()->nama_lengkap ?? 'Petugas' }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Role: {{ ucfirst(Auth::user()->role ?? 'petugas') }}</div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Body Content -->
        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 text-center text-muted small mt-auto no-print">
            Sistem Informasi Pajak Bumi dan Bangunan (PBB) &copy; {{ date('Y') }} Pemerintah Kampung Sidomulyo, Kec. Punggur.
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
