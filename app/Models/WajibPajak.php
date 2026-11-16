<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WajibPajak extends Model
{
    use HasFactory;

    protected $table = 'wajib_pajak';
    protected $primaryKey = 'id_wp';

    protected $fillable = [
        'nik',
        'nama_wp',
        'alamat_wp',
        'rt_wp',
        'rw_wp',
        'dusun_wp',
        'no_hp',
    ];

    public function objekPajak()
    {
        return $this->hasMany(ObjekPajak::class, 'id_wp', 'id_wp');
    }

    public function getAlamatLengkapAttribute(): string
    {
        $dusun = $this->dusun_wp ? "Dusun {$this->dusun_wp}, " : "";
        return "{$this->alamat_wp}, {$dusun}RT {$this->rt_wp} / RW {$this->rw_wp}";
    }

    public function getNikMaskedAttribute(): string
    {
        if (!$this->nik) return '-';
        if (strlen($this->nik) >= 16) {
            return substr($this->nik, 0, 6) . '******' . substr($this->nik, -4);
        }
        return substr($this->nik, 0, 2) . '****' . substr($this->nik, -2);
    }
}

