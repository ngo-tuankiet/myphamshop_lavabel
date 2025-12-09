<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            "amount" => "required|numeric|min:1000",
            "orderId" => "required|integer"
        ]);

        $client = new Client();

        // thông tin gửi lên PayOS
        $payload = [
            "orderCode" => $request->orderId,
            "amount" => $request->amount,
            "description" => "Thanh toán đơn hàng #" . $request->orderId,
            "returnUrl" => "http://localhost:3000/payment-success?orderId=" . $request->orderId,
            "cancelUrl" => "http://localhost:3000/payment-cancel?orderId=" . $request->orderId
        ];

        try {
            $response = $client->post("https://api.payos.vn/v2/payment-requests", [
                "headers" => [
                    "x-client-id" => env("PAYOS_CLIENT_ID"),
                    "x-api-key" => env("PAYOS_API_KEY"),
                    "Content-Type" => "application/json"
                ],
                "json" => $payload
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json([
                "success" => true,
                "checkoutUrl" => $data["data"]["checkoutUrl"],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Lỗi tạo thanh toán",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // PAYOS webhook trả kết quả thanh toán
    public function webhook(Request $request)
    {
        $data = $request->all();

        $orderId = $data["orderCode"] ?? null;
        $status = $data["status"] ?? null; // PAID, CANCELLED, PENDING...

        if (!$orderId) {
            return response()->json(["success" => false]);
        }

        // Mapping trạng thái PayOS → trạng thái đơn hàng
        $statusCode = match ($status) {
            "PAID" => 2,       // Đã thanh toán → Đã xác nhận
            "CANCELLED" => 5,  // Hủy thanh toán → Hủy đơn hàng
            default => 1,      // Pending → Chờ xác nhận
        };

        DB::table("orders")->where("id", $orderId)->update([
            "status" => $statusCode
        ]);

        return response()->json(["success" => true]);
    }
}
