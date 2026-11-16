@extends('layouts.app')

@section('title', 'Edit Objek Pajak: ' . $objekPajak->nop)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="bi bi-pencil-square me-1"></i> Edit Objek Pajak: {{ $objekPajak->nop }}
                </h5>
                <a href="{{ route('objek-pajak.show', $objekPajak->nop) }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
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

            <form action="{{ route('objek-pajak.update', $objekPajak->nop) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pemilik (Wajib Pajak) <span class="text-danger">*</span></label>
                    <select name="id_wp" class="form-select" required>
                        @foreach($wajibPajakList as $wp)
                            <option value="{{ $wp->id_wp }}" {{ (old('id_wp', $objekPajak->id_wp) == $wp->id_wp) ? 'selected' : '' }}>
                                {{ $wp->nama_wp }} ({{ $wp->nik ?? 'Tanpa NIK' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Letak Objek Pajak <span class="text-danger">*</span></label>
                    <input type="text" name="letak_objek" class="form-control" value="{{ old('letak_objek', $objekPajak->letak_objek) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Dusun <span class="text-danger">*</span></label>
                        <input type="text" name="dusun_op" class="form-control" value="{{ old('dusun_op', $objekPajak->dusun_op) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RT <span class="text-danger">*</span></label>
                        <input type="text" name="rt_op" class="form-control" value="{{ old('rt_op', $objekPajak->rt_op) }}" maxlength="5" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RW <span class="text-danger">*</span></label>
                        <input type="text" name="rw_op" class="form-control" value="{{ old('rw_op', $objekPajak->rw_op) }}" maxlength="5" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Luas Bumi (m²) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="luas_bumi" class="form-control" value="{{ old('luas_bumi', $objekPajak->luas_bumi) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Luas Bangunan (m²) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="luas_bangunan" class="form-control" value="{{ old('luas_bangunan', $objekPajak->luas_bangunan) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">NJOP Bumi (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="njop_bumi" class="form-control" value="{{ old('njop_bumi', $objekPajak->njop_bumi) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">NJOP Bangunan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="njop_bangunan" class="form-control" value="{{ old('njop_bangunan', $objekPajak->njop_bangunan) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Keterangan Tambahan</label>
                    <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $objekPajak->keterangan) }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('objek-pajak.show', $objekPajak->nop) }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Perbarui Objek Pajak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

