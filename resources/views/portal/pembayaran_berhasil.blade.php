@extends('layouts.portal')

@section('title', $transaksi->status_verifikasi === 'Terverifikasi' ? 'Pembayaran Berhasil! - SIM PBB Sidomulyo' : 'Status Pembayaran - SIM PBB Sidomulyo')

@section('styles')
<style>
    /* Styling Kartu Struk Kasir / Toko Modern */
    .receipt-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid rgba(19, 51, 41, 0.08);
        box-shadow: 0 20px 45px -10px rgba(19, 51, 41, 0.12);
        position: relative;
        overflow: hidden;
    }

    /* Ikon Centang Hijau Besar dengan Animasi Pop / Scale-in */
    .success-icon-circle {
        width: 88px;
        height: 88px;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 3.5px solid #10b981;
        color: #059669;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.35);
        animation: successPop 0.55s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
    }

    .success-icon-circle i {
        font-size: 3.2rem;
        font-weight: 900;
        line-height: 1;
    }

    @keyframes successPop {
        0% { transform: scale(0); opacity: 0; }
        60% { transform: scale(1.18); opacity: 1; }
        100% { transform: scale(1); }
    }

    /* Ikon Menunggu / Pending */
    .waiting-icon-circle {
        width: 88px;
        height: 88px;
        background: #fef3c7;
        border: 3.5px solid #f59e0b;
        color: #d97706;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.3);
        animation: pulseWaiting 2s infinite ease-in-out;
    }

    .waiting-icon-circle i {
        font-size: 2.8rem;
    }

    @keyframes pulseWaiting {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.06); }
    }

    /* Ikon Ditolak */
    .rejected-icon-circle {
        width: 88px;
        height: 88px;
        background: #fee2e2;
        border: 3.5px solid #ef4444;
        color: #dc2626;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.3);
    }

    .rejected-icon-circle i {
        font-size: 2.8rem;
    }

    /* Box Display Nominal Besar */
    .receipt-amount-display {
        background: #f8faf9;
        border: 1.5px dashed #cbd5e1;
        border-radius: 18px;
        padding: 20px;
    }

    /* Garis Pisah Struk Kasir Berlubang (Perforated Effect) */
    .receipt-perforated-divider {
        position: relative;
        border-top: 2px dashed #e2e8f0;
        margin: 24px -24px;
    }

    .receipt-perforated-divider::before,
    .receipt-perforated-divider::after {
        content: '';
        position: absolute;
        top: -12px;
        width: 24px;
        height: 24px;
        background-color: var(--bg-canvas, #f8faf9);
        border-radius: 50%;
    }

    .receipt-perforated-divider::before { left: -12px; }
    .receipt-perforated-divider::after { right: -12px; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb Navigasi -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('portal.cek', ['nop' => $transaksi->tagihanSppt->nop ?? '']) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Cek Tagihan
        </a>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
            <i class="bi bi-qr-code me-1"></i> Pembayaran QRIS
        </span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="receipt-card p-4 p-md-5">

                @if($transaksi->status_verifikasi === 'Terverifikasi')
                    <!-- ======================================================== -->
                    <!-- TAMPILAN PEMBAYARAN BERHASIL (MODERN STORE / CASHIER)     -->
                    <!-- ======================================================== -->
                    <div class="text-center mb-3">
                        <!-- 1. Ikon Centang Hijau Besar -->
                        <div class="success-icon-circle">
                            <i class="bi bi-check2"></i>
                        </div>

                        <!-- 2. Judul Pembayaran Berhasil! -->
                        <h3 class="fw-extrabold text-dark mb-1">Pembayaran Berhasil!</h3>

                        <!-- 3. Pesan Terima Kasih -->
                        <p class="text-muted small mb-0">
                            Terima kasih telah melakukan pembayaran. Transaksi Anda telah berhasil diverifikasi dan status pajak telah tercatat lunas.
                        </p>
                    </div>

                    <!-- 4. Nominal Pembayaran & Status Transaksi LUNAS -->
                    <div class="receipt-amount-display text-center my-3 shadow-sm">
                        <span class="text-muted small fw-semibold text-uppercase d-block" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            Total Pembayaran
                        </span>
                        <div class="display-5 fw-extrabold text-success my-1">
                            Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}
                        </div>
                        <div class="d-inline-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-success px-3 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> LUNAS
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-qr-code text-success me-1"></i> QRIS
                            </span>
                        </div>
                    </div>

                    <!-- Garis Pisah Struk Kasir Modern -->
                    <div class="receipt-perforated-divider"></div>

                    <!-- 5. Rincian Referensi Transaksi & SPPT -->
                    <div class="px-md-2 mb-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-receipt text-success me-1"></i> Rincian Transaksi
                        </h6>
                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor Referensi:</span>
                                <strong class="font-monospace text-dark fs-6">{{ $transaksi->kode_transaksi }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Tanggal Pembayaran:</span>
                                <span class="text-dark fw-semibold">
                                    {{ $transaksi->tgl_bayar ? $transaksi->tgl_bayar->format('d/m/Y, H:i') . ' WIB' : now()->format('d/m/Y, H:i') . ' WIB' }}
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Metode Pembayaran:</span>
                                <span class="fw-semibold text-dark"><i class="bi bi-qr-code text-success me-1"></i> {{ $transaksi->bank_tujuan ?? 'QRIS DANA Bisnis' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Status Transaksi:</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-bold">
                                    <i class="bi bi-check2-circle me-1"></i> LUNAS
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor Objek Pajak (NOP):</span>
                                <strong class="font-monospace text-dark">{{ $transaksi->tagihanSppt->nop ?? '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nama Wajib Pajak:</span>
                                <span class="fw-semibold text-dark">{{ $transaksi->tagihanSppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nama Penyetor / Pembayar:</span>
                                <span class="text-dark">{{ $transaksi->nama_penyetor ?? '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Tahun Pajak SPPT:</span>
                                <span class="badge bg-secondary">Tahun {{ $transaksi->tagihanSppt->tahun_pajak ?? date('Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Tombol Aksi: Kembali ke Dashboard & Bukti Pembayaran -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 pt-3 border-top">
                        <!-- Tombol Kembali ke Dashboard Sesuai Ketentuan 9 -->
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 w-100 w-sm-auto">
                            <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard
                        </a>
                        <div class="d-flex gap-2 w-100 w-sm-auto">
                            <a href="{{ route('portal.cek', ['nop' => $transaksi->tagihanSppt->nop ?? '']) }}" class="btn btn-theme rounded-pill px-4 py-2 fw-bold text-white shadow-sm w-100 w-sm-auto">
                                <i class="bi bi-arrow-left-circle me-1"></i> Cek Tagihan Lain
                            </a>
                        </div>
                    </div>

                @elseif($transaksi->status_verifikasi === 'Menunggu Verifikasi')
                    <!-- ======================================================== -->
                    <!-- TAMPILAN MENUNGGU VERIFIKASI (REAL-TIME POLLING AKTIF)     -->
                    <!-- ======================================================== -->
                    <div class="text-center mb-4">
                        <div class="waiting-icon-circle">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <h4 class="fw-extrabold text-dark mb-1">Menunggu Verifikasi Pembayaran</h4>
                        <p class="text-muted small mb-0">
                            Konfirmasi pembayaran Anda telah diterima. Admin loket sedang memeriksa mutasi penerimaan pembayaran pada akun DANA Bisnis.
                        </p>
                    </div>

                    <div class="receipt-amount-display text-center my-3 shadow-sm">
                        <span class="text-muted small fw-semibold text-uppercase d-block" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            Nominal yang Dikonfirmasi
                        </span>
                        <div class="display-6 fw-extrabold text-dark my-1">
                            Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}
                        </div>
                        <div class="d-inline-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-clock-history me-1"></i> STATUS: MENUNGGU VERIFIKASI
                            </span>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                <i class="bi bi-qr-code text-success me-1"></i> QRIS
                            </span>
                        </div>
                    </div>

                    <!-- Keterangan Verifikasi & Polling Otomatis -->
                    <div class="alert alert-light border small text-muted p-3 rounded-4 mb-4">
                        <div class="d-flex">
                            <i class="bi bi-info-circle text-primary fs-5 me-2 flex-shrink-0"></i>
                            <div>
                                <strong>Proses Verifikasi Transaksi:</strong><br>
                                Pembayaran tidak otomatis dinyatakan berhasil sebelum transaksi benar-benar diverifikasi terhadap mutasi masuk. Halaman ini akan <strong>otomatis beralih ke halaman Pembayaran Berhasil</strong> begitu admin menyelesaikan pemeriksaan.
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-4 border mb-4 small">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor Referensi:</span>
                                <strong class="font-monospace text-dark">{{ $transaksi->kode_transaksi }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Waktu Pengiriman:</span>
                                <span>{{ $transaksi->tgl_bayar ? $transaksi->tgl_bayar->format('d/m/Y H:i:s') . ' WIB' : '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">NOP:</span>
                                <strong>{{ $transaksi->tagihanSppt->nop ?? '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nama Penyetor:</span>
                                <strong class="text-primary">{{ $transaksi->nama_penyetor ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Indikator Polling Real-time -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-light border small text-muted">
                            <div class="spinner-grow text-success" role="status" style="width: 12px; height: 12px;"></div>
                            <span>Memeriksa status verifikasi otomatis secara real-time...</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 pt-3 border-top">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 w-100 w-sm-auto">
                            <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard
                        </a>
                        <a href="{{ route('portal.cek', ['nop' => $transaksi->tagihanSppt->nop ?? '']) }}" class="btn btn-theme rounded-pill px-4 fw-bold text-white shadow-sm w-100 w-sm-auto">
                            <i class="bi bi-arrow-left-circle me-1"></i> Cek Tagihan Lain
                        </a>
                    </div>

                @else
                    <!-- ======================================================== -->
                    <!-- TAMPILAN TRANSAKSI DITOLAK                              -->
                    <!-- ======================================================== -->
                    <div class="text-center mb-4">
                        <div class="rejected-icon-circle">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <h4 class="fw-extrabold text-danger mb-1">Pembayaran Ditolak</h4>
                        <p class="text-muted small mb-0">
                            Konfirmasi pembayaran tidak dapat diverifikasi oleh admin loket.
                        </p>
                    </div>

                    <div class="alert alert-danger border-0 rounded-4 small p-3 mb-4">
                        <strong>Alasan Penolakan:</strong><br>
                        {{ $transaksi->alasan_penolakan ?? 'Bukti pembayaran tidak sesuai atau mutasi tidak ditemukan.' }}
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 pt-3 border-top">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 w-100 w-sm-auto">
                            <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard
                        </a>
                        <a href="{{ route('portal.upload.form', $transaksi->id_sppt) }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto">
                            <i class="bi bi-arrow-repeat me-1"></i> Konfirmasi Ulang
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($transaksi->status_verifikasi === 'Menunggu Verifikasi')
<script>
    // Polling otomatis setiap 3 detik untuk mendeteksi konfirmasi admin/webhook secara real-time
    const checkStatusUrl = "{{ route('portal.pembayaran.check-status', $transaksi->kode_transaksi) }}";
    
    const statusInterval = setInterval(function() {
        fetch(checkStatusUrl, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.is_success) {
                clearInterval(statusInterval);
                // Arahkan otomatis ke halaman pembayaran berhasil
                window.location.reload();
            }
        })
        .catch(err => {
            console.warn('Cek status pembayaran gagal:', err);
        });
    }, 3000);
</script>
@endif
@endsection

