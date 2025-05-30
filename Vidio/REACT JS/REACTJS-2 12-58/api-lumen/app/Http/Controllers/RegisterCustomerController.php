<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;

class RegisterCustomerController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'pelanggan' => 'required|string|max:255',
            'email' => 'required|email|unique:pelanggans,email',
            'alamat' => 'required|string|max:255',
            'telp' => 'required|string|max:20',
            'password' => 'required|min:6',
        ]);

        $pelanggan = Pelanggan::create([
            'pelanggan' => $request->pelanggan,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'telp' => $request->telp,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'data' => $pelanggan
        ]);
    }
}
