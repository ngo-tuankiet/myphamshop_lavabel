<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'images']);

        $search = $request->query('search');
        $brandId = $request->query('brand_id');
        $categoryId = $request->query('category_id');

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        if ($categoryId) {
            $query->where('subcategory_id', $categoryId);
        }

        $products = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'message' => 'Lấy danh sách sản phẩm thành công.',
            'data' => $products,
            'search_parameters' => $request->query(),
        ], 200);
    }

    public function getHotProducts(int $limit = 10): Collection
    {
        return Product::with(['brand', 'images'])
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return $this->formatProductData($product);
            });
    }

    public function getProductDetail($id): array
    {
        $product = Product::with(['brand', 'images'])
            ->findOrFail($id);

        $allImages = $product->images->pluck('url')->toArray();
        // Lấy ảnh đại diện (ảnh đầu tiên)
        $mainImage = $allImages[0] ?? '../default.webp';

        $formattedPrice = number_format($product->price, 0, ',', '.') . 'đ';

        return [
            'id' => $product->id,
            'name' => $product->name,
            'brand' => $product->brand->brand_name ?? 'Chưa xác định',
            'description' => $product->description ?? 'Đang cập nhật...', // Mô tả chi tiết
            'price' => $product->price,
            'formatted_price' => $formattedPrice,
            'main_image' => $mainImage,
            'all_images' => $allImages, // Tất cả ảnh cho gallery
            'stock_quantity' => $product->stock ?? 0,
        ];
    }

    protected function formatProductData(Product $product): array
    {
        $formattedPrice = number_format($product->price, 0, ',', '.') . 'đ';
        $imageUrl = $product->images->first()->url ?? '../default.webp';

        return [
            'id' => $product->id,
            'image' => $imageUrl,
            'name' => $product->name,
            'brand' => $product->brand->brand_name ?? 'Chưa xác định',
            'price' => $product->price,
            'formatted_price' => $formattedPrice,
        ];
    }
}