<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PresensiController extends Controller
{
    public function getPresensiBySiswaID($id_siswa)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak terautentikasi',
                ], 401);
            }
            
            Log::info("Memanggil data presensi untuk siswa dengan ID: " . $id_siswa);
            $presensi = (new Presensi())->getPresensiByIdSiswa($id_siswa);
            
            if ($presensi->isEmpty()) {
                return response()->json([
                    'message' => 'Data presensi tidak ditemukan untuk siswa dengan ID: ' . $id_siswa
                ], 404);
            }
            
            foreach ($presensi as $row) {
                $numbers = preg_replace('/[^0-9]/', '', $row->tanggal);
                if (strlen($numbers) >= 6) {
                    $day = substr($numbers, 0, 2);
                    $month = substr($numbers, 2, 2);
                    $year = substr($numbers, 4, 2);
                    $row->tanggal = "$day/$month/$year";
                } else {
                    $today = date('d/m/y');
                    Log::warning("Format tanggal tidak valid untuk presensi ID: {$row->id}, menggunakan format default: $today");
                    $row->tanggal = $today;
                }
            }

            return response()->json([
                'status' => 'success',  
                'message' => 'Data presensi ditemukan',
                'data' => $presensi
            ], 200);
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat mengambil presensi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
