# Catatan Projek & Panduan Sistem Informasi PBB Berbasis Web
*Studi Kasus: Kampung Sidomulyo Punggur, Kec. Punggur, Kab. Lampung Tengah*

---

## 1. Status Implementasi & Arsitektur
- **Kerangka Kerja (Framework):** **Laravel 12** (PHP 8.2+)
- **Basis Data:** **MySQL** (`db_pbb_sidomulyo`) dengan InnoDB & Karakter UTF8MB4
- **Standar Normalisasi:** **3NF Murni** (6 Tabel Utama + 1 Tabel Audit Log)
- **Otomasi Basis Data:** **4 Database Triggers** untuk kalkulasi otomatis saldo, akumulasi terbayar, dan status lunas secara *real-time*.
- **Antarmuka (Frontend):** Bootstrap 5, Bootstrap Icons, Desain Responsif & Print-Ready Stylesheet.
- **Hasil Pengujian Otomatis:** **12/12 Tests PASS (100%)**

---

## 2. Struktur Modul & Fitur yang Telah Dibangun

### A. Portal Publik Warga (`/`)
- **Pencarian Cepat NOP:** Warga memasukkan 18 digit NOP untuk memeriksa tagihan tanpa perlu login.
- **Proteksi Privasi (UU PDP):** NIK dan nomor kontak disensor secara otomatis (contoh: `180204******0001`).
- **Konfirmasi Transfer Bank Mandiri:** Warga yang telah mentransfer via bank/ATM/M-Banking dapat mengunggah foto struk slip transfer langsung dari HP.

### B. Dashboard & Analitik Eksekutif (`/dashboard`)
- **Statistik KPI:** Target Baku Ketetapan PBB, Realisasi Kas Terkumpul, Sisa Piutang, dan Persentase Capaian.
- **Progres Visual:** Progress bar interaktif capaian tahunan.
- **Tabel Rekapitulasi per Dusun:** Rincian target vs realisasi Dusun 1, Dusun 2, Dusun 3, dst.
- **Peringatan Antrean Transfer Bank:** Notifikasi otomatis ketika ada warga yang mengunggah bukti bayar yang belum diverifikasi.

### C. Loket Kasir & Verifikasi Pembayaran (`/pembayaran`)
- **Pembayaran Tunai Loket:** Petugas melayani warga yang datang langsung ke balai desa, mendukung pelunasan penuh maupun cicilan bertahap.
- **Approval Workflow Transfer Bank:** Petugas memeriksa mutasi rekening bank kas kampung, lalu menekan tombol **"Setujui"** atau **"Tolak"** (disertai alasan penolakan).
- **Cetak Kuitansi Resmi (`/pembayaran/{id}/kuitansi`):** Tanda Bukti Pembayaran PBB berstandar formal lengkap dengan Kop Surat Balai Kampung Sidomulyo dan tanda tangan petugas/penyetor.

### D. Manajemen DHKP & Impor Massal (`/sppt`)
- **Daftar SPPT Digital:** Menggantikan lembaran tebal kertas DHKP fisik. Dilengkapi filter status (Lunas, Cicilan, Belum Lunas) dan filter Dusun/Tahun.
- **Modul Impor Massal CSV (`/sppt/import`):** Mampu mengunggah ribuan baris data DHKP tahunan dari Dispenda dalam hitungan detik. Tersedia contoh berkas di [sample_dhkp.csv](file:///D:/Perpajakan/sample_dhkp.csv).
- **Log Audit Impor (`log_import_dhkp`):** Merekam riwayat unggah file, waktu impor, total baris berhasil dan gagal.

### E. Master Wajib Pajak & Objek Pajak (`/wajib-pajak`, `/objek-pajak`)
- **Mendukung 1 WP Banyak NOP:** 1 profil warga dapat menampung daftar seluruh bidang tanah/bangunan miliknya secara terpusat tanpa duplikasi.

### F. Pelaporan Resmi & Dokumen Dispenda (`/laporan`)
- **Laporan Rekapitulasi Dispenda (`/laporan/dispenda`):** Format resmi rekapitulasi pelunasan untuk diserahkan ke Dinas Pendapatan Daerah Lampung Tengah, dilengkapi tanda tangan Kepala Kampung Sidomulyo dan Petugas PBB (Bapak Karmin).
- **Laporan Penerimaan Kas PBB (`/laporan/realisasi`):** Rincian mutasi uang masuk harian/bulanan.
- **Laporan Daftar Tunggakan Warga (`/laporan/tunggakan`):** Daftar piutang warga per Dusun/RT untuk memudahkan penagihan lapangan.

---

## 3. Akun Pengguna Bawaan (Default Login)

| Role | Username | Password | Keterangan |
|---|---|---|---|
| **Administrator** | `admin` | `password` | Pengelola TI Kampung / Akses Penuh |
| **Petugas Pajak** | `petugas_karmin` | `password` | Bpk. Karmin (Loket Kasir & Verifikasi Transfer) |
| **Kepala Kampung** | `kakam_sidomulyo` | `password` | Pimpinan (Monitoring & Cetak Laporan) |

---

## 4. Cara Menjalankan Aplikasi di Komputer Lokal

Buka terminal di direktori `D:\Perpajakan`, lalu jalankan:

```bash
php artisan serve
```

Aplikasi dapat langsung diakses melalui web browser:
- **Portal Publik Warga:** [http://localhost:8000](http://localhost:8000)
- **Halaman Masuk Petugas:** [http://localhost:8000/login](http://localhost:8000/login)
