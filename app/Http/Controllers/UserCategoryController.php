<?php

namespace App\Http\Controllers;

use App\Models\Category;

class UserCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'message' => 'Lấy danh sách loại sản phẩm thành công.',
            'data' => $categories,
        ], 200);
    }

}