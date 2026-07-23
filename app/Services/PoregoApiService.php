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
        $this->apiUrl = env('POREGO_API_URL', 'https://back.porego.com/depokargo/api/v1/merchant-api/v1'); 
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
            // Müşteri adını ve soyadını ayırmak için basit bir işlem (Varsayılan olarak son kelime soyadı kabul edilir)
            $nameParts = explode(' ', trim($order->customer_name));
            $surname = count($nameParts) > 1 ? array_pop($nameParts) : 'Bilinmiyor';
            $name = count($nameParts) > 0 ? implode(' ', $nameParts) : $order->customer_name;

            $payload = [
                'customerName' => $name,
                'customerSurname' => $surname,
                'customerPhone' => $order->customer_phone,
                'customerEmail' => $order->customer_email,
                'address' => $order->shipping_address,
                'city' => $order->shipping_city,
                'district' => $order->shipping_district,
                'paymentType' => $order->payment_method === 'cash_on_delivery' ? 'COD' : 'PREPAID',
                'platformOrderId' => (string)$order->id,
                'platformOrderNumber' => $order->order_number,
                'items' => $order->items->map(function ($item) {
                    $sku = $item->variant ? $item->variant->sku : ('SKU-' . $item->product_id);
                    return [
                        'sku' => $sku,
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->toArray(),
            ];

            if ($order->payment_method === 'cash_on_delivery') {
                $payload['codAmount'] = $order->grand_total;
            }

            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'X-Api-Secret' => $this->apiSecret,
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
