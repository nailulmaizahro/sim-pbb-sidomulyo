<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $transaksi->kode_transaksi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }
        .kuitansi-wrapper {
            max-width: 820px;
            margin: 30px auto;
            background: #fff;
            padding: 35px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #222;
            position: relative;
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        .kop-surat h5 {
            font-size: 1.15rem;
            margin-bottom: 2px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .kop-surat h4 {
            font-size: 1.35rem;
            margin-bottom: 2px;
            font-weight: 800;
        }
        .kop-surat p {
            margin-bottom: 0;
            font-size: 0.88rem;
        }
        .judul-kuitansi {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-kuitansi h5 {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
        }
        .table-data th, .table-data td {
            padding: 4px 8px;
            font-size: 0.95rem;
        }
        .tanda-tangan {
            margin-top: 30px;
            position: relative;
        }
        /* Stempel Resmi Digital */
        .stempel-resmi {
            position: absolute;
            right: 45px;
            top: 15px;
            width: 120px;
            height: 120px;
            border: 3px double #1a365d;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #1a365d;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
            font-weight: bold;
            transform: rotate(-15deg);
            opacity: 0.85;
            pointer-events: none;
            box-shadow: 0 0 0 1px #1a365d;
        }
        .stempel-resmi .stempel-text-top {
            font-size: 0.55rem;
            letter-spacing: 0.5px;
        }
        .stempel-resmi .stempel-core {
            font-size: 1rem;
            border-top: 1px solid #1a365d;
            border-bottom: 1px solid #1a365d;
            padding: 2px 6px;
            margin: 2px 0;
            color: #1a365d;
            letter-spacing: 1px;
        }
        .stempel-resmi .stempel-text-bot {
            font-size: 0.55rem;
            letter-spacing: 0.5px;
        }
        @media print {
            body {
                background: none;
            }
            .kuitansi-wrapper {
                margin: 0;
                padding: 15px;
                box-shadow: none;
                border: 2px solid #000;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="text-center my-3 no-print">
        <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
            <i class="bi bi-printer me-1"></i> Cetak Kuitansi Ini
        </button>
    </div>

    <div class="kuitansi-wrapper">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <h5>PEMERINTAH KABUPATEN LAMPUNG TENGAH</h5>
            <h5>KECAMATAN PUNGGUR</h5>
            <h4>PEMERINTAH KAMPUNG SIDOMULYO</h4>
            <p>{{ $profilDesa->alamat_kantor ?? 'Jl. Raya Sidomulyo, Kec. Punggur, Kab. Lampung Tengah' }}</p>
        </div>

        <!-- Judul & Nomor -->
        <div class="judul-kuitansi">
            <h5>TANDA BUKTI PEMBAYARAN PBB-P2</h5>
            <small>Nomor Bukti: <strong>{{ $transaksi->kode_transaksi }}</strong></small>
        </div>

        <div class="row align-items-start mb-3">
            <div class="col-9">
                <!-- Rincian Data -->
                <table class="table table-borderless table-data mb-0">
                    <tr>
                        <th style="width: 35%;">Telah Terima Dari</th>
                        <td style="width: 3%;">:</td>
                        <td class="fw-bold">{{ strtoupper($transaksi->nama_penyetor) }}</td>
                    </tr>
                    <tr>
                        <th>Wajib Pajak (Pemilik)</th>
                        <td>:</td>
                        <td>{{ $transaksi->tagihanSppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Objek Pajak (NOP)</th>
                        <td>:</td>
                        <td class="fw-bold">{{ $transaksi->tagihanSppt->nop ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Letak / Objek Pajak</th>
                        <td>:</td>
                        <td>{{ $transaksi->tagihanSppt->objekPajak->letak_objek ?? '-' }}, Dusun {{ $transaksi->tagihanSppt->objekPajak->dusun_op ?? '-' }} (RT {{ $transaksi->tagihanSppt->objekPajak->rt_op ?? '-' }}/RW {{ $transaksi->tagihanSppt->objekPajak->rw_op ?? '-' }})</td>
                    </tr>
                    <tr>
                        <th>Tahun Masa Pajak</th>
                        <td>:</td>
                        <td class="fw-bold">SPPT TAHUN {{ $transaksi->tagihanSppt->tahun_pajak ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Metode Pembayaran</th>
                        <td>:</td>
                        <td>{{ $transaksi->metode_bayar }} {{ $transaksi->bank_tujuan ? "({$transaksi->bank_tujuan})" : "" }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Bayar</th>
                        <td>:</td>
                        <td>{{ $transaksi->tgl_bayar->format('d F Y - H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <th>Status Ketetapan</th>
                        <td>:</td>
                        <td>
                            <strong>
                                @if($transaksi->tagihanSppt->status_bayar === 'Lunas')
                                    LUNAS
                                @else
                                    DICICIL (Sisa Tagihan: Rp {{ number_format($transaksi->tagihanSppt->sisa_tagihan, 0, ',', '.') }})
                                @endif
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Visual QR Code Verifikasi -->
            <div class="col-3 text-center pt-2">
                <div class="p-2 border rounded bg-light d-inline-block">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=95x95&data={{ urlencode(route('portal.cek', ['nop' => $transaksi->tagihanSppt->nop])) }}" 
                         alt="QR Code Verifikasi" 
                         width="95" height="95">
                    <div style="font-size: 0.65rem; font-family: Arial; margin-top: 4px; color: #555;">
                        <i class="bi bi-qr-code-scan"></i> Scan Verifikasi
                    </div>
                </div>
            </div>
        </div>

        <!-- Box Nominal Uang -->
        <div class="border border-dark p-3 my-2 text-center bg-light">
            <span class="text-muted d-block small">JUMLAH PEMBAYARAN:</span>
            <span class="fs-4 fw-bold text-dark">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }},-</span>
        </div>

        <!-- Tanda Tangan & Stempel Resmi -->
        <div class="row tanda-tangan text-center">
            <div class="col-6">
                <p class="mb-5">Penyetor,</p>
                <p class="fw-bold text-decoration-underline mb-0">{{ $transaksi->nama_penyetor }}</p>
                <small class="text-muted">Pembayar</small>
            </div>
            <div class="col-6 position-relative">
                <p class="mb-0">Sidomulyo, {{ $transaksi->tgl_bayar->format('d F Y') }}</p>
                <p class="mb-5">Petugas Penerima PBB,</p>
                <p class="fw-bold text-decoration-underline mb-0">{{ $transaksi->petugasVerifikator->nama_lengkap ?? 'Bapak Karmin' }}</p>
                <small class="text-muted">Pengelola Pajak Kampung</small>

                <!-- Elemen Visual Stempel Resmi Digital -->
                <div class="stempel-resmi">
                    <div class="stempel-text-top">Pemerintah Kampung</div>
                    <div class="stempel-core">LUNAS PBB</div>
                    <div class="stempel-text-bot">Sidomulyo • Punggur</div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 pt-3 border-top small text-muted">
            <em>Simpan bukti pembayaran ini sebagai tanda pelunasan Pajak Bumi dan Bangunan (PBB-P2) Kampung Sidomulyo. Keaslian tanda bukti ini dapat diverifikasi melalui pemindaian QR Code di atas.</em>
            <div class="fst-italic text-secondary mt-1" style="font-size: 0.72rem;">
                * Catatan: Nominal pembayaran yang tercantum merupakan nominal demonstrasi akademik UTS (Rp 1.000) untuk pengujian sistem, bukan ketetapan pajak sebenarnya.
            </div>
        </div>
    </div>
</body>
</html>
