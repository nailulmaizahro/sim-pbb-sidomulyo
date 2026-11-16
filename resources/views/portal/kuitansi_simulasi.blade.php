@extends('layouts.portal')

@section('title', 'Bukti Kuitansi Simulasi UTS - SIM PBB Sidomulyo')

@section('styles')
<style>
    .kuitansi-simulasi-card {
        background: #ffffff;
        border: 2px dashed #10b981;
        border-radius: 20px;
        box-shadow: 0 15px 35px -5px rgba(19, 51, 41, 0.1);
        position: relative;
        overflow: hidden;
    }

    /* Watermark Simulasi Transparan */
    .watermark-simulasi {
        position: absolute;
        top: 35%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-25deg);
        font-size: 4rem;
        font-weight: 900;
        color: rgba(220, 38, 38, 0.08);
        text-transform: uppercase;
        pointer-events: none;
        white-space: nowrap;
        z-index: 1;
        letter-spacing: 5px;
        user-select: none;
    }

    .kuitansi-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 20px;
        position: relative;
        z-index: 2;
    }

    .kuitansi-content {
        position: relative;
        z-index: 2;
    }

    .badge-simulasi-tag {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.75rem;
        padding: 5px 14px;
        border-radius: 20px;
        letter-spacing: 0.8px;
    }

    .nominal-simulasi-highlight {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 2px solid #86efac;
        border-radius: 14px;
        padding: 16px 20px;
    }

    @media print {
        .navbar-portal, .footer-portal, .no-print {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
        }
        .kuitansi-simulasi-card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 20px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Notifikasi Sukses: Pembayaran Simulasi Berhasil -->
    <div class="alert alert-success border-success-subtle shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center no-print">
        <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
            <i class="bi bi-check-lg fs-3"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="fw-bold mb-1 text-success">Pembayaran Simulasi Berhasil</h5>
            <div class="small text-muted">
                Transaksi pengujian mode demonstrasi UTS telah berhasil divalidasi oleh sistem payment gateway Sandbox.
            </div>
        </div>
        <div class="ms-auto d-none d-md-block">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-patch-check-fill me-1"></i> STATUS: TERVALIDASI
            </span>
        </div>
    </div>

    <!-- Tombol Aksi Navigasi & Cetak -->
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('portal.cek', ['nop' => $transaksi->nop]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Cek NOP
        </a>
        <button onclick="window.print()" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
            <i class="bi bi-printer me-1"></i> Cetak Bukti Pembayaran
        </button>
    </div>

    <!-- Dokumen Kuitansi / Bukti Pembayaran Simulasi UTS -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="kuitansi-simulasi-card p-4 p-md-5">
                <!-- Watermark Background -->
                <div class="watermark-simulasi">SIMULASI UTS</div>

                <div class="kuitansi-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success-subtle text-success rounded-circle p-3 me-3 d-flex align-items-center justify-content-center border" style="width: 52px; height: 52px;">
                            <i class="bi bi-patch-check-fill fs-3"></i>
                        </div>
                        <div>
                            <span class="badge-simulasi-tag mb-1 d-inline-block">
                                <i class="bi bi-mortarboard-fill me-1"></i> SIMULASI PEMBAYARAN UTS
                            </span>
                            <h5 class="fw-bold text-dark mb-0">TANDA BUKTI SIMULASI PEMBAYARAN PBB</h5>
                            <small class="text-muted">Pemerintah Kampung Sidomulyo, Kec. Punggur</small>
                        </div>
                    </div>
                    <div class="text-sm-end">
                        <div class="small text-muted">Nomor Transaksi:</div>
                        <div class="fw-bold text-dark font-monospace">{{ $transaksi->order_id }}</div>
                        <span class="badge bg-success rounded-pill px-2 py-1 mt-1">LUNAS (SIMULASI UTS)</span>
                    </div>
                </div>

                <div class="kuitansi-content pt-4">
                    <!-- Banner Peringatan Hukum/Akademik -->
                    <div class="alert alert-light border small text-muted p-2 rounded-3 mb-4">
                        <i class="bi bi-info-circle-fill text-danger me-1"></i>
                        <strong>BUKTI TRANSAKSI PENGUJIAN:</strong> Dokumen ini diterbitkan khusus untuk demonstrasi simulasi Ujian Tengah Semester (UTS).
                        <strong>Bukan merupakan tanda bukti pelunasan pajak resmi</strong> dan tidak mengurangi nilai kewajiban pajak asli di basis data pemerintah.
                    </div>

                    <!-- Kotak Nominal Pembayaran yang Dibayarkan -->
                    <div class="nominal-simulasi-highlight d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="d-block small text-muted fw-bold text-uppercase">Nominal yang Dibayarkan:</span>
                            <span class="badge bg-danger text-white rounded-pill px-2 py-1 small fw-bold" style="font-size: 0.7rem;">
                                SIMULASI PENGUJIAN
                            </span>
                        </div>
                        <div class="text-end">
                            <h2 class="fw-extrabold text-success mb-0">{{ $transaksi->format_rupiah }}</h2>
                            <small class="text-muted">Format Rupiah Valid</small>
                        </div>
                    </div>

                    <!-- Rincian Data Objek Pajak & Pembayaran -->
                    <div class="row g-3 mb-4 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Nomor Objek Pajak (NOP):</span>
                            <strong class="text-dark fs-6">{{ $transaksi->nop }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Nama Wajib Pajak Tercatat:</span>
                            <strong class="text-dark fs-6">{{ $transaksi->nama_wp }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Tahun Pajak SPPT:</span>
                            <strong>Tahun {{ $transaksi->tahun_pajak }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Nama Pembayar / Penguji:</span>
                            <strong class="text-primary">{{ $transaksi->nama_pembayar }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Kanal Pembayaran Gateway:</span>
                            <span class="badge bg-light text-dark border">{{ $transaksi->metode_pembayaran }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Waktu Transaksi & Validasi:</span>
                            <strong>{{ $transaksi->waktu_pembayaran ? $transaksi->waktu_pembayaran->format('d/m/Y H:i:s') : $transaksi->waktu_transaksi->format('d/m/Y H:i:s') }} WIB</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Ketetapan Pajak Asli di Database:</span>
                            <span class="text-muted">Rp {{ number_format($transaksi->tagihanSppt->total_tagihan ?? 0, 0, ',', '.') }} <em>(Nilai Asli Tetap Utuh)</em></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Status Verifikasi Sistem:</span>
                            <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Tervalidasi Otomatis (Settlement)</span>
                        </div>
                    </div>

                    <hr class="text-secondary-subtle my-3">

                    <!-- Tanda Tangan & QR Code Verifikasi -->
                    <div class="row align-items-center mt-3">
                        <div class="col-7">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border rounded bg-white me-3">
                                    <svg width="60" height="60" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="100" height="100" fill="#ffffff"/>
                                        <rect x="10" y="10" width="25" height="25" fill="#133329"/>
                                        <rect x="15" y="15" width="15" height="15" fill="#ffffff"/>
                                        <rect x="18" y="18" width="9" height="9" fill="#133329"/>
                                        <rect x="65" y="10" width="25" height="25" fill="#133329"/>
                                        <rect x="70" y="15" width="15" height="15" fill="#ffffff"/>
                                        <rect x="73" y="18" width="9" height="9" fill="#133329"/>
                                        <rect x="10" y="65" width="25" height="25" fill="#133329"/>
                                        <rect x="15" y="70" width="15" height="15" fill="#ffffff"/>
                                        <rect x="18" y="73" width="9" height="9" fill="#133329"/>
                                        <rect x="42" y="15" width="8" height="8" fill="#10b981"/>
                                        <rect x="45" y="35" width="12" height="12" fill="#133329"/>
                                        <rect x="65" y="45" width="8" height="8" fill="#10b981"/>
                                        <rect x="45" y="65" width="12" height="12" fill="#133329"/>
                                        <rect x="65" y="65" width="18" height="18" fill="#133329"/>
                                    </svg>
                                </div>
                                <div class="small text-muted" style="font-size: 0.72rem;">
                                    <div><strong>Digital Verification Code</strong></div>
                                    <div>Ref: {{ substr(md5($transaksi->order_id), 0, 16) }}</div>
                                    <div class="text-success"><i class="bi bi-shield-lock-fill"></i> Validasi Sistem UTS</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5 text-end small">
                            <div class="text-muted">Kampung Sidomulyo, {{ date('d F Y') }}</div>
                            <div class="fw-bold text-dark mt-4">Sistem SIM PBB Sidomulyo</div>
                            <div class="text-muted" style="font-size: 0.72rem;">(Mode Demonstrasi Pengujian UTS)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

