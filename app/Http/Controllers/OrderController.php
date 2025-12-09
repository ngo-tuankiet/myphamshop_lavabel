<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
   
    public function placeOrder(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:150',
            'phone'    => 'required|string|max:12',
            'address'  => 'required|string|max:255',
            'note'     => 'nullable|string|max:255',
            'cart'     => 'required|array'
        ]);

        $cart = $request->cart; 

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống, không thể đặt hàng'
            ], 400);
        }

        DB::beginTransaction();
        try {

            $orderId = DB::table('orders')->insertGetId([
                'code'            => rand(10000, 99999),
               'user_id' => $request->user_id ?? null,
                'total_amount'    => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']),
                'status'          => 1, 
                'customer_name'   => $request->fullname,
                'customer_phone'  => $request->phone,
                'customer_address'=> $request->address,
                'note'            => $request->note,
                'created_at'      => now(),
            ]);

            foreach ($cart as $item) {
                DB::table('order_details')->insert([
                    'order_id'  => $orderId,
                    'product_id'=> $item['product_id'],
                    'quantity'  => $item['quantity'],
                    'price'     => $item['price']
                ]);
            }
            if ($request->userId) {
            DB::table('cart_items')->where('user_id', $request->userId)->delete();
        }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => ['order_id' => $orderId]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đặt hàng',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ===========================
    // 2. API lấy danh sách đơn hàng của user
    // ===========================
    public function listOrders($userId)
    {
        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $orders
        ]);
    }

    // ===========================
    // 3. API xem chi tiết đơn hàng
    // ===========================
    public function orderDetail($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        $items = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('product_image', 'products.id', '=', 'product_image.product_id')
            ->select(
                'order_details.*',
                'products.name as product_name',
                DB::raw('GROUP_CONCAT(product_image.url) as images')
            )
            ->where('order_details.order_id', $id)
            ->groupBy(
                'order_details.id',
                'order_details.product_id',
                'order_details.quantity',
                'order_details.price',
                'products.name'
            )
            ->get()
            ->map(function ($item) {
                $item->images = explode(',', $item->images);
                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'items' => $items
            ]
        ]);
    }

    // ===========================
    // 4. API hủy đơn hàng
    // ===========================
    public function cancelOrder($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        DB::table('orders')
            ->where('id', $id)
            ->update(['status' => 5]);

        return response()->json([
            'success' => true,
            'message' => 'Hủy đơn hàng thành công'
        ]);
    }
}
