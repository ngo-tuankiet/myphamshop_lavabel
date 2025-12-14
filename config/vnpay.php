<?php

return [
    "vnp_TmnCode"    => env("VNPAY_TMNCODE", "KGFVRRU7"),
    "vnp_HashSecret" => env("VNPAY_HASHSECRET", "14QRXH1WVYJP7X2VTHEXKR1XF9W05BQS"),
    "vnp_Url"        => "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html",
    "vnp_ReturnUrl"  => "http://127.0.0.1:8000/api/vnpay/return",
];
