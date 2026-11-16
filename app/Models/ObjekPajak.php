<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjekPajak extends Model
{
    use HasFactory;

    protected $table = 'objek_pajak';
    protected $primaryKey = 'nop';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nop',
        'id_wp',
        'letak_objek',
        'rt_op',
        'rw_op',
        'dusun_op',
        'luas_bumi',
        'luas_bangunan',
        'kelas_bumi',
        'kelas_bangunan',
        'njop_bumi',
        'njop_bangunan',
        'perubahan_terakhir',
        'keterangan',
    ];

    public function wajibPajak()
    {
        return $this->belongsTo(WajibPajak::class, 'id_wp', 'id_wp');
    }

    public function tagihanSppt()
    {
        return $this->hasMany(TagihanSppt::class, 'nop', 'nop');
    }

    public function getLokasiLengkapAttribute(): string
    {
        return "{$this->letak_objek}, Dusun {$this->dusun_op}, RT {$this->rt_op}/RW {$this->rw_op}";
    }
}

