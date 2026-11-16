<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WajibPajak;

class WajibPajakController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = WajibPajak::withCount('objekPajak');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_wp', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('dusun_wp', 'like', "%{$search}%");
            });
        }

        $wajibPajak = $query->orderBy('nama_wp')->paginate(10)->withQueryString();

        return view('wajib_pajak.index', compact('wajibPajak', 'search'));
    }

    public function create()
    {
        return view('wajib_pajak.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'nullable|digits:16|unique:wajib_pajak,nik',
            'nama_wp' => 'required|string|max:150',
            'alamat_wp' => 'required|string|max:255',
            'rt_wp' => 'required|string|max:5',
            'rw_wp' => 'required|string|max:5',
            'dusun_wp' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
        ], [
            'nik.digits' => 'NIK harus berjumlah 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar dalam sistem.',
            'nama_wp.required' => 'Nama Wajib Pajak wajib diisi.',
        ]);

        $wp = WajibPajak::create($validated);

        return redirect()->route('wajib-pajak.show', $wp->id_wp)
            ->with('success', 'Data Wajib Pajak berhasil ditambahkan.');
    }

    public function show($id)
    {
        $wajibPajak = WajibPajak::with(['objekPajak.tagihanSppt' => function ($q) {
            $q->orderByDesc('tahun_pajak');
        }])->findOrFail($id);

        return view('wajib_pajak.show', compact('wajibPajak'));
    }

    public function edit($id)
    {
        $wajibPajak = WajibPajak::findOrFail($id);
        return view('wajib_pajak.edit', compact('wajibPajak'));
    }

    public function update(Request $request, $id)
    {
        $wajibPajak = WajibPajak::findOrFail($id);

        $validated = $request->validate([
            'nik' => "nullable|digits:16|unique:wajib_pajak,nik,{$id},id_wp",
            'nama_wp' => 'required|string|max:150',
            'alamat_wp' => 'required|string|max:255',
            'rt_wp' => 'required|string|max:5',
            'rw_wp' => 'required|string|max:5',
            'dusun_wp' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $wajibPajak->update($validated);

        return redirect()->route('wajib-pajak.show', $wajibPajak->id_wp)
            ->with('success', 'Data Wajib Pajak berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $wajibPajak = WajibPajak::withCount('objekPajak')->findOrFail($id);

        if ($wajibPajak->objek_pajak_count > 0) {
            return back()->with('error', "Gagal menghapus: Wajib Pajak '{$wajibPajak->nama_wp}' masih memiliki {$wajibPajak->objek_pajak_count} objek pajak terdaftar.");
        }

        $wajibPajak->delete();

        return redirect()->route('wajib-pajak.index')
            ->with('success', 'Data Wajib Pajak berhasil dihapus.');
    }
}

