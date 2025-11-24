<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart))
            return redirect('/cart')->with('error', 'Gio hàng của bạn đang trống');
        return view('orders.checkout', compact('cart'));
    }
    public function placeOrder(Request $request)
    {
        $request->validate([
            'fullame' => 'required|string|max:150',
            'phone' => 'required|string|max:12',
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
        ]);
        $cart = session('cart', []);
        if (empty($cart))
            return redirect('/cart')->with('error', 'Gio hàng  trống,không thể đặt hàng');

        DB::beginTransaction();
        try {
            $orderId = DB::table('orders')->insertGetId([
                'code' => rand(10000, 99999),
                'user_id' => Auth::id(),
                'total_amount' => collect($cart)->sum(function ($item) {
                    return $item['price'] * $item['quantity'];
                }),
                'status' => 1,
                'customer_name' => $request->fullname,
                'customer_phone' => $request->phone,
                'customer_address' => $request->address,
                'note' => $request->note,
                'created_at' => now(),
            ]);
            foreach ($cart as $item) {
                DB::table('order_details')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
            session()->forget('cart');
            DB::commit();
            return redirect('/orders/success')->with('success', 'Đặt hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi đặt hàng' . $e->getMessage());
        }
    }
    public function success()
    {
        return view('orders.success');
    }
    public function myOrders()
    {
        $orders = DB::table('orders')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('orders.my_orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', Auth::id())   // user chỉ xem được đơn của mình
            ->first();

        if (!$order) {
            return redirect('/my-orders')->with('error', 'Không tìm thấy đơn hàng!');
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
            ->get();

        return view('orders.order_detail', compact('order', 'items'));
    }
}