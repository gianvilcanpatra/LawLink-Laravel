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

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();
    //         $token = $user->createToken('AuthToken')->plainTextToken;

    //         return response()->json(['token' => $token], 200);
    //     }

    //     return response()->json(['error' => 'Unauthorized'], 401);
    // }
}
