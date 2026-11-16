@extends('layouts.portal')

@section('title', 'Pembayaran QRIS - SIM PBB Sidomulyo')

@section('styles')
<style>
    .payment-qris-card {
        border-radius: 20px;
        background: #ffffff;
        border: 1.5px solid rgba(16, 185, 129, 0.2);
        box-shadow: 0 12px 30px -5px rgba(19, 51, 41, 0.08);
    }

    .qris-frame {
        max-width: 300px;
        width: 100%;
        margin: 0 auto;
        padding: 14px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
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
        border-radius: 10px;
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

    <!-- Keterangan Proyek & Non-Klaim Kanal Resmi Pemerintah -->
    <div class="alert alert-light border small text-muted p-3 rounded-4 mb-4 shadow-sm">
        <div class="d-flex align-items-start">
            <i class="bi bi-info-circle text-primary fs-5 me-2 flex-shrink-0"></i>
            <div>
                <strong>Keterangan Pembayaran:</strong><br>
                Pembayaran QRIS pada aplikasi ini merupakan bagian dari <strong>demonstrasi proyek sistem informasi</strong> dan <strong>bukan pembayaran pajak resmi pemerintah</strong>.<br>
                Setelah konfirmasi dikirim, status transaksi akan tercatat sebagai <strong>Menunggu Verifikasi</strong> dan tidak otomatis berubah menjadi Lunas sebelum diverifikasi oleh admin/petugas.
            </div>
        </div>
    </div>

    <!-- Rincian Tagihan SPPT -->
    <div class="card p-4 rounded-4 shadow-sm border-0 mb-4 bg-white">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-success-subtle text-success rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-receipt-cutoff fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">Rincian Tagihan SPPT</h5>
                <p class="text-muted small mb-0">Nominal kewajiban pajak yang harus dibayarkan</p>
            </div>
        </div>

        <div class="bg-light p-3 rounded-3 border">
            <div class="row g-2 small">
                <div class="col-sm-6">
                    <span class="text-muted d-block">Nomor Objek Pajak (NOP):</span>
                    <strong class="font-monospace text-dark fs-6">{{ $sppt->nop }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Nama Wajib Pajak:</span>
                    <strong class="text-dark">{{ $sppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Tahun Pajak SPPT:</span>
                    <span class="badge bg-secondary">Tahun {{ $sppt->tahun_pajak }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Sisa Tagihan yang Harus Dibayar:</span>
                    <span class="fs-5 fw-bold text-danger">Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Pembayaran QRIS -->
    <div class="payment-qris-card p-4 p-md-5 mb-4">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center gap-2 mb-2">
                <span class="badge bg-success px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                    <i class="bi bi-qr-code me-1"></i> QRIS DANA BISNIS
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-1">Pembayaran QRIS</h3>
            <p class="text-muted small mx-auto" style="max-width: 600px;">
                Silakan pindai kode QRIS DANA Bisnis di bawah ini menggunakan aplikasi e-wallet atau mobile banking Anda untuk menyelesaikan pembayaran tagihan.
            </p>

            <!-- Box Nominal Tagihan dengan Jelas -->
            <div class="nominal-display-box d-inline-block text-center my-2 shadow-sm px-4 py-3">
                <span class="d-block small text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">
                    Nominal Tagihan:
                </span>
                <span class="display-6 fw-extrabold text-success" id="qrisNominalDisplay">
                    Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}
                </span>
                <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                    (SPPT Tahun {{ $sppt->tahun_pajak }} &bull; NOP: {{ $sppt->nop }})
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-center justify-content-center">
            <!-- Kolom Kiri: Gambar QRIS DANA Bisnis Responsif, Jelas, & Proporsional -->
            <div class="col-md-5 text-center">
                <div class="qris-frame">
                    <img src="/images/qris_dana_bisnis.png" 
                         alt="QRIS DANA Bisnis" 
                         class="img-fluid"
                         onerror="this.onerror=null; this.src='{{ asset('images/qris_dana_bisnis.png') }}';">
                </div>
                <div class="mt-2 text-muted small" style="font-size: 0.75rem;">
                    <span class="badge bg-white text-dark border"><i class="bi bi-shop me-1 text-success"></i> Nama Akun: <strong>KAMPUNG SIDOMULYO</strong></span>
                </div>
            </div>

            <!-- Kolom Kanan: Instruksi Singkat & Konfirmasi Pembayaran -->
            <div class="col-md-7">
                <!-- Instruksi Singkat Memindai QRIS -->
                <div class="alert alert-light border small text-muted p-3 rounded-3 mb-3" style="line-height: 1.55;">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-info-circle-fill text-success me-1"></i> Instruksi Pembayaran:
                    </h6>
                    <ol class="ps-3 mb-0" style="font-size: 0.78rem;">
                        <li class="mb-1">Buka aplikasi pembayaran (DANA, BCA, Mandiri, BRImo, GoPay, OVO, ShopeePay, dll).</li>
                        <li class="mb-1">Pilih menu <strong>Scan / Pindai QRIS</strong> dan arahkan kamera ke gambar QRIS di samping.</li>
                        <li class="mb-1">Masukkan nominal pembayaran sesuai tagihan: <strong>Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}</strong>.</li>
                        <li class="mb-1">Periksa nama penerima (<strong>KAMPUNG SIDOMULYO</strong>) lalu selesaikan pembayaran.</li>
                        <li class="mb-1">Setelah transfer berhasil, isi nama pembayar dan tekan tombol <strong>"Saya Sudah Transfer"</strong> di bawah ini.</li>
                        <li>Status transaksi akan tercatat sebagai <strong>Menunggu Verifikasi</strong> untuk dikonfirmasi oleh admin.</li>
                    </ol>
                </div>

                <!-- Input Nama Pembayar -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nama Pembayar / Penyetor <span class="text-danger">*</span></label>
                    <input type="text" id="namaPembayarDana" class="form-control rounded-3" 
                           placeholder="Contoh: {{ $sppt->objekPajak->wajibPajak->nama_wp ?? 'Nama Pembayar' }}" 
                           value="{{ $sppt->objekPajak->wajibPajak->nama_wp ?? '' }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">Catatan Pembayaran (Opsional)</label>
                    <input type="text" id="catatanDana" class="form-control rounded-3" 
                           placeholder="Contoh: Pembayaran tagihan via QRIS">
                </div>

                <!-- Tombol Aksi: Kembali dan Saya Sudah Transfer -->
                <div class="d-flex flex-column flex-sm-row gap-2 pt-2 border-top">
                    <a href="{{ route('portal.cek', ['nop' => $sppt->nop]) }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 w-100 w-sm-auto">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm w-100 w-sm-auto flex-grow-1" id="btnSayaSudahTransfer" onclick="submitKonfirmasiDana()">
                        <i class="bi bi-check-circle-fill me-1"></i> Saya Sudah Transfer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi pada SPPT ini (Jika Ada) -->
    @if($riwayatSimulasi->isNotEmpty())
        <div class="card p-4 rounded-4 shadow-sm border-0 mt-4 bg-white">
            <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-clock-history text-secondary me-1"></i> Riwayat Transaksi pada SPPT Ini
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Pembayar</th>
                            <th>Kanal</th>
                            <th>Nominal</th>
                            <th>Status Transaksi</th>
                            <th>Waktu Transaksi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSimulasi as $r)
                            <tr>
                                <td><code>{{ $r->order_id }}</code></td>
                                <td>{{ $r->nama_pembayar }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $r->metode_pembayaran }}</span></td>
                                <td class="fw-bold text-success">{{ $r->format_rupiah }}</td>
                                <td>
                                    @if($r->status_pembayaran === 'Berhasil')
                                        <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i> Berhasil</span>
                                    @elseif($r->status_pembayaran === 'Pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-2 py-1"><i class="bi bi-x-circle me-1"></i> Gagal</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $r->waktu_transaksi->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('portal.simulasi.status', $r->id_simulasi) }}" class="btn btn-outline-warning btn-sm py-0 px-2 rounded-pill text-dark" style="font-size: 0.72rem;">
                                        <i class="bi bi-eye"></i> Cek Status
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    const billNominal = {{ (int)$sppt->sisa_tagihan }};

    // Tombol "Saya Sudah Transfer" -> Kirim ke Backend & Tampilkan Status "Menunggu Verifikasi"
    function submitKonfirmasiDana() {
        const namaPembayar = document.getElementById('namaPembayarDana').value.trim();
        const catatan = document.getElementById('catatanDana').value.trim();

        if (!namaPembayar) {
            alert('Silakan masukkan nama pembayar/penyetor.');
            document.getElementById('namaPembayarDana').focus();
            return;
        }

        const btn = document.getElementById('btnSayaSudahTransfer');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan Konfirmasi...';

        fetch("{{ route('portal.simulasi.konfirmasi-dana') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                id_sppt: {{ $sppt->id_sppt }},
                nominal: billNominal > 0 ? billNominal : 1000,
                nama_pembayar: namaPembayar,
                catatan: catatan
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Alihkan ke halaman status yang menampilkan "Menunggu Verifikasi"
                window.location.href = data.redirect_url;
            } else {
                alert('Gagal mengonfirmasi: ' + (data.message || 'Terjadi kesalahan.'));
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Saya Sudah Transfer';
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Saya Sudah Transfer';
            alert('Gagal menghubungi server. Pastikan server aktif.');
        });
    }
</script>
@endsection
