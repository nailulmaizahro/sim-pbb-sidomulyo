@extends('layouts.app')

@section('title', 'Transaksi & Kasir Pembayaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Transaksi & Kasir Pembayaran PBB</h4>
        <p class="text-muted small mb-0">Pencatatan pembayaran tunai di loket dan verifikasi transfer bank</p>
    </div>
    <a href="{{ route('pembayaran.create') }}" class="btn btn-primary">
        <i class="bi bi-cash-coin me-1"></i> Input Pembayaran Tunai Loket
    </a>
</div>

<div class="alert alert-info border-info d-flex align-items-center p-3 mb-4 rounded-3 shadow-sm bg-info bg-opacity-10">
    <i class="bi bi-mortarboard-fill text-info fs-4 me-3 flex-shrink-0"></i>
    <div class="small">
        <strong class="text-dark d-block">Nominal Demonstrasi Akademik UTS:</strong>
        <span class="text-muted">Transaksi pembayaran pada kasir loket dan riwayat transfer bank menggunakan nominal demonstrasi sebesar <strong>Rp 1.000</strong> per ketetapan untuk keperluan pengujian alur pembayaran perkuliahan, bukan nilai transaksi PBB sebenarnya.</span>
    </div>
</div>

<!-- Tabs & Search -->
<div class="card">
    <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'semua' ? 'active fw-bold' : '' }}" href="{{ route('pembayaran.index', ['tab' => 'semua']) }}">
                    Semua Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'pending' ? 'active fw-bold text-danger' : '' }}" href="{{ route('pembayaran.index', ['tab' => 'pending']) }}">
                    <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi Bank
                    @if($countPending > 0)
                        <span class="badge bg-danger rounded-pill ms-1">{{ $countPending }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'terverifikasi' ? 'active fw-bold text-success' : '' }}" href="{{ route('pembayaran.index', ['tab' => 'terverifikasi']) }}">
                    <i class="bi bi-check-circle me-1"></i> Terverifikasi (Lunas / Cicilan)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'ditolak' ? 'active fw-bold text-secondary' : '' }}" href="{{ route('pembayaran.index', ['tab' => 'ditolak']) }}">
                    <i class="bi bi-x-circle me-1"></i> Ditolak
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'simulasi' ? 'active fw-bold text-warning' : '' }}" href="{{ route('pembayaran.index', ['tab' => 'simulasi']) }}">
                    <i class="bi bi-mortarboard-fill me-1"></i> Log Simulasi UTS (Sandbox)
                    @if($countSimulasi > 0)
                        <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $countSimulasi }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body border-top py-3">
        <form action="{{ route('pembayaran.index') }}" method="GET" class="row g-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari Kode Transaksi / NOP / Nama Warga..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Cari</button>
            </div>
            @if($search)
                <div class="col-md-2">
                    <a href="{{ route('pembayaran.index', ['tab' => $tab]) }}" class="btn btn-light w-100">Reset</a>
                </div>
            @endif
        </form>
    </div>

    @if($tab === 'simulasi')
        <div class="table-responsive">
            <div class="alert alert-warning m-3 mb-2 small d-flex align-items-center">
                <i class="bi bi-mortarboard-fill fs-5 me-2 text-warning"></i>
                <div>
                    <strong>Log Audit Mode Simulasi Pembayaran UTS (Midtrans Sandbox)</strong><br>
                    Transaksi di bawah ini adalah data pengujian demonstrasi akademik dengan nominal Rp1.000 / Rp3.000. Data ini <strong>tidak mengubah</strong> nilai tagihan pajak asli di database.
                </div>
            </div>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Order ID & Waktu</th>
                        <th>NOP & Wajib Pajak</th>
                        <th>Tahun</th>
                        <th>Kanal Gateway</th>
                        <th class="text-end">Nominal Simulasi</th>
                        <th class="text-center">Status Pembayaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simulasiList as $s)
                        <tr>
                            <td>
                                <strong class="d-block text-dark font-monospace">{{ $s->order_id }}</strong>
                                <small class="text-muted">{{ $s->waktu_transaksi->format('d/m/Y H:i') }} WIB</small>
                                <small class="text-muted d-block">Penguji: {{ $s->nama_pembayar }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ $s->nop }}</span>
                                <div class="small fw-semibold text-dark">{{ $s->nama_wp }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">Tahun {{ $s->tahun_pajak }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $s->metode_pembayaran }}</span>
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ $s->format_rupiah }}
                                <small class="badge bg-light text-secondary border d-block" style="font-size: 0.65rem;">SIMULASI</small>
                            </td>
                            <td class="text-center">
                                @if($s->status_pembayaran === 'Berhasil')
                                    <span class="badge bg-success px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i> Berhasil</span>
                                @elseif($s->status_pembayaran === 'Pending')
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i> Gagal</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($s->status_pembayaran === 'Berhasil')
                                    <a href="{{ route('portal.simulasi.kuitansi', $s->id_simulasi) }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-printer me-1"></i> Bukti Simulasi
                                    </a>
                                @else
                                    <form action="{{ route('pembayaran.simulasi.verifikasi-manual', $s->id_simulasi) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah mutasi penerimaan DANA sebesar {{ $s->format_rupiah }} sudah diperiksa dan ingin disetujui?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill py-0 px-2" style="font-size: 0.72rem;">
                                            <i class="bi bi-check-circle me-1"></i> Verifikasi Manual
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi simulasi pengujian UTS.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($simulasiList && $simulasiList->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $simulasiList->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Kode & Tanggal</th>
                    <th>NOP & Wajib Pajak</th>
                    <th>Tahun SPPT</th>
                    <th>Metode & Bukti Visual</th>
                    <th class="text-end">Jumlah Bayar</th>
                    <th class="text-center">Status Verifikasi</th>
                    <th class="text-center" style="width: 18%;">Aksi & Validasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiList as $t)
                    <tr>
                        <td>
                            <strong class="d-block text-dark">{{ $t->kode_transaksi }}</strong>
                            <small class="text-muted">{{ $t->tgl_bayar->format('d/m/Y H:i') }} WIB</small>
                            <small class="text-muted d-block">Penyetor: {{ $t->nama_penyetor }}</small>
                        </td>
                        <td>
                            <span class="fw-bold text-success">{{ $t->tagihanSppt->nop ?? '-' }}</span>
                            <div class="small fw-semibold text-dark">{{ $t->tagihanSppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</div>
                            <small class="text-muted">{{ $t->tagihanSppt->objekPajak->dusun_op ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">Tahun {{ $t->tagihanSppt->tahun_pajak ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $t->metode_bayar === 'Tunai' ? 'bg-info text-dark' : 'bg-primary' }}">
                                {{ $t->metode_bayar }}
                            </span>
                            @if($t->bukti_transfer)
                                <div class="mt-1">
                                    <button type="button" class="btn btn-outline-success btn-sm py-0 px-2" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#modalSlip{{ $t->id_bayar }}">
                                        <i class="bi bi-image-fill me-1"></i> Preview Visual Slip
                                    </button>
                                </div>

                                <!-- Modal Preview Visual Slip Transfer -->
                                <div class="modal fade" id="modalSlip{{ $t->id_bayar }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-dark text-white py-2">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-receipt me-1"></i> Bukti Visual Pembayaran: {{ $t->kode_transaksi }}
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3 text-center bg-light">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <div class="border rounded bg-white p-2 d-flex justify-content-center align-items-center" style="min-height: 380px; overflow: auto;">
                                                            <img src="{{ asset('storage/' . $t->bukti_transfer) }}" 
                                                                 id="imgSlip{{ $t->id_bayar }}"
                                                                 class="img-fluid rounded shadow-sm" 
                                                                 alt="Bukti Transfer" 
                                                                 style="max-height: 480px; object-fit: contain; transition: transform 0.3s ease;">
                                                        </div>
                                                        <div class="btn-group btn-group-sm mt-2">
                                                            <button type="button" class="btn btn-outline-secondary" onclick="zoomImage('imgSlip{{ $t->id_bayar }}', 1.25)">
                                                                <i class="bi bi-zoom-in"></i> Perbesar
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary" onclick="zoomImage('imgSlip{{ $t->id_bayar }}', 0.8)">
                                                                <i class="bi bi-zoom-out"></i> Perkecil
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary" onclick="resetZoom('imgSlip{{ $t->id_bayar }}')">
                                                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                                                            </button>
                                                            <a href="{{ asset('storage/' . $t->bukti_transfer) }}" target="_blank" class="btn btn-outline-primary">
                                                                <i class="bi bi-box-arrow-up-right"></i> Buka Asli
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-start">
                                                        <div class="card p-3 h-100 bg-white border">
                                                            <h6 class="fw-bold border-bottom pb-2 mb-2 text-dark">Data Pengirim</h6>
                                                            <small class="text-muted d-block">Nomor Objek Pajak (NOP):</small>
                                                            <strong class="text-success">{{ $t->tagihanSppt->nop }}</strong>

                                                            <small class="text-muted d-block mt-2">Wajib Pajak:</small>
                                                            <strong>{{ $t->tagihanSppt->objekPajak->wajibPajak->nama_wp ?? '-' }}</strong>

                                                            <small class="text-muted d-block mt-2">Nominal Transfer:</small>
                                                            <h5 class="fw-bold text-success mb-2">Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}</h5>

                                                            <small class="text-muted d-block">Bank Tujuan:</small>
                                                            <span>{{ $t->bank_tujuan ?? '-' }}</span>

                                                            <small class="text-muted d-block mt-2">Rekening Pengirim:</small>
                                                            <span>{{ $t->nama_rekening_pengirim ?? '-' }} ({{ $t->nomor_rekening_pengirim ?? '-' }})</span>

                                                            <small class="text-muted d-block mt-2">Waktu Bayar:</small>
                                                            <span>{{ $t->tgl_bayar->format('d/m/Y H:i') }}</span>

                                                            @if($t->status_verifikasi === 'Menunggu Verifikasi')
                                                                <div class="mt-4 pt-2 border-top">
                                                                    <form action="{{ route('pembayaran.verifikasi', $t->id_bayar) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran transfer ini?')">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success w-100 btn-sm mb-2">
                                                                            <i class="bi bi-check-circle me-1"></i> Setujui Bukti Bayar
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($t->status_verifikasi === 'Terverifikasi')
                                <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i> Terverifikasi</span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Oleh: {{ $t->petugasVerifikator->nama_lengkap ?? 'Petugas' }}</small>
                            @elseif($t->status_verifikasi === 'Menunggu Verifikasi')
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
                            @else
                                <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                                <small class="text-danger d-block" style="font-size: 0.7rem;">{{ $t->alasan_penolakan }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($t->status_verifikasi === 'Menunggu Verifikasi')
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('pembayaran.verifikasi', $t->id_bayar) }}" method="POST" onsubmit="return confirm('Setujui dan verifikasi bukti transfer ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm me-1" title="Setujui Pembayaran">
                                            <i class="bi bi-check-lg me-1"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $t->id_bayar }}" title="Tolak">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </div>

                                <!-- Modal Tolak -->
                                <div class="modal fade" id="modalTolak{{ $t->id_bayar }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('pembayaran.tolak', $t->id_bayar) }}" method="POST" class="modal-content text-start">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold text-danger">Tolak Transaksi {{ $t->kode_transaksi }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label small fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="alasan_penolakan" class="form-control" rows="3" placeholder="Contoh: Bukti transfer tidak terbaca / mutasi bank tidak masuk / nominal tidak cocok" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger btn-sm">Konfirmasi Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @elseif($t->status_verifikasi === 'Terverifikasi')
                                <a href="{{ route('pembayaran.kuitansi', $t->id_bayar) }}" class="btn btn-outline-dark btn-sm" style="font-size: 0.75rem;">
                                    <i class="bi bi-printer me-1"></i> Cetak Kuitansi
                                </a>
                            @else
                                <span class="text-muted small">Transaksi Dibatalkan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada catatan transaksi pada filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transaksiList && $transaksiList->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $transaksiList->links('pagination::bootstrap-5') }}
        </div>
    @endif
    @endif
</div>
@endsection

@section('scripts')
<script>
    const scales = {};

    function zoomImage(imgId, factor) {
        if (!scales[imgId]) scales[imgId] = 1;
        scales[imgId] *= factor;
        if (scales[imgId] < 0.5) scales[imgId] = 0.5;
        if (scales[imgId] > 3) scales[imgId] = 3;
        const img = document.getElementById(imgId);
        if (img) img.style.transform = `scale(${scales[imgId]})`;
    }

    function resetZoom(imgId) {
        scales[imgId] = 1;
        const img = document.getElementById(imgId);
        if (img) img.style.transform = 'scale(1)';
    }
</script>
@endsection
