<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán</title>
</head>
<body>
    <h2>Thông tin giao hàng</h2>

    <form method="POST" action="{{ route('checkout.place') }}">
        @csrf

        Họ và tên: <input type="text" name="fullname" required><br><br>
        Số điện thoại: <input type="text" name="phone" required><br><br>
        Địa chỉ nhận hàng: <input type="text" name="address" required><br><br>
        Ghi chú: <textarea name="note"></textarea><br><br>

        <h3>Giỏ hàng của bạn:</h3>

        <ul>
            @foreach ($cart as $item)
                <li>{{ $item['name'] }} - {{ $item['quantity'] }} x {{ number_format($item['price']) }}đ</li>
            @endforeach
        </ul>

        <button type="submit">Đặt hàng</button>
    </form>

</body>
</html>
