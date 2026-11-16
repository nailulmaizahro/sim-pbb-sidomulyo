<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiPembayaran;
use App\Models\TransaksiSimulasiUts;
use App\Models\TagihanSppt;
use App\Models\ProfilDesa;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'semua');
        $search = $request->get('search');

        $query = TransaksiPembayaran::with(['tagihanSppt.objekPajak.wajibPajak', 'petugasVerifikator']);

        if ($tab === 'pending') {
            $query->where('status_verifikasi', 'Menunggu Verifikasi');
        } elseif ($tab === 'terverifikasi') {
            $query->where('status_verifikasi', 'Terverifikasi');
        } elseif ($tab === 'ditolak') {
            $query->where('status_verifikasi', 'Ditolak');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_penyetor', 'like', "%{$search}%")
                    ->orWhereHas('tagihanSppt', function ($s) use ($search) {
                        $s->where('nop', 'like', "%{$search}%")
                            ->orWhereHas('objekPajak.wajibPajak', function ($w) use ($search) {
                                $w->where('nama_wp', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $transaksiList = $query->orderByDesc('tgl_bayar')->paginate(15)->withQueryString();

        $countPending = TransaksiPembayaran::where('status_verifikasi', 'Menunggu Verifikasi')->count();
        $countTerverifikasi = TransaksiPembayaran::where('status_verifikasi', 'Terverifikasi')->count();
        $countSimulasi = TransaksiSimulasiUts::count();

        $simulasiList = null;
        if ($tab === 'simulasi') {
            $simulasiQuery = TransaksiSimulasiUts::with('tagihanSppt.objekPajak.wajibPajak');
            if ($search) {
                $simulasiQuery->where(function($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                      ->orWhere('nop', 'like', "%{$search}%")
                      ->orWhere('nama_pembayar', 'like', "%{$search}%")
                      ->orWhere('nama_wp', 'like', "%{$search}%");
                });
            }
            $simulasiList = $simulasiQuery->orderByDesc('created_at')->paginate(15)->withQueryString();
        }

        return view('pembayaran.index', compact('transaksiList', 'tab', 'search', 'countPending', 'countTerverifikasi', 'countSimulasi', 'simulasiList'));
    }

    public function create(Request $request)
    {
        $id_sppt = $request->get('id_sppt');
        $spptTerpilih = null;

        if ($id_sppt) {
            $spptTerpilih = TagihanSppt::with('objekPajak.wajibPajak')->findOrFail($id_sppt);
        }

        $spptBelumLunas = TagihanSppt::with('objekPajak.wajibPajak')
            ->whereIn('status_bayar', ['Belum Lunas', 'Cicilan'])
            ->orderBy('nop')
            ->get();

        return view('pembayaran.create', compact('spptTerpilih', 'spptBelumLunas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_sppt' => 'required|exists:tagihan_sppt,id_sppt',
            'jumlah_bayar' => 'required|numeric|min:1000',
            'nama_penyetor' => 'required|string|max:150',
            'hubungan_penyetor' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'id_sppt.required' => 'Tagihan SPPT wajib dipilih.',
            'jumlah_bayar.min' => 'Nominal pembayaran minimal Rp 1.000.',
            'nama_penyetor.required' => 'Nama penyetor wajib diisi.',
        ]);

        $sppt = TagihanSppt::findOrFail($request->id_sppt);

        if ($request->jumlah_bayar > $sppt->sisa_tagihan) {
            return back()->withInput()->with('error', "Nominal pembayaran (Rp " . number_format($request->jumlah_bayar, 0, ',', '.') . ") melebihi sisa tagihan (Rp " . number_format($sppt->sisa_tagihan, 0, ',', '.') . ").");
        }

        $kodeTransaksi = TransaksiPembayaran::generateKodeTransaksi();

        $transaksi = TransaksiPembayaran::create([
            'kode_transaksi' => $kodeTransaksi,
            'id_sppt' => $sppt->id_sppt,
            'tgl_bayar' => now(),
            'jumlah_bayar' => $request->jumlah_bayar,
            'metode_bayar' => 'Tunai',
            'nama_penyetor' => $request->nama_penyetor,
            'hubungan_penyetor' => $request->hubungan_penyetor ?? 'Wajib Pajak Langsung',
            'status_verifikasi' => 'Terverifikasi',
            'id_petugas_verifikator' => Auth::id() ?? 1,
            'tgl_verifikasi' => now(),
            'keterangan' => $request->keterangan ?? 'Pembayaran tunai di loket balai kampung',
        ]);

        return redirect()->route('pembayaran.kuitansi', $transaksi->id_bayar)
            ->with('success', 'Pembayaran tunai berhasil diproses dan tercatat lunas/cicilan.');
    }

    public function verifikasi($id)
    {
        $transaksi = TransaksiPembayaran::with('tagihanSppt')->findOrFail($id);

        if ($transaksi->status_verifikasi === 'Terverifikasi') {
            return back()->with('info', 'Transaksi ini sudah pernah diverifikasi sebelumnya.');
        }

        $transaksi->update([
            'status_verifikasi' => 'Terverifikasi',
            'id_petugas_verifikator' => Auth::id() ?? 1,
            'tgl_verifikasi' => now(),
            'alasan_penolakan' => null,
        ]);

        return back()->with('success', "Transaksi {$transaksi->kode_transaksi} berhasil diverifikasi. Saldo tagihan otomatis diperbarui.");
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255',
        ]);

        $transaksi = TransaksiPembayaran::findOrFail($id);

        $transaksi->update([
            'status_verifikasi' => 'Ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'id_petugas_verifikator' => Auth::id() ?? 1,
            'tgl_verifikasi' => now(),
        ]);

        return back()->with('warning', "Transaksi {$transaksi->kode_transaksi} telah ditolak. Alasan: {$request->alasan_penolakan}");
    }

    public function cetakKuitansi($id)
    {
        $transaksi = TransaksiPembayaran::with([
            'tagihanSppt.objekPajak.wajibPajak',
            'petugasVerifikator'
        ])->findOrFail($id);

        $profilDesa = ProfilDesa::first();

        return view('pembayaran.kuitansi', compact('transaksi', 'profilDesa'));
    }
}

