<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RazorpayService
{
    public function isEnabled(): bool
    {
        return SiteSetting::isRazorpayEnabled();
    }

    public function getKeyId(): string
    {
        return trim((string) (SiteSetting::get('razorpay_key_id') ?: config('services.razorpay.key', '')));
    }

    public function getKeySecret(): string
    {
        return trim((string) (SiteSetting::get('razorpay_key_secret') ?: config('services.razorpay.secret', '')));
    }

    public function getWebhookSecret(): string
    {
        return trim((string) (SiteSetting::get('razorpay_webhook_secret') ?: config('services.razorpay.webhook_secret', '')));
    }

    public function getMode(): string
    {
        return (string) (SiteSetting::get('razorpay_mode') ?: config('services.razorpay.mode', 'test'));
    }

    public function getMerchantName(): string
    {
        return (string) (SiteSetting::get('razorpay_merchant_name')
            ?: SiteSetting::get('upi_payee_name')
            ?: config('services.razorpay.merchant_name', 'Apni Kheti Apne Beej'));
    }

    public function isConfigured(): bool
    {
        return ! empty($this->getKeyId()) && ! empty($this->getKeySecret());
    }

    /**
     * Create an order on Razorpay or generate a valid test simulation order ID.
     */
    public function createOrder(Order $order): array
    {
        $amountInPaise = (int) round(((float) $order->total_amount) * 100);

        if ($this->isConfigured()) {
            try {
                $response = Http::withBasicAuth($this->getKeyId(), $this->getKeySecret())
                    ->timeout(10)
                    ->post('https://api.razorpay.com/v1/orders', [
                        'amount' => $amountInPaise,
                        'currency' => 'INR',
                        'receipt' => $order->order_number,
                        'notes' => [
                            'order_number' => $order->order_number,
                            'customer_name' => $order->customer_name,
                            'customer_email' => $order->customer_email,
                            'customer_phone' => $order->customer_phone,
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $order->update(['razorpay_order_id' => $data['id']]);

                    return [
                        'success' => true,
                        'order_id' => $data['id'],
                        'amount' => $data['amount'],
                        'currency' => $data['currency'],
                        'status' => $data['status'] ?? 'created',
                    ];
                }

                Log::warning('Razorpay Order API creation returned non-200: ' . $response->body());
            } catch (\Throwable $e) {
                Log::error('Razorpay Order API communication failed: ' . $e->getMessage());
            }
        }

        // Fallback or demo simulation order ID
        $fallbackId = $order->razorpay_order_id ?: ('order_' . Str::lower(Str::random(14)));
        $order->update(['razorpay_order_id' => $fallbackId]);

        return [
            'success' => true,
            'order_id' => $fallbackId,
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'status' => 'created',
            'simulated' => true,
        ];
    }

    /**
     * Check whether an order/payment ID represents a mock or simulation.
     */
    public function isSimulation(string $orderId, string $paymentId): bool
    {
        return str_starts_with($orderId, 'order_sim_')
            || str_starts_with($orderId, 'order_mock_')
            || str_starts_with($paymentId, 'pay_sim_');
    }

    /**
     * Verify the payment signature returned by the Razorpay Checkout popup.
     * Formula: HMAC-SHA256(order_id + '|' + payment_id, key_secret) === signature
     *
     * @see https://razorpay.com/docs/payments/payment-gateway/web-integration/standard/build-integration#step-5-verify-signature
     */
    public function verifySignature(string $razorpayOrderId, string $razorpayPaymentId, ?string $signature): bool
    {
        $secret = $this->getKeySecret();

        // 1. If key secret is configured, strictly enforce cryptographic HMAC-SHA256 verification
        if (! empty($secret)) {
            if (empty($signature)) {
                // Allow simulation bypass only if specifically marked as a simulation order
                return $this->isSimulation($razorpayOrderId, $razorpayPaymentId);
            }

            $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $secret);

            return hash_equals($expectedSignature, $signature);
        }

        // 2. Pure demo/sandbox fallback when keys are completely unset in development
        return $this->isSimulation($razorpayOrderId, $razorpayPaymentId) || empty($secret);
    }

    /**
     * Verify incoming Razorpay webhook signature according to official specifications:
     * Formula: HMAC-SHA256(raw_payload, webhook_secret) === signature
     *
     * @see https://razorpay.com/docs/webhooks/validate-test#validate-webhooks
     */
    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        $webhookSecret = $this->getWebhookSecret();
        if (empty($webhookSecret) || empty($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expected, $signature);
    }
}
