<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class ProfileController extends Controller {
    public function index () {
        $user = Auth:: user();
        return view ('profile.index', compact('user'));

    }
    public function update (Request $request){
        $request->validate([
            'fullname'=> 'required|string|max:150',
        ]);
        return back()->with('success', 'Cập nhật thông tin thành công');
    }
    public function changePassword(Request $request) {
        $request -> validate([
            'old_password' => 'required',
            'password'=> 'required|min:6|confirmed',
        ]);
        $user = Auth:: user();
        if (!Hash:: check($request-> old_password, $user ->password)){
             return back()->withErrors(['old_password' => 'Mật khẩu cũ không đúng']);
        }
        DB:table('users')->where('id',$user->id)->update([
            'password'=>Hash:make($request-> password),

        ]);
        return back() ->with('success','Đổi mật khẩu thành công');
    }

}