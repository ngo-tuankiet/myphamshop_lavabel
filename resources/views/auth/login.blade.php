<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 80px auto; text-align:center;">
        <h2>ĐĂNG NHẬP</h2>

        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        @if($errors->any())
            <div style="color:red">
                <ul style="list-style:none; padding:0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div>
                <input type="email" name="email" placeholder="Email" required style="width:100%; padding:10px; margin-bottom:10px;">
            </div>
            <div>
                <input type="password" name="password" placeholder="Mật khẩu" required style="width:100%; padding:10px; margin-bottom:10px;">
            </div>
            <button type="submit" style="width:100%; padding:10px; background:black; color:white;">Đăng nhập</button>
        </form>

        <p style="margin-top:10px;">Chưa có tài khoản?
            <a href="{{ route('register.form') }}">Đăng ký ngay</a>
        </p>
    </div>
</body>
</html>
