<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel database.
     */
    protected $table = 'users';

    /**
     * Primary key.
     */
    protected $primaryKey = 'id_user';

    /**
     * Tipe primary key.
     */
    protected $keyType = 'int';

    public $incrementing = true;

    /**
     * Kolom yang boleh diisi.
     */
    protected $fillable = [
        'username',
        'password_hash',
        'nama_lengkap',
        'no_hp',
        'role',
        'foto',
        'is_active',
        'id_desa',
    ];

    /**
     * Kolom yang disembunyikan.
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Pengaturan autentikasi password.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Nama kolom password pada database.
     */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    /**
     * Relasi dengan profil desa.
     */
    public function profilDesa()
    {
        return $this->belongsTo(
            ProfilDesa::class,
            'id_desa',
            'id_desa'
        );
    }

    /**
     * Relasi transaksi yang diverifikasi petugas.
     */
    public function transaksiDiverifikasi()
    {
        return $this->hasMany(
            TransaksiPembayaran::class,
            'id_petugas_verifikator',
            'id_user'
        );
    }

    /**
     * Relasi log import DHKP.
     */
    public function logImport()
    {
        return $this->hasMany(
            LogImportDhkp::class,
            'id_user',
            'id_user'
        );
    }

    /**
     * Pemeriksaan role admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Pemeriksaan role petugas.
     */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Pemeriksaan role pimpinan.
     */
    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }
}
