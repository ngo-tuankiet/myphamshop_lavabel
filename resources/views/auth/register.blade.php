<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký tài khoản</title>
</head>
<body>
  <h2>Đăng ký tài khoản</h2>

  @if ($errors->any())
    <ul style="color:red;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  <form action="{{ route('register') }}" method="POST">
    @csrf
    <label>Tên đăng nhập:</label><br>
    <input type="text" name="username" value="{{ old('username') }}" required><br><br>

    <label>Họ và tên:</label><br>
    <input type="text" name="fullname" value="{{ old('fullname') }}" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="{{ old('email') }}" required><br><br>

    <label>Mật khẩu:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Nhập lại mật khẩu:</label><br>
    <input type="password" name="password_confirmation" required><br><br>

    <button type="submit">Đăng ký</button>
  </form>
</body>
</html>
