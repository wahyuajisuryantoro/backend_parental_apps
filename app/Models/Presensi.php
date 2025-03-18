<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Presensi extends Model
{
    protected $table = 'tbl_presensi';
    public $timestamps = false;

    protected $fillable = [
        'keterangan',
        'nama_siswa',
        'id_siswa',
        'rfid',
        'kelas',
        'waktu',
        'tanggal',
        'foto'
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'id_siswa' => 'string'
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    
    public function getPresensiByIdSiswa($id_siswa)
    {
        $result = DB::table('tbl_presensi')
                    ->where('id_siswa', $id_siswa)
                    ->get();
        foreach ($result as $row) {
            $row->keterangan = $this->convertKeterangan($row->keterangan);
        }

        return $result;
    }

    // Fungsi untuk mendapatkan data presensi berdasarkan no_kk
    public function getPresensiByNoKk($no_kk)
    {
        $result = DB::table('tbl_presensi')
                    ->join('mstr_siswa', 'mstr_siswa.id', '=', 'tbl_presensi.id_siswa')
                    ->where('mstr_siswa.no_kk', $no_kk)
                    ->select('tbl_presensi.*')
                    ->get();
        foreach ($result as $row) {
            $row->keterangan = $this->convertKeterangan($row->keterangan);
        }

        return $result;
    }

    // Fungsi untuk konversi kode keterangan ke teks
    private function convertKeterangan($kode)
    {
        switch ($kode) {
            case 'h':
                return 'Hadir';
            case 'i':
                return 'Izin';
            case 's':
                return 'Sakit';
            default:
                return 'Tidak Diketahui';
        }
    }
}
