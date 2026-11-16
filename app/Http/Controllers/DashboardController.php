<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanSppt;
use App\Models\TransaksiPembayaran;
use App\Models\WajibPajak;
use App\Models\ObjekPajak;
use App\Models\ProfilDesa;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        // Jika belum ada data untuk tahun sekarang, ambil tahun terbaru yang tersedia di database
        if (!TagihanSppt::where('tahun_pajak', $tahun)->exists()) {
            $latestTahun = TagihanSppt::max('tahun_pajak');
            if ($latestTahun) {
                $tahun = $latestTahun;
            }
        }

        $daftarTahun = TagihanSppt::select('tahun_pajak')->distinct()->orderByDesc('tahun_pajak')->pluck('tahun_pajak');

        // Ringkasan KPI Finansial
        $spptsTahun = TagihanSppt::where('tahun_pajak', $tahun);
        $totalTarget = (clone $spptsTahun)->sum('total_tagihan');
        $totalRealisasi = (clone $spptsTahun)->sum('total_terbayar');
        $totalTunggakan = (clone $spptsTahun)->sum('sisa_tagihan');
        $persenCapaian = $totalTarget > 0 ? round(($totalRealisasi / $totalTarget) * 100, 2) : 0;

        // Hitungan Jumlah SPPT
        $countTotal = (clone $spptsTahun)->count();
        $countLunas = (clone $spptsTahun)->where('status_bayar', 'Lunas')->count();
        $countCicilan = (clone $spptsTahun)->where('status_bayar', 'Cicilan')->count();
        $countBelumLunas = (clone $spptsTahun)->where('status_bayar', 'Belum Lunas')->count();

        // Hitungan Master
        $totalWp = WajibPajak::count();
        $totalOp = ObjekPajak::count();

        // Rekapitulasi per Dusun di Kampung Sidomulyo
        $rekapDusun = ObjekPajak::join('tagihan_sppt', 'objek_pajak.nop', '=', 'tagihan_sppt.nop')
            ->where('tagihan_sppt.tahun_pajak', $tahun)
            ->select(
                'objek_pajak.dusun_op',
                DB::raw('COUNT(tagihan_sppt.id_sppt) as total_sppt'),
                DB::raw('SUM(tagihan_sppt.total_tagihan) as target_dusun'),
                DB::raw('SUM(tagihan_sppt.total_terbayar) as realisasi_dusun'),
                DB::raw('SUM(tagihan_sppt.sisa_tagihan) as sisa_dusun'),
                DB::raw('SUM(CASE WHEN tagihan_sppt.status_bayar = "Lunas" THEN 1 ELSE 0 END) as lunas_count')
            )
            ->groupBy('objek_pajak.dusun_op')
            ->orderBy('objek_pajak.dusun_op')
            ->get();

        // Antrean Transfer Bank Menunggu Verifikasi
        $pendingVerifikasi = TransaksiPembayaran::with(['tagihanSppt.objekPajak.wajibPajak'])
            ->where('status_verifikasi', 'Menunggu Verifikasi')
            ->orderByDesc('tgl_bayar')
            ->get();

        // Riwayat Transaksi Terbaru
        $transaksiTerbaru = TransaksiPembayaran::with(['tagihanSppt.objekPajak.wajibPajak', 'petugasVerifikator'])
            ->where('status_verifikasi', 'Terverifikasi')
            ->orderByDesc('tgl_bayar')
            ->limit(7)
            ->get();

        $profilDesa = ProfilDesa::first();

        return view('dashboard.index', compact(
            'tahun',
            'daftarTahun',
            'totalTarget',
            'totalRealisasi',
            'totalTunggakan',
            'persenCapaian',
            'countTotal',
            'countLunas',
            'countCicilan',
            'countBelumLunas',
            'totalWp',
            'totalOp',
            'rekapDusun',
            'pendingVerifikasi',
            'transaksiTerbaru',
            'profilDesa'
        ));
    }
}

