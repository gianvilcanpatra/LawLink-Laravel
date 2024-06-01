<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lawyer;
use Illuminate\Support\Facades\Auth;

class UserController extends AuthController
{
    public function index()
    {
        $user = Auth::user();
        $lawyers = Lawyer::all();
        return response()->json([
            'user' => $user,
            'lawyers' => $lawyers,
        ]);
    }

    public function IndexUser()
    {
        $auth = Auth::user();
        $user = User::first();
        return response()->json([
            'user' => $user,
        ]);
    }
}
