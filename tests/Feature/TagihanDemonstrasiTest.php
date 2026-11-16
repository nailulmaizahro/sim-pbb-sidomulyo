<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WajibPajak;
use App\Models\ObjekPajak;
use App\Models\TagihanSppt;
use App\Models\TransaksiPembayaran;

class TagihanDemonstrasiTest extends TestCase
{
    public function test_semua_tagihan_sppt_menggunakan_nominal_demonstrasi_1000(): void
    {
        $sppts = TagihanSppt::all();
        $this->assertNotEmpty($sppts, 'Data SPPT harus tersedia.');
        $this->assertGreaterThanOrEqual(8, $sppts->count());

        foreach ($sppts as $sppt) {
            $this->assertEquals(1000.00, (float)$sppt->pbb_terhutang, "SPPT ID {$sppt->id_sppt} pokok harus Rp 1.000");
            $this->assertEquals(1000.00, (float)$sppt->total_tagihan, "SPPT ID {$sppt->id_sppt} total tagihan harus Rp 1.000");
            $this->assertEquals(0.00, (float)$sppt->denda, "SPPT ID {$sppt->id_sppt} denda harus Rp 0");
            $this->assertStringContainsString('Rp 1.000', $sppt->keterangan);
        }

        // Total target ketetapan dihitung berdasarkan nominal demonstrasi
        $totalTarget = TagihanSppt::where('tahun_pajak', 2024)->sum('total_tagihan');
        $this->assertEquals($sppts->count() * 1000.00, (float)$totalTarget);
    }

    public function test_semua_transaksi_pembayaran_menggunakan_nominal_1000(): void
    {
        $transaksis = TransaksiPembayaran::all();
        $this->assertNotEmpty($transaksis, 'Data Transaksi Pembayaran harus tersedia.');

        foreach ($transaksis as $tx) {
            $this->assertEquals(1000.00, (float)$tx->jumlah_bayar, "Transaksi ID {$tx->id_bayar} harus Rp 1.000");
        }
    }

    public function test_data_identitas_wajib_pajak_tetap_utuh(): void
    {
        // Data Wajib Pajak asli tidak boleh terhapus atau berubah identitasnya
        $this->assertGreaterThanOrEqual(5, WajibPajak::count());
        $this->assertDatabaseHas('wajib_pajak', ['nama_wp' => 'Sugeng Raharjo']);
        $this->assertDatabaseHas('wajib_pajak', ['nama_wp' => 'Bambang Hermanto']);
        $this->assertDatabaseHas('wajib_pajak', ['nama_wp' => 'H. Ahmad Fauzi']);
        $this->assertDatabaseHas('wajib_pajak', ['nama_wp' => 'Siti Aminah']);
        $this->assertDatabaseHas('wajib_pajak', ['nama_wp' => 'Suparman']);

        // 8 Objek Pajak tetap utuh
        $this->assertEquals(8, ObjekPajak::count());
    }

    public function test_dashboard_menampilkan_nominal_1000_dan_keterangan_demonstrasi(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Nominal Demonstrasi Akademik UTS');
        $response->assertSee('Rp 1.000');
        // Total target 8 SPPT x Rp 1.000 = Rp 8.000
        $response->assertSee('8.000');
    }

    public function test_daftar_tagihan_sppt_menampilkan_nominal_1000_dan_keterangan(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get(route('sppt.index'));

        $response->assertStatus(200);
        $response->assertSee('Nominal Demonstrasi Akademik UTS');
        $response->assertSee('1.000');
    }

    public function test_riwayat_transaksi_pembayaran_menampilkan_nominal_1000_dan_keterangan(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get(route('pembayaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Nominal Demonstrasi Akademik UTS');
        $response->assertSee('1.000');
    }

    public function test_halaman_cek_tagihan_dan_qris_menampilkan_nominal_1000(): void
    {
        // Cari SPPT yang belum lunas (sisa Rp 1.000)
        $sppt = TagihanSppt::where('status_bayar', 'Belum Lunas')->first();
        $this->assertNotNull($sppt);

        // Cek halaman portal cek tagihan
        $responseCek = $this->get(route('portal.cek', ['nop' => $sppt->nop]));
        $responseCek->assertStatus(200);
        $responseCek->assertSee('Keterangan Demonstrasi');
        $responseCek->assertSee('1.000');

        // Cek halaman upload/bayar QRIS
        $responseQris = $this->get(route('portal.upload.form', $sppt->id_sppt));
        $responseQris->assertStatus(200);
        $responseQris->assertSee('1.000');
        $responseQris->assertSee('demonstrasi proyek perkuliahan');
    }
}

