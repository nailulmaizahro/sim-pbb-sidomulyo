@extends('layouts.portal')

@section('title', 'Pembayaran QRIS - SIM PBB Sidomulyo')

@section('styles')
<style>
    .payment-card {
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid rgba(19, 51, 41, 0.1);
        box-shadow: 0 10px 30px -5px rgba(19, 51, 41, 0.08);
        transition: all 0.25s ease;
    }

    .qris-frame {
        max-width: 310px;
        width: 100%;
        margin: 0 auto;
        padding: 14px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.09);
        border: 2.5px solid #10b981;
        transition: all 0.3s ease;
    }

    .qris-frame:hover {
        transform: scale(1.02);
        box-shadow: 0 12px 30px rgba(16, 185, 129, 0.2);
    }

    .qris-frame img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 12px;
        object-fit: contain;
    }

    .nominal-display-box {
        background: linear-gradient(145deg, #f0fdf4 0%, #ffffff 100%);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 16px;
        padding: 16px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb & Navigasi -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('portal.cek', ['nop' => $sppt->nop]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Cek Tagihan
        </a>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
            <i class="bi bi-qr-code me-1"></i> Pembayaran QRIS
        </span>
    </div>

    <!-- Alert Sukses / Error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-3 rounded-4 small mb-4 shadow-sm">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Periksa kembali formulir konfirmasi Anda:</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Tampilan QRIS DANA Bisnis & Petunjuk Scan (Responsif, Proporsional, Jelas) -->
        <div class="col-lg-5">
            <div class="payment-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-qr-code me-1"></i> QRIS DANA BISNIS
                    </span>
                    <span class="badge bg-light text-secondary border px-2 py-1 small">
                        PBB Sidomulyo
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-1">Pembayaran QRIS</h5>
                <p class="text-muted small mb-3">
                    Pindai kode QRIS di bawah ini menggunakan aplikasi e-wallet atau mobile banking Anda.
                </p>

                <!-- Box Nominal Tagihan dengan Jelas -->
                <div class="nominal-display-box text-center mb-3 shadow-sm">
                    <span class="d-block small text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">
                        Nominal Tagihan:
                    </span>
                    <span class="display-6 fw-extrabold text-success d-block">
                        Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}
                    </span>
                    <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                        SPPT Tahun <strong>{{ $sppt->tahun_pajak }}</strong> &bull; NOP: <strong>{{ $sppt->nop }}</strong>
                    </div>
                </div>

                <!-- Frame QRIS DANA Bisnis: Proporsional, Tajam, & Terbaca Jelas -->
                <div class="qris-frame text-center my-2">
                    <img src="/images/qris_dana_bisnis.png" 
                         alt="QRIS DANA Bisnis" 
                         class="img-fluid"
                         onerror="this.onerror=null; this.src='{{ asset('images/qris_dana_bisnis.png') }}';">
                </div>

                <div class="text-center mt-2 mb-3">
                    <span class="badge bg-white text-dark border px-3 py-1 shadow-sm">
                        <i class="bi bi-shop text-success me-1"></i> Nama Akun: <strong>KAMPUNG SIDOMULYO</strong>
                    </span>
                </div>

                <!-- Instruksi Singkat Memindai QRIS -->
                <div class="alert alert-light border small text-muted p-3 rounded-3 mb-0" style="font-size: 0.8rem; line-height: 1.55;">
                    <strong class="d-block text-dark mb-2">
                        <i class="bi bi-info-circle-fill text-success me-1"></i> Instruksi Pembayaran:
                    </strong>
                    <ol class="ps-3 mb-0">
                        <li class="mb-1">Buka aplikasi pembayaran (DANA, BCA, Mandiri, BRImo, GoPay, OVO, ShopeePay, dll).</li>
                        <li class="mb-1">Pilih menu <strong>Scan / Pindai QRIS</strong> dan arahkan kamera ke gambar QRIS di atas.</li>
                        <li class="mb-1">Masukkan nominal pembayaran sesuai tagihan: <strong>Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}</strong>.</li>
                        <li class="mb-1">Periksa nama penerima (<strong>KAMPUNG SIDOMULYO</strong>) dan selesaikan transaksi.</li>
                        <li>Simpan struk / bukti pembayaran untuk diunggah pada formulir konfirmasi di samping.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian SPPT & Formulir Konfirmasi Pembayaran -->
        <div class="col-lg-7">
            <div class="payment-card p-4 p-md-5 h-100">
                <!-- Rincian Objek & Tagihan -->
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-receipt-cutoff text-success me-1"></i> Rincian Tagihan SPPT
                    </h5>
                    <p class="text-muted small mb-0">Periksa kembali rincian data SPPT sebelum mengirimkan konfirmasi pembayaran</p>
                </div>

                <div class="bg-light p-3 rounded-4 border mb-4">
                    <div class="row g-2 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Nomor Objek Pajak (NOP):</span>
                            <strong class="text-dark font-monospace">{{ $sppt->nop }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Nama Wajib Pajak:</span>
                            <strong class="text-dark">{{ $sppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Letak Objek Pajak:</span>
                            <span>{{ $sppt->objekPajak->letak_objek ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Tahun Pajak SPPT:</span>
                            <span class="badge bg-secondary">Tahun {{ $sppt->tahun_pajak }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Pokok Ketetapan PBB:</span>
                            <span>Rp {{ number_format($sppt->total_tagihan, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Sisa Tagihan yang Harus Dibayar:</span>
                            <span class="fw-bold text-danger fs-6">Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Formulir Konfirmasi Pembayaran -->
                <form action="{{ route('portal.upload.simpan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_sppt" value="{{ $sppt->id_sppt }}">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            Nominal yang Dibayarkan (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" name="jumlah_bayar" class="form-control" 
                                   value="{{ old('jumlah_bayar', $sppt->sisa_tagihan) }}" 
                                   max="{{ $sppt->sisa_tagihan }}" min="1000" required>
                        </div>
                        <div class="form-text small">Nominal otomatis terisi sesuai sisa tagihan yang dipilih.</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Kanal / Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="bank_tujuan" class="form-select" required>
                                <option value="QRIS DANA Bisnis" selected>QRIS DANA Bisnis (DANA / E-Wallet / Bank)</option>
                                <option value="Bank Lampung">Bank Lampung (Rek. Kas Kampung)</option>
                                <option value="Bank BRI">Bank BRI</option>
                                <option value="Bank Mandiri">Bank Mandiri</option>
                                <option value="BCA">BCA</option>
                                <option value="Bank BNI">Bank BNI</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tanggal Pembayaran <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bayar" class="form-control" value="{{ old('tgl_bayar', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Akun Pengirim / Pemilik E-Wallet</label>
                            <input type="text" name="nama_rekening_pengirim" class="form-control" 
                                   placeholder="Contoh: Akun DANA Bpk. Sugeng" 
                                   value="{{ old('nama_rekening_pengirim') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Penyetor (Yang Mengonfirmasi) <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penyetor" class="form-control" 
                                   placeholder="Nama Anda" 
                                   value="{{ old('nama_penyetor', $sppt->objekPajak->wajibPajak->nama_wp ?? '') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            Unggah Foto Struk / Screenshot Bukti Pembayaran QRIS <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="bukti_transfer" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                        <div class="form-text small">Unggah screenshot struk pembayaran DANA Bisnis atau resi m-Banking (JPG, PNG, maks 3MB).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" 
                               placeholder="Contoh: Pembayaran tagihan via QRIS DANA Bisnis" 
                               value="{{ old('keterangan') }}">
                    </div>

                    <!-- Keterangan Proyek & Ketentuan Verifikasi Transaksi -->
                    <div class="alert alert-light border small text-muted p-3 rounded-3 mb-4">
                        <div class="d-flex">
                            <i class="bi bi-info-circle text-primary fs-5 me-2 flex-shrink-0"></i>
                            <div>
                                <strong>Keterangan Pembayaran Demonstrasi:</strong><br>
                                Pembayaran QRIS pada aplikasi ini merupakan bagian dari <strong>demonstrasi proyek perkuliahan</strong> dan <strong>bukan pembayaran pajak resmi pemerintah</strong>. Seluruh tagihan menggunakan nominal demonstrasi akademik (Rp 1.000) untuk pengujian alur pembayaran, bukan nilai ketetapan PBB sebenarnya.<br>
                                Setelah konfirmasi dikirim, status transaksi akan tercatat sebagai <strong>Menunggu Verifikasi</strong> dan tidak otomatis berubah menjadi Lunas sebelum diverifikasi oleh admin/petugas loket balai kampung.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a href="{{ route('portal.cek', ['nop' => $sppt->nop]) }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm flex-grow-1">
                            <i class="bi bi-send-check-fill me-1"></i> Kirim Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
