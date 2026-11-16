<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ObjekPajak;
use App\Models\TagihanSppt;
use App\Models\TransaksiPembayaran;

class PbbSystemTest extends TestCase
{
    public function test_portal_home_can_be_accessed(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Layanan Pengecekan PBB Online');
    }

    public function test_portal_check_valid_nop(): void
    {
        $objek = ObjekPajak::first();
        if ($objek) {
            $response = $this->get('/cek-tagihan?nop=' . $objek->nop);
            $response->assertStatus(200);
            $response->assertSee($objek->nop);
        }
    }

    public function test_login_page_can_be_accessed(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIM PBB KAMPUNG SIDOMULYO');
    }

    public function test_dashboard_can_be_accessed_by_authenticated_user(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Realisasi PBB-P2');
    }

    public function test_wajib_pajak_page_can_be_accessed(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $response = $this->get('/wajib-pajak');
        $response->assertStatus(200);
        $response->assertSee('Data Wajib Pajak (Warga)');
    }

    public function test_objek_pajak_page_can_be_accessed(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $response = $this->get('/objek-pajak');
        $response->assertStatus(200);
        $response->assertSee('Data Objek Pajak (NOP)');
    }

    public function test_sppt_page_can_be_accessed(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $response = $this->get('/sppt');
        $response->assertStatus(200);
        $response->assertSee('Daftar Himpunan Ketetapan Pajak (DHKP)');
    }

    public function test_pembayaran_page_can_be_accessed(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $response = $this->get('/pembayaran');
        $response->assertStatus(200);
        $response->assertSee('Kasir Pembayaran PBB');
    }

    public function test_laporan_dispenda_can_be_accessed(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $response = $this->get('/laporan/dispenda');
        $response->assertStatus(200);
        $response->assertSee('BUKTI PELUNASAN KAMPUNG KE DINAS PENDAPATAN DAERAH (DISPENDA)');
    }

    public function test_kuitansi_can_be_rendered(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $transaksi = TransaksiPembayaran::first();
        if ($transaksi) {
            $response = $this->get("/pembayaran/{$transaksi->id_bayar}/kuitansi");
            $response->assertStatus(200);
            $response->assertSee('TANDA BUKTI PEMBAYARAN PBB-P2');
        }
    }
}

