@extends('layouts.app')

@section('title', 'Tambah Objek Pajak Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="bi bi-geo-alt-fill me-1"></i> Form Tambah Objek Pajak (NOP)
                </h5>
                <a href="{{ route('objek-pajak.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
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

            <form action="{{ route('objek-pajak.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nomor Objek Pajak (NOP 18 Digit) <span class="text-danger">*</span></label>
                        <input type="text" name="nop" class="form-control" placeholder="180204..." value="{{ old('nop') }}" maxlength="18" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Wajib Pajak (Pemilik) <span class="text-danger">*</span></label>
                        <select name="id_wp" class="form-select" required>
                            <option value="">-- Pilih Wajib Pajak --</option>
                            @foreach($wajibPajakList as $wp)
                                <option value="{{ $wp->id_wp }}" {{ (old('id_wp', $id_wp) == $wp->id_wp) ? 'selected' : '' }}>
                                    {{ $wp->nama_wp }} ({{ $wp->nik ?? 'Tanpa NIK' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Letak Objek Pajak (Alamat Lengkap) <span class="text-danger">*</span></label>
                    <input type="text" name="letak_objek" class="form-control" placeholder="Contoh: Jl. Kenanga No. 12" value="{{ old('letak_objek') }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Dusun <span class="text-danger">*</span></label>
                        <input type="text" name="dusun_op" class="form-control" placeholder="Dusun 1 Sidomulyo" value="{{ old('dusun_op', 'Dusun 1 Sidomulyo') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RT <span class="text-danger">*</span></label>
                        <input type="text" name="rt_op" class="form-control" placeholder="001" value="{{ old('rt_op', '001') }}" maxlength="5" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RW <span class="text-danger">*</span></label>
                        <input type="text" name="rw_op" class="form-control" placeholder="001" value="{{ old('rw_op', '001') }}" maxlength="5" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Luas Bumi (m²) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="luas_bumi" class="form-control" value="{{ old('luas_bumi', 0) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Luas Bangunan (m²) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="luas_bangunan" class="form-control" value="{{ old('luas_bangunan', 0) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">NJOP Bumi (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="njop_bumi" class="form-control" value="{{ old('njop_bumi', 0) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">NJOP Bangunan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="njop_bangunan" class="form-control" value="{{ old('njop_bangunan', 0) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Keterangan Tambahan</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Misal: Rumah induk, kebun sawit, ruko pasar, dsb." value="{{ old('keterangan') }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('objek-pajak.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Objek Pajak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

