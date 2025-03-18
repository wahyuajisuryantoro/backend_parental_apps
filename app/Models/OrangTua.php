<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrangTua extends Model
{
    protected $table = 'apps_detail_orang_tua';

    protected $fillable = [
        'username',
        'nama',
        'email',
        'no_kk',
        'telepon',
        'foto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

      /**
     * Get all anak (siswa)
     */
    public function anak(): HasMany
    {
        return $this->hasMany(Siswa::class, 'no_kk', 'no_kk');
    }

    /**
     * Get pengajuan absensi
     */
    public function pengajuanAbsensi(): HasMany
    {
        return $this->hasMany(PengajuanAbsensi::class, 'id_orang_tua');
    }
}
