@extends('layouts.app')

@section('title', 'Data Wajib Pajak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Data Wajib Pajak (Warga)</h4>
        <p class="text-muted small mb-0">Master data subjek pajak Kampung Sidomulyo Punggur</p>
    </div>
    <a href="{{ route('wajib-pajak.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Wajib Pajak
    </a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form action="{{ route('wajib-pajak.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama WP / NIK / Dusun..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Cari</button>
            </div>
            @if($search)
                <div class="col-md-2">
                    <a href="{{ route('wajib-pajak.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Wajib Pajak</th>
                    <th>NIK</th>
                    <th>Alamat Domisili</th>
                    <th>No. Telepon / HP</th>
                    <th class="text-center">Jumlah Objek Pajak</th>
                    <th class="text-center" style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wajibPajak as $index => $wp)
                    <tr>
                        <td>{{ $wajibPajak->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('wajib-pajak.show', $wp->id_wp) }}" class="fw-bold text-decoration-none text-success">
                                {{ $wp->nama_wp }}
                            </a>
                        </td>
                        <td><code>{{ $wp->nik ?? '-' }}</code></td>
                        <td>{{ $wp->alamat_lengkap }}</td>
                        <td>{{ $wp->no_hp ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary rounded-pill px-3">{{ $wp->objek_pajak_count }} Objek</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('wajib-pajak.show', $wp->id_wp) }}" class="btn btn-outline-info" title="Lihat Profil & Objek">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('wajib-pajak.edit', $wp->id_wp) }}" class="btn btn-outline-warning" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('wajib-pajak.destroy', $wp->id_wp) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus wajib pajak ini?')" class="d-inline">
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
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data Wajib Pajak yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($wajibPajak->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $wajibPajak->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

