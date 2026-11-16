<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjekPajak;
use App\Models\TagihanSppt;
use App\Models\TransaksiPembayaran;
use App\Models\ProfilDesa;
use Illuminate\Support\Facades\Storage;

class PortalWargaController extends Controller
{
    public function index()
    {
        $profilDesa = ProfilDesa::first();
        return view('portal.index', compact('profilDesa'));
    }

    public function cekTagihan(Request $request)
    {
        $request->validate([
            'nop' => 'required|string',
        ], [
            'nop.required' => 'Silakan masukkan 18 digit Nomor Objek Pajak (NOP).',
        ]);

        $nop = preg_replace('/[^0-9]/', '', $request->nop);
        $objekPajak = ObjekPajak::with([
            'wajibPajak', 
            'tagihanSppt' => function ($q) {
                $q->orderByDesc('tahun_pajak');
            },
            'tagihanSppt.transaksiPembayaran',
            'tagihanSppt.transaksiSimulasiUts'
        ])->where('nop', $nop)->first();

        $profilDesa = ProfilDesa::first();

        if (!$objekPajak) {
            return back()->withInput()->with('error', "Nomor Objek Pajak (NOP) '{$request->nop}' tidak ditemukan di basis data Kampung Sidomulyo.");
        }

        return view('portal.hasil_cek', compact('objekPajak', 'profilDesa'));
    }

    public function formUpload($id_sppt)
    {
        $sppt = TagihanSppt::with('objekPajak.wajibPajak')->findOrFail($id_sppt);

        if ($sppt->status_bayar === 'Lunas') {
            return redirect()->route('portal.cek', ['nop' => $sppt->nop])
                ->with('info', 'Tagihan SPPT ini sudah berstatus LUNAS.');
        }

        $profilDesa = ProfilDesa::first();
        return view('portal.upload_bukti', compact('sppt', 'profilDesa'));
    }

    public function simpanUpload(Request $request)
    {
        $request->validate([
            'id_sppt' => 'required|exists:tagihan_sppt,id_sppt',
            'jumlah_bayar' => 'required|numeric|min:1000',
            'bank_tujuan' => 'required|string|max:50',
            'nomor_rekening_pengirim' => 'nullable|string|max:50',
            'nama_rekening_pengirim' => 'nullable|string|max:150',
            'nama_penyetor' => 'required|string|max:150',
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'tgl_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'bukti_transfer.required' => 'Foto atau gambar slip bukti transfer bank wajib diunggah.',
            'bukti_transfer.image' => 'File harus berupa gambar (JPG, PNG, atau WEBP).',
            'bukti_transfer.max' => 'Ukuran file foto bukti maksimal 3 MB.',
            'nama_penyetor.required' => 'Nama penyetor / pembayar wajib diisi.',
        ]);

        $sppt = TagihanSppt::findOrFail($request->id_sppt);

        // Upload bukti foto
        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');

        $kodeTransaksi = TransaksiPembayaran::generateKodeTransaksi();

        TransaksiPembayaran::create([
            'kode_transaksi' => $kodeTransaksi,
            'id_sppt' => $sppt->id_sppt,
            'tgl_bayar' => $request->tgl_bayar . ' ' . date('H:i:s'),
            'jumlah_bayar' => $request->jumlah_bayar,
            'metode_bayar' => 'Transfer Bank',
            'bank_tujuan' => $request->bank_tujuan,
            'nomor_rekening_pengirim' => $request->nomor_rekening_pengirim,
            'nama_rekening_pengirim' => $request->nama_rekening_pengirim,
            'bukti_transfer' => $path,
            'nama_penyetor' => $request->nama_penyetor,
            'hubungan_penyetor' => 'Warga (Unggah Mandiri)',
            'status_verifikasi' => 'Menunggu Verifikasi',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('portal.pembayaran.status', $kodeTransaksi)
            ->with('success', "Konfirmasi pembayaran berhasil dikirim dengan Kode Transaksi {$kodeTransaksi}. Status saat ini: Menunggu Verifikasi.");
    }

    /**
     * Halaman Status & Sukses Pembayaran Warga
     */
    public function statusPembayaran($kode)
    {
        $transaksi = TransaksiPembayaran::with('tagihanSppt.objekPajak.wajibPajak')
            ->where('kode_transaksi', $kode)
            ->firstOrFail();
        $profilDesa = ProfilDesa::first();

        return view('portal.pembayaran_berhasil', compact('transaksi', 'profilDesa'));
    }

    /**
     * API Real-time Polling Status Pembayaran Transfer / QRIS
     */
    public function checkStatusApi($kode)
    {
        $transaksi = TransaksiPembayaran::where('kode_transaksi', $kode)->first();
        if (!$transaksi) {
            return response()->json(['status' => 'Not Found', 'is_success' => false], 404);
        }

        return response()->json([
            'status' => $transaksi->status_verifikasi,
            'is_success' => $transaksi->status_verifikasi === 'Terverifikasi',
            'redirect_url' => route('portal.pembayaran.status', $transaksi->kode_transaksi),
        ]);
    }
}

