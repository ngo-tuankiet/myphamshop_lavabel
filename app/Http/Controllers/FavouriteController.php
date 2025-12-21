<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user_id;

        if (!$userId) {
            return response()->json([
                "success" => false,
                "message" => "Thiếu user_id"
            ], 400);
        }

        $favourites = DB::table('favourites')
            ->join('products', 'favourites.product_id', '=', 'products.id')
            ->leftJoin('productimage', 'products.id', '=', 'productimage.product_id')
            ->where('favourites.user_id', $userId)
            ->select(
                'products.id',
                'products.name',
                'products.price',
                DB::raw('MIN(productimage.url) as image')  // tránh duplicate ảnh
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->get();

        // Convert URL ảnh sang dạng FULL PATH để FE load không lỗi
        $favourites->transform(function ($item) {

            // URL từ Cloudinary → dùng trực tiếp
            $item->image = $item->image
                ?? 'https://via.placeholder.com/300x300?text=No+Image';

            $item->formatted_price = number_format($item->price, 0, ',', '.') . 'đ';

            return $item;
        });


        return response()->json([
            "success" => true,
            "data" => $favourites
        ]);
    }

    public function store(Request $request)
    {
        $userId = $request->user_id;
        $productId = $request->product_id;

        if (!$userId || !$productId) {
            return response()->json([
                "success" => false,
                "message" => "Thiếu user_id hoặc product_id"
            ], 400);
        }

        $exists = DB::table('favourites')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return response()->json([
                "success" => false,
                "message" => "Sản phẩm đã tồn tại trong yêu thích"
            ], 409);
        }

        DB::table('favourites')->insert([
            "user_id" => $userId,
            "product_id" => $productId
        ]);

        return response()->json([
            "success" => true,
            "message" => "Đã thêm vào yêu thích"
        ], 201);
    }

    public function destroy(Request $request)
    {
        $userId = $request->user_id;
        $productId = $request->product_id;

        if (!$userId || !$productId) {
            return response()->json([
                "success" => false,
                "message" => "Thiếu user_id hoặc product_id"
            ], 400);
        }

        $deleted = DB::table('favourites')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        if ($deleted === 0) {
            return response()->json([
                "success" => false,
                "message" => "Không tìm thấy sản phẩm trong danh sách yêu thích"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Đã xóa khỏi yêu thích"
        ]);
    }
}
