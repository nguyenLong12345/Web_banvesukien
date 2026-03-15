<?php

namespace App\Services\Payment;

class VNPayService
{
    public function __construct(
        protected string $tmnCode,
        protected string $hashSecret,
        protected string $url,
        protected string $returnUrl,
    ) {
    }

    /**
     * Generate VNPay payment URL.
     *
     * @param  array{amount: float, orderInfo: string, userIp: string, txnRef: string, expireMinutes?: int}  $params
     */
    public function createPaymentUrl(array $params): string
    {
        $amount = (int) ($params['amount'] * 100); // VNPay expects amount in VND (smallest unit)
        $orderInfo = $params['orderInfo'] ?? 'Thanh toán vé sự kiện';
        $userIp = $params['userIp'] ?? request()->ip();
        $txnRef = $params['txnRef'] ?? $this->generateTxnRef();
        $expireMinutes = $params['expireMinutes'] ?? 15;

        $createDate = now()->format('YmdHis');
        $expireDate = now()->addMinutes($expireMinutes)->format('YmdHis');

        $inputDataRaw = [
            'vnp_Version' => config('vnpay.version', '2.1.0'),
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_Amount' => $amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $createDate,
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $userIp,
            'vnp_Locale' => config('vnpay.locale', 'vn'),
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_TxnRef' => $txnRef,
            'vnp_ExpireDate' => $expireDate,
        ];

        $inputData = [];
        foreach ($inputDataRaw as $k => $v) {
            if ($v !== null && $v !== '') {
                $inputData[$k] = $v;
            }
        }

        ksort($inputData);
        $hashdata = $this->buildHashData($inputData);
        $secureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);

        // ALWAYS use the exactly hashed string for the URL query to prevent PHP vs VNPay urlencode differences
        return $this->url . '?' . $hashdata . '&vnp_SecureHash=' . $secureHash;
    }

    /**
     * Verify VNPay SecureHash from callback.
     *
     * @param  array<string, string>  $queryParams
     */
    public function verifyReturnHash(array $queryParams): bool
    {
        $secureHash = $queryParams['vnp_SecureHash'] ?? '';

        $inputDataRaw = [];
        foreach ($queryParams as $key => $value) {
            if (str_starts_with($key, 'vnp_') && $key !== 'vnp_SecureHash') {
                $inputDataRaw[$key] = $value;
            }
        }

        $inputData = [];
        foreach ($inputDataRaw as $k => $v) {
            if ($v !== null && $v !== '') {
                $inputData[$k] = $v;
            }
        }
        
        ksort($inputData);

        $hashData = $this->buildHashData($inputData);
        $calculatedHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return hash_equals($calculatedHash, $secureHash);
    }

    protected function buildHashData(array $data): string
    {
        $parts = [];
        foreach ($data as $key => $value) {
            $parts[] = urlencode($key) . '=' . urlencode($value);
        }
        return implode('&', $parts);
    }

    public function generateTxnRef(): string
    {
        return now()->format('ymd') . '_' . now()->format('His') . '_' . sprintf('%04d', random_int(0, 9999));
    }
}
