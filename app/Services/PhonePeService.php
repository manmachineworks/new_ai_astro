<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhonePeService
{
    protected $merchantId;
    protected $saltKey;
    protected $saltIndex;
    protected $env;
    protected $baseUrl;
    protected $callbackUrl;

    public function __construct()
    {
        $this->merchantId = config('services.phonepe.merchant_id');
        $this->saltKey = config('services.phonepe.salt_key');
        $this->saltIndex = config('services.phonepe.salt_index');
        $this->env = config('services.phonepe.env');
        $this->callbackUrl = config('services.phonepe.callback_url');

        $this->baseUrl = $this->env === 'PROD'
            ? 'https://api.phonepe.com/apis/hermes'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }

    public function initiatePayment(string $merchantTxnId, float $amount, string $userId, string $mobileNumber = null)
    {
        // PhonePe expects amount in paise (integer)
        $amountInPaise = (int) ($amount * 100);

        $payload = [
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $merchantTxnId,
            'merchantUserId' => $userId,
            'amount' => $amountInPaise,
            'redirectUrl' => config('services.phonepe.redirect_url'),
            'redirectMode' => 'POST',
            'callbackUrl' => $this->callbackUrl,
            'mobileNumber' => $mobileNumber,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE'
            ]
        ];

        $base64Payload = base64_encode(json_encode($payload));
        $checksum = hash('sha256', $base64Payload . "/pg/v1/pay" . $this->saltKey) . '###' . $this->saltIndex;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
        ])->post($this->baseUrl . '/pg/v1/pay', [
                    'request' => $base64Payload
                ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('PhonePe Payment Initiation Failed', ['response' => $response->body()]);
        return null;
    }

    public function verifyCallback(string $base64Payload, string $xVerify): bool
    {
        $calculatedChecksum = hash('sha256', $base64Payload . $this->saltKey) . '###' . $this->saltIndex;
        return $calculatedChecksum === $xVerify;
    }
}
