<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembayaran extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pembayaran';
    protected $primaryKey = 'id_bayar';

    protected $fillable = [
        'kode_transaksi',
        'id_sppt',
        'tgl_bayar',
        'jumlah_bayar',
        'metode_bayar',
        'bank_tujuan',
        'nomor_rekening_pengirim',
        'nama_rekening_pengirim',
        'bukti_transfer',
        'nama_penyetor',
        'hubungan_penyetor',
        'status_verifikasi',
        'alasan_penolakan',
        'id_petugas_verifikator',
        'tgl_verifikasi',
        'keterangan',
    ];

    protected $casts = [
        'tgl_bayar' => 'datetime',
        'tgl_verifikasi' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
    ];

    public function tagihanSppt()
    {
        return $this->belongsTo(TagihanSppt::class, 'id_sppt', 'id_sppt');
    }

    public function petugasVerifikator()
    {
        return $this->belongsTo(User::class, 'id_petugas_verifikator', 'id_user');
    }

    public static function generateKodeTransaksi(): string
    {
        $prefix = 'BYR-' . date('Ymd') . '-';
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
        return $prefix . $random;
    }
}

