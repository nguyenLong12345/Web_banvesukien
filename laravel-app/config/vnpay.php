<?php

return [
    'tmn_code' => env('VNPAY_TMNCODE', ''),
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url' => env('VNPAY_RETURNURL', ''),
    'ipn_url' => env('VNPAY_IPNURL', ''),
    'version' => '2.1.0',
    'locale' => 'vn',
];
