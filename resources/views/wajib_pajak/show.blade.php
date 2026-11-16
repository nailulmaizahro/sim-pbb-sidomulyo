@extends('layouts.app')

@section('title', 'Detail Wajib Pajak: ' . $wajibPajak->nama_wp)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('wajib-pajak.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Wajib Pajak
        </a>
        <h4 class="fw-bold mb-0">{{ $wajibPajak->nama_wp }}</h4>
    </div>
    <div>
        <a href="{{ route('objek-pajak.create', ['id_wp' => $wajibPajak->id_wp]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Daftarkan Objek Pajak Baru untuk Warga Ini
        </a>
        <a href="{{ route('wajib-pajak.edit', $wajibPajak->id_wp) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Data Warga
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Profil Wajib Pajak -->
    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3 text-success">
                <i class="bi bi-person-badge me-1"></i> Biodata Subjek Pajak
            </h6>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <th class="text-muted" style="width: 35%;">Nama</th>
                    <td class="fw-bold">{{ $wajibPajak->nama_wp }}</td>
                </tr>
                <tr>
                    <th class="text-muted">NIK</th>
                    <td><code>{{ $wajibPajak->nik ?? '-' }}</code></td>
                </tr>
                <tr>
                    <th class="text-muted">Alamat</th>
                    <td>{{ $wajibPajak->alamat_wp }}</td>
                </tr>
                <tr>
                    <th class="text-muted">RT / RW</th>
                    <td>RT {{ $wajibPajak->rt_wp }} / RW {{ $wajibPajak->rw_wp }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Dusun</th>
                    <td>{{ $wajibPajak->dusun_wp ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-muted">No. Telepon</th>
                    <td>{{ $wajibPajak->no_hp ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Total Objek</th>
                    <td><span class="badge bg-secondary">{{ $wajibPajak->objekPajak->count() }} Bidang Tanah/Bangunan</span></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Daftar Objek Pajak yang Dimiliki (1 to N) -->
    <div class="col-lg-8">
        <div class="card p-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                <i class="bi bi-buildings me-1 text-primary"></i> Daftar Bidang Objek Pajak (NOP) Milik Warga Ini
            </h6>

            @forelse($wajibPajak->objekPajak as $index => $op)
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge bg-dark me-2">Bidang #{{ $index + 1 }}</span>
                            <a href="{{ route('objek-pajak.show', $op->nop) }}" class="fw-bold text-success text-decoration-none">
                                NOP: {{ $op->nop }}
                            </a>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('sppt.create', ['nop' => $op->nop]) }}" class="btn btn-outline-primary" title="Terbitkan SPPT Baru">
                                <i class="bi bi-plus-square me-1"></i> Terbitkan SPPT
                            </a>
                            <a href="{{ route('objek-pajak.edit', $op->nop) }}" class="btn btn-outline-secondary" title="Edit Objek">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>

                    <div class="row g-2 small text-muted mb-2">
                        <div class="col-md-6">
                            <i class="bi bi-geo-alt"></i> Letak: <strong>{{ $op->lokasi_lengkap }}</strong>
                        </div>
                        <div class="col-md-6">
                            <i class="bi bi-aspect-ratio"></i> Luas Bumi/Bangunan: <strong>{{ $op->luas_bumi }} m² / {{ $op->luas_bangunan }} m²</strong>
                        </div>
                    </div>

                    <!-- Riwayat SPPT pada NOP ini -->
                    @if($op->tagihanSppt->isNotEmpty())
                        <div class="table-responsive bg-white rounded border">
                            <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tahun</th>
                                        <th class="text-end">Ketetapan</th>
                                        <th class="text-end">Terbayar</th>
                                        <th class="text-end">Sisa</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($op->tagihanSppt as $sppt)
                                        <tr>
                                            <td class="fw-bold">{{ $sppt->tahun_pajak }}</td>
                                            <td class="text-end">Rp {{ number_format($sppt->total_tagihan, 0, ',', '.') }}</td>
                                            <td class="text-end text-success">Rp {{ number_format($sppt->total_terbayar, 0, ',', '.') }}</td>
                                            <td class="text-end text-danger fw-semibold">Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}</td>
                                            <td class="text-center">{!! $sppt->badge_status !!}</td>
                                            <td class="text-center">
                                                @if($sppt->status_bayar !== 'Lunas')
                                                    <a href="{{ route('pembayaran.create', ['id_sppt' => $sppt->id_sppt]) }}" class="btn btn-success btn-sm py-0 px-2" style="font-size: 0.72rem;">
                                                        <i class="bi bi-cash"></i> Bayar
                                                    </a>
                                                @else
                                                    <span class="text-success small"><i class="bi bi-check-all"></i> Terlunasi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <small class="text-muted d-block fst-italic">Belum ada tagihan SPPT yang diterbitkan untuk NOP ini.</small>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    Warga ini belum memiliki bidang objek pajak yang terdaftar.
                    <div class="mt-2">
                        <a href="{{ route('objek-pajak.create', ['id_wp' => $wajibPajak->id_wp]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Daftarkan Sekarang
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

