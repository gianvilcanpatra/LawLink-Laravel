<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validasi data yang diterima
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Buat user baru
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Kirim respon berhasil
            return response()->json(['message' => 'Registrasi berhasil', 'user' => $user], 201);
        } catch (\Exception $e) {
            // Tangkap dan kirim pesan error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Proses validasi login, Anda mungkin sudah punya ini
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                // Jika login berhasil, arahkan ke LawyerProfilePage
                return response()->json(['redirect' => 'Lawyer-Page'], 200);
            }

            // Jika login gagal, kirim pesan error
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            // Tangkap dan kirim pesan error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
