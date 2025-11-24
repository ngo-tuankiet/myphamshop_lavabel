<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Giỏ hàng của tôi</title>
  <link rel="stylesheet" href="/css/auth.css">
</head>
<body>

  <div class="container-auth" style="flex-direction:column; align-items:center;">
    <div class="auth-box" style="width:700px;">
      <h2>🛒 Giỏ hàng của tôi</h2>

      @if (session('success'))
        <p style="color:green;">{{ session('success') }}</p>
      @endif
      @if (session('error'))
        <p style="color:red;">{{ session('error') }}</p>
      @endif

      @if (empty($cart))
        <p>Giỏ hàng trống. <a href="/">Quay lại mua sắm</a></p>
      @else

        <table width="100%" border="1" cellspacing="0" cellpadding="10">
          <tr style="background:#eee;">
            <th>Hình ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tổng</th>
            <th>Thao tác</th>
          </tr>

          @php $total = 0; @endphp

          @foreach ($cart as $item)
            @php $total += $item['price'] * $item['quantity']; @endphp

            <tr>
              <td><img src="/{{ $item['image'] }}" width="60"></td>
              <td>{{ $item['name'] }}</td>
              <td>{{ number_format($item['price'], 2) }} đ</td>

              <td>
                <form action="{{ route('cart.update') }}" method="POST" style="display:inline;">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                  <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width:60px;">
                  <button type="submit">Cập nhật</button>
                </form>
              </td>

              <td>{{ number_format($item['price'] * $item['quantity'], 2) }} đ</td>

              <td>
                <form action="{{ route('cart.remove') }}" method="POST">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                  <button type="submit" style="color:red;">Xóa</button>
                </form>
              </td>
            </tr>
          @endforeach
        </table>

        <h3 style="text-align:right;margin-top:10px;">
          Tổng tiền: <span style="color:green">{{ number_format($total, 2) }} đ</span>
        </h3>

        <!-- 🔥 CHÈN ĐÚNG ĐOẠN NÚT TIẾN HÀNH ĐẶT HÀNG TẠI ĐÂY -->
        @if(count($cart) > 0)
            <div style="margin-top:20px; text-align:right;">
                <a href="/checkout" class="btn-checkout" 
                style="padding:10px 20px; background:black; color:white; text-decoration:none; border-radius:5px;">
                    Tiến hành đặt hàng →
                </a>
            </div>
        @endif
        <!-- 🔥 END -->

      @endif
    </div>
  </div>

</body>
</html>
