<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhonePeClient
{
    protected $merchantId;
    protected $saltKey;
    protected $saltIndex;
    protected $baseUrl;
    protected $redirectUrl;
    protected $callbackUrl;

    public function __construct()
    {
        $this->merchantId = config('phonepe.merchant_id');
        $this->saltKey = config('phonepe.salt_key');
        $this->saltIndex = config('phonepe.salt_index');
        $this->baseUrl = config('phonepe.base_url');
        $this->redirectUrl = config('phonepe.redirect_url');
        $this->callbackUrl = config('phonepe.callback_url');
    }

    public function createPayment(string $merchantTxnId, float $amount, string $userId, string $mobileNumber)
    {
        $payload = [
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $merchantTxnId,
            'merchantUserId' => $userId,
            'amount' => $amount * 100, // paise
            'redirectUrl' => $this->redirectUrl,
            'redirectMode' => 'POST',
            'callbackUrl' => $this->callbackUrl,
            'mobileNumber' => $mobileNumber,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE',
            ],
        ];

        $base64Payload = base64_encode(json_encode($payload));
        $checksum = hash('sha256', $base64Payload . '/pg/v1/pay' . $this->saltKey) . '###' . $this->saltIndex;

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
            ])->post($this->baseUrl . '/pg/v1/pay', [
                        'request' => $base64Payload,
                    ]);

            Log::info('PhonePe Init Response', ['status' => $response->status(), 'body' => $response->json()]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('PhonePe Init Error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function verifyWebhookSignature($responseBody, $xVerifyHeader): bool
    {
        // $responseBody is the base64 encoded string received in "response" key
        // PhonePe Webhook Format: {"response": "base64..."}
        // Actually, PhonePe sends `base64_payload` in the body key `response`? 
        // Docs: "The Content-Type... application/json... 'response': '<Base64>'"
        // Checksum logic for webhook: SHA256(response + saltKey) + ### + saltIndex

        if (!$this->saltKey)
            return false;

        $generatedSignature = hash('sha256', $responseBody . $this->saltKey) . '###' . $this->saltIndex;

        return hash_equals($generatedSignature, $xVerifyHeader);
    }
}
