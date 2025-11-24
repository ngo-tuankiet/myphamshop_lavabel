<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập - MyPhamShop</title>
  <link rel="stylesheet" href="/css/auth.css">
</head>
<body>

  <div class="container-auth">
    <div class="auth-box">
      <h2>ĐĂNG NHẬP</h2>

      @if ($errors->any())
        <div style="color:red; text-align:left;">
          @foreach ($errors->all() as $error)
              <p>• {{ $error }}</p>
          @endforeach
        </div>
      @endif

      @if (session('success'))
        <p style="color:green;">{{ session('success') }}</p>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
      </form>

      <p>Chưa có tài khoản? <a href="{{ route('register.form') }}">Đăng ký ngay</a></p>
    </div>
  </div>
</body>
</html>
