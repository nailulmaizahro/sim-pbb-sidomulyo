@extends('layouts.app')

@section('title', 'Tambah Wajib Pajak Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="bi bi-person-plus me-1"></i> Form Tambah Wajib Pajak Baru
                </h5>
                <a href="{{ route('wajib-pajak.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
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

            <form action="{{ route('wajib-pajak.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">NIK (16 Digit Angka)</label>
                        <input type="text" name="nik" class="form-control" placeholder="180204..." value="{{ old('nik') }}" maxlength="16">
                        <div class="form-text small">Kosongkan jika warga belum memiliki NIK.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Lengkap Wajib Pajak <span class="text-danger">*</span></label>
                        <input type="text" name="nama_wp" class="form-control" placeholder="Contoh: SUWANDI" value="{{ old('nama_wp') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alamat Tempat Tinggal / Domisili <span class="text-danger">*</span></label>
                    <input type="text" name="alamat_wp" class="form-control" placeholder="Contoh: Jl. Kenanga No. 15" value="{{ old('alamat_wp') }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Dusun <span class="text-danger">*</span></label>
                        <input type="text" name="dusun_wp" class="form-control" placeholder="Dusun 1 Sidomulyo" value="{{ old('dusun_wp') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RT <span class="text-danger">*</span></label>
                        <input type="text" name="rt_wp" class="form-control" placeholder="001" value="{{ old('rt_wp', '001') }}" maxlength="5" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">RW <span class="text-danger">*</span></label>
                        <input type="text" name="rw_wp" class="form-control" placeholder="001" value="{{ old('rw_wp', '001') }}" maxlength="5" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08..." value="{{ old('no_hp') }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('wajib-pajak.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Wajib Pajak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

