<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanSppt;
use App\Models\TransaksiSimulasiUts;
use App\Models\ProfilDesa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SimulasiUtsController extends Controller
{
    /**
     * Tampilkan formulir simulasi pembayaran UTS dengan pilihan nominal Rp 1.000 & Rp 3.000
     */
    public function formSimulasi($id_sppt)
    {
        $sppt = TagihanSppt::with('objekPajak.wajibPajak')->findOrFail($id_sppt);
        $profilDesa = ProfilDesa::first();

        // Riwayat pengujian simulasi pada SPPT ini
        $riwayatSimulasi = TransaksiSimulasiUts::where('id_sppt', $id_sppt)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('portal.simulasi_uts', compact('sppt', 'profilDesa', 'riwayatSimulasi'));
    }

    /**
     * Inisiasi transaksi pengujian Midtrans Sandbox / Simulator Backend
     */
    public function createTransaction(Request $request)
    {
        $request->validate([
            'id_sppt' => 'required|exists:tagihan_sppt,id_sppt',
            'nominal' => 'required|in:1000,3000',
            'nama_pembayar' => 'required|string|max:150',
            'email_pembayar' => 'nullable|email|max:150',
        ], [
            'nominal.required' => 'Nominal simulasi pembayaran wajib dipilih.',
            'nominal.in' => 'Nominal demonstrasi UTS yang valid hanya Rp 1.000 dan Rp 3.000.',
            'nama_pembayar.required' => 'Nama penguji/pembayar simulasi wajib diisi.',
        ]);

        $sppt = TagihanSppt::with('objekPajak.wajibPajak')->findOrFail($request->id_sppt);
        $nominal = (int) $request->nominal;
        $orderId = TransaksiSimulasiUts::generateOrderId();

        $serverKey = config('services.midtrans.server_key');
        $clientKey = config('services.midtrans.client_key');
        $isProduction = config('services.midtrans.is_production', false);
        $snapUrl = config('services.midtrans.snap_url');

        $snapToken = null;
        $useMidtransApi = false;

        // Jika Server Key bukan dummy default dan memiliki format kredensial Midtrans
        if (!empty($serverKey) && $serverKey !== 'SB-Mid-server-DemoUtsKey12345' && str_starts_with($serverKey, 'SB-Mid-server-')) {
            try {
                $response = Http::withBasicAuth($serverKey, '')
                    ->timeout(10)
                    ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', [
                        'transaction_details' => [
                            'order_id' => $orderId,
                            'gross_amount' => $nominal,
                        ],
                        'customer_details' => [
                            'first_name' => $request->nama_pembayar,
                            'email' => $request->email_pembayar ?? 'penguji.uts@sidomulyo.desa.id',
                        ],
                        'item_details' => [
                            [
                                'id' => 'SIM-UTS-' . $nominal,
                                'price' => $nominal,
                                'quantity' => 1,
                                'name' => 'Simulasi Pembayaran UTS (' . 'Rp ' . number_format($nominal, 0, ',', '.') . ')',
                            ]
                        ],
                    ]);

                if ($response->successful()) {
                    $snapToken = $response->json('token');
                    $useMidtransApi = true;
                } else {
                    Log::warning('Midtrans API Request Failed: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Midtrans API Exception: ' . $e->getMessage());
            }
        }

        // Token verifikasi aman untuk simulator sandbox (mencegah bypass atau manipulasi tanpa konfirmasi valid)
        $securityHash = hash('sha256', $orderId . '|' . $nominal . '|' . config('app.key'));

        // Catat transaksi simulasi baru ke database (Status: Pending)
        // PENTING: Nilai pajak PBB asli pada tagihan_sppt TIDAK disentuh sama sekali!
        $simulasi = TransaksiSimulasiUts::create([
            'order_id' => $orderId,
            'id_sppt' => $sppt->id_sppt,
            'nop' => $sppt->nop,
            'tahun_pajak' => $sppt->tahun_pajak,
            'nama_wp' => $sppt->objekPajak->wajibPajak->nama_wp ?? 'Wajib Pajak',
            'nama_pembayar' => $request->nama_pembayar,
            'nominal_pembayaran' => $nominal,
            'metode_pembayaran' => $useMidtransApi ? 'Midtrans Snap Sandbox' : 'Midtrans Sandbox Simulator',
            'status_pembayaran' => 'Pending',
            'snap_token' => $snapToken,
            'waktu_transaksi' => now(),
            'keterangan' => 'SIMULASI PEMBAYARAN UTS',
            'payload_gateway' => json_encode([
                'security_hash' => $securityHash,
                'email' => $request->email_pembayar,
            ]),
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $orderId,
            'id_simulasi' => $simulasi->id_simulasi,
            'nominal' => $nominal,
            'nominal_format' => 'Rp ' . number_format($nominal, 0, ',', '.'),
            'snap_token' => $snapToken,
            'client_key' => $clientKey,
            'snap_url' => $snapUrl,
            'use_midtrans_api' => $useMidtransApi,
            'security_token' => $securityHash,
        ]);
    }

    /**
     * Konfirmasi & Validasi Pembayaran yang Valid
     * Status pembayaran HANYA berubah jika menerima konfirmasi valid (tidak otomatis karena klik kembali atau redirect)
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:transaksi_simulasi_uts,order_id',
        ]);

        $transaksi = TransaksiSimulasiUts::where('order_id', $request->order_id)->firstOrFail();

        // 1. Jika menggunakan Midtrans Snap API nyata
        if ($transaksi->metode_pembayaran === 'Midtrans Snap Sandbox' && !empty(config('services.midtrans.server_key'))) {
            try {
                $statusRes = Http::withBasicAuth(config('services.midtrans.server_key'), '')
                    ->timeout(10)
                    ->get("https://api.sandbox.midtrans.com/v2/{$transaksi->order_id}/status");

                if ($statusRes->successful()) {
                    $resData = $statusRes->json();
                    $txStatus = $resData['transaction_status'] ?? '';
                    $fraudStatus = $resData['fraud_status'] ?? 'accept';

                    if (in_array($txStatus, ['settlement', 'capture']) && $fraudStatus === 'accept') {
                        $transaksi->update([
                            'status_pembayaran' => 'Berhasil',
                            'waktu_pembayaran' => now(),
                            'transaction_id_gateway' => $resData['transaction_id'] ?? null,
                            'payment_type' => $resData['payment_type'] ?? 'qris',
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Pembayaran Simulasi Berhasil',
                            'redirect_url' => route('portal.simulasi.kuitansi', $transaksi->id_simulasi),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Verification Error Midtrans: ' . $e->getMessage());
            }
        }

        // 2. Jika menggunakan Sandbox Gateway Simulator UTS (Wajib menyertakan security_token valid)
        $securityToken = $request->security_token;
        $expectedHash = hash('sha256', $transaksi->order_id . '|' . (int)$transaksi->nominal_pembayaran . '|' . config('app.key'));

        if ($securityToken && hash_equals($expectedHash, $securityToken)) {
            $transaksi->update([
                'status_pembayaran' => 'Berhasil',
                'waktu_pembayaran' => now(),
                'transaction_id_gateway' => 'MOCK-SANDBOX-' . strtoupper(bin2hex(random_bytes(6))),
                'payment_type' => $request->payment_type ?? 'qris_sandbox',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran Simulasi Berhasil',
                'redirect_url' => route('portal.simulasi.kuitansi', $transaksi->id_simulasi),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Konfirmasi pembayaran tidak valid. Status pembayaran tidak dapat diubah.',
        ], 422);
    }

    /**
     * Webhook Notification Handler (Midtrans HTTP Notification Callback)
     */
    public function notification(Request $request)
    {
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;
        $serverKey = config('services.midtrans.server_key');
        $signatureKey = $request->signature_key;

        $myHash = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($myHash !== $signatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaksi = TransaksiSimulasiUts::where('order_id', $orderId)->first();
        if (!$transaksi) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $txStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? 'accept';

        if (in_array($txStatus, ['capture', 'settlement']) && $fraudStatus === 'accept') {
            $transaksi->update([
                'status_pembayaran' => 'Berhasil',
                'waktu_pembayaran' => now(),
                'transaction_id_gateway' => $request->transaction_id,
                'payment_type' => $request->payment_type,
            ]);
        } elseif (in_array($txStatus, ['cancel', 'deny', 'expire'])) {
            $transaksi->update([
                'status_pembayaran' => 'Gagal',
            ]);
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * Tampilkan Kuitansi / Bukti Pembayaran Simulasi UTS
     */
    public function cetakKuitansi($id)
    {
        $transaksi = TransaksiSimulasiUts::with('tagihanSppt.objekPajak.wajibPajak')->findOrFail($id);
        $profilDesa = ProfilDesa::first();

        return view('portal.kuitansi_simulasi', compact('transaksi', 'profilDesa'));
    }

    /**
     * Konfirmasi Pembayaran QRIS DANA Bisnis
     * Status yang dicatat adalah "Pending" (Menunggu Verifikasi)
     * TIDAK otomatis menjadi Lunas dan TIDAK mengubah database pajak PBB asli
     */
    public function konfirmasiDana(Request $request)
    {
        $request->validate([
            'id_sppt' => 'required|exists:tagihan_sppt,id_sppt',
            'nominal' => 'required|numeric|min:1000',
            'nama_pembayar' => 'required|string|max:150',
            'catatan' => 'nullable|string|max:255',
        ], [
            'nominal.required' => 'Nominal pembayaran wajib diisi.',
            'nominal.min' => 'Nominal pembayaran minimal Rp 1.000.',
            'nama_pembayar.required' => 'Nama pembayar/penyetor wajib diisi.',
        ]);

        $sppt = TagihanSppt::with('objekPajak.wajibPajak')->findOrFail($request->id_sppt);
        $nominal = (int) $request->nominal;
        $orderId = 'QRIS-' . date('YmdHis') . '-' . strtoupper(Str::random(4));

        // Catat transaksi dengan status "Pending" (Menunggu Verifikasi)
        // Nilai ketetapan pajak PBB asli pada tagihan_sppt TIDAK disentuh sama sekali!
        $simulasi = TransaksiSimulasiUts::create([
            'order_id' => $orderId,
            'id_sppt' => $sppt->id_sppt,
            'nop' => $sppt->nop,
            'tahun_pajak' => $sppt->tahun_pajak,
            'nama_wp' => $sppt->objekPajak->wajibPajak->nama_wp ?? 'Wajib Pajak',
            'nama_pembayar' => $request->nama_pembayar,
            'nominal_pembayaran' => $nominal,
            'metode_pembayaran' => 'QRIS DANA Bisnis',
            'status_pembayaran' => 'Pending', // Menunggu Verifikasi Manual
            'waktu_transaksi' => now(),
            'keterangan' => 'Pembayaran QRIS DANA Bisnis - Menunggu Verifikasi Manual',
            'payload_gateway' => json_encode([
                'channel' => 'qris_dana_bisnis.png',
                'catatan' => $request->catatan,
            ]),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => 'Menunggu Verifikasi',
                'order_id' => $orderId,
                'redirect_url' => route('portal.simulasi.status', $simulasi->id_simulasi),
            ]);
        }

        return redirect()->route('portal.simulasi.status', $simulasi->id_simulasi);
    }

    /**
     * Halaman Status Pembayaran Demonstrasi (Menunggu Verifikasi / Selesai)
     */
    public function statusSimulasi($id)
    {
        $transaksi = TransaksiSimulasiUts::with('tagihanSppt.objekPajak.wajibPajak')->findOrFail($id);
        $profilDesa = ProfilDesa::first();

        return view('portal.status_simulasi', compact('transaksi', 'profilDesa'));
    }

    /**
     * API Real-time Polling Status Pembayaran QRIS
     */
    public function checkStatusApi($id)
    {
        $transaksi = TransaksiSimulasiUts::find($id);
        if (!$transaksi) {
            return response()->json(['status' => 'Not Found', 'is_success' => false], 404);
        }

        return response()->json([
            'status' => $transaksi->status_pembayaran,
            'is_success' => $transaksi->status_pembayaran === 'Berhasil',
            'redirect_url' => route('portal.simulasi.status', $transaksi->id_simulasi),
        ]);
    }

    /**
     * Verifikasi Manual oleh Petugas / Verifikator setelah mengecek mutasi DANA
     * Status baru berubah menjadi "Berhasil" setelah diperiksa secara manual
     */
    public function verifikasiManual($id)
    {
        $transaksi = TransaksiSimulasiUts::findOrFail($id);

        if ($transaksi->status_pembayaran === 'Berhasil') {
            return back()->with('info', 'Transaksi simulasi ini sudah pernah diverifikasi sebelumnya.');
        }

        $transaksi->update([
            'status_pembayaran' => 'Berhasil',
            'waktu_pembayaran' => now(),
            'transaction_id_gateway' => 'MANUAL-DANA-' . strtoupper(Str::random(6)),
        ]);

        return back()->with('success', "Transaksi demonstrasi {$transaksi->order_id} berhasil diverifikasi manual.");
    }
}

