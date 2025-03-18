<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AkunOrangTuaController extends Controller
{
    public function getAkunOrangTua()
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'orang_tua') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $parentProfile = $user->detailOrangTua;

            if ($parentProfile) {
                $children = $parentProfile->anak;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $parentProfile->id,
                        'username' => $parentProfile->username,
                        'nama' => $parentProfile->nama,
                        'email' => $parentProfile->email,
                        'no_kk' => $parentProfile->no_kk,
                        'telepon' => $parentProfile->telepon,
                        'foto' => $parentProfile->foto 
                            ? asset('storage/foto_ortu/' . $parentProfile->foto) 
                            : null,
                        'jumlah_anak' => $children->count(),
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Parent profile not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkUsername(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $currentUser = Auth::user();

            $existingUser = User::where('username', $request->username)
                ->where('id', '!=', $currentUser->id)
                ->first();

            return response()->json([
                'success' => true,
                'available' => $existingUser === null,
                'message' => $existingUser === null 
                    ? 'Username tersedia' 
                    : 'Username sudah digunakan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa ketersediaan username',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[a-zA-Z0-9_]+$/',
                    \Illuminate\Validation\Rule::unique('app_login', 'username')
                        ->ignore($user->id, 'id')
                ],
                'telepon' => 'required|string|max:20',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'username.regex' => 'Username hanya boleh berisi huruf, angka, dan underscore',
                'foto.image' => 'File harus berupa gambar',
                'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
                'foto.max' => 'Ukuran gambar maksimal 2MB'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $parentProfile = $user->detailOrangTua;
    
            if (!$parentProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil orang tua tidak ditemukan'
                ], 404);
            }
    
            $user->username = $request->username;
            $user->save();
    
            $parentProfile->username = $request->username;
            $parentProfile->nama = $request->nama;
            $parentProfile->telepon = $request->telepon;
    
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'foto_ortu_' . $parentProfile->id . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('foto_ortu', $filename, 'public');
                $parentProfile->foto = $filename;
            }
    
            $parentProfile->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'nama' => $parentProfile->nama,
                    'username' => $user->username,
                    'telepon' => $parentProfile->telepon,
                    'foto' => $parentProfile->foto 
                        ? asset('storage/foto_ortu/' . $parentProfile->foto)
                        : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
