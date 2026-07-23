<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PoregoApiService
{
    protected $apiKey;
    protected $apiSecret;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('POREGO_API_KEY');
        $this->apiSecret = env('POREGO_API_SECRET');
        // Varsa Porego API URL'sini .env'den alalım, yoksa varsayılan veya placeholder bir adres
        $this->apiUrl = env('POREGO_API_URL', 'https://api.porego.com/v1'); 
    }

    /**
     * Siparişi Porego'ya gönderir (Kargo oluşturma işlemi)
     */
    public function sendOrder(Order $order)
    {
        if (!$this->apiKey || !$this->apiSecret) {
            Log::warning("Porego API Key veya Secret eksik olduğu için #{$order->order_number} numaralı sipariş gönderilemedi.");
            return false;
        }

        try {
            // TODO: Porego'nun gerçek dokümantasyonuna göre aşağıdaki JSON yapısı ve Endpoint URL (/orders veya /shipments) güncellenecektir.
            $payload = [
                'order_number' => $order->order_number,
                'customer' => [
                    'name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'shipping_address' => [
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'district' => $order->shipping_district,
                ],
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->toArray(),
                'total_amount' => $order->grand_total,
                'payment_method' => $order->payment_method,
                // Eğer sipariş kapıda ödemeli ise tahsilat tutarı gönderilir
                'is_cash_on_delivery' => $order->payment_method === 'cash_on_delivery',
                'cod_amount' => $order->payment_method === 'cash_on_delivery' ? $order->grand_total : 0,
            ];

            // Gerçek API uç noktasını (Endpoint) dokümantasyon gelince düzelteceğiz.
            $response = Http::withHeaders([
                'X-Porego-Api-Key' => $this->apiKey,
                'X-Porego-Api-Secret' => $this->apiSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/orders", $payload);

            if ($response->successful()) {
                Log::info("Sipariş başarıyla Porego'ya iletildi. Sipariş No: {$order->order_number}", $response->json());
                
                // İsteğe bağlı olarak dönen kargo takip kodunu veritabanına kaydedebiliriz.
                // $order->update(['cargo_tracking_code' => $response->json('tracking_code')]);
                
                return true;
            } else {
                Log::error("Porego API Sipariş Gönderim Hatası. Sipariş No: {$order->order_number}", [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'payload' => $payload,
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Porego API Sipariş Gönderim İstisnası. Sipariş No: {$order->order_number}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
