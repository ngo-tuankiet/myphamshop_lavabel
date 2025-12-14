<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $role = $request->query('role');
        if ($role !== null) {
            if (in_array($role, [User::ROLE_USER, User::ROLE_ADMIN])) {
                $query->where('role', $role);
            }
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('fullname', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest('id')->paginate(15);

        return response()->json([
            'message' => 'Lấy danh sách người dùng thành công.',
            'data' => $users,
        ], 200);
    }

    public function show($id)
    {
        $user = User::with('orders')->findOrFail($id);

        return response()->json([
            'message' => 'Lấy chi tiết người dùng thành công.',
            'data' => $user,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validRoles = [User::ROLE_USER, User::ROLE_ADMIN];

        $request->validate([
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'fullname' => 'nullable|string|max:145',
            'role' => ['required', 'integer', Rule::in($validRoles)], // Cập nhật vai trò
        ]);

        DB::beginTransaction();

        try {
            $user->update([
                'email' => $request->email,
                'fullname' => $request->fullname,
                'role' => $request->role,
            ]);

            DB::commit();

            $user->load('orders');

            return response()->json([
                'message' => 'Cập nhật thông tin người dùng thành công.',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Lỗi server khi cập nhật người dùng.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        DB::beginTransaction();

        try {
            if ($user->orders()->exists()) {
                return response()->json([
                    'error' => 'Không thể xóa người dùng này vì họ đã có đơn hàng liên kết. Vui lòng xử lý đơn hàng trước.'
                ], 409); 
            }

            $user->favourites()->detach();

            $user->delete();

            DB::commit();

            return response()->json([
                'message' => 'Xóa người dùng thành công.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Lỗi server khi xóa người dùng.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
