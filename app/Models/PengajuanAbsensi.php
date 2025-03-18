<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanAbsensi extends Model
{
    protected $table = 'apps_pengajuan_absensi';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_siswa',
        'id_orang_tua',
        'jenis_izin',
        'keterangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'file_path',
        'file_type',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'created_at' => 'datetime'
    ];

    /**
     * Get siswa data
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    /**
     * Get orang tua data
     */
    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(OrangTua::class, 'id_orang_tua');
    }
}
