<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Pelunasan PBB ke Dispenda - Tahun {{ $tahun }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }
        .report-page {
            max-width: 950px;
            margin: 25px auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 25px;
            text-align: center;
        }
        .kop-surat h5 {
            font-size: 1.15rem;
            margin-bottom: 2px;
            font-weight: bold;
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
        .table-report th, .table-report td {
            border: 1px solid #000 !important;
            padding: 7px 10px;
            font-size: 0.95rem;
        }
        @media print {
            body {
                background: none;
            }
            .report-page {
                margin: 0;
                padding: 10px;
                box-shadow: none;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="text-center my-3 no-print">
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Laporan
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
            <i class="bi bi-printer me-1"></i> Cetak Laporan Resmi Dispenda
        </button>
    </div>

    <div class="report-page">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <h5>PEMERINTAH KABUPATEN LAMPUNG TENGAH</h5>
            <h5>KECAMATAN PUNGGUR</h5>
            <h4>PEMERINTAH KAMPUNG SIDOMULYO</h4>
            <p>{{ $profilDesa->alamat_kantor ?? 'Jl. Raya Sidomulyo, Kec. Punggur, Kab. Lampung Tengah' }}</p>
        </div>

        <div class="text-center mb-4">
            <h5 class="fw-bold mb-1 text-decoration-underline">LAPORAN REKAPITULASI REALISASI PENERIMAAN PBB-P2</h5>
            <div class="fw-semibold">BUKTI PELUNASAN KAMPUNG KE DINAS PENDAPATAN DAERAH (DISPENDA)</div>
            <small class="text-muted">Masa Pajak: Tahun Anggaran {{ $tahun }}</small>
        </div>

        <table class="table table-report table-bordered align-middle mb-4">
            <thead class="text-center" style="background-color: #f0f0f0;">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Wilayah / Dusun</th>
                    <th style="width: 12%;">Jumlah SPPT</th>
                    <th style="width: 12%;">Lunas</th>
                    <th class="text-end" style="width: 20%;">Target Ketetapan (Baku)</th>
                    <th class="text-end" style="width: 20%;">Realisasi Setoran</th>
                    <th class="text-end" style="width: 20%;">Sisa Piutang</th>
                    <th class="text-center" style="width: 10%;">% Capaian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapDusun as $idx => $d)
                    @php
                        $persen = $d->target_baku > 0 ? round(($d->realisasi_setor / $d->target_baku) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="fw-bold">{{ $d->dusun_op }}</td>
                        <td class="text-center">{{ $d->total_buku }}</td>
                        <td class="text-center">{{ $d->total_lunas }}</td>
                        <td class="text-end">Rp {{ number_format($d->target_baku, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($d->realisasi_setor, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($d->sisa_tunggakan, 0, ',', '.') }}</td>
                        <td class="text-center fw-bold">{{ $persen }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Belum ada data DHKP untuk tahun {{ $tahun }}.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="fw-bold" style="background-color: #f8f8f8;">
                <tr>
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ $grandTotalBuku }}</td>
                    <td class="text-center">{{ $grandLunas }}</td>
                    <td class="text-end">Rp {{ number_format($grandTarget, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($grandRealisasi, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($grandSisa, 0, ',', '.') }}</td>
                    <td class="text-center">
                        {{ $grandTarget > 0 ? round(($grandRealisasi / $grandTarget) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan Pengesahan -->
        <div class="row text-center mt-5" style="page-break-inside: avoid;">
            <div class="col-6">
                <p class="mb-5">Mengetahui,<br><strong>Kepala Kampung Sidomulyo</strong></p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">{{ $profilDesa->nama_kepala_desa ?? 'KEPALA KAMPUNG SIDOMULYO' }}</p>
                <small class="text-muted">{{ $profilDesa->nip_kepala_desa ? 'NIP. ' . $profilDesa->nip_kepala_desa : 'Pemerintah Kampung Sidomulyo' }}</small>
            </div>
            <div class="col-6">
                <p class="mb-5">Sidomulyo, {{ date('d F Y') }}<br><strong>Pengelola Pajak Bumi dan Bangunan</strong></p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">BAPAK KARMIN</p>
                <small class="text-muted">Petugas Pajak Kampung Sidomulyo</small>
            </div>
        </div>
    </div>
</body>
</html>

