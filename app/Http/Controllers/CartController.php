<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function getCart(Request $request)
    {
        $userId = $request->user_id;

        $cart = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->leftJoin('product_image', 'products.id', '=', 'product_image.product_id')
            ->select(
                'cart_items.id',
                'products.id as product_id',
                'products.name',
                'products.price',
                'cart_items.quantity',
                'product_image.url as image'
            )
            ->where('cart_items.user_id', $userId)
            ->groupBy(
                'cart_items.id',
                'products.id',
                'products.name',
                'products.price',
                'cart_items.quantity',
                'product_image.url'
            )
            ->get();

        return response()->json([
            'success' => true,
            'cart' => $cart
        ]);
    }

    public function add(Request $request)
    {
        $userId = $request->user_id;
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        $exists = DB::table('cart_items')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            DB::table('cart_items')
                ->where('id', $exists->id)
                ->update([
                    'quantity' => $exists->quantity + $quantity
                ]);
        } else {
            DB::table('cart_items')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng'
        ]);
    }

    public function update(Request $request)
    {
        DB::table('cart_items')
            ->where('id', $request->cart_id)
            ->update([
                'quantity' => $request->quantity
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
    }

    public function remove(Request $request)
    {
        DB::table('cart_items')
            ->where('id', $request->cart_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
        ]);
    }

    public function clear(Request $request)
    {
        DB::table('cart_items')
            ->where('user_id', $request->user_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng'
        ]);
    }
}
