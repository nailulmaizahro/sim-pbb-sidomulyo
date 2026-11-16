@extends('layouts.app')

@section('title', 'Impor Massal Data DHKP (CSV)')

@section('content')
<div class="row g-4">
    <!-- Form Impor -->
    <div class="col-lg-6">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i> Impor Massal File DHKP
                </h5>
                <a href="{{ route('sppt.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
            </div>

            <p class="small text-muted mb-3">
                Gunakan modul ini untuk memasukkan ribuan data DHKP tahunan dari Dispenda Kabupaten Lampung Tengah sekaligus tanpa perlu mengetik manual satu per satu.
            </p>

            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sppt.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tahun Pajak Penetapan <span class="text-danger">*</span></label>
                    <input type="number" name="tahun_pajak" class="form-control" value="{{ date('Y') }}" min="2000" max="2099" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih File CSV DHKP <span class="text-danger">*</span></label>
                    <input type="file" name="file_dhkp" class="form-control" accept=".csv, .txt" required>
                    <div class="form-text small">File format CSV dengan pemisah koma (<code>,</code>) atau titik koma (<code>;</code>).</div>
                </div>

                <div class="alert alert-light border p-3 small mb-4">
                    <strong class="d-block mb-1 text-dark"><i class="bi bi-info-circle text-primary me-1"></i> Format Kolom File CSV (Urutan Header):</strong>
                    <code>NOP, Nama_WP, Alamat_WP, Letak_OP, Dusun_OP, RT, RW, Luas_Bumi, Luas_Bangunan, PBB_Terhutang</code>
                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                        Sistem secara cerdas akan otomatis membuat profil Wajib Pajak baru, mendaftarkan Objek Pajak, dan menerbitkan SPPT tahunan.
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold" onclick="return confirm('Mulai memproses impor data massal DHKP?')">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Mulai Proses Impor Massal
                </button>
            </form>
        </div>
    </div>

    <!-- Riwayat Impor (Log Audit) -->
    <div class="col-lg-6">
        <div class="card p-4">
            <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                <i class="bi bi-clock-history me-1 text-secondary"></i> Riwayat Audit Impor DHKP
            </h6>

            @forelse($riwayatImport as $log)
                <div class="border rounded p-3 mb-2 bg-light small">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-success"><i class="bi bi-calendar3 me-1"></i> DHKP Tahun {{ $log->tahun_pajak }}</strong>
                        <span class="text-muted">{{ date('d/m/Y H:i', strtotime($log->waktu_import)) }}</span>
                    </div>
                    <div class="text-muted mb-2">Nama File: <code>{{ $log->nama_file_asli }}</code></div>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span>Total: <strong>{{ $log->total_baris_file }} baris</strong></span>
                        <span class="text-success">Sukses: <strong>{{ $log->total_sukses }}</strong></span>
                        <span class="{{ $log->total_gagal > 0 ? 'text-danger fw-bold' : 'text-muted' }}">Gagal: {{ $log->total_gagal }}</span>
                        <span class="text-muted">Oleh: {{ $log->user->nama_lengkap ?? 'Admin' }}</span>
                    </div>
                    @if($log->catatan_error)
                        <div class="mt-2 p-2 bg-white border rounded text-danger" style="font-size: 0.7rem; max-height: 80px; overflow-y: auto;">
                            {{ $log->catatan_error }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted">Belum ada riwayat berkas DHKP yang diimpor.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

