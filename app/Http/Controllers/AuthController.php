<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'fullname' => 'required|string|max:150',
            'email'    => 'required|email|unique:users|unique:pending_registrations',
            'password' => 'required|min:6',
        ]);

        $token = Str::random(50);

        DB::table('pending_registrations')->insert([
            'username'  => $request->username,
            'fullname'  => $request->fullname,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'token'     => $token,
            'created_at' => now(),
        ]);

        // Gửi email xác minh
        $verifyUrl = url('api/verify-email/' . $token);

        Mail::send('emails.verify', ['url' => $verifyUrl], function ($msg) use ($request) {
            $msg->to($request->email);
            $msg->subject('Xác minh tài khoản MyPhamShop');
        });

        return response()->json([
            'success' => true,
            'message' => 'Vui lòng kiểm tra email để xác minh tài khoản!'
        ]);
    }

    public function verifyEmail($token)
    {
        $pending = DB::table('pending_registrations')->where('token', $token)->first();

        if (!$pending) {
            return "Token không hợp lệ hoặc đã hết hạn.";
        }

        User::create([
            'username' => $pending->username,
            'fullname' => $pending->fullname,
            'email'    => $pending->email,
            'password' => $pending->password,
            'email_verified_at' => now(),
        ]);

        DB::table('pending_registrations')->where('id', $pending->id)->delete();

        return "Xác minh thành công! Bạn có thể đăng nhập.";
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email không tồn tại'], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email chưa được xác minh'], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Mật khẩu không đúng'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|integer',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User không tồn tại'
            ], 404);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu cũ không chính xác'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }

    public function logout()
    {
        return response()->json([
            'success' => true,
            'message' => 'Đã logout'
        ]);
    }
}
