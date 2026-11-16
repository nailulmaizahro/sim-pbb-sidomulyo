@extends('layouts.app')

@section('title', 'Edit Wajib Pajak: ' . $wajibPajak->nama_wp)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="bi bi-pencil-square me-1"></i> Edit Data Wajib Pajak
                </h5>
                <a href="{{ route('wajib-pajak.show', $wajibPajak->id_wp) }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
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

            <form action="{{ route('wajib-pajak.update', $wajibPajak->id_wp) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">NIK</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik', $wajibPajak->nik) }}" maxlength="16">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Lengkap Wajib Pajak <span class="text-danger">*</span></label>
                        <input type="text" name="nama_wp" class="form-control" value="{{ old('nama_wp', $wajibPajak->nama_wp) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alamat Domisili <span class="text-danger">*</span></label>
                    <input type="text" name="alamat_wp" class="form-control" value="{{ old('alamat_wp', $wajibPajak->alamat_wp) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Dusun <span class="text-danger">*</span></label>
                        <input type="text" name="dusun_wp" class="form-control" value="{{ old('dusun_wp', $wajibPajak->dusun_wp) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RT <span class="text-danger">*</span></label>
                        <input type="text" name="rt_wp" class="form-control" value="{{ old('rt_wp', $wajibPajak->rt_wp) }}" maxlength="5" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RW <span class="text-danger">*</span></label>
                        <input type="text" name="rw_wp" class="form-control" value="{{ old('rw_wp', $wajibPajak->rw_wp) }}" maxlength="5" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Nomor Telepon / HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $wajibPajak->no_hp) }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('wajib-pajak.show', $wajibPajak->id_wp) }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

