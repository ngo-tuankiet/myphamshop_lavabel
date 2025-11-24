
@section('content')
<div class="container" style="margin-top:100px;">

    <h2 style="margin-bottom:20px;">Đơn hàng của tôi</h2>

    @if (session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <table border="1" width="100%" cellpadding="10" style="border-collapse: collapse;">
        <thead style="background:#f4f4f4;">
            <tr>
                <th>STT</th>
                <th>Ngày đặt</th>
                <th>Số lượng SP</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>

        <tbody>
            @php $i = 1; @endphp

            @forelse ($orders as $order)
            <tr>
                <td>{{ $i++ }}</td>

                <td>{{ date('H:i d/m/Y', strtotime($order->created_at)) }}</td>

                <td>
                    {{ DB::table('order_details')->where('order_id', $order->id)->count() }}
                </td>

                <td>{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>

                <td>
                    @if ($order->status == 1)
                        <span style="color: green;">Đã xác nhận</span>
                    @elseif ($order->status == 2)
                        <span style="color: orange;">Đang giao hàng</span>
                    @else
                        <span style="color: gray;">Chờ xác nhận</span>
                    @endif
                </td>

                <td>
                    <a href="/order-detail/{{ $order->id }}" 
                       style="text-decoration:none; font-size:18px;">
                        👁️
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px;">
                    Bạn chưa có đơn hàng nào.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
