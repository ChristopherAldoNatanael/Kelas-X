<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Pelanggan::all();

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $this->validate($request, [
            'pelanggan' => 'required',
            'alamat' => 'required',
            'telp' => 'required | numeric'
        ]);

        $pelanggan = Pelanggan::create($request->all());

        return response()->json($pelanggan);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $data = Pelanggan::where('idpelanggan', $id)->get(); // Baris lama
        $data = Pelanggan::where('idpelanggan', $id)->first(); // Baris baru

        if (!$data) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404); // Tambahkan ini
        }

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function edit(Pelanggan $pelanggan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Pelanggan::where('idpelanggan', $id)->update($request->all());

        return response()->json("data sudah di update");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);
            $pelanggan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Pelanggan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus pelanggan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource by email.
     *
     * @param  string  $email
     * @return \Illuminate\Http\Response
     */
    public function getByEmail($email)
    {
        $pelanggan = \App\Models\Pelanggan::where('email', $email)->first();

        if ($pelanggan) {
            return response()->json([
                'status' => 'success',
                'data' => $pelanggan
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Pelanggan tidak ditemukan'
        ], 404);
    }

    /**
     * Update the password for the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $id)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        if (!Hash::check($request->old_password, $pelanggan->password)) {
            return response()->json(['message' => 'Password lama tidak sesuai'], 400);
        }

        $pelanggan->password = Hash::make($request->new_password);
        $pelanggan->save();

        return response()->json(['message' => 'Password berhasil diperbarui']);
    }
}
