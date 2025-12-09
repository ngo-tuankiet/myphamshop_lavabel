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
<<<<<<< HEAD
    // ======================
    // 1. API ĐĂNG KÝ
    // ======================
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
=======
    public function showRegisterForm()
    {
        return view('auth.register');

    }
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|',
            'fullname' => 'required|string|max:150|',
            'email' => 'required|email|unique:pending_registrations|unique:users',
            'password' => 'required|min:1|confirmed',
        ]);
        $token = Str::random(64);
        \DB::table('pending_registrations')->insert([
            'username' => $request->username,
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
>>>>>>> 1e8a500c1c4cfc926ff7ea9d7a119c317d93851f
            'created_at' => now(),
        ]);
<<<<<<< HEAD

        $verifyUrl = url('api/verify-email/' . $token);

        Mail::send('emails.verify', ['url' => $verifyUrl], function ($msg) use ($request) {
            $msg->to($request->email);
            $msg->subject('Xác minh tài khoản MyPhamShop');
        });

        return response()->json([
            'success' => true,
            'message' => 'Vui lòng kiểm tra email để xác minh tài khoản!'
        ]);
=======
        $verifyUrl = url('/verify-email/' . $token);
        Mail::send('emails.verify', ['url' => $verifyUrl], function ($msg) use ($request) {
            $msg->to($request->email);
            $msg->subject('Xác minh tài khoản MyPhamShop');
        });

        return back()->with('message', 'Vui lòng kiểm tra email của bạn để xác minh tài khoản.');

    }
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:1',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user)
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống']);
        if (is_null($user->email_verified_at))
            return back()->withErrors(['email' => 'Tài khoản chưa xác minh Gmail. Vui lòng kiểm tra email']);
        if (!Hash::check($request->password, $user->password))
            return back()->withErrors(['password' => 'Mật khẩu không chính xác'])->withInput();

        Auth::login($user);
        $request->session()->regenerate();
        return redirect('/')->with('success', 'Đăng nhập thành công!');



>>>>>>> 1e8a500c1c4cfc926ff7ea9d7a119c317d93851f
    }

    // ======================
    // 2. API XÁC MINH EMAIL
    // ======================
    public function verifyEmail($token)
    {
        $pending = DB::table('pending_registrations')->where('token', $token)->first();

        if (!$pending) {
            return "Token không hợp lệ hoặc đã hết hạn.";
        }

        // Tạo user vào bảng users
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

    // ======================
    // 3. API LOGIN
    // ======================
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

        // Lấy user
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User không tồn tại'
            ], 404);
        }

        // Kiểm tra mật khẩu cũ có đúng không
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu cũ không chính xác'
            ], 400);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }
    // ======================
    // 4. LOGOUT (FE tự xoá token/session)
    // ======================
    public function logout()
    {
        return response()->json([
            'success' => true,
            'message' => 'Đã logout'
        ]);
    }
}
