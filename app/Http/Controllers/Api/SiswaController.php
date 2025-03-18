<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    public function getDataSiswaById($id_siswa)
    {
        try {
            $user = Auth::user();
            Log::info("User ID yang login: " . $user->id);
    
            $orangTua = $user->detailOrangTua;
            if (!$orangTua) {
                Log::error('Data orang tua tidak ditemukan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data orang tua tidak ditemukan'
                ], 404);
            }
    
            Log::info("Orang Tua ditemukan, no_kk: " . $orangTua->no_kk);
    
            $siswa = Siswa::where('id', $id_siswa)
                          ->where('no_kk', $orangTua->no_kk)
                          ->first();

            return response()->json([
                'status' => 'success',
                'data' => $siswa
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Error in getDataSiswaById: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }    
}
