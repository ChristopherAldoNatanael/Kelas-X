<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Tambah item ke cart
    public function store(Request $request)
    {
        $this->validate($request, [
            'idpelanggan' => 'required|exists:pelanggans,idpelanggan',
            'idmenu' => 'required|exists:menus,idmenu',
            'qty' => 'required|integer|min:1'
        ]);

        // Cek jika sudah ada item yang sama di cart pelanggan, update qty
        $cart = Cart::where('idpelanggan', $request->idpelanggan)
            ->where('idmenu', $request->idmenu)
            ->first();

        if ($cart) {
            $cart->qty += $request->qty;
            $cart->save();
        } else {
            $cart = Cart::create([
                'idpelanggan' => $request->idpelanggan,
                'idmenu' => $request->idmenu,
                'qty' => $request->qty
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'data' => $cart
        ]);
    }

    // Lihat semua cart milik pelanggan
    public function index($idpelanggan)
    {
        $carts = Cart::with('menu')  // Pastikan relasi menu sudah didefinisikan di model
            ->where('idpelanggan', $idpelanggan)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $carts
        ]);
    }

    // Hapus item dari cart
    public function destroy($idcart)
    {
        $cart = Cart::find($idcart);
        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        $cart->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil dihapus'
        ]);
    }

    // Kosongkan cart pelanggan
    public function clear($idpelanggan)
    {
        Cart::where('idpelanggan', $idpelanggan)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }
}
