<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if (empty($request->cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $total = collect($request->cart)
                ->sum(fn($i) => $i['price'] * $i['quantity']);

            $orderId = DB::table('orders')->insertGetId([
                'code'            => rand(10000, 99999),
                'user_id'         => $request->user_id ?? null,
                'total_amount'    => $total,
                'status'          => 2, // Pending
                'customer_name'   => $request->fullname,
                'customer_phone'  => $request->phone,
                'customer_address' => $request->address,
                'note'            => $request->note,
                'created_at'      => now()
            ]);

            foreach ($request->cart as $item) {
                DB::table('order_details')->insert([
                    'order_id'   => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price']
                ]);
            }

            DB::commit();

            $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_TmnCode    = "KGFVRRU7";
            $vnp_HashSecret = "14QRXH1WVYJP7X2VTHEXKR1XF9W05BQS";
            $vnp_ReturnUrl  = "http://127.0.0.1:8000/api/vnpay/return";

            $inputData = [
                "vnp_Version"    => "2.1.0",
                "vnp_TmnCode"    => $vnp_TmnCode,
                "vnp_Amount"     => $total * 100,
                "vnp_Command"    => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode"   => "VND",
                "vnp_IpAddr"     => $request->ip(),
                "vnp_Locale"     => "vn",
                "vnp_OrderInfo"  => "Thanh toan don hang #" . $orderId,
                "vnp_OrderType"  => "other",
                "vnp_ReturnUrl"  => $vnp_ReturnUrl,
                "vnp_TxnRef"     => $orderId
            ];

            // SORT + BUILD HASH
            ksort($inputData);
            $hashData = "";
            $query = "";
            $i = 0;

            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }

                $query .= urlencode($key) . "=" . urlencode($value) . "&";
            }

            $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;

            // TRẢ VỀ CHO FE
            return response()->json([
                'success'     => true,
                'message'     => 'Tạo đơn hàng thành công',
                'order_id'    => $orderId,
                'checkoutUrl' => $paymentUrl
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi đặt hàng',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

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
            ->leftJoin('productimage', 'products.id', '=', 'productimage.product_id')
            ->select(
                'order_details.*',
                'products.name as product_name',
                DB::raw('GROUP_CONCAT(productimage.url) as images')
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
