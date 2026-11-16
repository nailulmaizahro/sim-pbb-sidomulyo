<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransaksiSimulasiUts extends Model
{
    use HasFactory;

    protected $table = 'transaksi_simulasi_uts';
    protected $primaryKey = 'id_simulasi';

    protected $fillable = [
        'order_id',
        'id_sppt',
        'nop',
        'tahun_pajak',
        'nama_wp',
        'nama_pembayar',
        'nominal_pembayaran',
        'metode_pembayaran',
        'status_pembayaran',
        'snap_token',
        'transaction_id_gateway',
        'payment_type',
        'waktu_transaksi',
        'waktu_pembayaran',
        'keterangan',
        'payload_gateway',
    ];

    protected $casts = [
        'nominal_pembayaran' => 'decimal:2',
        'waktu_transaksi' => 'datetime',
        'waktu_pembayaran' => 'datetime',
    ];

    public function tagihanSppt()
    {
        return $this->belongsTo(TagihanSppt::class, 'id_sppt', 'id_sppt');
    }

    public function getFormatRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_pembayaran, 0, ',', '.');
    }

    public static function generateOrderId(): string
    {
        return 'SIM-UTS-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
    }
}

