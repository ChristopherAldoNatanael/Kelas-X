<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Build query
            $query = DB::table('orders')
                ->join('pelanggans', 'orders.idpelanggan', '=', 'pelanggans.idpelanggan')
                ->select(
                    'orders.*',
                    'pelanggans.pelanggan'
                );

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tglorder', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Get results ordered by newest first
            $orders = $query->orderBy('orders.idorder', 'desc')->get();

            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // Endpoint khusus untuk filter tanggal
    public function getByDate(Request $request)
    {
        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $orders = DB::table('orders')
                ->join('pelanggans', 'orders.idpelanggan', '=', 'pelanggans.idpelanggan')
                ->select(
                    'orders.*',
                    'pelanggans.pelanggan'
                )
                ->whereBetween('tglorder', [$startDate, $endDate])
                ->orderBy('orders.idorder', 'desc')
                ->get();

            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $order = DB::table('orders')
                ->where('idorder', $id)
                ->update([
                    'bayar' => $request->bayar,
                    'kembali' => $request->kembali,
                    'status' => $request->status
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil diproses'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'idpelanggan' => $request->idpelanggan,
                'tglorder' => $request->tglorder,
                'total' => $request->total,
                'bayar' => $request->bayar,
                'kembali' => $request->kembali,
                'status' => $request->status,
                'payment_method' => $request->payment_method
            ]);

            // Create order details
            foreach ($request->items as $item) {
                OrderDetail::create([
                    'idorder' => $order->idorder,
                    'idmenu' => $item['idmenu'],
                    'jumlah' => $item['jumlah'],
                    'hargajual' => $item['hargajual']
                ]);
            }

            // Clear cart
            Cart::where('idpelanggan', $request->idpelanggan)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order berhasil dibuat',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat order: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getByPelanggan($id)
    {
        try {
            $orders = Order::where('idpelanggan', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching orders: ' . $e->getMessage()
            ], 500);
        }
    }
}
