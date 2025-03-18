<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrangTuaController extends Controller
{
    // Get Detail OrangTua
    public function getOrangTua(): JsonResponse
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

            return response()->json([
                'status' => 'success',
                'data' => $orangTua
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Get Data Siswa by KK
    public function getAnak(): JsonResponse
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
            $anak = $orangTua->anak;
            if ($anak->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data anak tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $anak
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get Pengajuan Absensi
    public function getPengajuanAbsensi(): JsonResponse
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
            
            $pengajuan = $orangTua->pengajuanAbsensi()->with('siswa')->get();

            if ($pengajuan->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pengajuan absensi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $pengajuan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
