<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanSppt;
use App\Models\ObjekPajak;
use App\Models\WajibPajak;
use App\Models\LogImportDhkp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpptController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        if (!TagihanSppt::where('tahun_pajak', $tahun)->exists()) {
            $latestTahun = TagihanSppt::max('tahun_pajak');
            if ($latestTahun) {
                $tahun = $latestTahun;
            }
        }

        $status = $request->get('status');
        $dusun = $request->get('dusun');
        $search = $request->get('search');

        $query = TagihanSppt::with(['objekPajak.wajibPajak'])->where('tahun_pajak', $tahun);

        if ($status) {
            $query->where('status_bayar', $status);
        }

        if ($dusun) {
            $query->whereHas('objekPajak', function ($q) use ($dusun) {
                $q->where('dusun_op', $dusun);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nop', 'like', "%{$search}%")
                    ->orWhereHas('objekPajak.wajibPajak', function ($w) use ($search) {
                        $w->where('nama_wp', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        $spptList = $query->orderBy('status_bayar')->orderBy('nop')->paginate(15)->withQueryString();

        $daftarTahun = TagihanSppt::select('tahun_pajak')->distinct()->orderByDesc('tahun_pajak')->pluck('tahun_pajak');
        $daftarDusun = ObjekPajak::select('dusun_op')->distinct()->pluck('dusun_op');

        return view('sppt.index', compact('spptList', 'tahun', 'status', 'dusun', 'search', 'daftarTahun', 'daftarDusun'));
    }

    public function create(Request $request)
    {
        $nop = $request->get('nop');
        $objekPajakList = ObjekPajak::with('wajibPajak')->orderBy('nop')->get();
        return view('sppt.create', compact('objekPajakList', 'nop'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nop' => 'required|exists:objek_pajak,nop',
            'tahun_pajak' => 'required|integer|min:2000|max:2099',
            'pbb_terhutang' => 'required|numeric|min:0',
            'skp' => 'nullable|numeric|min:0',
            'stp' => 'nullable|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'tgl_jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Cek duplikasi SPPT di tahun yang sama
        $exists = TagihanSppt::where('nop', $validated['nop'])
            ->where('tahun_pajak', $validated['tahun_pajak'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', "Gagal: Tagihan SPPT untuk NOP {$validated['nop']} pada tahun pajak {$validated['tahun_pajak']} sudah pernah dibuat.");
        }

        TagihanSppt::create([
            'nop' => $validated['nop'],
            'tahun_pajak' => $validated['tahun_pajak'],
            'pbb_terhutang' => $validated['pbb_terhutang'],
            'skp' => $validated['skp'] ?? 0,
            'stp' => $validated['stp'] ?? 0,
            'denda' => $validated['denda'] ?? 0,
            'tgl_jatuh_tempo' => $validated['tgl_jatuh_tempo'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('sppt.index', ['tahun' => $validated['tahun_pajak']])
            ->with('success', 'Ketetapan Tagihan SPPT berhasil ditambahkan.');
    }

    public function importForm()
    {
        $riwayatImport = LogImportDhkp::with('user')->orderByDesc('waktu_import')->limit(10)->get();
        return view('sppt.import', compact('riwayatImport'));
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'tahun_pajak' => 'required|integer|min:2000|max:2099',
            'file_dhkp' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file_dhkp.required' => 'File CSV DHKP wajib dipilih.',
            'file_dhkp.mimes' => 'Format file harus berupa CSV (Comma Separated Values).',
            'file_dhkp.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $file = $request->file('file_dhkp');
        $namaFileAsli = $file->getClientOriginalName();
        $path = $file->store('import_dhkp');

        $tahunPajak = $request->tahun_pajak;
        $totalBaris = 0;
        $totalSukses = 0;
        $totalGagal = 0;
        $catatanError = [];

        if (($handle = fopen(storage_path('app/' . $path), 'r')) !== false) {
            $header = fgetcsv($handle, 2000, ';'); // Bisa coba titik koma atau koma
            if (!$header || count($header) < 5) {
                // Coba dengan delimiter koma biasa
                rewind($handle);
                $header = fgetcsv($handle, 2000, ',');
                $delimiter = ',';
            } else {
                $delimiter = ';';
            }

            while (($data = fgetcsv($handle, 2000, $delimiter)) !== false) {
                $totalBaris++;
                // Kolom standar CSV:
                // 0: NOP (18 digit), 1: Nama WP, 2: Alamat WP, 3: Letak OP, 4: Dusun OP, 5: RT, 6: RW, 7: Luas Bumi, 8: Luas Bangunan, 9: PBB Terhutang
                if (count($data) < 5) {
                    $totalGagal++;
                    $catatanError[] = "Baris {$totalBaris}: Jumlah kolom tidak mencukupi.";
                    continue;
                }

                $nop = preg_replace('/[^0-9]/', '', trim($data[0] ?? ''));
                $namaWp = trim($data[1] ?? 'Warga Sidomulyo');
                $alamatWp = trim($data[2] ?? 'Kampung Sidomulyo');
                $letakOp = trim($data[3] ?? $alamatWp);
                $dusunOp = trim($data[4] ?? 'Dusun 1 Sidomulyo');
                $rt = trim($data[5] ?? '001');
                $rw = trim($data[6] ?? '001');
                $luasBumi = floatval(trim($data[7] ?? 0));
                $luasBangunan = floatval(trim($data[8] ?? 0));
                $pbbTerhutang = floatval(trim($data[9] ?? 0));

                if (empty($nop)) {
                    $totalGagal++;
                    $catatanError[] = "Baris {$totalBaris}: NOP kosong atau tidak valid.";
                    continue;
                }

                try {
                    DB::transaction(function () use ($nop, $namaWp, $alamatWp, $letakOp, $dusunOp, $rt, $rw, $luasBumi, $luasBangunan, $pbbTerhutang, $tahunPajak) {
                        // 1. Cari atau buat Wajib Pajak
                        $wp = WajibPajak::firstOrCreate(
                            ['nama_wp' => $namaWp, 'rt_wp' => $rt, 'rw_wp' => $rw],
                            ['alamat_wp' => $alamatWp, 'dusun_wp' => $dusunOp]
                        );

                        // 2. Cari atau buat Objek Pajak
                        $op = ObjekPajak::firstOrCreate(
                            ['nop' => $nop],
                            [
                                'id_wp' => $wp->id_wp,
                                'letak_objek' => $letakOp,
                                'rt_op' => $rt,
                                'rw_op' => $rw,
                                'dusun_op' => $dusunOp,
                                'luas_bumi' => $luasBumi,
                                'luas_bangunan' => $luasBangunan,
                                'njop_bumi' => $luasBumi * 50000,
                                'njop_bangunan' => $luasBangunan * 250000,
                            ]
                        );

                        // 3. Buat Tagihan SPPT
                        TagihanSppt::updateOrCreate(
                            ['nop' => $nop, 'tahun_pajak' => $tahunPajak],
                            [
                                'pbb_terhutang' => $pbbTerhutang,
                                'tgl_jatuh_tempo' => "{$tahunPajak}-09-30",
                            ]
                        );
                    });
                    $totalSukses++;
                } catch (\Exception $e) {
                    $totalGagal++;
                    $catatanError[] = "Baris {$totalBaris} (NOP {$nop}): " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        // Catat ke Log Import
        LogImportDhkp::create([
            'tahun_pajak' => $tahunPajak,
            'nama_file_asli' => $namaFileAsli,
            'path_file' => $path,
            'total_baris_file' => $totalBaris,
            'total_sukses' => $totalSukses,
            'total_gagal' => $totalGagal,
            'catatan_error' => !empty($catatanError) ? implode("\n", array_slice($catatanError, 0, 50)) : null,
            'id_user' => Auth::id() ?? 1,
        ]);

        return redirect()->route('sppt.index', ['tahun' => $tahunPajak])
            ->with('success', "Proses Impor DHKP selesai: {$totalSukses} data berhasil diproses, {$totalGagal} data gagal dari total {$totalBaris} baris.");
    }
}

