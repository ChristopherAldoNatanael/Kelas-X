<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    public function index()
    {
        try {
            $orderDetails = DB::table('order_details')
                ->join('orders', 'order_details.idorder', '=', 'orders.idorder')
                ->join('menus', 'order_details.idmenu', '=', 'menus.idmenu')
                ->select(
                    'order_details.*',
                    'menus.menu',
                    'menus.gambar',
                    'orders.tglorder',
                    'orders.status'
                )
                ->orderBy('order_details.iddetail', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $orderDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching order details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByOrderId($idorder)
    {
        try {
            $orderDetails = DB::table('order_details')
                ->join('menus', 'order_details.idmenu', '=', 'menus.idmenu')
                ->select(
                    'order_details.*',
                    'menus.menu',
                    'menus.gambar',
                    'menus.deskripsi'
                )
                ->where('order_details.idorder', $idorder)
                ->get();

            if ($orderDetails->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order details not found'
                ], 404);
            }

            // Get order information
            $order = DB::table('orders')
                ->join('pelanggans', 'orders.idpelanggan', '=', 'pelanggans.idpelanggan')
                ->select(
                    'orders.*',
                    'pelanggans.pelanggan',
                    'pelanggans.email'
                )
                ->where('orders.idorder', $idorder)
                ->first();

            return response()->json([
                'status' => 'success',
                'order' => $order,
                'details' => $orderDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching order details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $orderDetail = OrderDetail::create([
                'idorder' => $request->idorder,
                'idmenu' => $request->idmenu,
                'jumlah' => $request->jumlah,
                'hargajual' => $request->hargajual
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order detail created successfully',
                'data' => $orderDetail
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating order detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($id);

            $orderDetail->update([
                'jumlah' => $request->jumlah,
                'hargajual' => $request->hargajual
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order detail updated successfully',
                'data' => $orderDetail
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating order detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($id);
            $orderDetail->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Order detail deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting order detail: ' . $e->getMessage()
            ], 500);
        }
    }
}
