@extends('layouts.app')

@section('title', 'Detail Objek Pajak: ' . $objekPajak->nop)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('objek-pajak.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Objek Pajak
        </a>
        <h4 class="fw-bold mb-0">NOP: {{ $objekPajak->nop }}</h4>
    </div>
    <div>
        <a href="{{ route('sppt.create', ['nop' => $objekPajak->nop]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-square me-1"></i> Terbitkan Tagihan SPPT
        </a>
        <a href="{{ route('objek-pajak.edit', $objekPajak->nop) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Objek
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Info Objek & Pemilik -->
    <div class="col-lg-5">
        <div class="card p-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3 text-success">
                <i class="bi bi-info-circle me-1"></i> Spesifikasi Bidang Properti
            </h6>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <th class="text-muted" style="width: 40%;">Pemilik (WP)</th>
                    <td>
                        <a href="{{ route('wajib-pajak.show', $objekPajak->id_wp) }}" class="fw-bold text-success text-decoration-none">
                            {{ $objekPajak->wajibPajak->nama_wp ?? '-' }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <th class="text-muted">NIK Pemilik</th>
                    <td><code>{{ $objekPajak->wajibPajak->nik ?? '-' }}</code></td>
                </tr>
                <tr>
                    <th class="text-muted">Letak Objek</th>
                    <td>{{ $objekPajak->letak_objek }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Dusun</th>
                    <td>{{ $objekPajak->dusun_op }}</td>
                </tr>
                <tr>
                    <th class="text-muted">RT / RW</th>
                    <td>RT {{ $objekPajak->rt_op }} / RW {{ $objekPajak->rw_op }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Luas Bumi</th>
                    <td>{{ number_format($objekPajak->luas_bumi, 0, ',', '.') }} m² (Kelas: {{ $objekPajak->kelas_bumi ?? '-' }})</td>
                </tr>
                <tr>
                    <th class="text-muted">Luas Bangunan</th>
                    <td>{{ number_format($objekPajak->luas_bangunan, 0, ',', '.') }} m² (Kelas: {{ $objekPajak->kelas_bangunan ?? '-' }})</td>
                </tr>
                <tr>
                    <th class="text-muted">NJOP Bumi</th>
                    <td>Rp {{ number_format($objekPajak->njop_bumi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th class="text-muted">NJOP Bangunan</th>
                    <td>Rp {{ number_format($objekPajak->njop_bangunan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Keterangan</th>
                    <td>{{ $objekPajak->keterangan ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Riwayat SPPT -->
    <div class="col-lg-7">
        <div class="card p-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                <i class="bi bi-clock-history me-1 text-primary"></i> Riwayat Penetapan SPPT Tahunan
            </h6>

            @forelse($objekPajak->tagihanSppt as $sppt)
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold fs-6 text-success">SPPT Tahun {{ $sppt->tahun_pajak }}</span>
                        {!! $sppt->badge_status !!}
                    </div>

                    <div class="row g-2 small mb-3">
                        <div class="col-4">
                            <span class="text-muted d-block">Pokok Terhutang:</span>
                            <strong>Rp {{ number_format($sppt->total_tagihan, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-4">
                            <span class="text-muted d-block">Terbayar:</span>
                            <strong class="text-success">Rp {{ number_format($sppt->total_terbayar, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-4">
                            <span class="text-muted d-block">Sisa Tagihan:</span>
                            <strong class="text-danger">Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if($sppt->status_bayar !== 'Lunas')
                        <div class="text-end">
                            <a href="{{ route('pembayaran.create', ['id_sppt' => $sppt->id_sppt]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-cash-stack me-1"></i> Bayar di Loket Balai Desa
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted">Belum ada penetapan tagihan SPPT pada NOP ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

