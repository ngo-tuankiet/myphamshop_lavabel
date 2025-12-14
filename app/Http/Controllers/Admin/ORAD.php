<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ORAD extends Controller
{

    public function index(Request $request)
    {
        $query = Order::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->with('user')->latest()->paginate(15);

        return response()->json([
            'message' => 'Lấy danh sách đơn hàng thành công.',
            'data' => $orders,
        ], 200);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);

        return response()->json([
            'message' => 'Lấy chi tiết đơn hàng thành công.',
            'data' => $order,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ];

        $request->validate([
            'status' => ['required', 'integer', Rule::in($validStatuses)],
        ]);

        DB::beginTransaction();

        try {
            $order->update([
                'status' => $request->status,
            ]);

            DB::commit();

            $order->load(['user', 'items.product']);

            return response()->json([
                'message' => 'Cập nhật trạng thái đơn hàng thành công.',
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Lỗi server khi cập nhật trạng thái đơn hàng.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        DB::beginTransaction();

        try {
            $order->items()->delete();

            $order->delete();

            DB::commit();

            return response()->json([
                'message' => 'Xóa đơn hàng thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Lỗi server khi xóa đơn hàng.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
