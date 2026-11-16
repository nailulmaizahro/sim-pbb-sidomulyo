<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TagihanSppt;
use App\Models\TransaksiSimulasiUts;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SimulasiUtsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_simulasi_form_can_be_accessed(): void
    {
        $sppt = TagihanSppt::first();
        $this->assertNotNull($sppt, 'Data SPPT harus tersedia untuk pengujian.');

        $response = $this->get(route('portal.simulasi.form', $sppt->id_sppt));
        $response->assertStatus(200);
        $response->assertSee('Pembayaran QRIS');
        $response->assertSee('Rincian Tagihan SPPT');
        $response->assertSee('/images/qris_dana_bisnis.png');
        $response->assertDontSee('SIMULASI PEMBAYARAN UTS');
        $response->assertDontSee('Mode Pengujian UTS');
    }

    public function test_simulasi_create_transaction_rejects_invalid_nominal(): void
    {
        $sppt = TagihanSppt::first();

        // Nominal selain 1000 atau 3000 wajib ditolak
        $response = $this->postJson(route('portal.simulasi.create'), [
            'id_sppt' => $sppt->id_sppt,
            'nominal' => 5000,
            'nama_pembayar' => 'Penguji UTS',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nominal']);
    }

    public function test_simulasi_create_transaction_succeeds_with_default_1000(): void
    {
        $sppt = TagihanSppt::first();
        $originalTerbayar = $sppt->total_terbayar;
        $originalSisa = $sppt->sisa_tagihan;

        $response = $this->postJson(route('portal.simulasi.create'), [
            'id_sppt' => $sppt->id_sppt,
            'nominal' => 1000,
            'nama_pembayar' => 'Mahasiswa Penguji UTS',
            'email_pembayar' => 'mahasiswa@sidomulyo.desa.id',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'nominal' => 1000,
            'nominal_format' => 'Rp 1.000',
        ]);

        $orderId = $response->json('order_id');
        $this->assertNotNull($orderId);

        // Verifikasi transaksi tercatat di database dengan status Pending
        $this->assertDatabaseHas('transaksi_simulasi_uts', [
            'order_id' => $orderId,
            'nominal_pembayaran' => 1000.00,
            'status_pembayaran' => 'Pending',
        ]);

        // Verifikasi penting: Nilai pajak PBB asli pada tagihan_sppt TIDAK BERUBAH
        $spptAfter = TagihanSppt::find($sppt->id_sppt);
        $this->assertEquals($originalTerbayar, $spptAfter->total_terbayar);
        $this->assertEquals($originalSisa, $spptAfter->sisa_tagihan);
    }

    public function test_simulasi_verify_payment_requires_valid_confirmation(): void
    {
        $sppt = TagihanSppt::first();
        $originalTerbayar = $sppt->total_terbayar;

        // 1. Inisiasi transaksi
        $initResponse = $this->postJson(route('portal.simulasi.create'), [
            'id_sppt' => $sppt->id_sppt,
            'nominal' => 3000,
            'nama_pembayar' => 'Dosen Penguji UTS',
        ]);

        $orderId = $initResponse->json('order_id');
        $validToken = $initResponse->json('security_token');

        // 2. Verifikasi tanpa token yang valid -> Wajib ditolak (Status TIDAK boleh berubah)
        $invalidVerify = $this->postJson(route('portal.simulasi.verify'), [
            'order_id' => $orderId,
            'security_token' => 'invalid-token-12345',
        ]);
        $invalidVerify->assertStatus(422);

        $transaksi = TransaksiSimulasiUts::where('order_id', $orderId)->first();
        $this->assertEquals('Pending', $transaksi->status_pembayaran);

        // 3. Verifikasi dengan token yang sah
        $validVerify = $this->postJson(route('portal.simulasi.verify'), [
            'order_id' => $orderId,
            'security_token' => $validToken,
            'payment_type' => 'qris_sandbox',
        ]);

        $validVerify->assertStatus(200);
        $validVerify->assertJson([
            'success' => true,
            'message' => 'Pembayaran Simulasi Berhasil',
        ]);

        // Status di database berubah menjadi Berhasil
        $transaksiRefreshed = TransaksiSimulasiUts::where('order_id', $orderId)->first();
        $this->assertEquals('Berhasil', $transaksiRefreshed->status_pembayaran);
        $this->assertNotNull($transaksiRefreshed->waktu_pembayaran);

        // Nilai ketetapan pajak PBB asli tetap TIDAK BERUBAH
        $spptAfter = TagihanSppt::find($sppt->id_sppt);
        $this->assertEquals($originalTerbayar, $spptAfter->total_terbayar);
    }

    public function test_simulasi_kuitansi_can_be_rendered(): void
    {
        $sppt = TagihanSppt::first();

        $transaksi = TransaksiSimulasiUts::create([
            'order_id' => TransaksiSimulasiUts::generateOrderId(),
            'id_sppt' => $sppt->id_sppt,
            'nop' => $sppt->nop,
            'tahun_pajak' => $sppt->tahun_pajak,
            'nama_wp' => $sppt->objekPajak->wajibPajak->nama_wp ?? 'Wajib Pajak',
            'nama_pembayar' => 'Penguji Akademik UTS',
            'nominal_pembayaran' => 1000,
            'metode_pembayaran' => 'Midtrans Sandbox',
            'status_pembayaran' => 'Berhasil',
            'waktu_transaksi' => now(),
            'waktu_pembayaran' => now(),
            'keterangan' => 'SIMULASI PEMBAYARAN UTS',
        ]);

        $response = $this->get(route('portal.simulasi.kuitansi', $transaksi->id_simulasi));
        $response->assertStatus(200);
        $response->assertSee('Pembayaran Simulasi Berhasil');
        $response->assertSee('SIMULASI PEMBAYARAN UTS');
        $response->assertSee('Rp 1.000');
        $response->assertSee('SIMULASI');
        $response->assertSee('BUKTI TRANSAKSI PENGUJIAN');
    }

    public function test_qris_dana_displayed_on_simulasi_page(): void
    {
        $sppt = TagihanSppt::first();
        $response = $this->get(route('portal.simulasi.form', $sppt->id_sppt));

        $response->assertStatus(200);
        $response->assertSee('/images/qris_dana_bisnis.png');
        $response->assertSee('Pembayaran QRIS');
        $response->assertSee('QRIS DANA BISNIS');
        $response->assertDontSee('Simulasi UTS');
        $response->assertDontSee('Mode Pengujian UTS');
        $response->assertDontSee('SIMULASI PEMBAYARAN UTS');
        $response->assertDontSee('/images/qris_dana_demo.png');
        $response->assertDontSee('QR Saya DANA Personal');
        $response->assertDontSee('Transfer DANA Personal');
        $response->assertSee('Saya Sudah Transfer');
        $response->assertSee('Kembali');
    }

    public function test_konfirmasi_dana_records_pending_status_and_preserves_database_tax(): void
    {
        $sppt = TagihanSppt::first();
        $originalTerbayar = $sppt->total_terbayar;
        $originalSisa = $sppt->sisa_tagihan;

        $response = $this->postJson(route('portal.simulasi.konfirmasi-dana'), [
            'id_sppt' => $sppt->id_sppt,
            'nominal' => 3000,
            'nama_pembayar' => 'Warga Pembayar QRIS',
            'catatan' => 'Uji Pembayaran QRIS',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'Menunggu Verifikasi',
        ]);

        $orderId = $response->json('order_id');
        $this->assertNotNull($orderId);

        // Status di database wajib Pending (Menunggu Verifikasi)
        $this->assertDatabaseHas('transaksi_simulasi_uts', [
            'order_id' => $orderId,
            'nominal_pembayaran' => 3000.00,
            'status_pembayaran' => 'Pending',
            'metode_pembayaran' => 'QRIS DANA Bisnis',
        ]);

        // Nilai ketetapan pajak PBB asli pada tagihan_sppt TIDAK BOLEH BERUBAH
        $spptAfter = TagihanSppt::find($sppt->id_sppt);
        $this->assertEquals($originalTerbayar, $spptAfter->total_terbayar);
        $this->assertEquals($originalSisa, $spptAfter->sisa_tagihan);

        // Akses halaman status untuk melihat status Menunggu Verifikasi
        $transaksi = TransaksiSimulasiUts::where('order_id', $orderId)->first();
        $statusPage = $this->get(route('portal.simulasi.status', $transaksi->id_simulasi));
        $statusPage->assertStatus(200);
        $statusPage->assertSee('Pembayaran QRIS');
        $statusPage->assertSee('STATUS: MENUNGGU VERIFIKASI');
        $statusPage->assertDontSee('Simulasi UTS');
        $statusPage->assertDontSee('DEMONSTRASI UTS');
        $statusPage->assertDontSee('/images/qris_dana_demo.png');
        $statusPage->assertSee('Kembali ke Cek Tagihan');
    }

    public function test_manual_verification_by_petugas_does_not_affect_real_tax(): void
    {
        $admin = \App\Models\User::first();
        $sppt = TagihanSppt::first();
        $originalTerbayar = $sppt->total_terbayar;

        $transaksi = TransaksiSimulasiUts::create([
            'order_id' => TransaksiSimulasiUts::generateOrderId(),
            'id_sppt' => $sppt->id_sppt,
            'nop' => $sppt->nop,
            'tahun_pajak' => $sppt->tahun_pajak,
            'nama_wp' => $sppt->objekPajak->wajibPajak->nama_wp ?? 'Wajib Pajak',
            'nama_pembayar' => 'Warga Pembayar QRIS',
            'nominal_pembayaran' => 1000,
            'metode_pembayaran' => 'QRIS DANA Bisnis',
            'status_pembayaran' => 'Pending',
            'waktu_transaksi' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('pembayaran.simulasi.verifikasi-manual', $transaksi->id_simulasi));
        $response->assertStatus(302);

        $transaksi->refresh();
        $this->assertEquals('Berhasil', $transaksi->status_pembayaran);

        // Nilai tagihan PBB asli di basis data tetap utuh
        $spptAfter = TagihanSppt::find($sppt->id_sppt);
        $this->assertEquals($originalTerbayar, $spptAfter->total_terbayar);
    }

    public function test_halaman_status_simulasi_berhasil_menampilkan_format_struk_modern(): void
    {
        $sppt = TagihanSppt::first();

        $transaksi = TransaksiSimulasiUts::create([
            'order_id' => TransaksiSimulasiUts::generateOrderId(),
            'id_sppt' => $sppt->id_sppt,
            'nop' => $sppt->nop,
            'tahun_pajak' => $sppt->tahun_pajak,
            'nama_wp' => $sppt->objekPajak->wajibPajak->nama_wp ?? 'Wajib Pajak',
            'nama_pembayar' => 'Warga Berhasil Bayar',
            'nominal_pembayaran' => 1000,
            'metode_pembayaran' => 'QRIS DANA Bisnis',
            'status_pembayaran' => 'Berhasil',
            'waktu_transaksi' => now(),
            'waktu_pembayaran' => now(),
        ]);

        $response = $this->get(route('portal.simulasi.status', $transaksi->id_simulasi));
        $response->assertStatus(200);

        // 1. Ikon centang hijau besar
        $response->assertSee('success-icon-circle');
        $response->assertSee('bi-check2');

        // 2. Judul Pembayaran Berhasil!
        $response->assertSee('Pembayaran Berhasil!');

        // 3. Nominal pembayaran
        $response->assertSee('Rp 1.000');

        // 4. Metode pembayaran QRIS
        $response->assertSee('QRIS');

        // 5. Status transaksi LUNAS
        $response->assertSee('LUNAS');

        // 6. Nomor referensi transaksi & tanggal pembayaran
        $response->assertSee($transaksi->order_id);
        $response->assertSee(now()->format('d/m/Y'));

        // 7. Pesan terima kasih
        $response->assertSee('Terima kasih telah melakukan pembayaran');

        // 8. Tombol Kembali ke Dashboard
        $response->assertSee('Kembali ke Dashboard');
        $response->assertSee(route('dashboard'));

        // 9. API check-status
        $apiResponse = $this->getJson(route('portal.simulasi.check-status', $transaksi->id_simulasi));
        $apiResponse->assertStatus(200);
        $apiResponse->assertJson([
            'status' => 'Berhasil',
            'is_success' => true,
        ]);
    }
}

