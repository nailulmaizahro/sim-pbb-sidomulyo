<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TagihanSppt;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PembayaranQrisBisnisTest extends TestCase
{
    use DatabaseTransactions;

    public function test_halaman_pembayaran_qris_bisnis_can_be_accessed_and_displays_qris_properly(): void
    {
        $sppt = TagihanSppt::where('status_bayar', '<>', 'Lunas')->first();
        $this->assertNotNull($sppt, 'SPPT yang belum lunas harus tersedia.');

        $response = $this->get(route('portal.upload.form', $sppt->id_sppt));
        $response->assertStatus(200);

        // 1. Gambar QRIS DANA Bisnis ditampilkan dengan judul Pembayaran QRIS
        $response->assertSee('/images/qris_dana_bisnis.png');
        $response->assertSee('Pembayaran QRIS');
        $response->assertSee('QRIS DANA BISNIS');

        // 2. Nominal tagihan yang dipilih ditampilkan dengan jelas
        $response->assertSee('Nominal Tagihan:');
        $response->assertSee(number_format($sppt->sisa_tagihan, 0, ',', '.'));

        // 3. Instruksi pembayaran QRIS jelas
        $response->assertSee('Instruksi Pembayaran');
        $response->assertSee('KAMPUNG SIDOMULYO');
        $response->assertSee('Scan / Pindai QRIS');

        // 4. Tidak ada informasi QRIS lama
        $response->assertDontSee('/images/qris_dana_demo.png');
        $response->assertDontSee('QR Saya DANA Personal');
        $response->assertDontSee('Transfer DANA Personal');

        // 5. Bebas dari label UTS dan bebas dari klaim resmi pemerintah
        $response->assertDontSee('Simulasi UTS');
        $response->assertDontSee('Mode Pengujian UTS');
        $response->assertDontSee('Mode Demonstrasi UTS');
        $response->assertDontSee('PEMBAYARAN RESMI PBB');
        $response->assertSee('demonstrasi proyek');
    }

    public function test_konfirmasi_pembayaran_qris_submits_with_pending_status_and_does_not_auto_lunas(): void
    {
        Storage::fake('public');

        $sppt = TagihanSppt::where('status_bayar', '<>', 'Lunas')->first();
        $originalStatus = $sppt->status_bayar;
        $originalTerbayar = $sppt->total_terbayar;
        $originalSisa = $sppt->sisa_tagihan;

        // Dummy 1x1 pixel JPEG file yang valid untuk validasi rule 'image' tanpa butuh ekstensi GD
        $jpegContent = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $tempPath = tempnam(sys_get_temp_dir(), 'test_img');
        file_put_contents($tempPath, $jpegContent);
        $file = new UploadedFile($tempPath, 'bukti_qris_dana.jpg', 'image/jpeg', null, true);

        $response = $this->post(route('portal.upload.simpan'), [
            'id_sppt' => $sppt->id_sppt,
            'jumlah_bayar' => $sppt->sisa_tagihan,
            'bank_tujuan' => 'QRIS DANA Bisnis',
            'tgl_bayar' => date('Y-m-d'),
            'nama_rekening_pengirim' => 'Akun DANA Warga',
            'nama_penyetor' => 'Warga Penguji',
            'bukti_transfer' => $file,
            'keterangan' => 'Pelunasan via QRIS DANA Bisnis',
        ]);

        $response->assertStatus(302);
        
        $transaksi = TransaksiPembayaran::latest('id_bayar')->first();
        $this->assertNotNull($transaksi);
        $response->assertRedirect(route('portal.pembayaran.status', $transaksi->kode_transaksi));

        // Status di database wajib 'Menunggu Verifikasi' (TIDAK otomatis Lunas)
        $this->assertDatabaseHas('transaksi_pembayaran', [
            'id_sppt' => $sppt->id_sppt,
            'bank_tujuan' => 'QRIS DANA Bisnis',
            'status_verifikasi' => 'Menunggu Verifikasi',
            'nama_penyetor' => 'Warga Penguji',
        ]);

        // Tagihan SPPT tetap TIDAK berubah statusnya menjadi Lunas otomatis sebelum verifikasi
        $spptAfter = TagihanSppt::find($sppt->id_sppt);
        $this->assertEquals($originalStatus, $spptAfter->status_bayar);
        $this->assertEquals($originalTerbayar, $spptAfter->total_terbayar);
        $this->assertEquals($originalSisa, $spptAfter->sisa_tagihan);

        // Halaman status pembayaran saat Menunggu Verifikasi
        $statusPage = $this->get(route('portal.pembayaran.status', $transaksi->kode_transaksi));
        $statusPage->assertStatus(200);
        $statusPage->assertSee('Menunggu Verifikasi Pembayaran');
        $statusPage->assertSee('STATUS: MENUNGGU VERIFIKASI');
        $statusPage->assertSee('Kembali ke Dashboard');
    }

    public function test_halaman_pembayaran_berhasil_menampilkan_format_struk_modern_toko(): void
    {
        $admin = \App\Models\User::first();
        $sppt = TagihanSppt::first();

        $transaksi = TransaksiPembayaran::create([
            'kode_transaksi' => TransaksiPembayaran::generateKodeTransaksi(),
            'id_sppt' => $sppt->id_sppt,
            'tgl_bayar' => now(),
            'jumlah_bayar' => 75000,
            'metode_bayar' => 'Transfer Bank',
            'bank_tujuan' => 'QRIS DANA Bisnis',
            'nama_penyetor' => 'Budi Santoso',
            'status_verifikasi' => 'Terverifikasi',
            'id_petugas_verifikator' => $admin->id_user ?? 1,
            'tgl_verifikasi' => now(),
        ]);

        $response = $this->get(route('portal.pembayaran.status', $transaksi->kode_transaksi));
        $response->assertStatus(200);

        // 1. Tampilkan judul "Pembayaran Berhasil!"
        $response->assertSee('Pembayaran Berhasil!');

        // 2. Ikon centang hijau besar
        $response->assertSee('success-icon-circle');
        $response->assertSee('bi-check2');

        // 3. Tampilkan nominal pembayaran
        $response->assertSee('75.000');

        // 4. Tampilkan metode pembayaran QRIS
        $response->assertSee('QRIS');

        // 5. Tampilkan status transaksi LUNAS
        $response->assertSee('LUNAS');

        // 6. Tampilkan nomor referensi transaksi dan tanggal pembayaran
        $response->assertSee($transaksi->kode_transaksi);
        $response->assertSee(now()->format('d/m/Y'));

        // 7. Tambahkan pesan "Terima kasih telah melakukan pembayaran"
        $response->assertSee('Terima kasih telah melakukan pembayaran');

        // 8. Tambahkan tombol "Kembali ke Dashboard"
        $response->assertSee('Kembali ke Dashboard');
        $response->assertSee(route('dashboard'));

        // 9. API check-status mengembalikan status verified
        $apiResponse = $this->getJson(route('portal.pembayaran.check-status', $transaksi->kode_transaksi));
        $apiResponse->assertStatus(200);
        $apiResponse->assertJson([
            'status' => 'Terverifikasi',
            'is_success' => true,
        ]);
    }
}
