@extends('layouts.app')

@section('title', 'Laporan Daftar Tunggakan Warga')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Laporan
        </a>
        <h4 class="fw-bold mb-0">Laporan Daftar Tunggakan Wajib Pajak</h4>
    </div>
    <button onclick="window.print()" class="btn btn-danger">
        <i class="bi bi-printer me-1"></i> Cetak Daftar Tunggakan
    </button>
</div>

<!-- Filter Bar -->
<div class="card p-3 mb-4 no-print">
    <form action="{{ route('laporan.tunggakan') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Tahun Pajak</label>
            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}">
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-semibold">Wilayah Dusun</label>
            <input type="text" name="dusun" class="form-control" placeholder="Semua Dusun (atau ketik: Dusun 1)" value="{{ $dusun }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="bi bi-filter me-1"></i> Filter Data
            </button>
        </div>
    </form>
</div>

<!-- Report Sheet -->
<div class="card p-4">
    <div class="text-center mb-4 border-bottom pb-3">
        <h5 class="fw-bold mb-0">PEMERINTAH KAMPUNG SIDOMULYO</h5>
        <div class="text-muted small">Kecamatan Punggur, Kabupaten Lampung Tengah</div>
        <h6 class="fw-bold text-danger text-decoration-underline mt-3 mb-1">DAFTAR PIUTANG & TUNGGAKAN PAJAK BUMI DAN BANGUNAN (PBB-P2)</h6>
        <small class="text-muted">Masa Pajak: Tahun {{ $tahun }} {{ $dusun ? "- {$dusun}" : "" }}</small>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light small text-center">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>NOP</th>
                    <th>Nama Wajib Pajak</th>
                    <th>Alamat Domisili</th>
                    <th>Dusun / RT</th>
                    <th class="text-end">Pokok PBB</th>
                    <th class="text-end">Sudah Terbayar</th>
                    <th class="text-end">Sisa Piutang</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarTunggakan as $index => $sppt)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><code class="text-dark">{{ $sppt->nop }}</code></td>
                        <td class="fw-bold">{{ $sppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</td>
                        <td>{{ $sppt->objekPajak->wajibPajak->alamat_wp ?? '-' }}</td>
                        <td>{{ $sppt->objekPajak->dusun_op ?? '-' }} (RT {{ $sppt->objekPajak->rt_op ?? '-' }})</td>
                        <td class="text-end">Rp {{ number_format($sppt->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($sppt->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-danger">Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}</td>
                        <td class="text-center">{!! $sppt->badge_status !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Luar biasa! Tidak ada tunggakan pajak pada kriteria ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="7" class="text-end">TOTAL SISA PIUTANG TERTUNGGAK:</td>
                    <td class="text-end text-danger fs-6">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                    <td></td>
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
            <p class="mb-5">Sidomulyo, {{ date('d F Y') }}<br><strong>Pengelola Pajak Kampung</strong></p>
            <br>
            <p class="fw-bold text-decoration-underline mb-0">BAPAK KARMIN</p>
        </div>
    </div>
</div>
@endsection

