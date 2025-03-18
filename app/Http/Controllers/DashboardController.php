<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData(): JsonResponse
    {
        try {
            $user = Auth::user();
            $orangTua = $user->detailOrangTua;
 
            if (!$orangTua) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data orang tua tidak ditemukan'
                ], 404);
            }
            $anakList = Siswa::where('no_kk', $orangTua->no_kk)->get();
 
            $dataAnak = [];
            foreach ($anakList as $anak) {
                $presensi = DB::table('tbl_presensi')
                             ->where('id_siswa', $anak->id)
                             ->orderBy('waktu', 'desc')
                             ->get();
 
                $hadir = $presensi->where('keterangan', 'Hadir')->count();
                $total = $presensi->count();
                $attendance = $total > 0 ? round(($hadir / $total) * 100) : 0;
 
                $dataAnak[] = [
                    'id' => $anak->id,
                    'nama_siswa' => $anak->nama_siswa,
                    'kelas' => $anak->kelas,
                    'foto' => $anak->foto,
                    'status' => $presensi->first() ? $presensi->first()->keterangan : 'Tidak Diketahui',
                    'attendance' => $attendance,
                    'nisn' => $anak->nisn,
                    'no_induk' => $anak->no_induk,
                    'rfid' => $anak->rfid,
                    'kelamin' => $anak->kelamin,
                    'tempat_lahir' => $anak->tempat_lahir,
                    'no_kk' => $anak->no_kk,
                    'alamat' => $anak->alamat,
                    'tanggal_lahir' => $anak->tanggal_lahir,
                    'nama_ayah' => $anak->nama_ayah, 
                    'nama_ibu' => $anak->nama_ibu,
                    'pekerjaan_ayah' => $anak->pekerjaan_ayah,
                    'pekerjaan_ibu' => $anak->pekerjaan_ibu,
                    'jml_sdr' => $anak->jml_sdr
                ];
            }
 
            return response()->json([
                'status' => 'success',
                'data' => [
                    'orang_tua' => [
                        'nama' => $orangTua->nama,
                        'email' => $orangTua->email,
                        'no_kk' => $orangTua->no_kk
                    ],
                    'anak_list' => $dataAnak
                ]
            ]);
 
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPresensiSiswa(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $orangTua = $user->detailOrangTua;
    
            if (!$orangTua) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data orang tua tidak ditemukan'
                ], 404);
            }
    
            $siswa = Siswa::where('id', $request->id_siswa)
                         ->where('no_kk', $orangTua->no_kk)
                         ->first();
    
            if (!$siswa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            $presensi = DB::table('tbl_presensi')
                         ->where('id_siswa', $request->id_siswa)
                         ->orderBy('waktu', 'desc')  
                         ->get();
    
            if ($presensi->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'presensi' => [],
                        'statistik' => [
                            'hadir' => 0,
                            'izin' => 0,
                            'sakit' => 0, 
                            'total' => 0,
                            'attendance' => 0,
                            'status_terakhir' => 'Tidak Diketahui'
                        ]
                    ]
                ]);
            }
    
            $hadir = $presensi->where('keterangan', 'h')->count();  
            $izin = $presensi->where('keterangan', 'i')->count();   
            $sakit = $presensi->where('keterangan', 's')->count();  
            $total = $presensi->count();

            $attendance = $total > 0 ? round(($hadir / $total) * 100) : 0;
            $lastStatus = $presensi->first()->keterangan ?? 'Tidak Diketahui';
    
            return response()->json([
                'status' => 'success',
                'data' => [
                    'presensi' => $presensi,
                    'statistik' => [
                        'hadir' => $hadir,
                        'izin' => $izin,
                        'sakit' => $sakit,
                        'total' => $total,
                        'attendance' => $attendance,
                        'status_terakhir' => $lastStatus 
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data presensi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
}
