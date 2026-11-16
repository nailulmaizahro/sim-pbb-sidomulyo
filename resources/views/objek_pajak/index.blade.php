@extends('layouts.app')

@section('title', 'Data Objek Pajak (NOP)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Data Objek Pajak (NOP)</h4>
        <p class="text-muted small mb-0">Master data bidang tanah dan bangunan di Kampung Sidomulyo</p>
    </div>
    <a href="{{ route('objek-pajak.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Objek Pajak
    </a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form action="{{ route('objek-pajak.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari NOP / Letak Objek / Nama Pemilik..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="dusun" class="form-select">
                    <option value="">-- Semua Dusun --</option>
                    @foreach($daftarDusun as $d)
                        <option value="{{ $d }}" {{ $dusun == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
            </div>
            @if($search || $dusun)
                <div class="col-md-2">
                    <a href="{{ route('objek-pajak.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nomor Objek Pajak (NOP)</th>
                    <th>Pemilik (Wajib Pajak)</th>
                    <th>Letak & Dusun</th>
                    <th>Luas Bumi / Bangunan</th>
                    <th>NJOP Total</th>
                    <th class="text-center" style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($objekPajak as $index => $op)
                    <tr>
                        <td>{{ $objekPajak->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('objek-pajak.show', $op->nop) }}" class="fw-bold text-success text-decoration-none">
                                {{ $op->nop }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('wajib-pajak.show', $op->id_wp) }}" class="text-dark fw-semibold text-decoration-none">
                                {{ $op->wajibPajak->nama_wp ?? '-' }}
                            </a>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $op->letak_objek }}</div>
                            <small class="text-muted">{{ $op->dusun_op }} (RT {{ $op->rt_op }}/RW {{ $op->rw_op }})</small>
                        </td>
                        <td>{{ $op->luas_bumi }} m² / {{ $op->luas_bangunan }} m²</td>
                        <td>Rp {{ number_format($op->njop_bumi + $op->njop_bangunan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('objek-pajak.show', $op->nop) }}" class="btn btn-outline-info" title="Detail Objek">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('objek-pajak.edit', $op->nop) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('objek-pajak.destroy', $op->nop) }}" method="POST" onsubmit="return confirm('Hapus data objek pajak ini?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data Objek Pajak yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($objekPajak->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $objekPajak->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

