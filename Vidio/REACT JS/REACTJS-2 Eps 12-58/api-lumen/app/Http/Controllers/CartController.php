<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Tambah item ke cart
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'idpelanggan' => 'required|exists:pelanggans,idpelanggan',
                'idmenu' => 'required|exists:menus,idmenu',
                'qty' => 'required|integer|min:1'
            ]);

            // Cek jika item sudah ada di cart
            $existing = Cart::where('idpelanggan', $request->idpelanggan)
                ->where('idmenu', $request->idmenu)
                ->first();

            if ($existing) {
                $existing->qty += $request->qty;
                $existing->save();
                $cart = $existing;
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan item ke keranjang'
            ], 500);
        }
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

    // Mengupdate kuantitas item di cart
    public function updateQuantity(Request $request, $idcart)
    {
        try {
            $cart = Cart::findOrFail($idcart);
            $cart->qty = $request->qty;
            $cart->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Kuantitas berhasil diperbarui',
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui kuantitas'
            ], 500);
        }
    }

    // Hapus item dari cart
    public function destroy($idcart)
    {
        try {
            $cart = Cart::findOrFail($idcart);
            $cart->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item berhasil dihapus dari keranjang'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus item'
            ], 500);
        }
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

    // Mengambil isi cart beserta detail menu dan pelanggan
    public function getCartWithDetails($idpelanggan)
    {
        try {
            $cartItems = DB::table('carts')
                ->join('menus', 'carts.idmenu', '=', 'menus.idmenu')
                ->select(
                    'carts.idcart',
                    'carts.idpelanggan',
                    'carts.idmenu',
                    'carts.qty',
                    'menus.menu',
                    'menus.gambar',
                    'menus.harga'
                )
                ->where('carts.idpelanggan', $idpelanggan)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $cartItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data keranjang'
            ], 500);
        }
    }
}
