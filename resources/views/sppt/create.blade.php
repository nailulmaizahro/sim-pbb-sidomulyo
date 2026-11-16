@extends('layouts.app')

@section('title', 'Terbitkan SPPT Manual')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="bi bi-file-earmark-plus me-1"></i> Form Penerbitan Tagihan SPPT
                </h5>
                <a href="{{ route('sppt.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sppt.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Objek Pajak (NOP) <span class="text-danger">*</span></label>
                    <select name="nop" class="form-select" required>
                        <option value="">-- Pilih NOP --</option>
                        @foreach($objekPajakList as $op)
                            <option value="{{ $op->nop }}" {{ (old('nop', $nop) == $op->nop) ? 'selected' : '' }}>
                                {{ $op->nop }} - {{ $op->wajibPajak->nama_wp ?? '-' }} ({{ $op->letak_objek }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tahun Pajak <span class="text-danger">*</span></label>
                        <input type="number" name="tahun_pajak" class="form-control" value="{{ old('tahun_pajak', date('Y')) }}" min="2000" max="2099" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tanggal Jatuh Tempo</label>
                        <input type="date" name="tgl_jatuh_tempo" class="form-control" value="{{ old('tgl_jatuh_tempo', date('Y') . '-09-30') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Pokok PBB Terhutang (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="pbb_terhutang" class="form-control" placeholder="Contoh: 125000" value="{{ old('pbb_terhutang') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Denda Tambahan (Rp)</label>
                        <input type="number" step="any" name="denda" class="form-control" placeholder="0" value="{{ old('denda', 0) }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Catatan / Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Opsional" value="{{ old('keterangan') }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('sppt.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Terbitkan SPPT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

