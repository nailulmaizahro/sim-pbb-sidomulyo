<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjekPajak;
use App\Models\WajibPajak;

class ObjekPajakController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $dusun = $request->get('dusun');

        $query = ObjekPajak::with('wajibPajak');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nop', 'like', "%{$search}%")
                    ->orWhere('letak_objek', 'like', "%{$search}%")
                    ->orWhereHas('wajibPajak', function ($w) use ($search) {
                        $w->where('nama_wp', 'like', "%{$search}%");
                    });
            });
        }

        if ($dusun) {
            $query->where('dusun_op', $dusun);
        }

        $objekPajak = $query->orderBy('nop')->paginate(10)->withQueryString();
        $daftarDusun = ObjekPajak::select('dusun_op')->distinct()->pluck('dusun_op');

        return view('objek_pajak.index', compact('objekPajak', 'search', 'dusun', 'daftarDusun'));
    }

    public function create(Request $request)
    {
        $id_wp = $request->get('id_wp');
        $wajibPajakList = WajibPajak::orderBy('nama_wp')->get();
        return view('objek_pajak.create', compact('wajibPajakList', 'id_wp'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nop' => 'required|string|size:18|unique:objek_pajak,nop',
            'id_wp' => 'required|exists:wajib_pajak,id_wp',
            'letak_objek' => 'required|string|max:255',
            'rt_op' => 'required|string|max:5',
            'rw_op' => 'required|string|max:5',
            'dusun_op' => 'required|string|max:100',
            'luas_bumi' => 'required|numeric|min:0',
            'luas_bangunan' => 'required|numeric|min:0',
            'kelas_bumi' => 'nullable|string|max:10',
            'kelas_bangunan' => 'nullable|string|max:10',
            'njop_bumi' => 'required|numeric|min:0',
            'njop_bangunan' => 'required|numeric|min:0',
            'perubahan_terakhir' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nop.size' => 'Nomor Objek Pajak (NOP) standar harus berjumlah 18 digit angka.',
            'nop.unique' => 'NOP ini sudah terdaftar dalam sistem.',
            'id_wp.required' => 'Wajib Pajak pemilik objek harus dipilih.',
        ]);

        $op = ObjekPajak::create($validated);

        return redirect()->route('objek-pajak.show', $op->nop)
            ->with('success', 'Data Objek Pajak berhasil ditambahkan.');
    }

    public function show($nop)
    {
        $objekPajak = ObjekPajak::with(['wajibPajak', 'tagihanSppt' => function ($q) {
            $q->orderByDesc('tahun_pajak');
        }])->findOrFail($nop);

        return view('objek_pajak.show', compact('objekPajak'));
    }

    public function edit($nop)
    {
        $objekPajak = ObjekPajak::findOrFail($nop);
        $wajibPajakList = WajibPajak::orderBy('nama_wp')->get();
        return view('objek_pajak.edit', compact('objekPajak', 'wajibPajakList'));
    }

    public function update(Request $request, $nop)
    {
        $objekPajak = ObjekPajak::findOrFail($nop);

        $validated = $request->validate([
            'id_wp' => 'required|exists:wajib_pajak,id_wp',
            'letak_objek' => 'required|string|max:255',
            'rt_op' => 'required|string|max:5',
            'rw_op' => 'required|string|max:5',
            'dusun_op' => 'required|string|max:100',
            'luas_bumi' => 'required|numeric|min:0',
            'luas_bangunan' => 'required|numeric|min:0',
            'kelas_bumi' => 'nullable|string|max:10',
            'kelas_bangunan' => 'nullable|string|max:10',
            'njop_bumi' => 'required|numeric|min:0',
            'njop_bangunan' => 'required|numeric|min:0',
            'perubahan_terakhir' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $objekPajak->update($validated);

        return redirect()->route('objek-pajak.show', $objekPajak->nop)
            ->with('success', 'Data Objek Pajak berhasil diperbarui.');
    }

    public function destroy($nop)
    {
        $objekPajak = ObjekPajak::withCount('tagihanSppt')->findOrFail($nop);

        if ($objekPajak->tagihan_sppt_count > 0) {
            return back()->with('error', "Gagal menghapus: Objek Pajak dengan NOP '{$objekPajak->nop}' masih memiliki {$objekPajak->tagihan_sppt_count} riwayat tagihan SPPT.");
        }

        $objekPajak->delete();

        return redirect()->route('objek-pajak.index')
            ->with('success', 'Data Objek Pajak berhasil dihapus.');
    }
}

