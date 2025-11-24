<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        return view('cart.index', compact('cart'));
    }
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $product = DB::table('products')->where('id', $productId)->first();
        if (!$product) return back()->with('error', 'sản phẩm không tồn tại');
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => DB::table('productimage')->where('product_id', $productId)->value('url')
            ];
        }
        session(['cart' => $cart]);
        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng');
    }
    public function update(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = max(1, $request->input('quantity', 1));
        $cart = session('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session(['cart' => $cart]);
        }
        return back()->with('success', 'Cập nhật số lượng thành công!');
    }
    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}