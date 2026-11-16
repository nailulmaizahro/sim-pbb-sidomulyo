@extends('layouts.app')

@section('title', 'Dashboard Realisasi PBB')

@section('styles')
<style>
    /* KPI Card 3D Styling */
    .kpi-card-3d {
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
    }

    .kpi-card-3d:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 35px -8px rgba(19, 51, 41, 0.12);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .kpi-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    .kpi-icon-target {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .kpi-icon-realisasi {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #059669;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
    }

    .kpi-icon-piutang {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
    }

    .kpi-icon-capaian {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);
    }

    /* 3D Map Container */
    .map-showcase-card {
        border-radius: 18px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid rgba(16, 185, 129, 0.15);
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .map-img-frame {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        background: #0f172a;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.4);
    }

    .map-img-frame img {
        width: 100%;
        height: auto;
        max-height: 340px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }

    .map-img-frame:hover img {
        transform: scale(1.03);
    }

    .dusun-stat-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        transition: all 0.25s ease;
    }

    .dusun-stat-pill:hover {
        border-color: #10b981;
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.1);
        transform: translateX(3px);
    }
</style>
@endsection

@section('content')
<!-- Header & Filter Tahun -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                <i class="bi bi-shield-check me-1"></i> MONITORING REALISASI
            </span>
            <span class="text-muted small">Pemerintah Kampung Sidomulyo</span>
        </div>
        <h4 class="fw-extrabold mb-0 text-dark">Dashboard Realisasi PBB-P2</h4>
    </div>
    
    <div class="d-flex align-items-center bg-white p-2 px-3 rounded-pill border shadow-sm">
        <label class="me-2 text-muted small fw-bold text-nowrap"><i class="bi bi-calendar-event text-success me-1"></i>Tahun Pajak:</label>
        <form action="{{ route('dashboard') }}" method="GET" class="d-inline">
            <select name="tahun" class="form-select form-select-sm border-0 fw-bold text-success bg-light rounded-pill" onchange="this.form.submit()" style="cursor: pointer;">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<!-- Alert Antrean Verifikasi Transfer Bank (Jika Ada) -->
@if($pendingVerifikasi->isNotEmpty())
    <div class="alert alert-warning border-warning d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3 mb-4 rounded-3 shadow-sm">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <div class="bg-warning text-dark rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="bi bi-bell-fill fs-4"></i>
            </div>
            <div>
                <strong class="d-block text-dark">Terdapat {{ $pendingVerifikasi->count() }} Bukti Transfer Bank Menunggu Verifikasi!</strong>
                <span class="small text-muted">Warga telah mengunggah slip setoran transfer melalui portal publik. Silakan lakukan pencocokan mutasi kas desa.</span>
            </div>
        </div>
        <a href="{{ route('pembayaran.index', ['tab' => 'pending']) }}" class="btn btn-warning btn-sm fw-bold px-3 py-2 rounded-pill">
            <i class="bi bi-shield-check me-1"></i> Periksa Antrean Sekarang
        </a>
    </div>
@endif

<!-- Banner Keterangan Demonstrasi Akademik UTS -->
<div class="alert alert-info border-info d-flex align-items-center p-3 mb-4 rounded-3 shadow-sm bg-info bg-opacity-10">
    <i class="bi bi-mortarboard-fill text-info fs-4 me-3 flex-shrink-0"></i>
    <div class="small">
        <strong class="text-dark d-block">Nominal Demonstrasi Akademik UTS (Rp 1.000):</strong>
        <span class="text-muted">Seluruh data tagihan dan transaksi pembayaran pada sistem ini menggunakan nominal demonstrasi sebesar <strong>Rp 1.000</strong> per ketetapan untuk keperluan pengujian teknis perkuliahan, bukan nilai ketetapan PBB sebenarnya.</span>
    </div>
</div>

