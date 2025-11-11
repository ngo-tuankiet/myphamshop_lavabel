<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Xác minh email</title>
</head>
<body>
  <h2>Xác minh địa chỉ Gmail của bạn</h2>

  <p>Chúng tôi đã gửi một liên kết xác minh đến email của bạn.</p>

  @if (session('message'))
    <p style="color: green">{{ session('message') }}</p>
  @endif

  <form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit">Gửi lại email xác minh</button>
  </form>
</body>
</html>
