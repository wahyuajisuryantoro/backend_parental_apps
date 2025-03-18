<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string'
            ], [
                'username.required' => 'Username harus diisi',
                'password.required' => 'Password harus diisi'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::where('username', $request->username)
                ->where('role', 'orang_tua')
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Username atau password salah'
                ], 401);
            }
            $detailOrangTua = $user->detailOrangTua()->first();

            if (!$detailOrangTua) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data orang tua tidak ditemukan'
                ], 404);
            }



            $siswa = $detailOrangTua->anak()->first();

            $idSiswa = null;
            if ($siswa) {
                $idSiswa = $siswa->id;
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'id' => $detailOrangTua->id,
                    'username' => $user->username,
                    'email' => $detailOrangTua->email,
                    'nama' => $detailOrangTua->nama,
                    'id_siswa' => $idSiswa,
                    'token' => $token
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string',
                'email' => 'required|email|unique:app_detail_orang_tua,email',
                'no_kk' => 'required|string|size:16',
                'telepon' => 'required|numeric',
                'username' => 'required|string|unique:app_login,username',
                'password' => 'required|string|min:6'
            ], [
                'nama.required' => 'Nama harus diisi',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'no_kk.required' => 'Nomor KK harus diisi',
                'no_kk.size' => 'Nomor KK harus 16 digit',
                'telepon.required' => 'Nomor telepon harus diisi',
                'telepon.numeric' => 'Nomor telepon harus berupa angka',
                'username.required' => 'Username harus diisi',
                'username.unique' => 'Username sudah digunakan',
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password minimal 6 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $siswaExists = DB::table('mstr_siswa')
                ->where('no_kk', $request->no_kk)
                ->exists();

            if (!$siswaExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nomor KK tidak terdaftar di sistem'
                ], 422);
            }

            DB::beginTransaction();

            try {
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'role' => 'orang_tua'
                ]);
                $orangTua = $user->detailOrangTua()->create([
                    'username' => $request->username,
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'no_kk' => $request->no_kk,
                    'telepon' => $request->telepon
                ]);

                DB::commit();

                $token = $user->createToken('auth-token')->plainTextToken;
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pendaftaran berhasil',
                    'data' => [
                        'id' => $orangTua->id,
                        'username' => $user->username,
                        'email' => $orangTua->email,
                        'nama' => $orangTua->nama,
                        'token' => $token
                    ]
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mendaftar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            if (Auth::check()) {
                PersonalAccessToken::where('tokenable_id', Auth::id())->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil logout'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
