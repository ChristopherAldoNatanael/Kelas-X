<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;

class LoginCustomerController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $pelanggan = Pelanggan::where('email', $request->email)->first();

        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login gagal: Email tidak ditemukan'
            ], 401);
        }

        if (!Hash::check($request->password, $pelanggan->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login gagal: Password salah'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'customer_token' => \Illuminate\Support\Str::random(60),
                'customer_email' => $pelanggan->email,
                'customer_name' => $pelanggan->pelanggan,
                'customer_id' => $pelanggan->idpelanggan
            ]
        ]);
    }
}
