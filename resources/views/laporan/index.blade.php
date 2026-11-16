@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Pusat Laporan & Rekapitulasi PBB</h4>
    <p class="text-muted small mb-0">Cetak dokumen pelaporan resmi kampung dan arsip pertanggungjawaban ke Dispenda</p>
</div>

<div class="row g-4">
    <!-- Card 1: Laporan Dispenda (Kebutuhan Bab I) -->
    <div class="col-md-4">
        <div class="card h-100 p-4 border-top border-4 border-success">
            <div class="fs-1 text-success mb-2"><i class="bi bi-file-earmark-check-fill"></i></div>
            <h5 class="fw-bold text-dark">Laporan Rekapitulasi Dispenda</h5>
            <p class="text-muted small">
                Format laporan resmi bukti pelunasan dan realisasi penerimaan PBB per dusun yang wajib diserahkan pihak kampung ke Dinas Pendapatan Daerah (Dispenda) Kabupaten Lampung Tengah.
            </p>
            <div class="mt-auto">
                <form action="{{ route('laporan.dispenda') }}" method="GET" class="row g-2">
                    <div class="col-8">
                        <select name="tahun" class="form-select form-select-sm">
                            @foreach($daftarTahun as $t)
                                <option value="{{ $t }}">Tahun {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-success btn-sm w-100">Buka</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Card 2: Laporan Realisasi Kas Harian/Bulanan -->
    <div class="col-md-4">
        <div class="card h-100 p-4 border-top border-4 border-primary">
            <div class="fs-1 text-primary mb-2"><i class="bi bi-cash-stack"></i></div>
            <h5 class="fw-bold text-dark">Laporan Penerimaan Kas PBB</h5>
            <p class="text-muted small">
                Rincian mutasi uang kas pembayaran PBB yang masuk ke kasir balai desa maupun transfer bank berdasarkan rentang tanggal tertentu.
            </p>
            <div class="mt-auto">
                <a href="{{ route('laporan.realisasi') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-calendar-range me-1"></i> Buka Laporan Realisasi Kas
                </a>
            </div>
        </div>
    </div>

    <!-- Card 3: Laporan Daftar Tunggakan Piutang -->
    <div class="col-md-4">
        <div class="card h-100 p-4 border-top border-4 border-danger">
            <div class="fs-1 text-danger mb-2"><i class="bi bi-person-x-fill"></i></div>
            <h5 class="fw-bold text-dark">Laporan Daftar Tunggakan Warga</h5>
            <p class="text-muted small">
                Daftar nama-nama wajib pajak yang belum lunas atau masih mencicil per dusun/RT untuk mempermudah penagihan langsung di lapangan oleh perangkat desa.
            </p>
            <div class="mt-auto">
                <a href="{{ route('laporan.tunggakan') }}" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-list-check me-1"></i> Buka Laporan Tunggakan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

