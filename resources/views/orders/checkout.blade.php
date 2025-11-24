
@section('content')
<div class="container" style="margin-top:100px;">

    <h2>Thông tin giao hàng</h2>

    @if (session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    <h3>Giỏ hàng của bạn</h3>

    <table border="1" width="100%" cellpadding="10" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Hình</th>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($cart as $item)
            <tr>
                <td><img src="/{{ $item['image'] }}" width="70"></td>
                <td>{{ $item['name'] }}</td>
                <td>{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <h3>Tổng tiền: 
        <strong>
            {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 0, ',', '.') }} đ
        </strong>
    </h3>

    <form action="/place-order" method="POST" style="margin-top:20px;">
        @csrf

        <label>Họ và tên</label>
        <input type="text" name="fullname" required class="form-control"><br>

        <label>Số điện thoại</label>
        <input type="text" name="phone" required class="form-control"><br>

        <label>Địa chỉ giao hàng</label>
        <input type="text" name="address" required class="form-control"><br>

        <label>Ghi chú</label>
        <textarea name="note" class="form-control"></textarea><br>

        <button type="submit" style="padding:10px 20px;">Đặt hàng</button>
    </form>

</div>
@endsection
