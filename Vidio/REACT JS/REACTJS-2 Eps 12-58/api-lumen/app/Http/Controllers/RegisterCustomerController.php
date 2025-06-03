<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterCustomerController extends Controller
{
    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'pelanggan' => 'required|string|max:255',
                'email' => 'required|email|unique:pelanggans',
                'password' => 'required|min:6',
                'alamat' => 'required|string',
                'telp' => 'required|string'
            ]);

            $pelanggan = Pelanggan::create([
                'pelanggan' => $request->pelanggan,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'alamat' => $request->alamat,
                'telp' => $request->telp,
                'api_token' => Str::random(60)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil',
                'data' => [
                    'id' => $pelanggan->idpelanggan,
                    'name' => $pelanggan->pelanggan,
                    'email' => $pelanggan->email
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registrasi gagal'
            ], 500);
        }
    }
}
