<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class UserBrandController extends Controller
{

    public function index(Request $request)
    {
        $query = Brand::query();

        $search = $request->query('search');

        if ($search) {
            $query->where('brand_name', 'LIKE', "%{$search}%");
        }

        $brands = $query->with('logo')->orderBy('id', 'DESC')->paginate(15);
        
        return response()->json([
            'message' => 'Lấy danh sách thương hiệu thành công.',
            'data' => $brands,
        ], 200);
    }

}