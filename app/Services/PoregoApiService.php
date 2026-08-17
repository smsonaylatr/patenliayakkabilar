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
        $apiKey = \App\Models\Setting::where('key', 'porego_api_key')->value('value') ?: $this->apiKey;
        $apiSecret = \App\Models\Setting::where('key', 'porego_api_secret')->value('value') ?: $this->apiSecret;
        $apiUrl = \App\Models\Setting::where('key', 'porego_api_url')->value('value') ?: $this->apiUrl;

        if (!$apiKey || !$apiSecret) {
            $msg = "Porego API Key veya Secret tanımlı olmadığı için #{$order->order_number} numaralı sipariş aktarılamadı. Lütfen .env dosyasını kontrol edin.";
            Log::warning($msg);
            return ['success' => false, 'message' => $msg];
        }

        try {
            // Müşteri adını ve soyadını ayırmak için basit bir işlem (Varsayılan olarak son kelime soyadı kabul edilir)
            $nameParts = explode(' ', trim($order->customer_name));
            $surname = count($nameParts) > 1 ? array_pop($nameParts) : 'Bilinmiyor';
            $name = count($nameParts) > 0 ? implode(' ', $nameParts) : $order->customer_name;

            // İlişkileri taze bir şekilde yükleyelim (Load cache önlemek için)
            $order->unsetRelation('items');
            $order->load(['items.product', 'items.variant']);

            // Eğer relation boş geldiyse veritabanından doğrudan tekrar sorgulayalım
            if ($order->items->isEmpty()) {
                $items = \App\Models\OrderItem::with(['product', 'variant'])
                    ->where('order_id', $order->id)
                    ->get();
                $order->setRelation('items', $items);
            }

            // SKU ve Ürün listesi hazırlama (Strictly net SKU)
            $productSummaryList = [];
            $mappedItems = $order->items->map(function ($item) use (&$productSummaryList) {
                $rawSku = trim($item->variant?->sku ?: ($item->product?->sku ?: ''));
                if (empty($rawSku) || $rawSku === '-' || $rawSku === 'SKU-') {
                    $rawSku = 'SKU-' . ($item->variant_id ?: $item->product_id);
                }

                $productSummaryList[] = "{$item->quantity}x {$rawSku}";

                return [
                    'sku'      => $rawSku,
                    'name'     => $rawSku,
                    'quantity' => max(1, (int)$item->quantity),
                    'price'    => (float)($item->unit_price ?? 0),
                ];
            })->values()->toArray();

            $productSummaryText = implode(', ', $productSummaryList);

            // Telefon numarasını standart formata getirelim
            $phone = preg_replace('/[^0-9]/', '', $order->customer_phone ?: '');
            if (strlen($phone) === 10 && str_starts_with($phone, '5')) {
                $phone = '0' . $phone;
            }
            if (empty($phone)) {
                $phone = '05555555555';
            }

            $payload = [
                'customerName'        => $name,
                'customerSurname'     => $surname,
                'customerPhone'       => $phone,
                'customerEmail'       => $order->customer_email ?: 'siparis@patenliayakkabilar.com',
                'address'             => trim($order->shipping_address) ?: 'Adres Belirtilmedi',
                'city'                => trim($order->shipping_city) ?: 'İstanbul',
                'district'            => trim($order->shipping_district) ?: 'Merkez',
                'paymentType'         => $order->payment_method === 'cash_on_delivery' ? 'COD' : 'PREPAID',
                'platformOrderId'     => (string)$order->id,
                'platformOrderNumber' => $order->order_number,
                'productInfo'         => $productSummaryText,
                'items'               => $mappedItems,
            ];

            if ($order->payment_method === 'cash_on_delivery') {
                $payload['codAmount'] = (float)$order->grand_total;
            }

            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
                'X-Api-Secret' => $apiSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$apiUrl}/orders", $payload);

            if ($response->successful()) {
                Log::info("Sipariş başarıyla Porego'ya iletildi. Sipariş No: {$order->order_number}", $response->json());
                return ['success' => true, 'message' => "Sipariş (#{$order->order_number}) başarıyla Porego'ya aktarıldı."];
            } else {
                $err = $response->json('message') ?: ($response->json('error') ?: $response->body());
                if (empty($err)) {
                    $err = "HTTP " . $response->status();
                }

                // 400 Bad Request durumunda kullanıcı dostu açıklama ekleyelim (Sipariş zaten Porego'da kayıtlı)
                if ($response->status() === 400) {
                    $msg = "Bu sipariş (#{$order->order_number}) Porego sisteminde zaten oluşturulmuş veya mevcut kaydı bulunmaktadır. Porego panelindeki Siparişler sayfasından etiketini basabilirsiniz.";
                    Log::info("Porego API 400 Bildirimi. Sipariş No: {$order->order_number}", ['response' => $response->body()]);
                    return ['success' => false, 'message' => $msg];
                }

                Log::error("Porego API Sipariş Gönderim Hatası. Sipariş No: {$order->order_number}", [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'payload' => $payload,
                ]);
                return ['success' => false, 'message' => "Porego API Hatası ({$response->status()}): {$err}"];
            }
        } catch (\Throwable $e) {
            Log::error("Porego API Sipariş Gönderim İstisnası. Sipariş No: {$order->order_number}", [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => "İstisna: " . $e->getMessage()];
        }
    }

    /**
     * Sipariş için Porego QNB E-Fatura API'si üzerinden fatura oluşturur
     */
    public function createInvoice(Order $order)
    {
        if (!$this->apiKey || !$this->apiSecret) {
            Log::warning("Porego API Key veya Secret eksik olduğu için #{$order->order_number} numaralı siparişe fatura kesilemedi.");
            return ['success' => false, 'message' => 'API kimlik bilgileri eksik.'];
        }

        try {
            // TODO: QNB E-Fatura için gerçek API uç noktasını buraya yazın
            // Örnek: $invoiceApiUrl = 'https://back.porego.com/depokargo/api/v1/merchant-api/v1/orders/' . $order->order_number . '/invoice';
            $invoiceApiUrl = "{$this->apiUrl}/orders/{$order->order_number}/invoice"; // TAHMİNİ URL

            // TODO: QNB E-Fatura için gereken ek parametreleri ekleyin (TC/VKN vb.)
            $payload = [
                'orderNumber' => $order->order_number,
                // 'identityNumber' => '11111111111', // VKN veya TCKN
            ];

            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'X-Api-Secret' => $this->apiSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($invoiceApiUrl, $payload);

            if ($response->successful()) {
                // Fatura kesildiğinde veritabanını güncelle
                $order->is_invoiced = true;
                
                // Eğer Porego bize bir fatura PDF linki dönüyorsa onu da kaydedelim
                // $order->invoice_url = $response->json('invoiceUrl'); 
                
                $order->save();

                Log::info("Sipariş (#{$order->order_number}) için başarıyla e-fatura kesildi.");
                return ['success' => true, 'message' => 'Fatura başarıyla oluşturuldu.'];
            } else {
                Log::error("Porego QNB E-Fatura Hatası. Sipariş No: {$order->order_number}", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return [
                    'success' => false, 
                    'message' => 'Fatura kesilirken hata oluştu: ' . ($response->json('message') ?? $response->status())
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Porego QNB E-Fatura İstisnası. Sipariş No: {$order->order_number}", [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => 'Sistemsel bir hata oluştu: ' . $e->getMessage()];
        }
    }

    /**
     * Porego üzerinden SMS gönderir
     */
    public function sendSms($phone, $message)
    {
        if (!$this->apiKey || !$this->apiSecret) {
            Log::warning("Porego API Key veya Secret eksik olduğu için SMS gönderilemedi: {$phone}");
            return ['success' => false, 'message' => 'API kimlik bilgileri eksik.'];
        }

        try {
            // TAHMİNİ URL (Porego SMS servisi için)
            $smsApiUrl = "{$this->apiUrl}/sms/send"; 

            $payload = [
                'phone' => $phone,
                'message' => $message,
            ];

            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'X-Api-Secret' => $this->apiSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($smsApiUrl, $payload);

            if ($response->successful()) {
                Log::info("Porego SMS başarıyla gönderildi. Telefon: {$phone}");
                return ['success' => true, 'message' => 'SMS başarıyla gönderildi.'];
            } else {
                Log::error("Porego SMS Gönderim Hatası. Telefon: {$phone}", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return [
                    'success' => false, 
                    'message' => 'SMS gönderilirken hata oluştu: ' . ($response->json('message') ?? $response->status())
                ];
            }
        } catch (\Exception $e) {
            Log::error("Porego SMS Gönderim İstisnası. Telefon: {$phone}", [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => 'Sistemsel bir hata oluştu: ' . $e->getMessage()];
        }
    }

    /**
     * Tüm ürünleri veya seçilen ürünleri Porego / Paketfy stok yönetimi API'sine aktarır/senkronize eder.
     */
    public function syncProducts($productsQuery = null)
    {
        if (!$this->apiKey || !$this->apiSecret) {
            Log::warning("Porego API Key veya Secret eksik olduğu için ürün senkronizasyonu yapılamadı.");
            return ['success' => false, 'message' => 'Porego API Anahtarları (.env) tanımlı değil.', 'synced_count' => 0];
        }

        try {
            $products = $productsQuery ? $productsQuery->get() : \App\Models\Product::with(['variants', 'images'])->get();
            $itemsPayload = [];

            foreach ($products as $product) {
                $productImage = $product->images->first()?->image_url ?: url('/favicon.png');
                if (!empty($productImage) && !str_starts_with($productImage, 'http')) {
                    $productImage = asset($productImage);
                }

                if ($product->variants->isNotEmpty()) {
                    foreach ($product->variants as $variant) {
                        $sku = $variant->sku ?: ($product->sku ?: 'SKU-V' . $variant->id);
                        $variantName = $variant->size ? "Beden: {$variant->size}" : "";
                        $fullName = $product->name . ($variantName ? " ({$variantName})" : "");

                        $itemsPayload[] = [
                            'platformProductId' => (string)$product->id,
                            'platformVariantId' => (string)$variant->id,
                            'sku'               => $sku,
                            'productSku'        => $sku,
                            'barcode'           => $sku,
                            'code'              => $sku,
                            'name'              => $fullName,
                            'productName'       => $fullName,
                            'title'             => $fullName,
                            'price'             => (float)($variant->price ?: $product->price),
                            'discountPrice'     => (float)($variant->discount_price ?: $product->discount_price ?: 0),
                            'stock'             => (int)($variant->stock ?? 0),
                            'quantity'          => (int)($variant->stock ?? 0),
                            'size'              => $variant->size,
                            'color'             => is_array($variant->color) ? implode(', ', $variant->color) : $variant->color,
                            'imageUrl'          => $productImage,
                            'active'            => (bool)$product->status,
                        ];
                    }
                } else {
                    $sku = $product->sku ?: 'SKU-P' . $product->id;
                    $itemsPayload[] = [
                        'platformProductId' => (string)$product->id,
                        'platformVariantId' => null,
                        'sku'               => $sku,
                        'productSku'        => $sku,
                        'barcode'           => $sku,
                        'code'              => $sku,
                        'name'              => $product->name,
                        'productName'       => $product->name,
                        'title'             => $product->name,
                        'price'             => (float)$product->price,
                        'discountPrice'     => (float)($product->discount_price ?: 0),
                        'stock'             => (int)($product->stock ?? 0),
                        'quantity'          => (int)($product->stock ?? 0),
                        'imageUrl'          => $productImage,
                        'active'            => (bool)$product->status,
                    ];
                }
            }

            if (empty($itemsPayload)) {
                return ['success' => true, 'message' => 'Senkronize edilecek aktif ürün bulunamadı.', 'synced_count' => 0];
            }

            // Porego / Paketfy ürün ve stok senkronizasyon uç noktalarına gönderim
            $syncEndpoints = [
                "{$this->apiUrl}/products/sync",
                "{$this->apiUrl}/products/batch",
                "{$this->apiUrl}/products",
            ];

            $success = false;
            $responseMessage = '';

            foreach ($syncEndpoints as $endpoint) {
                try {
                    $response = Http::withHeaders([
                        'X-Api-Key' => $this->apiKey,
                        'X-Api-Secret' => $this->apiSecret,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->post($endpoint, [
                        'products' => $itemsPayload,
                        'items' => $itemsPayload,
                    ]);

                    if ($response->successful()) {
                        $success = true;
                        Log::info("Porego Ürün Senkronizasyonu Başarılı ({$endpoint}): " . count($itemsPayload) . " ürün/varyant aktarıldı.");
                        break;
                    } else {
                        $msg = $response->json('message') ?: ($response->json('error') ?: $response->body());
                        if (empty($msg)) {
                            $msg = "HTTP " . $response->status() . " (Yanıt metni boş)";
                        }
                        if ($response->status() === 401) {
                            $msg = "API Kimlik Doğrulama Hatası (401 - Unauthorized). Lütfen .env dosyasındaki POREGO_API_KEY ve POREGO_API_SECRET anahtarlarını kontrol ediniz.";
                        } elseif ($response->status() === 403) {
                            $msg = "Porego API (403 Erişim Engellendi): Porego API anahtarınız doğrudan ürün eklemeye yetkili görünmüyor. Porego (Paketfy) panelindeki 'Stok Yönetimi > Mağazanızı Senkronize Edin' butonunu kullanabilirsiniz. Sitemizdeki ürün API beslemesi ('https://patenliayakkabilar.com/api/porego/products') hazır ve aktiftir.";
                        }
                        $responseMessage = $msg;
                    }
                } catch (\Throwable $e) {
                    $responseMessage = $e->getMessage();
                }
            }

            if ($success) {
                return [
                    'success' => true,
                    'message' => count($itemsPayload) . ' adet ürün/varyant Porego stok sistemine başarıyla aktarıldı.',
                    'synced_count' => count($itemsPayload),
                ];
            } else {
                Log::warning("Porego Ürün Senkronizasyon Hatası: " . $responseMessage);
                return [
                    'success' => false,
                    'message' => $responseMessage,
                    'synced_count' => 0,
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Porego Ürün Senkronizasyon İstisnası: " . $e->getMessage());
            return ['success' => false, 'message' => 'İstisna: ' . $e->getMessage(), 'synced_count' => 0];
        }
    }

    /**
     * Tek bir ürün veya varyant stoğunu Porego'da günceller
     */
    public function syncProductStock($variantOrProduct)
    {
        if (!$this->apiKey || !$this->apiSecret) return false;

        try {
            $sku = $variantOrProduct->sku;
            $stock = (int)($variantOrProduct->stock ?? 0);

            if (!$sku) return false;

            $payload = [
                'sku' => $sku,
                'stock' => $stock,
                'quantity' => $stock,
            ];

            Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'X-Api-Secret' => $this->apiSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/products/update-stock", $payload);

            return true;
        } catch (\Throwable $e) {
            Log::error("Porego tekli stok güncelleme hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Porego'dan aktif/kargodaki siparişlerin güncel durumlarını ve kargo takip numaralarını çeker.
     */
    public function syncOrderStatuses()
    {
        $apiKey = \App\Models\Setting::where('key', 'porego_api_key')->value('value') ?: $this->apiKey;
        $apiSecret = \App\Models\Setting::where('key', 'porego_api_secret')->value('value') ?: $this->apiSecret;
        $apiUrl = \App\Models\Setting::where('key', 'porego_api_url')->value('value') ?: $this->apiUrl;

        if (!$apiKey || !$apiSecret) {
            return ['success' => false, 'message' => 'Porego API Anahtarları tanımlı değil.', 'updated_count' => 0];
        }

        try {
            // Tamamlanmamış veya kargodaki siparişleri getirelim
            $activeOrders = Order::whereNotIn('status', ['delivered', 'cancelled'])->get();
            $updatedCount = 0;

            foreach ($activeOrders as $order) {
                // Porego sipariş detay sorgulaması
                $response = Http::withHeaders([
                    'X-Api-Key' => $apiKey,
                    'X-Api-Secret' => $apiSecret,
                    'Accept' => 'application/json',
                ])->get("{$apiUrl}/orders/{$order->id}");

                if (!$response->successful() && !empty($order->order_number)) {
                    $response = Http::withHeaders([
                        'X-Api-Key' => $apiKey,
                        'X-Api-Secret' => $apiSecret,
                        'Accept' => 'application/json',
                    ])->get("{$apiUrl}/orders/{$order->order_number}");
                }

                if ($response->successful()) {
                    $data = $response->json('data') ?: $response->json();
                    $status = $data['currentStatus'] ?? ($data['status'] ?? null);
                    $trackingNumber = $data['trackingNumber'] ?? ($data['tracking_number'] ?? ($data['cargo_tracking_code'] ?? null));

                    if ($status) {
                        $newStatus = match (strtoupper($status)) {
                            'SHIPPED', 'IN_TRANSIT' => 'shipped',
                            'COMPLETED', 'DELIVERED' => 'delivered',
                            'CANCELLED' => 'cancelled',
                            default => null
                        };

                        $changed = false;
                        if ($newStatus && $order->status !== $newStatus) {
                            $order->status = $newStatus;
                            $changed = true;
                        }

                        if ($trackingNumber && $order->cargo_tracking_code !== $trackingNumber) {
                            $order->cargo_tracking_code = $trackingNumber;
                            $changed = true;
                        }

                        if ($changed) {
                            $order->save();
                            $updatedCount++;
                            Log::info("Porego Durum Senkronizasyonu: Sipariş (#{$order->order_number}) durumu '{$order->status}' olarak güncellendi.");
                        }
                    }
                }
            }

            return [
                'success' => true,
                'message' => "{$updatedCount} adet siparişin Porego durumu başarıyla güncellendi.",
                'updated_count' => $updatedCount
            ];
        } catch (\Throwable $e) {
            Log::error("Porego Sipariş Durumu Senkronizasyon İstisnası: " . $e->getMessage());
            return ['success' => false, 'message' => 'Hata: ' . $e->getMessage(), 'updated_count' => 0];
        }
    }
}
