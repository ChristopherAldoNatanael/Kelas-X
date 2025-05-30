<?php

namespace App\Http\Controllers;

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
}
