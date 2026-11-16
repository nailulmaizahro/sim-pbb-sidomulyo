@extends('layouts.app')

@section('title', 'Input Pembayaran Tunai Loket')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="bi bi-cash-stack me-1"></i> Loket Kasir Pembayaran PBB Tunai
                </h5>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
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

            <form action="{{ route('pembayaran.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Tagihan SPPT Warga <span class="text-danger">*</span></label>
                    <select name="id_sppt" id="selectSppt" class="form-select" onchange="updateTagihanInfo(this)" required>
                        <option value="">-- Cari NOP / Nama Warga --</option>
                        @foreach($spptBelumLunas as $sppt)
                            <option value="{{ $sppt->id_sppt }}" 
                                    data-sisa="{{ $sppt->sisa_tagihan }}"
                                    data-nama="{{ $sppt->objekPajak->wajibPajak->nama_wp ?? '-' }}"
                                    data-nop="{{ $sppt->nop }}"
                                    data-tahun="{{ $sppt->tahun_pajak }}"
                                    {{ (old('id_sppt', $spptTerpilih->id_sppt ?? null) == $sppt->id_sppt) ? 'selected' : '' }}>
                                [{{ $sppt->tahun_pajak }}] NOP: {{ $sppt->nop }} - {{ $sppt->objekPajak->wajibPajak->nama_wp ?? '-' }} (Sisa: Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Info Box Sisa Tagihan -->
                <div class="alert alert-light border p-3 small mb-3" id="infoBox">
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted d-block">Wajib Pajak:</span>
                            <strong id="infoNama">{{ $spptTerpilih->objekPajak->wajibPajak->nama_wp ?? '-' }}</strong>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted d-block">Sisa Tagihan Belum Lunas:</span>
                            <span class="fs-5 fw-bold text-danger" id="infoSisa">
                                Rp {{ number_format($spptTerpilih->sisa_tagihan ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nominal Pembayaran Diterima (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="jumlah_bayar" id="inputJumlah" class="form-control" placeholder="Contoh: 125000" value="{{ old('jumlah_bayar', $spptTerpilih->sisa_tagihan ?? '') }}" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="isiLunas()">Lunas Penuh</button>
                    </div>
                    <div class="form-text small">Mendukung pembayaran lunas penuh atau pembayaran sebagian (cicilan).</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Penyetor / Pembayar <span class="text-danger">*</span></label>
                        <input type="text" name="nama_penyetor" id="inputPenyetor" class="form-control" placeholder="Nama warga" value="{{ old('nama_penyetor', $spptTerpilih->objekPajak->wajibPajak->nama_wp ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Hubungan Penyetor</label>
                        <select name="hubungan_penyetor" class="form-select">
                            <option value="Wajib Pajak Langsung" selected>Wajib Pajak Langsung</option>
                            <option value="Keluarga (Istri/Suami/Anak)">Keluarga (Istri/Suami/Anak)</option>
                            <option value="Ketua RT / Petugas Penarik Dusun">Ketua RT / Petugas Penarik Dusun</option>
                            <option value="Kuasa / Tetangga">Kuasa / Tetangga</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Catatan Transaksi</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Opsional (misal: setoran tunai loket)" value="{{ old('keterangan') }}">
                </div>

                <div class="text-end">
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-printer me-1"></i> Simpan & Cetak Kuitansi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let sisaSaatIni = {{ $spptTerpilih->sisa_tagihan ?? 0 }};

    function updateTagihanInfo(select) {
        const option = select.options[select.selectedIndex];
        if (option && option.value) {
            sisaSaatIni = parseFloat(option.getAttribute('data-sisa')) || 0;
            const nama = option.getAttribute('data-nama') || '-';

            document.getElementById('infoNama').textContent = nama;
            document.getElementById('infoSisa').textContent = 'Rp ' + sisaSaatIni.toLocaleString('id-ID');
            document.getElementById('inputJumlah').value = sisaSaatIni;
            document.getElementById('inputPenyetor').value = nama;
        } else {
            sisaSaatIni = 0;
            document.getElementById('infoNama').textContent = '-';
            document.getElementById('infoSisa').textContent = 'Rp 0';
            document.getElementById('inputJumlah').value = '';
        }
    }

    function isiLunas() {
        if (sisaSaatIni > 0) {
            document.getElementById('inputJumlah').value = sisaSaatIni;
        }
    }
</script>
@endsection

