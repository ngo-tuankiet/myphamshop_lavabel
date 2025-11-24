<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
</head>
<body>

<h2>📄 Chi tiết đơn hàng #{{ $order->code }}</h2>

<p><strong>Ngày đặt:</strong> {{ $order->created_at }}</p>
<p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount) }}đ</p>

<h3>Sản phẩm</h3>

<table border="1" cellpadding="10">
    <tr>
        <th>Sản phẩm</th>
        <th>Ảnh</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Tổng</th>
    </tr>

    @foreach ($items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>
                @if ($item->images)
                    <img src="/{{ explode(',', $item->images)[0] }}" width="60">
                @endif
            </td>
            <td>{{ number_format($item->price) }}đ</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price * $item->quantity) }}đ</td>
        </tr>
    @endforeach
</table>

<a href="/my-orders">← Quay lại danh sách đơn hàng</a>

</body>
</html>
