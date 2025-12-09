<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>

<div class="container-auth" style="flex-direction: column;">
    <div class="auth-box" style="width:500px;">

        <h2>Thông tin của bạn</h2>

        @if(session('success'))
            <p style="color:green;">{{ session('success') }}</p>
        @endif

        <form method="POST" action="/profile/update">
            @csrf
            <label>Họ và tên</label>
            <input type="text" name="fullname" value="{{ $user->fullname }}" required>

            <label>Email</label>
            <input type="text" value="{{ $user->email }}" disabled>

            <button type="submit">Cập nhật</button>
        </form>

        <hr>
        <h3>Đổi mật khẩu</h3>

        @if ($errors->any())
            <div style="color:red;">
                @foreach ($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/profile/change-password">
            @csrf

            <input type="password" name="old_password" placeholder="Mật khẩu cũ" required>
            <input type="password" name="password" placeholder="Mật khẩu mới" required>
            <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>

            <button type="submit">Đổi mật khẩu</button>
        </form>

    </div>
</div>

</body>
</html>
