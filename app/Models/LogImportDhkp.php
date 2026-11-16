<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogImportDhkp extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'log_import_dhkp';
    protected $primaryKey = 'id_import';

    protected $fillable = [
        'tahun_pajak',
        'nama_file_asli',
        'path_file',
        'total_baris_file',
        'total_sukses',
        'total_gagal',
        'catatan_error',
        'id_user',
        'waktu_import',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

