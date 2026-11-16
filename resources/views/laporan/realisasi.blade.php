@extends('layouts.app')

@section('title', 'Laporan Realisasi Penerimaan Kas PBB')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Laporan
        </a>
        <h4 class="fw-bold mb-0">Laporan Penerimaan Kas PBB</h4>
    </div>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i> Cetak Laporan Ini
    </button>
</div>

<!-- Filter Bar (Hidden in Print) -->
<div class="card p-3 mb-4 no-print">
    <form action="{{ route('laporan.realisasi') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" class="form-control" value="{{ $tgl_mulai }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" class="form-control" value="{{ $tgl_akhir }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Metode Pembayaran</label>
            <select name="metode" class="form-select">
                <option value="">-- Semua Metode --</option>
                <option value="Tunai" {{ $metode == 'Tunai' ? 'selected' : '' }}>Tunai Loket</option>
                <option value="Transfer Bank" {{ $metode == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-filter me-1"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- Laporan Print Sheet -->
<div class="card p-4">
    <!-- Header Cetak -->
    <div class="text-center mb-4 border-bottom pb-3">
        <h5 class="fw-bold mb-0">PEMERINTAH KAMPUNG SIDOMULYO</h5>
        <div class="text-muted small">Kecamatan Punggur, Kabupaten Lampung Tengah</div>
        <h6 class="fw-bold text-decoration-underline mt-3 mb-1">LAPORAN MUTASI PENERIMAAN KAS PBB-P2</h6>
        <small class="text-muted">Periode: {{ date('d/m/Y', strtotime($tgl_mulai)) }} s.d. {{ date('d/m/Y', strtotime($tgl_akhir)) }}</small>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light small text-center">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Kode Transaksi</th>
                    <th>Waktu Bayar</th>
                    <th>NOP & Wajib Pajak</th>
                    <th>Dusun</th>
                    <th>Tahun SPPT</th>
                    <th>Metode</th>
                    <th>Penyetor</th>
                    <th class="text-end">Jumlah Masuk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $index => $t)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><code>{{ $t->kode_transaksi }}</code></td>
                        <td>{{ $t->tgl_bayar->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="fw-bold text-success">{{ $t->tagihanSppt->nop ?? '-' }}</div>
                            <small class="text-muted">{{ $t->tagihanSppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</small>
                        </td>
                        <td>{{ $t->tagihanSppt->objekPajak->dusun_op ?? '-' }}</td>
                        <td class="text-center">{{ $t->tagihanSppt->tahun_pajak ?? '-' }}</td>
                        <td><span class="badge {{ $t->metode_bayar === 'Tunai' ? 'bg-info text-dark' : 'bg-primary' }}">{{ $t->metode_bayar }}</span></td>
                        <td>{{ $t->nama_penyetor }}</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Tidak ada transaksi penerimaan kas pada rentang tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="8" class="text-end">TOTAL PENERIMAAN KAS:</td>
                    <td class="text-end text-success fs-6">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Tanda Tangan Cetak -->
    <div class="row text-center mt-5 d-none d-print-flex">
        <div class="col-6">
            <p class="mb-5">Mengetahui,<br><strong>Kepala Kampung Sidomulyo</strong></p>
            <br>
            <p class="fw-bold text-decoration-underline mb-0">{{ $profilDesa->nama_kepala_desa ?? 'Kepala Kampung' }}</p>
        </div>
        <div class="col-6">
            <p class="mb-5">Sidomulyo, {{ date('d F Y') }}<br><strong>Bendahara / Pengelola PBB</strong></p>
            <br>
            <p class="fw-bold text-decoration-underline mb-0">BAPAK KARMIN</p>
        </div>
    </div>
</div>
@endsection

