@extends('layouts.portal')

@section('title', 'Hasil Pengecekan NOP ' . $objekPajak->nop)

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('portal.home') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pencarian NOP
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Info Objek & Wajib Pajak -->
        <div class="col-lg-5">
            <div class="card card-custom p-4 bg-white mb-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-success">
                    <i class="bi bi-geo-alt-fill me-1"></i> Rincian Objek Pajak
                </h5>
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%;">NOP</th>
                        <td class="fw-bold">{{ $objekPajak->nop }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Nama Wajib Pajak</th>
                        <td class="fw-bold">{{ $objekPajak->wajibPajak->nama_wp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">NIK (Tersensor)</th>
                        <td><code>{{ $objekPajak->wajibPajak->nik_masked ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Letak Objek</th>
                        <td>{{ $objekPajak->letak_objek }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Wilayah</th>
                        <td>Dusun {{ $objekPajak->dusun_op }}, RT {{ $objekPajak->rt_op }} / RW {{ $objekPajak->rw_op }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Luas Bumi / Bangunan</th>
                        <td>{{ number_format($objekPajak->luas_bumi, 0, ',', '.') }} m² / {{ number_format($objekPajak->luas_bangunan, 0, ',', '.') }} m²</td>
                    </tr>
                    <tr>
                        <th class="text-muted">NJOP Bumi</th>
                        <td>Rp {{ number_format($objekPajak->njop_bumi, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">NJOP Bangunan</th>
                        <td>Rp {{ number_format($objekPajak->njop_bangunan, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Petunjuk Pembayaran -->
            <div class="card card-custom p-4 bg-light border">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-wallet2 text-primary me-1"></i> Cara Pembayaran</h6>
                <ol class="small text-muted ps-3 mb-0">
                    <li class="mb-1"><strong>Bayar Tunai:</strong> Datang langsung ke loket balai Kampung Sidomulyo menemui petugas pengelola pajak (Bpk. Karmin).</li>
                    <li><strong>Transfer Bank:</strong> Transfer ke rekening kas kampung resmi, lalu tekan tombol <em>"Konfirmasi Bukti Transfer"</em> di samping tagihan Anda.</li>
                </ol>
            </div>
        </div>

        <!-- Daftar Tagihan SPPT -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                    <i class="bi bi-clock-history text-primary me-1"></i> Riwayat Tagihan SPPT per Tahun
                </h5>

                <div class="alert alert-info border-info d-flex align-items-center p-2 px-3 mb-3 rounded-3 small bg-info bg-opacity-10">
                    <i class="bi bi-info-circle-fill text-info me-2 flex-shrink-0"></i>
                    <span class="text-muted" style="font-size: 0.8rem;">
                        <strong>Keterangan Demonstrasi:</strong> Seluruh tagihan menggunakan nominal demonstrasi akademik Rp 1.000 untuk pengujian pembayaran, bukan nilai PBB resmi pemerintah.
                    </span>
                </div>

                @if($objekPajak->tagihanSppt->isEmpty())
                    <div class="alert alert-info py-3 text-center">
                        Belum ada penetapan data tagihan SPPT untuk objek pajak ini.
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($objekPajak->tagihanSppt as $sppt)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-success">
                                        <i class="bi bi-calendar-check me-1"></i> SPPT Tahun {{ $sppt->tahun_pajak }}
                                    </h6>
                                    <div>
                                        @if($sppt->status_bayar === 'Lunas')
                                            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> LUNAS</span>
                                        @elseif($sppt->status_bayar === 'Cicilan')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-pie-chart me-1"></i> DICICIL</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> BELUM LUNAS</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row g-2 small mb-3">
                                    <div class="col-sm-4">
                                        <span class="text-muted d-block">Pokok PBB:</span>
                                        <span class="fw-semibold">Rp {{ number_format($sppt->total_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="text-muted d-block">Sudah Terbayar:</span>
                                        <span class="fw-semibold text-success">Rp {{ number_format($sppt->total_terbayar, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="text-muted d-block">Sisa Tagihan:</span>
                                        <span class="fw-bold {{ $sppt->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($sppt->sisa_tagihan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                    @if($sppt->status_bayar !== 'Lunas')
                                        <a href="{{ route('portal.upload.form', $sppt->id_sppt) }}" class="btn btn-sm btn-success fw-bold rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-qr-code-scan me-1"></i> Pembayaran QRIS
                                        </a>
                                    @endif
                                </div>

                                <!-- Riwayat Pembayaran Tagihan ini -->
                                @if($sppt->transaksiPembayaran->isNotEmpty())
                                    <div class="bg-light p-2 rounded mt-2 small">
                                        <div class="fw-bold text-muted mb-1" style="font-size: 0.75rem;">Riwayat Pembayaran (Kasir / QRIS / Bank):</div>
                                        @foreach($sppt->transaksiPembayaran as $t)
                                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1">
                                                <span>{{ $t->kode_transaksi }} ({{ $t->metode_bayar }}) - Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</span>
                                                <span>
                                                    @if($t->status_verifikasi === 'Terverifikasi')
                                                        <span class="badge bg-success">Terverifikasi</span>
                                                    @elseif($t->status_verifikasi === 'Menunggu Verifikasi')
                                                        <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                                    @else
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

