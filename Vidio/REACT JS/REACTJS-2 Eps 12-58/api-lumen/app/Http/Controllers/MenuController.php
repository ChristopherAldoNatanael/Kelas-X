<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Add this line
use Illuminate\Support\Facades\DB; // Add this line
use Illuminate\Support\Facades\File;
use Carbon\Carbon; // Add this use statement at the top of the file

class MenuController extends Controller
{
    /**
     * Format response sukses
     *
     * @param mixed $data
     * @param string $pesan
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $pesan = 'Operasi berhasil', $status = 200)
    {
        return response()->json([
            'status' => $status,
            'pesan' => $pesan,
            'data' => $data
        ]);
    }

    /**
     * Format response error
     *
     * @param string $pesan
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($pesan = 'Terjadi kesalahan', $status = 400, $errors = null)
    {
        $responsePayload = [
            'status' => $status,
            'pesan' => $pesan,
            'data' => null
        ];
        if ($errors !== null) {
            $responsePayload['errors'] = $errors;
        }
        return response()->json($responsePayload, $status);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $menus = DB::table('menus')
                ->join('kategoris', 'menus.idkategori', '=', 'kategoris.idkategori')
                ->select('menus.*', 'kategoris.kategori as nama_kategori')
                ->get();

            return response()->json([
                'data' => $menus
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'idkategori' => 'required|numeric',
                'menu'       => 'required|string|max:255',
                'gambar'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'harga'      => 'required|numeric',
                'deskripsi'  => 'nullable|string',
            ]);

            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $menuNameSlug = Str::slug($validatedData['menu']);
                $namaGambar = time() . '_' . $menuNameSlug . '.' . $file->getClientOriginalExtension();
                $file->move('upload', $namaGambar); // Moves to public/upload
                // Store only the file name in the database
                $gambarPath = $namaGambar;
            } else {
                // This case should not be reached if 'gambar' is 'required|image' due to validation
                return $this->errorResponse('File gambar wajib diisi dan valid.', 422);
            }

            $menu = Menu::create([
                'idkategori' => $validatedData['idkategori'],
                'menu'       => $validatedData['menu'],
                'gambar'     => $gambarPath,
                'harga'      => $validatedData['harga'],
                'deskripsi'  => $validatedData['deskripsi'] ?? null,
            ]);

            return $this->successResponse($menu, 'Data Berhasil Ditambahkan');
        } catch (ValidationException $e) {
            Log::error('Validation Error creating menu: ' . $e->getMessage(), ['errors' => $e->errors()]); // Add logging
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        } catch (\Exception $e) {
            Log::error('Error creating menu: ' . $e->getMessage()); // Add logging
            return $this->errorResponse('Data tidak berhasil Ditambahkan: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'idkategori' => 'required|exists:kategoris,idkategori',
                'menu' => 'required|string|max:255',
                'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'harga' => 'required|numeric|min:0'
            ]);

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(base_path('public/upload'), $filename);

                $result = DB::table('menus')->insert([
                    'idkategori' => $request->idkategori,
                    'menu' => $request->menu,
                    'gambar' => $filename,
                    'harga' => $request->harga,
                    'created_at' => Carbon::now(), // Changed from now()
                    'updated_at' => Carbon::now()  // Changed from now()
                ]);

                if ($result) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Menu berhasil ditambahkan'
                    ], 201);
                }
            }

            throw new \Exception('Gagal mengupload gambar');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return $this->errorResponse('Menu tidak ditemukan', 404);
        }

        return $this->successResponse($menu, 'Menu berhasil ditemukan');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $data = $request->all();

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . str_replace(' ', '-', strtolower($request->menu)) . '.' . $extension;
                $file->move(public_path('upload'), $filename);

                // HANYA SIMPAN NAMA FILE SAJA
                $data['gambar'] = $filename;

                // Hapus gambar lama jika ada
                if ($menu->gambar) {
                    $oldFilePath = public_path('upload/' . $menu->gambar);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            }

            $menu->update($data);
            return response()->json([
                'message' => 'Menu berhasil diupdate',
                'data' => $menu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengupdate menu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $menu = Menu::find($id);

            if (!$menu) {
                return $this->errorResponse('Menu tidak ditemukan', 404);
            }

            $menu->delete();

            return $this->successResponse(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting menu: ' . $e->getMessage()); // Add logging
            return $this->errorResponse('Data gagal dihapus: ' . $e->getMessage());
        }
    }
}