<!-- Finansial KPI Cards (4 Indikator Utama) -->
<div class="row g-3 mb-4">
    <!-- 1. Target Baku -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card-3d p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Target Ketetapan (Baku)</span>
                    <h4 class="fw-bold text-dark mt-2 mb-1">Rp {{ number_format($totalTarget, 0, ',', '.') }}</h4>
                    <span class="small text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-file-text me-1"></i>Total Baku SPPT {{ $tahun }}
                    </span>
                </div>
                <div class="kpi-icon-box kpi-icon-target">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Realisasi Terkumpul -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card-3d p-3" style="border-bottom: 3px solid #10b981;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Realisasi Terkumpul</span>
                    <h4 class="fw-bold text-success mt-2 mb-1">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</h4>
                    <span class="small text-success fw-semibold" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>{{ $countLunas }} SPPT Lunas
                    </span>
                </div>
                <div class="kpi-icon-box kpi-icon-realisasi">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Sisa Piutang / Tunggakan -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card-3d p-3" style="border-bottom: 3px solid #ef4444;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Sisa Piutang / Tunggakan</span>
                    <h4 class="fw-bold text-danger mt-2 mb-1">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h4>
                    <span class="small text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-clock-history me-1"></i>{{ $countBelumLunas }} SPPT Belum Lunas
                    </span>
                </div>
                <div class="kpi-icon-box kpi-icon-piutang">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Persentase Capaian -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card-3d p-3" style="border-bottom: 3px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Persentase Capaian</span>
                    <h4 class="fw-bold text-dark mt-2 mb-1">{{ $persenCapaian }}%</h4>
                    <span class="small fw-semibold text-warning" style="font-size: 0.75rem;">
                        <i class="bi bi-speedometer me-1"></i>Efektivitas Pemungutan
                    </span>
                </div>
                <div class="kpi-icon-box kpi-icon-capaian">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Bar Capaian Realisasi Desa -->
<div class="card p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center small mb-1">
        <span class="fw-bold text-secondary">
            <i class="bi bi-graph-up-arrow text-success me-1"></i> Progres Pelunasan PBB Kampung Sidomulyo (Tahun {{ $tahun }})
        </span>
        <span class="fw-bold text-success">{{ $persenCapaian }}% dari Target Baku</span>
    </div>
    <div class="progress" style="height: 12px; border-radius: 6px;">
        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $persenCapaian }}%;"></div>
    </div>
    <div class="d-flex flex-wrap justify-content-between mt-2 pt-2 border-top small text-muted">
        <span><i class="bi bi-check-circle-fill text-success me-1"></i> Lunas: <strong>{{ $countLunas }} SPPT</strong></span>
        <span><i class="bi bi-pie-chart-fill text-warning me-1"></i> Cicilan: <strong>{{ $countCicilan }} SPPT</strong></span>
        <span><i class="bi bi-x-circle-fill text-danger me-1"></i> Belum Lunas: <strong>{{ $countBelumLunas }} SPPT</strong></span>
        <span><i class="bi bi-files text-secondary me-1"></i> Total Ketetapan: <strong>{{ $countTotal }} SPPT</strong></span>
    </div>
</div>

