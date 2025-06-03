<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailController extends Controller
{
    public function index()
    {
        try {
            $details = DB::table('details')
                ->join('orders', 'details.idorder', '=', 'orders.idorder')
                ->join('menus', 'details.idmenu', '=', 'menus.idmenu')
                ->join('pelanggans', 'orders.idpelanggan', '=', 'pelanggans.idpelanggan')
                ->join('kategoris', 'menus.idkategori', '=', 'kategoris.idkategori')
                ->select(
                    'details.*',
                    'orders.tglorder',
                    'orders.total',
                    'orders.bayar',
                    'orders.kembali',
                    'orders.status',
                    'menus.menu',
                    'menus.gambar',
                    'menus.harga',
                    'pelanggans.pelanggan',
                    'pelanggans.alamat',
                    'pelanggans.telp',
                    'kategoris.kategori'
                )
                ->orderBy('orders.tglorder', 'desc')
                ->get();

            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getByDate(Request $request)
    {
        try {
            $query = DB::table('details')
                ->join('orders', 'details.idorder', '=', 'orders.idorder')
                ->join('menus', 'details.idmenu', '=', 'menus.idmenu')
                ->select(
                    'details.*',
                    'orders.tglorder',
                    'menus.menu'
                );

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('orders.tglorder', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $details = $query->orderBy('orders.tglorder', 'desc')->get();

            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
