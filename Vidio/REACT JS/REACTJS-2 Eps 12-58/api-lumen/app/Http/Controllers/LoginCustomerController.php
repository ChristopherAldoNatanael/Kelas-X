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
                'message' => 'Email tidak ditemukan'
            ], 401);
        }

        if (!Hash::check($request->password, $pelanggan->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password salah'
            ], 401);
        }

        // Generate token
        $token = \Illuminate\Support\Str::random(60);
        $pelanggan->api_token = $token;
        $pelanggan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'customer_id' => $pelanggan->idpelanggan,
                'customer_email' => $pelanggan->email,
                'customer_name' => $pelanggan->pelanggan,
                'customer_token' => $token
            ]
        ]);
    }
}
