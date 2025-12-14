<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class VnPayController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate(['orderId' => 'required|integer']);

        $order = DB::table('orders')->where('id', $request->orderId)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        $vnp_TmnCode    = "KGFVRRU7";
        $vnp_HashSecret = "14QRXH1WVYJP7X2VTHEXKR1XF9W05BQS";
        $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl  = "http://127.0.0.1:8000/api/vnpay/return";

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $order->total_amount * 100,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $request->ip(),
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => "Thanh toan don hang #" . $order->id,
            "vnp_OrderType"  => "other",
            "vnp_ReturnUrl"  => $vnp_ReturnUrl, // ❗ KHÔNG encode
            "vnp_TxnRef"     => $order->id,
        ];

        // Sắp xếp A-Z
        ksort($inputData);

        $hashdata = "";
        $query = "";
        $i = 0;

        foreach ($inputData as $key => $value) {

            // HASH DATA
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }

            // QUERY URL
            $query .= urlencode($key) . "=" . urlencode($value) . "&";
        }

        // Tạo secure hash
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        // URL cuối
        $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;

        return response()->json([
            "success" => true,
            "url"     => $paymentUrl,
            "debug"   => $hashdata
        ]);
    }
    public function vnpReturn(Request $request)
    {
        $data = $request->all();
        $orderId = $data['vnp_TxnRef'] ?? null;
        $responseCode = $data['vnp_ResponseCode'] ?? null;

        if ($responseCode == "00") {

            $order = DB::table('orders')->where('id', $orderId)->first();

            if ($order && $order->user_id) {
                DB::table('cart_items')->where('user_id', $order->user_id)->delete();
            }

            return redirect("http://localhost:3000?success=1");
        }

        return redirect("http://localhost:3000?success=0");
    }




    public function ipnHandler(Request $request)
    {
        $vnp_HashSecret = "14QRXH1WVYJP7X2VTHEXKR1XF9W05BQS"; // SECRET giống createPayment

        $inputData = $request->all();

        if (!isset($inputData['vnp_SecureHash'])) {
            return response()->json([
                "RspCode" => "97",
                "Message" => "Missing secure hash"
            ]);
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);

        // Build HASH DATA chính xác format VNPAY yêu cầu
        $hashData = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHashCheck = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($vnp_SecureHash !== $secureHashCheck) {
            return response()->json([
                "RspCode" => "97",
                "Message" => "Invalid signature"
            ]);
        }

        // Lấy thông tin đơn hàng
        $orderId = $inputData['vnp_TxnRef'] ?? null;
        $responseCode = $inputData['vnp_ResponseCode'];

        if (!$orderId) {
            return response()->json([
                "RspCode" => "01",
                "Message" => "Order not found"
            ]);
        }

        if ($responseCode === "00") {
            DB::table('orders')->where('id', $orderId)->update(["status" => 2]);

            $order = DB::table('orders')->where('id', $orderId)->first();

            if ($order && $order->user_id) {
                DB::table('cart_items')->where('user_id', $order->user_id)->delete();
            }

            return response()->json(["RspCode" => "00", "Message" => "Success"]);
        }



        // Nếu không thành công
        DB::table('orders')->where('id', $orderId)->update([
            "status" => 5
        ]);

        return response()->json([
            "RspCode" => "00",
            "Message" => "Payment Failed"
        ]);
    }
}
