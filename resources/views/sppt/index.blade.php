@extends('layouts.app')

@section('title', 'Data SPPT & DHKP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Himpunan Ketetapan Pajak (DHKP)</h4>
        <p class="text-muted small mb-0">Kelola ketetapan SPPT PBB tahunan dan status pelunasan</p>
    </div>
    <div>
        <a href="{{ route('sppt.import') }}" class="btn btn-outline-success me-2">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Impor Massal DHKP (CSV)
        </a>
        <a href="{{ route('sppt.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Terbitkan SPPT Manual
        </a>
    </div>
</div>

<div class="alert alert-info border-info d-flex align-items-center p-3 mb-4 rounded-3 shadow-sm bg-info bg-opacity-10">
    <i class="bi bi-mortarboard-fill text-info fs-4 me-3 flex-shrink-0"></i>
    <div class="small">
        <strong class="text-dark d-block">Nominal Demonstrasi Akademik UTS:</strong>
        <span class="text-muted">Ketetapan nominal pada seluruh daftar tagihan SPPT disesuaikan menjadi <strong>Rp 1.000</strong> per ketetapan untuk keperluan pengujian dan demonstrasi kelas, bukan nilai ketetapan PBB sebenarnya.</span>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form action="{{ route('sppt.index') }}" method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="tahun" class="form-select">
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="Lunas" {{ $status == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Cicilan" {{ $status == 'Cicilan' ? 'selected' : '' }}>Cicilan</option>
                    <option value="Belum Lunas" {{ $status == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="dusun" class="form-select">
                    <option value="">-- Semua Dusun --</option>
                    @foreach($daftarDusun as $d)
                        <option value="{{ $d }}" {{ $dusun == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NOP / Nama Wajib Pajak..." value="{{ $search }}">
            </div>
            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-outline-secondary w-100 me-1">Filter</button>
                @if($status || $dusun || $search)
                    <a href="{{ route('sppt.index', ['tahun' => $tahun]) }}" class="btn btn-light" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>NOP & Letak Objek</th>
                    <th>Nama Wajib Pajak</th>
                    <th>Dusun / RT</th>
                    <th class="text-end">Pokok PBB</th>
                    <th class="text-end">Terbayar</th>
                    <th class="text-end">Sisa Tagihan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 12%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spptList as $index => $sppt)
                    <tr>
                        <td>{{ $spptList->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('objek-pajak.show', $sppt->nop) }}" class="fw-bold text-success text-decoration-none">
                                {{ $sppt->nop }}
                            </a>
                            <small class="text-muted d-block">{{ $sppt->objekPajak->letak_objek ?? '-' }}</small>
                        </td>
                        <td>
                            <a href="{{ route('wajib-pajak.show', $sppt->objekPajak->id_wp ?? 0) }}" class="text-dark fw-semibold text-decoration-none">
                                {{ $sppt->objekPajak->wajibPajak->nama_wp ?? '-' }}
                            </a>
                        </td>
                        <td>
                            <span class="small">{{ $sppt->objekPajak->dusun_op ?? '-' }}</span>
                            <small class="text-muted d-block">RT {{ $sppt->objekPajak->rt_op ?? '-' }}/RW {{ $sppt->objekPajak->rw_op ?? '-' }}</small>
                        </td>
                        <td class="text-end fw-semibold">Rp {{ number_format($sppt->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($sppt->total_terbayar, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold {{ $sppt->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="text-center">{!! $sppt->badge_status !!}</td>
                        <td class="text-center">
                            @if($sppt->status_bayar !== 'Lunas')
                                <a href="{{ route('pembayaran.create', ['id_sppt' => $sppt->id_sppt]) }}" class="btn btn-sm btn-primary py-1 px-2" style="font-size: 0.75rem;">
                                    <i class="bi bi-cash me-1"></i> Bayar Loket
                                </a>
                            @else
                                <span class="text-success small fw-semibold"><i class="bi bi-check2-all"></i> Lunas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Tidak ada data SPPT untuk kriteria pencarian ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($spptList->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $spptList->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

