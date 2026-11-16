<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanSppt;
use App\Models\TransaksiPembayaran;
use App\Models\ObjekPajak;
use App\Models\ProfilDesa;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $daftarTahun = TagihanSppt::select('tahun_pajak')->distinct()->orderByDesc('tahun_pajak')->pluck('tahun_pajak');
        $daftarDusun = ObjekPajak::select('dusun_op')->distinct()->pluck('dusun_op');
        return view('laporan.index', compact('daftarTahun', 'daftarDusun'));
    }

    public function realisasi(Request $request)
    {
        $tgl_mulai = $request->get('tgl_mulai', date('Y-01-01'));
        $tgl_akhir = $request->get('tgl_akhir', date('Y-m-d'));
        $metode = $request->get('metode');

        $query = TransaksiPembayaran::with(['tagihanSppt.objekPajak.wajibPajak', 'petugasVerifikator'])
            ->where('status_verifikasi', 'Terverifikasi')
            ->whereDate('tgl_bayar', '>=', $tgl_mulai)
            ->whereDate('tgl_bayar', '<=', $tgl_akhir);

        if ($metode) {
            $query->where('metode_bayar', $metode);
        }

        $transaksi = $query->orderBy('tgl_bayar')->get();
        $totalPenerimaan = $transaksi->sum('jumlah_bayar');

        $profilDesa = ProfilDesa::first();
        $isPrint = $request->has('print');

        return view('laporan.realisasi', compact('transaksi', 'totalPenerimaan', 'tgl_mulai', 'tgl_akhir', 'metode', 'profilDesa', 'isPrint'));
    }

    public function tunggakan(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $dusun = $request->get('dusun');

        $query = TagihanSppt::with(['objekPajak.wajibPajak'])
            ->where('tahun_pajak', $tahun)
            ->whereIn('status_bayar', ['Belum Lunas', 'Cicilan']);

        if ($dusun) {
            $query->whereHas('objekPajak', function ($q) use ($dusun) {
                $q->where('dusun_op', $dusun);
            });
        }

        $daftarTunggakan = $query->orderBy('nop')->get();
        $totalPiutang = $daftarTunggakan->sum('sisa_tagihan');

        $profilDesa = ProfilDesa::first();
        $isPrint = $request->has('print');

        return view('laporan.tunggakan', compact('daftarTunggakan', 'totalPiutang', 'tahun', 'dusun', 'profilDesa', 'isPrint'));
    }

    public function dispenda(Request $request)
    {
        $tahun = $request->get('tahun', 2024);

        $rekapDusun = ObjekPajak::join('tagihan_sppt', 'objek_pajak.nop', '=', 'tagihan_sppt.nop')
            ->where('tagihan_sppt.tahun_pajak', $tahun)
            ->select(
                'objek_pajak.dusun_op',
                DB::raw('COUNT(tagihan_sppt.id_sppt) as total_buku'),
                DB::raw('SUM(tagihan_sppt.total_tagihan) as target_baku'),
                DB::raw('SUM(tagihan_sppt.total_terbayar) as realisasi_setor'),
                DB::raw('SUM(tagihan_sppt.sisa_tagihan) as sisa_tunggakan'),
                DB::raw('SUM(CASE WHEN tagihan_sppt.status_bayar = "Lunas" THEN 1 ELSE 0 END) as total_lunas')
            )
            ->groupBy('objek_pajak.dusun_op')
            ->orderBy('objek_pajak.dusun_op')
            ->get();

        $grandTotalBuku = $rekapDusun->sum('total_buku');
        $grandTarget = $rekapDusun->sum('target_baku');
        $grandRealisasi = $rekapDusun->sum('realisasi_setor');
        $grandSisa = $rekapDusun->sum('sisa_tunggakan');
        $grandLunas = $rekapDusun->sum('total_lunas');

        $profilDesa = ProfilDesa::first();
        $isPrint = $request->has('print');

        return view('laporan.dispenda', compact(
            'rekapDusun',
            'tahun',
            'grandTotalBuku',
            'grandTarget',
            'grandRealisasi',
            'grandSisa',
            'grandLunas',
            'profilDesa',
            'isPrint'
        ));
    }
}

