<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Siswa extends Model
{
    protected $table = 'mstr_siswa';
    public $timestamps = false;

    protected $fillable = [
        'nisn',
        'no_induk',
        'nis',
        'nama_siswa',
        'rfid',
        'kelas',
        'status',
        'usia',
        'kelamin',
        'tempat_lahir',
        'no_kk',
        'kk',
        'nik',
        'foto',
        'alamat',
        'tanggal_lahir',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'jml_sdr'
    ];

    /**
     * Get orangtua by no_kk
     */
    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(OrangTua::class, 'no_kk', 'no_kk');
    }

    /**
     * Get pengajuan absensi
     */
    public function pengajuanAbsensi(): HasMany
    {
        return $this->hasMany(PengajuanAbsensi::class, 'id_siswa');
    }

}
