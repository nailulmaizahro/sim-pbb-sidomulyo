@extends('layouts.portal')

@section('title', 'Layanan Pengecekan PBB Online - Kampung Sidomulyo')

@section('styles')
<style>
    /* Hero Gradient & Mesh */
    .hero-section {
        background: radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.25) 0%, transparent 60%),
                    linear-gradient(135deg, #0d261e 0%, #133329 45%, #1b4d3e 80%, #0f5132 100%);
        color: #fff;
        padding: 70px 0 95px;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #c89650 0%, #10b981 50%, #c89650 100%);
    }

    /* Glow Backdrop for Hero Image */
    .hero-image-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-image-glow {
        position: absolute;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.35) 0%, rgba(200, 150, 80, 0.15) 50%, transparent 70%);
        filter: blur(40px);
        z-index: 1;
        border-radius: 50%;
    }

    .hero-3d-img {
        position: relative;
        z-index: 2;
        max-height: 380px;
        width: auto;
        filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.35));
    }

    /* Modern Elevated Form Search Card */
    .search-card-container {
        margin-top: -55px;
        position: relative;
        z-index: 20;
    }

    .search-card-main {
        border-radius: 24px;
        background: #ffffff;
        border: 1px solid rgba(16, 185, 129, 0.2);
        box-shadow: 0 20px 45px -10px rgba(19, 51, 41, 0.16);
        padding: 32px 36px;
    }

    .form-nop-input {
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        font-size: 1.15rem;
        font-weight: 600;
        letter-spacing: 1px;
        padding: 14px 20px;
        color: #1e293b;
        transition: all 0.25s ease;
    }

    .form-nop-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
        background-color: #fbfdfc;
    }

    .btn-search-pbb {
        background: linear-gradient(135deg, #10b981 0%, #1b4d3e 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 1.05rem;
        border-radius: 14px;
        border: none;
        padding: 14px 30px;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        transition: all 0.25s ease;
    }

    .btn-search-pbb:hover {
        background: linear-gradient(135deg, #059669 0%, #133329 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.45);
    }

    /* Step Workflow Cards */
    .step-card {
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        padding: 24px;
        height: 100%;
        transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
    }

    .step-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 35px -8px rgba(19, 51, 41, 0.14);
        border-color: rgba(16, 185, 129, 0.35);
    }

    .step-img-box {
        width: 100%;
        height: 190px;
        border-radius: 14px;
        background: linear-gradient(145deg, #f0fdf4 0%, #f8fafc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid rgba(16, 185, 129, 0.1);
    }

    .step-img-box img {
        max-height: 165px;
        width: auto;
        object-fit: contain;
        transition: transform 0.4s ease;
        filter: drop-shadow(0 10px 15px rgba(0,0,0,0.08));
    }

    .step-card:hover .step-img-box img {
        transform: scale(1.06);
    }

    .step-badge {
        font-size: 0.72rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(16, 185, 129, 0.12);
        color: #065f46;
        display: inline-block;
        margin-bottom: 8px;
    }

    /* Feature Chip */
    .feature-chip {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 30px;
        padding: 6px 14px;
        font-size: 0.8rem;
        color: #ecfdf5;
        backdrop-filter: blur(8px);
    }
</style>
@endsection

@section('content')
<!-- Hero Section: Modern Government Digital Service with 3D Visual -->
<section class="hero-section">
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <!-- Left Column: Title & Information -->
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-patch-check-fill me-1"></i> PORTAL RESMI DESA DIGITAL
                    </span>
                    <span class="text-white-50 small d-none d-sm-inline">Tahun Pajak {{ date('Y') }}</span>
                </div>

                <h1 class="display-6 fw-bold text-white mb-2" style="line-height: 1.25;">
                    Layanan Pengecekan PBB Online
                </h1>
                <p class="fs-5 text-emerald mb-3" style="color: #6ee7b7; font-weight: 500;">
                    Kampung Sidomulyo, Kecamatan Punggur, Kabupaten Lampung Tengah
                </p>
                <p class="lead text-white-50 mb-4" style="font-size: 1.05rem; line-height: 1.6;">
                    Wujud komitmen pelayanan desa yang transparan, mudah, dan akuntabel. Warga Kampung Sidomulyo kini dapat mengecek status tagihan Pajak Bumi dan Bangunan (PBB-P2) serta melakukan konfirmasi bukti transfer pembayaran secara mandiri kapan pun dan di mana pun.
                </p>

                <!-- Value Props Pills -->
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <div class="feature-chip">
                        <i class="bi bi-shield-check text-warning me-2"></i> Keamanan Transaksi Terenkripsi
                    </div>
                    <div class="feature-chip">
                        <i class="bi bi-phone text-emerald me-2"></i> Akses Mandiri 24 Jam
                    </div>
                    <div class="feature-chip">
                        <i class="bi bi-file-earmark-check text-info me-2"></i> Terintegrasi DHKP Resmi
                    </div>
                </div>
            </div>

            <!-- Right Column: 3D House, Phone & Security Shield Illustration -->
            <div class="col-lg-5 text-center">
                <div class="hero-image-wrapper">
                    <div class="hero-image-glow"></div>
                    <img src="{{ asset('assets/images/3d/hero-pbb-3d.png') }}" 
                         alt="Ilustrasi 3D Layanan PBB Sidomulyo" 
                         class="hero-3d-img floating-3d img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Form Card Section -->
<div class="container search-card-container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="search-card-main">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success-subtle text-success rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-search fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Cek Status Tagihan & Objek Pajak</h5>
                        <p class="text-muted small mb-0">Masukkan Nomor Objek Pajak (NOP) yang tertera pada lembar SPPT Anda</p>
                    </div>
                </div>

                <form action="{{ route('portal.cek') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-2 border-end-0 border-secondary-subtle px-3 text-muted">
                                    <i class="bi bi-upc-scan fs-5"></i>
                                </span>
                                <input type="text" 
                                       name="nop" 
                                       class="form-control form-nop-input border-start-0" 
                                       placeholder="Ketik 18 Digit NOP (contoh: 180204200100100010)" 
                                       value="{{ old('nop') }}" 
                                       pattern="[0-9]{18}"
                                       title="Masukkan 18 digit angka NOP tanpa spasi atau titik"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <button class="btn btn-search-pbb w-100 d-flex align-items-center justify-content-center" type="submit">
                                <i class="bi bi-arrow-right-circle-fill me-2 fs-5"></i>
                                <span>Cek Tagihan</span>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mt-3 pt-2 border-top border-light-subtle small text-muted">
                        <div>
                            <i class="bi bi-info-circle text-success me-1"></i>
                            <strong>Petunjuk:</strong> NOP 18 digit dapat dilihat di bagian atas lembaran SPPT kertas tahun sebelumnya.
                        </div>
                        <div class="mt-1 mt-sm-0 text-success fw-semibold">
                            <i class="bi bi-lock-fill me-1"></i> Data Kependudukan Terlindungi (UU PDP)
                        </div>
                    </div>
                </form>

                @if(session('error'))
                    <div class="alert alert-danger mt-3 mb-0 d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Alur Layanan Pembayaran 3D Visual Cards -->
    <div class="my-5">
        <div class="text-center mb-4">
            <span class="badge bg-emerald-subtle text-success px-3 py-1 rounded-pill fw-bold text-uppercase" style="background-color: #d1fae5; color: #065f46; font-size: 0.75rem;">
                <i class="bi bi-diagram-3-fill me-1"></i> Panduan Langkah Pelayanan
            </span>
            <h3 class="fw-bold mt-2 text-dark">Alur Layanan Pembayaran PBB Kampung Sidomulyo</h3>
            <p class="text-muted small mx-auto max-w-700" style="max-width: 620px;">
                Tiga tahapan sederhana bagi warga Kampung Sidomulyo untuk menunaikan kewajiban pajak tanah dan bangunan secara aman dan transparan.
            </p>
        </div>

        <div class="row g-4">
            <!-- Tahap 1: Cek Tagihan -->
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-img-box">
                        <img src="{{ asset('assets/images/3d/step1-cek-sppt.png') }}" alt="Tahap 1: Cek Tagihan SPPT 3D" class="img-fluid">
                    </div>
                    <div class="step-badge">Langkah 01</div>
                    <h5 class="fw-bold text-dark mb-2">1. Cek NOP & Ketetapan</h5>
                    <p class="text-muted small mb-0 flex-grow-1">
                        Masukkan 18 digit NOP tanah Anda pada kotak pencarian di atas. Sistem secara otomatis menampilkan rincian ketetapan baku, tunggakan, serta status pembayaran Anda.
                    </p>
                    <div class="mt-3 pt-2 border-top small text-success fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Cepat, Instan & Akurat
                    </div>
                </div>
            </div>

            <!-- Tahap 2: Lakukan Pembayaran -->
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-img-box">
                        <img src="{{ asset('assets/images/3d/step2-bayar-digital.png') }}" alt="Tahap 2: Lakukan Pembayaran 3D" class="img-fluid">
                    </div>
                    <div class="step-badge" style="background: rgba(200, 150, 80, 0.15); color: #92400e;">Langkah 02</div>
                    <h5 class="fw-bold text-dark mb-2">2. Lakukan Pembayaran</h5>
                    <p class="text-muted small mb-0 flex-grow-1">
                        Pilih metode pelunasan: bayar langsung secara tunai di loket Balai Kampung kepada Bpk. Karmin, atau transfer bank melalui ATM/Mobile Banking ke rekening kas desa.
                    </p>
                    <div class="mt-3 pt-2 border-top small text-warning fw-semibold" style="color: #b45309 !important;">
                        <i class="bi bi-wallet-fill me-1"></i> Fleksibel Tunai atau Digital
                    </div>
                </div>
            </div>

            <!-- Tahap 3: Konfirmasi & Bukti Lunas -->
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-img-box">
                        <img src="{{ asset('assets/images/3d/step3-verifikasi-lunas.png') }}" alt="Tahap 3: Verifikasi Bukti Lunas 3D" class="img-fluid">
                    </div>
                    <div class="step-badge" style="background: rgba(59, 130, 246, 0.15); color: #1e40af;">Langkah 03</div>
                    <h5 class="fw-bold text-dark mb-2">3. Konfirmasi & Bukti Lunas</h5>
                    <p class="text-muted small mb-0 flex-grow-1">
                        Unggah foto bukti transfer melalui portal ini. Petugas akan memvalidasi mutasi, mengubah status menjadi <strong>Lunas</strong>, dan menerbitkan Bukti Kuitansi Ber-QR Code.
                    </p>
                    <div class="mt-3 pt-2 border-top small text-primary fw-semibold">
                        <i class="bi bi-patch-check-fill me-1"></i> Kuitansi Resmi Bertanda QR
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Keunggulan Pelayanan Digital Desa -->
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card-3d p-4 h-100">
                <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 mb-3 p-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Pelayanan Mandiri 24 Jam</h6>
                <p class="text-muted small mb-0">
                    Warga dapat memeriksa rincian SPPT kapan saja tanpa harus menunggu jam buka kantor desa atau antrean di balai kampung.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-3d p-4 h-100">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3 mb-3 p-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-bank fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Konfirmasi Transfer Bank Praktis</h6>
                <p class="text-muted small mb-0">
                    Bagi wajib pajak yang bertempat tinggal di luar desa atau sibuk bekerja, pembayaran via transfer dapat dikonfirmasi dengan mengunggah foto struk.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-3d p-4 h-100">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-3 mb-3 p-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-shield-lock-fill fs-4 text-warning"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Privasi & Transparansi Data</h6>
                <p class="text-muted small mb-0">
                    Data kependudukan seperti NIK disensor sesuai standar regulasi privasi UU PDP, sementara pencatatan pembukuan kas desa tetap akurat dan transparan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
