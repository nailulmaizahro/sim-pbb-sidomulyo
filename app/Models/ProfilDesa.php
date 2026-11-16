<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilDesa extends Model
{
    use HasFactory;

    protected $table = 'profil_desa';
    protected $primaryKey = 'id_desa';

    protected $fillable = [
        'kode_desa',
        'nama_desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'nama_kepala_desa',
        'nip_kepala_desa',
        'alamat_kantor',
        'kontak_kantor',
        'logo_desa',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_desa', 'id_desa');
    }
}

