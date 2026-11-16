<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanSppt extends Model
{
    use HasFactory;

    protected $table = 'tagihan_sppt';
    protected $primaryKey = 'id_sppt';

    protected $fillable = [
        'nop',
        'tahun_pajak',
        'pbb_terhutang',
        'skp',
        'stp',
        'denda',
        'total_tagihan',
        'total_terbayar',
        'sisa_tagihan',
        'tgl_jatuh_tempo',
        'status_bayar',
        'keterangan',
    ];

    protected $casts = [
        'pbb_terhutang' => 'decimal:2',
        'skp' => 'decimal:2',
        'stp' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'tgl_jatuh_tempo' => 'date',
    ];

    public function objekPajak()
    {
        return $this->belongsTo(ObjekPajak::class, 'nop', 'nop');
    }

    public function transaksiPembayaran()
    {
        return $this->hasMany(TransaksiPembayaran::class, 'id_sppt', 'id_sppt');
    }

    public function transaksiSimulasiUts()
    {
        return $this->hasMany(TransaksiSimulasiUts::class, 'id_sppt', 'id_sppt')->orderByDesc('created_at');
    }

    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status_bayar) {
            'Lunas' => '<span class="badge bg-success text-white px-2 py-1 rounded">Lunas</span>',
            'Cicilan' => '<span class="badge bg-warning text-dark px-2 py-1 rounded">Cicilan</span>',
            default => '<span class="badge bg-danger text-white px-2 py-1 rounded">Belum Lunas</span>',
        };
    }
}

