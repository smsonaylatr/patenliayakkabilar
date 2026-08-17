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

            // Ürün özet metni oluşturalım (Sadece SKU çekilmesi talebi doğrultusunda)
            $productSummaryList = [];
            $mappedItems = $order->items->map(function ($item) use (&$productSummaryList) {
                $sku = $item->variant?->sku ?: ($item->product?->sku ?: ('SKU-' . $item->product_id));
                $onlySku = ($sku && $sku !== '-') ? $sku : ($item->product_name ?: ('SKU-' . $item->product_id));

                $productSummaryList[] = "{$item->quantity}x {$onlySku}";

                return [
                    'sku'                 => $onlySku,
                    'productSku'          => $onlySku,
                    'product_sku'         => $onlySku,
                    'barcode'             => $onlySku,
                    'code'                => $onlySku,
                    'productCode'         => $onlySku,

                    // Ürün Adı varyasyonları (Sadece SKU basılması istendiği için strictly SKU aktarılıyor)
                    'name'                => $onlySku,
                    'productName'         => $onlySku,
                    'product_name'        => $onlySku,
                    'itemName'            => $onlySku,
                    'item_name'           => $onlySku,
                    'title'               => $onlySku,
                    'productTitle'        => $onlySku,
                    'product_title'       => $onlySku,
                    'description'         => $onlySku,
                    'productDescription'  => $onlySku,
                    'product_description' => $onlySku,
                    'urun_adi'            => $onlySku,
                    'urunAdi'             => $onlySku,
                    'product'             => $onlySku,
                    'item'                => $onlySku,
                    'goodsName'           => $onlySku,
                    'goods_name'          => $onlySku,
                    'label'               => $onlySku,

                    // Yalın isim ve varyant detayı
                    'raw_name'            => $item->product_name,
                    'rawName'             => $item->product_name,
                    'variantInfo'         => $item->variant_info,
                    'variant_info'        => $item->variant_info,

                    // Adet varyasyonları
                    'quantity'            => (int)$item->quantity,
                    'qty'                 => (int)$item->quantity,
                    'count'               => (int)$item->quantity,
                    'amount'              => (int)$item->quantity,
                    'adet'                => (int)$item->quantity,
                    'piece'               => (int)$item->quantity,
                    'pieces'              => (int)$item->quantity,
                    'units'               => (int)$item->quantity,

                    // Fiyat ve Varyant
                    'price'               => (float)($item->unit_price ?? 0),
                    'unitPrice'           => (float)($item->unit_price ?? 0),
                    'unit_price'          => (float)($item->unit_price ?? 0),
                    'totalPrice'          => (float)($item->total_price ?? ($item->unit_price * $item->quantity)),
                    'total_price'         => (float)($item->total_price ?? ($item->unit_price * $item->quantity)),
                ];
            })->toArray();

            $productSummaryText = implode(', ', $productSummaryList);

            $payload = [
                'customerName'         => $name,
                'customerSurname'      => $surname,
                'customerPhone'        => $order->customer_phone,
                'customerEmail'        => $order->customer_email,
                'address'              => $order->shipping_address,
                'city'                 => $order->shipping_city,
                'district'             => $order->shipping_district,
                'paymentType'          => $order->payment_method === 'cash_on_delivery' ? 'COD' : 'PREPAID',
                'platformOrderId'      => (string)$order->id,
                'platformOrderNumber'  => $order->order_number,

                // Root seviye özet metinleri (Tüm etiket şablonları için)
                'productInfo'          => $productSummaryText,
                'product_info'         => $productSummaryText,
                'cargoContent'         => $productSummaryText,
                'cargo_content'        => $productSummaryText,
                'cargoDescription'     => $productSummaryText,
                'cargo_description'    => $productSummaryText,
                'description'          => $productSummaryText,
                'content'              => $productSummaryText,
                'items_summary'        => $productSummaryText,
                'itemsSummary'         => $productSummaryText,
                'orderContent'         => $productSummaryText,
                'order_content'        => $productSummaryText,
                'packageContent'       => $productSummaryText,
                'package_content'      => $productSummaryText,
                'productDetails'       => $productSummaryText,
                'product_details'      => $productSummaryText,
                'goodsDescription'     => $productSummaryText,
                'goods_description'    => $productSummaryText,
                'urun_bilgisi'         => $productSummaryText,
                'urunBilgisi'          => $productSummaryText,
                'note'                 => $order->customer_note ?: $productSummaryText,

                // Tüm olası ürün koleksiyonu anahtarları
                'items'                => $mappedItems,
                'products'             => $mappedItems,
                'orderItems'           => $mappedItems,
                'order_items'          => $mappedItems,
                'lineItems'            => $mappedItems,
                'line_items'           => $mappedItems,
                'orderDetails'         => $mappedItems,
                'order_details'        => $mappedItems,
                'cargoItems'           => $mappedItems,
                'cargo_items'          => $mappedItems,
                'packages'             => $mappedItems,
                'packageItems'         => $mappedItems,
                'package_items'        => $mappedItems,
                'goods'                => $mappedItems,
                'orderLines'           => $mappedItems,
                'order_lines'          => $mappedItems,
                'lines'                => $mappedItems,
            ];

            if ($order->payment_method === 'cash_on_delivery') {
                $payload['codAmount'] = (float)$order->grand_total;
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
        } catch (\Throwable $e) {
            Log::error("Porego API Sipariş Gönderim İstisnası. Sipariş No: {$order->order_number}", [
                'error' => $e->getMessage()
            ]);
            return false;
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
}