<!-- Visualisasi Wilayah Dusun: Peta 3D & Sebaran Realisasi Aktual -->
<div class="map-showcase-card p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div>
            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem;">
                <i class="bi bi-geo-alt-fill me-1"></i> Visualisasi Geospasial Dusun 3D
            </span>
            <h5 class="fw-bold text-dark mt-1 mb-0">Peta Digital & Sebaran Realisasi Wilayah Kampung Sidomulyo</h5>
            <small class="text-muted">Pemetaan lingkungan pemukiman dan capaian pembayaran PBB per Dusun tahun {{ $tahun }}</small>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                <i class="bi bi-houses-fill text-success me-1"></i> Wilayah Dusun 1 s/d Dusun 3
            </span>
        </div>
    </div>

    <div class="row g-4 align-items-center">
        <!-- Kolom Kiri: Ilustrasi Peta Dusun 3D -->
        <div class="col-lg-7">
            <div class="map-img-frame">
                <img src="{{ asset('assets/images/3d/peta-desa-3d.png') }}" 
                     alt="Peta Wilayah Dusun Kampung Sidomulyo 3D" 
                     class="img-fluid">
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 px-1 text-muted small" style="font-size: 0.75rem;">
                <span><i class="bi bi-info-circle me-1"></i> Penanda lokasi: Dusun 1, Dusun 2, dan Dusun 3 Kampung Sidomulyo</span>
                <span class="text-success"><i class="bi bi-check-all me-1"></i> Data Real-Time Database</span>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Metrik Aktual per Dusun -->
        <div class="col-lg-5">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill text-success me-1"></i> Capaian Realisasi Berdasarkan Dusun:</h6>
            
            <div class="d-flex flex-column gap-2">
                @forelse($rekapDusun as $dusun)
                    @php
                        $persen = $dusun->target_dusun > 0 ? round(($dusun->realisasi_dusun / $dusun->target_dusun) * 100, 1) : 0;
                        $colorClass = $persen >= 75 ? 'success' : ($persen >= 40 ? 'warning' : 'danger');
                    @endphp
                    <div class="dusun-stat-pill">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">
                                <i class="bi bi-geo-fill text-{{ $colorClass }} me-1"></i> {{ $dusun->dusun_op }}
                            </span>
                            <span class="badge bg-{{ $colorClass }} text-{{ $persen >= 40 && $persen < 75 ? 'dark' : 'white' }} rounded-pill px-2 py-1">
                                {{ $persen }}% Capaian
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-{{ $colorClass }}" style="width: {{ $persen }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted" style="font-size: 0.75rem;">
                            <span>Target: <strong>Rp {{ number_format($dusun->target_dusun, 0, ',', '.') }}</strong></span>
                            <span class="text-success">Terkumpul: <strong>Rp {{ number_format($dusun->realisasi_dusun, 0, ',', '.') }}</strong></span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted pt-1 mt-1 border-top" style="font-size: 0.72rem;">
                            <span>{{ $dusun->total_sppt }} Total SPPT</span>
                            <span class="text-success fw-semibold">{{ $dusun->lunas_count }} Lunas</span>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border small text-center text-muted mb-0">
                        Belum ada data ketetapan dusun untuk tahun {{ $tahun }}.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Visualisasi Grafik Data (Chart.js Modern) -->
<div class="row g-4 mb-4">
    <!-- Bar Chart Komparasi: Target vs Realisasi per Dusun -->
    <div class="col-lg-8">
        <div class="card h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark">
                    <i class="bi bi-bar-chart-line-fill text-success me-1"></i> Grafik Target Baku vs Realisasi per Dusun (Tahun {{ $tahun }})
                </span>
                <span class="badge bg-light text-secondary border">Diagram Batang Komparatif</span>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="chartDusunBar"></canvas>
            </div>
        </div>
    </div>

    <!-- Donut Chart: Proporsi Status SPPT -->
    <div class="col-lg-4">
        <div class="card h-100 p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark">
                    <i class="bi bi-pie-chart-fill text-warning me-1"></i> Proporsi Pelunasan SPPT
                </span>
                <span class="badge bg-light text-secondary border">Status Pajak</span>
            </div>
            <div style="height: 280px; position: relative;" class="d-flex justify-content-center align-items-center">
                <canvas id="chartStatusDonut"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Rekapitulasi Tabel & Riwayat Transaksi -->
<div class="row g-4">
    <!-- Rekapitulasi per Dusun -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-table me-1 text-success"></i> Rekapitulasi Pembukuan per Dusun (Tahun {{ $tahun }})</span>
                <a href="{{ route('laporan.dispenda', ['tahun' => $tahun]) }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                    <i class="bi bi-printer me-1"></i> Cetak Format Dispenda
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Dusun</th>
                            <th class="text-center">Jumlah SPPT</th>
                            <th class="text-end">Target (Baku)</th>
                            <th class="text-end">Realisasi</th>
                            <th class="text-end">Sisa Tunggakan</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapDusun as $dusun)
                            @php
                                $dusunPersen = $dusun->target_dusun > 0 ? round(($dusun->realisasi_dusun / $dusun->target_dusun) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark">
                                    <i class="bi bi-geo-alt text-success me-1"></i> {{ $dusun->dusun_op }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $dusun->total_sppt }} SPPT</span>
                                    <small class="text-success d-block" style="font-size: 0.7rem;">({{ $dusun->lunas_count }} Lunas)</small>
                                </td>
                                <td class="text-end text-muted">Rp {{ number_format($dusun->target_dusun, 0, ',', '.') }}</td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($dusun->realisasi_dusun, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp {{ number_format($dusun->sisa_dusun, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $dusunPersen >= 75 ? 'bg-success' : ($dusunPersen >= 40 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-2 py-1">
                                        {{ $dusunPersen }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data DHKP untuk tahun {{ $tahun }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi Terbaru -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-clock-history me-1 text-primary"></i> Transaksi Kasir Terbaru</span>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-link text-decoration-none text-success fw-semibold">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($transaksiTerbaru as $trx)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div class="fw-bold text-dark">{{ $trx->tagihanSppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</div>
                                <small class="text-muted d-block">
                                    NOP: {{ $trx->tagihanSppt->nop ?? '-' }} | <span class="badge bg-light text-secondary border">{{ $trx->metode_bayar }}</span>
                                </small>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <i class="bi bi-calendar3"></i> {{ $trx->tgl_bayar->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-success d-block">+Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}</span>
                                <a href="{{ route('pembayaran.kuitansi', $trx->id_bayar) }}" class="btn btn-outline-secondary btn-sm py-0 px-2 mt-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="bi bi-receipt"></i> Kuitansi
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">Belum ada transaksi pembayaran masuk.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Data Bar Chart Dusun Komparasi
    const dusunLabels = {!! json_encode($rekapDusun->pluck('dusun_op')) !!};
    const targetData = {!! json_encode($rekapDusun->pluck('target_dusun')) !!};
    const realisasiData = {!! json_encode($rekapDusun->pluck('realisasi_dusun')) !!};

    const ctxBar = document.getElementById('chartDusunBar').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: dusunLabels,
            datasets: [
                {
                    label: 'Target Baku (Rp)',
                    data: targetData,
                    backgroundColor: 'rgba(59, 130, 246, 0.75)', // Biru Modern
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    barPercentage: 0.65,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Realisasi Setor (Rp)',
                    data: realisasiData,
                    backgroundColor: 'rgba(16, 185, 129, 0.85)', // Hijau Emerald
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    barPercentage: 0.65,
                    categoryPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#133329',
                    titleFont: { family: "'Plus Jakarta Sans', sans-serif" },
                    bodyFont: { family: "'Plus Jakarta Sans', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", weight: '500' } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif" },
                        callback: function(value) {
                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                        }
                    }
                }
            }
        }
    });

    // 2. Data Donut Chart Status SPPT (Modern Rounded & Palette)
    const ctxDonut = document.getElementById('chartStatusDonut').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Cicilan', 'Belum Lunas'],
            datasets: [{
                data: [{{ $countLunas }}, {{ $countCicilan }}, {{ $countBelumLunas }}],
                backgroundColor: [
                    '#10b981', // Hijau Emerald Lunas
                    '#f59e0b', // Emas/Kuning Cicilan
                    '#ef4444'  // Merah Belum Lunas
                ],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16
                    }
                },
                tooltip: {
                    backgroundColor: '#133329',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' SPPT';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
</script>
@endsection
