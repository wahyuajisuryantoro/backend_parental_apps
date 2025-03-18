<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use App\Models\PengajuanAbsensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AbsensiController extends Controller
{
    public function postAbsensiByIdSiswa(Request $request, $id_siswa)
    {
        $validator = Validator::make($request->all(), [
            'jenis_izin' => 'required|string',
            'keterangan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'file_path' => 'nullable|string',
            'file_type' => 'nullable|string',
            'status' => 'required|string',
        ]);


        $siswa = Siswa::find($id_siswa);
        try {
            $absensi = PengajuanAbsensi::create([
                'id_siswa' => $id_siswa,
                'id_orang_tua' => $siswa->orangTua->id,
                'jenis_izin' => $request->jenis_izin,
                'keterangan' => $request->keterangan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'file_path' => $request->file_path,
                'file_type' => $request->file_type,
                'status' => $request->status,
            ]);
            return response()->json([
                'message' => 'Absensi berhasil diajukan',
                'data' => $absensi
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat mengajukan absensi: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server'], 500);
        }
    }

    public function listAbsensiSiswaByLoginUser(Request $request)
    {
        try {
            // Dapatkan user yang sedang login
            $user = Auth::user();

            // Cari data orang tua berdasarkan user yang login
            $orangTua = OrangTua::where('id', $user->id)->first();

            // Jika orang tua tidak ditemukan, kembalikan respon error
            if (!$orangTua) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orang tua tidak ditemukan',
                ], 404);
            }

            // Buat query untuk mengambil data absensi anak-anak
            $query = PengajuanAbsensi::where('id_orang_tua', $orangTua->id)
                ->with('siswa') // Eager load data siswa
                ->orderBy('created_at', 'desc');

            // Filter berdasarkan status jika ada
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            // Filter berdasarkan jenis izin jika ada
            if ($request->has('jenis_izin')) {
                $query->where('jenis_izin', $request->input('jenis_izin'));
            }

            // Filter berdasarkan rentang tanggal jika ada
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_mulai', [
                    $request->input('start_date'), 
                    $request->input('end_date')
                ]);
            }

            // Ambil data dengan pagination
            $perPage = $request->input('per_page', 10);
            $absences = $query->paginate($perPage);

            // Transform data untuk response
            $transformedData = $absences->map(function($absence) {
                return [
                    'id' => $absence->id,
                    'id_siswa' => $absence->id_siswa,
                    'nama_siswa' => $absence->siswa->nama ?? 'Nama Siswa Tidak Tersedia',
                    'jenis_izin' => $absence->jenis_izin,
                    'keterangan' => $absence->keterangan,
                    'tanggal_mulai' => $absence->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $absence->tanggal_selesai->format('Y-m-d'),
                    'status' => $absence->status,
                    'created_at' => $absence->created_at->format('Y-m-d H:i:s')
                ];
            });

            // Kembalikan respon JSON
            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'meta' => [
                    'total' => $absences->total(),
                    'current_page' => $absences->currentPage(),
                    'last_page' => $absences->lastPage(),
                    'per_page' => $absences->perPage(),
                ]
            ]);

        } catch (\Exception $e) {
            // Catat error di log
            Log::error('Kesalahan saat mengambil data riwayat absensi: ' . $e->getMessage());

            // Kembalikan respon error
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
