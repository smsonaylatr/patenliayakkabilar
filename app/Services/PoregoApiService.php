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
    protected $dashboardApiUrl;
    protected $sessionCookie;

    public function __construct()
    {
        $this->apiKey = env('POREGO_API_KEY');
        $this->apiSecret = env('POREGO_API_SECRET');
        $this->apiUrl = env('POREGO_API_URL', 'https://back.porego.com/depokargo/api/v1/merchant-api/v1');
        // Porego Dashboard API (cookie-based session auth) - products güncelleme için
        $this->dashboardApiUrl = env('POREGO_DASHBOARD_API_URL', 'https://back.porego.com/depokargo/api/v1');
        $this->sessionCookie = env('POREGO_SESSION_COOKIE', '');
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

            // SKU ve Ürün listesi hazırlama (Strictly net SKU & Porego format)
            $productSummaryList = [];
            $mappedItems = $order->items->map(function ($item) use (&$productSummaryList) {
                $rawSku = trim($item->variant?->sku ?: ($item->product?->sku ?: ''));
                if (empty($rawSku) || $rawSku === '-' || $rawSku === 'SKU-') {
                    $rawSku = 'SKU-' . ($item->variant_id ?: $item->product_id);
                }

                // Ürün adını OrderItem üzerindeki product_name alanından al,
                // yoksa ilişkili product modelinin name alanından,
                // son çare olarak SKU kullan
                $productName = trim($item->product_name ?? '');
                if (empty($productName)) {
                    $productName = trim($item->product?->name ?? '');
                }
                if (empty($productName)) {
                    $productName = $rawSku;
                }

                // Varyant bilgisini (beden/renk) ekle
                $variantInfo = trim($item->variant_info ?? '');
                if (empty($variantInfo) && $item->variant) {
                    $sizePart = $item->variant->size ? "Beden: {$item->variant->size}" : '';
                    $colorPart = $item->variant->color ? (is_array($item->variant->color) ? implode(', ', $item->variant->color) : $item->variant->color) : '';
                    $variantInfo = implode(' / ', array_filter([$sizePart, $colorPart]));
                }

                $fullName = $variantInfo ? "{$productName} ({$variantInfo})" : $productName;
                $qty = max(1, (int)$item->quantity);
                $unitPrice = (float)($item->unit_price ?? 0);
                $totalPrice = (float)($unitPrice * $qty);

                $productSummaryList[] = "{$qty}x {$fullName}";

                return [
                    'sku'               => $rawSku,
                    'name'              => $fullName,
                    'quantity'          => $qty,
                    'price'             => $unitPrice,
                    'id'                => $item->variant_id ?: $item->product_id ?: $item->id,
                    'title'             => $fullName,
                    'productTitle'      => $fullName,
                    'productName'       => $fullName,
                    'productSku'        => $rawSku,
                    'description'       => $variantInfo ?: $fullName,
                    'totalPrice'        => $totalPrice,
                    'unitPrice'         => $unitPrice,
                ];
            })->values()->toArray();

            $productSummaryText = implode(', ', $productSummaryList);
            $firstProductName = $order->items->first()?->product_name ?: $productSummaryText;

            // Porego Merchant API standart OrderItem formatı (OpenAPI spec: sku, name, quantity, price)
            $openApiItems = array_map(function ($it) {
                return [
                    'sku'      => (string)$it['sku'],
                    'name'     => (string)$it['name'],
                    'quantity' => (int)$it['quantity'],
                    'price'    => (float)$it['price'],
                ];
            }, $mappedItems);

            // Porego kargo etiket ve panel motorunun okuduğu products JSON string formatı
            $productsJsonString = json_encode($mappedItems, JSON_UNESCAPED_UNICODE);

            // Telefon numarasını standart formata getirelim
            $phone = preg_replace('/[^0-9]/', '', $order->customer_phone ?: '');
            if (strlen($phone) === 10 && str_starts_with($phone, '5')) {
                $phone = '0' . $phone;
            }
            if (empty($phone)) {
                $phone = '05555555555';
            }

            $rawAddress = trim($order->shipping_address) ?: 'Adres Belirtilmedi';

            // Extract or resolve neighborhood name cleanly from model attribute or address
            $neighborhoodName = trim($order->shipping_neighborhood ?? '');
            if (empty($neighborhoodName)) {
                if (preg_match('/([a-zA-ZçğıöşüÇĞİÖŞÜ0-9\.\s]+?)(?:\s+mah|\s+mahalle|\s+mahallesi|\s+mh)/i', $rawAddress, $matches)) {
                    $neighborhoodName = trim($matches[1]);
                }
            }
            if (empty($neighborhoodName)) {
                $neighborhoodName = trim($order->shipping_district) ?: 'Merkez';
            }

            $cleanMah = trim(preg_replace('/(mah|mahalle|mahallesi|mh\.)/i', '', $neighborhoodName));

            $payload = [
                'customerName'          => $name,
                'customerSurname'       => $surname,
                'customerPhone'         => $phone,
                'customerEmail'         => $order->customer_email ?: 'siparis@patenliayakkabilar.com',
                'address'               => $rawAddress,
                'city'                  => trim($order->shipping_city) ?: 'İstanbul',
                'district'              => trim($order->shipping_district) ?: 'Merkez',
                'neighbourhood'         => $cleanMah,
                'postalCode'            => $order->shipping_postal_code ?: '',
                'paymentType'           => $order->payment_method === 'cash_on_delivery' ? 'COD' : 'PREPAID',
                'platformOrderId'       => (string)$order->id,
                'platformOrderNumber'   => $order->order_number,
                'items'                 => $openApiItems,
                // Products JSON string — Dashboard API güncellemesi başarısız olursa
                // en azından notes alanı etiketin "Sipariş Notu" bölümünde görünsün
                'products'              => $productsJsonString,
                'notes'                 => "📦 Ürünler: " . $productSummaryText,
                'totalAmount'           => (float)$order->grand_total,
                'totalWeight'           => max(1, count($mappedItems)),
                'totalDeci'             => max(1, count($mappedItems)),
                'currency'              => 'TRY',
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
                $responseData = $response->json();
                Log::info("Sipariş başarıyla Porego'ya iletildi. Sipariş No: {$order->order_number}", $responseData);

                // Porego response'undan kargo takip kodunu anında kaydet
                $this->saveTrackingFromResponse($order, $responseData);

                // Porego Dashboard API ile ürün bilgilerini güncelle
                // (Merchant API products alanını etiket motoruna aktarmıyor - Porego bug)
                $poregoOrderId = $responseData['id'] ?? null;
                if ($poregoOrderId) {
                    $this->updateOrderProductsViaDashboard($poregoOrderId, $mappedItems, $order);
                }

                // Porego'da otomatik barkod/kargo kodu oluştur
                try {
                    $barcodeResult = $this->createBarcode($order, $apiKey, $apiSecret, $apiUrl);
                    if ($barcodeResult['success']) {
                        Log::info("Kargo kodu otomatik oluşturuldu. Sipariş: #{$order->order_number}, Kod: {$order->cargo_tracking_code}");
                        return ['success' => true, 'message' => "Sipariş (#{$order->order_number}) Porego'ya aktarıldı ve kargo kodu oluşturuldu: {$order->cargo_tracking_code}"];
                    } else {
                        Log::warning("Kargo kodu otomatik oluşturulamadı. Sipariş: #{$order->order_number}, Hata: {$barcodeResult['message']}");
                    }
                } catch (\Throwable $barcodeEx) {
                    Log::warning("Kargo kodu oluşturma istisnası. Sipariş: #{$order->order_number}, Hata: " . $barcodeEx->getMessage());
                }

                return ['success' => true, 'message' => "Sipariş (#{$order->order_number}) başarıyla Porego'ya aktarıldı."];
            } else {
                $err = $response->json('message') ?: ($response->json('error') ?: $response->body());
                if (empty($err)) {
                    $err = "HTTP " . $response->status();
                }

                // 400 Bad Request: Sipariş zaten Porego'da kayıtlı - PUT ile güncellemeyi dene
                if ($response->status() === 400) {
                    Log::info("Porego API 400: Sipariş zaten mevcut, mevcut sipariş verisi çekilip Dashboard API ile ürün güncellemesi deneniyor. Sipariş No: {$order->order_number}");

                    // 1. Önce mevcut siparişi Porego'dan (Dashboard veya Merchant API) bulalım
                    try {
                        $existingOrderData = $this->findPoregoOrderData($order, $apiKey, $apiSecret, $apiUrl);
                        if (!empty($existingOrderData)) {
                            $this->saveTrackingFromResponse($order, $existingOrderData);

                            $existingPoregoId = $existingOrderData['id'] ?? null;
                            if ($existingPoregoId) {
                                $updatedProducts = $this->updateOrderProductsViaDashboard((int)$existingPoregoId, $mappedItems, $order);
                                if ($updatedProducts) {
                                    try { $this->createBarcode($order, $apiKey, $apiSecret, $apiUrl); } catch (\Throwable $e) {}
                                    return [
                                        'success' => true,
                                        'message' => "Mevcut sipariş (#{$order->order_number}) Porego'da bulundu ve ürün bilgileri başarıyla güncellendi!"
                                    ];
                                }
                            }
                        }
                    } catch (\Throwable $getEx) {
                        Log::warning("Porego mevcut sipariş bilgisi çekme hatası: " . $getEx->getMessage());
                    }
                    
                    // 2. Porego API'sinde standart güncelleme endpoint'lerini dene
                    $updateEndpoints = [
                        "{$apiUrl}/orders/{$order->order_number}",
                        "{$apiUrl}/orders/{$order->id}",
                    ];
                    
                    foreach ($updateEndpoints as $updateUrl) {
                        try {
                            $updateResponse = Http::withHeaders([
                                'X-Api-Key' => $apiKey,
                                'X-Api-Secret' => $apiSecret,
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                            ])->put($updateUrl, $payload);
                            
                            if ($updateResponse->successful()) {
                                Log::info("Sipariş PUT ile güncellendi. Sipariş No: {$order->order_number}, URL: {$updateUrl}");
                                $this->saveTrackingFromResponse($order, $updateResponse->json());
                                try { $this->createBarcode($order, $apiKey, $apiSecret, $apiUrl); } catch (\Throwable $e) {}
                                return ['success' => true, 'message' => "Sipariş (#{$order->order_number}) Porego'da güncellendi (ürün bilgileri dahil)."];
                            }
                        } catch (\Throwable $putEx) {
                            Log::warning("Porego PUT denemesi başarısız: {$updateUrl} - " . $putEx->getMessage());
                        }
                    }
                    
                    // 3. PUT da çalışmadıysa, siparişi Porego'dan silip yeniden oluşturmayı dene
                    try {
                        $deleteResponse = Http::withHeaders([
                            'X-Api-Key' => $apiKey,
                            'X-Api-Secret' => $apiSecret,
                            'Accept' => 'application/json',
                        ])->delete("{$apiUrl}/orders/{$order->order_number}");
                        
                        if ($deleteResponse->successful() || $deleteResponse->status() === 204) {
                            // Silme başarılı, yeniden oluştur
                            $recreateResponse = Http::withHeaders([
                                'X-Api-Key' => $apiKey,
                                'X-Api-Secret' => $apiSecret,
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                            ])->post("{$apiUrl}/orders", $payload);
                            
                            if ($recreateResponse->successful()) {
                                Log::info("Sipariş Porego'da silindi ve yeniden oluşturuldu. Sipariş No: {$order->order_number}");
                                $recreateData = $recreateResponse->json();
                                $this->saveTrackingFromResponse($order, $recreateData);
                                
                                if (!empty($recreateData['id'])) {
                                    $this->updateOrderProductsViaDashboard($recreateData['id'], $mappedItems, $order);
                                }
                                
                                try { $this->createBarcode($order, $apiKey, $apiSecret, $apiUrl); } catch (\Throwable $e) {}
                                return ['success' => true, 'message' => "Sipariş (#{$order->order_number}) Porego'da silindi ve ürün bilgileriyle yeniden oluşturuldu."];
                            }
                        }
                    } catch (\Throwable $delEx) {
                        Log::warning("Porego DELETE+POST denemesi başarısız: " . $delEx->getMessage());
                    }
                    
                    $msg = "Bu sipariş (#{$order->order_number}) Porego sisteminde zaten mevcut. Ürün bilgisi güncellenemedi. Porego panelinden kontrol edebilirsiniz.";
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
     * Porego'da sipariş için kargo barkodu/takip kodu oluşturur.
     * sendOrder() başarılı olduktan sonra otomatik çağrılır.
     *
     * Strateji:
     * 1. Porego createbarcode API endpoint'ini dene
     * 2. Başarısız olursa, Porego siparişi zaten trackingNumber atadığı için
     *    GET /orders ile sipariş bilgisini çekip tracking code'u kaydet
     */
    public function createBarcode(Order $order, ?string $apiKey = null, ?string $apiSecret = null, ?string $apiUrl = null): array
    {
        $apiKey = $apiKey ?: (\App\Models\Setting::where('key', 'porego_api_key')->value('value') ?: $this->apiKey);
        $apiSecret = $apiSecret ?: (\App\Models\Setting::where('key', 'porego_api_secret')->value('value') ?: $this->apiSecret);
        $apiUrl = $apiUrl ?: (\App\Models\Setting::where('key', 'porego_api_url')->value('value') ?: $this->apiUrl);

        if (!$apiKey || !$apiSecret) {
            return ['success' => false, 'message' => 'API kimlik bilgileri eksik.'];
        }

        $headers = [
            'X-Api-Key' => $apiKey,
            'X-Api-Secret' => $apiSecret,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        // 1. Porego createbarcode endpoint'ini dene
        try {
            $order->loadMissing(['items.product', 'items.variant']);
            $productSummaryList = [];
            $mappedItems = $order->items->map(function ($item) use (&$productSummaryList) {
                $rawSku = trim($item->variant?->sku ?: ($item->product?->sku ?: ''));
                if (empty($rawSku) || $rawSku === '-' || $rawSku === 'SKU-') {
                    $rawSku = 'SKU-' . ($item->variant_id ?: $item->product_id);
                }
                $productName = trim($item->product_name ?: ($item->product?->name ?: $rawSku));
                $variantInfo = trim($item->variant_info ?? '');
                $fullName = $variantInfo ? "{$productName} ({$variantInfo})" : $productName;
                $qty = max(1, (int)$item->quantity);
                $productSummaryList[] = "{$qty}x {$fullName}";
                return [
                    'sku' => $rawSku,
                    'name' => $fullName,
                    'title' => $fullName,
                    'productTitle' => $fullName,
                    'productName' => $fullName,
                    'quantity' => $qty,
                    'count' => $qty,
                    'qty' => $qty,
                    'price' => (float)($item->unit_price ?? 0),
                    'weight' => 1,
                    'deci' => 1,
                ];
            })->values()->toArray();

            $productSummaryText = implode(', ', $productSummaryList);

            $payload = [
                'platformOrderId'     => (string)$order->id,
                'platformOrderNumber' => $order->order_number,
                'orderNumber'         => $order->order_number,
                'products'            => json_encode($mappedItems, JSON_UNESCAPED_UNICODE),
                'items'               => $mappedItems,
                'productInfo'         => $productSummaryText,
                'note'                => $productSummaryText,
                'notes'               => $productSummaryText,
                'description'         => $productSummaryText,
                'orderNote'           => $productSummaryText,
                'noteToCargoPersonnel'=> $productSummaryText,
            ];

            $response = Http::withHeaders($headers)->timeout(10)
                ->post("{$apiUrl}/createbarcode", $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info("Porego createbarcode başarılı. Sipariş: #{$order->order_number}", $responseData ?? []);
                $this->saveTrackingFromResponse($order, $responseData);
                return ['success' => true, 'message' => 'Kargo kodu API ile oluşturuldu.'];
            }

            Log::info("Porego createbarcode endpoint: Status {$response->status()} (Sipariş: #{$order->order_number})");
        } catch (\Throwable $e) {
            Log::info("Porego createbarcode erişilemedi: " . $e->getMessage());
        }

        // 2. Porego, sipariş kabul ettiğinde otomatik trackingNumber atar.
        //    GET /orders ile siparişi çekip tracking code'u kaydet.
        try {
            $trackingResult = $this->fetchAndSaveOrderTracking($order);
            if ($trackingResult && !empty($order->cargo_tracking_code)) {
                Log::info("Kargo kodu Porego senkronizasyonu ile alındı. Sipariş: #{$order->order_number}, Kod: {$order->cargo_tracking_code}");
                return ['success' => true, 'message' => "Kargo kodu alındı: {$order->cargo_tracking_code}"];
            }
        } catch (\Throwable $e) {
            Log::warning("Porego kargo kodu senkronizasyon hatası: " . $e->getMessage());
        }

        return ['success' => false, 'message' => 'Kargo kodu henüz oluşturulmadı. Porego siparişi işlendikten sonra senkronizasyonla alınacak.'];
    }

    /**
     * Porego API response'undan kargo takip kodunu çıkarır ve siparişe anında kaydeder.
     * sendOrder(), PUT güncelleme ve DELETE+POST akışlarının tümünde kullanılır.
     */
    private function saveTrackingFromResponse(Order $order, ?array $responseData): void
    {
        if (empty($responseData)) {
            return;
        }

        // Porego response yapısı: doğrudan veya data/order anahtarı altında olabilir
        $orderData = $responseData['data'] ?? $responseData['order'] ?? $responseData;

        // Tracking number: birden fazla alan adı dene
        $trackingCode = $orderData['trackingNumber']
            ?? $orderData['carrierTrackingNumber']
            ?? $orderData['platformCargoTrackingNumber']
            ?? $orderData['tracking_number']
            ?? $orderData['barcode']
            ?? null;

        // Kargo firması
        $cargoCompany = $orderData['carrierName']
            ?? $orderData['carrierCode']
            ?? $orderData['platformCargoCompany']
            ?? $orderData['cargo_company']
            ?? null;

        // Tracking URL
        $trackingUrl = $orderData['carrierTrackingUrl']
            ?? $orderData['trackingUrl']
            ?? $orderData['tracking_url']
            ?? null;

        $cleanTrackingCode = trim((string)$trackingCode);
        $cleanOrderNumber = trim((string)$order->order_number);
        $cleanOrderId = trim((string)$order->id);

        $changed = false;

        // Geçerli bir tracking code varsa (sipariş numarasının kendisi değilse) kaydet
        if (
            !empty($cleanTrackingCode)
            && $cleanTrackingCode !== $cleanOrderNumber
            && $cleanTrackingCode !== $cleanOrderId
            && $cleanTrackingCode !== '#' . $cleanOrderNumber
        ) {
            $order->cargo_tracking_code = $cleanTrackingCode;
            $changed = true;
            Log::info("Kargo takip kodu anında kaydedildi. Sipariş: #{$order->order_number}, Kod: {$cleanTrackingCode}");
        }

        // Kargo firması kaydet
        if (!empty($cargoCompany)) {
            $order->cargo_company = $cargoCompany;
            $changed = true;
        } elseif (empty($order->cargo_company)) {
            $order->cargo_company = 'DHL eCommerce';
            $changed = true;
        }

        // Tracking URL varsa loga yaz (ileride DB alanı eklenebilir)
        if (!empty($trackingUrl)) {
            Log::info("Porego tracking URL: {$trackingUrl} (Sipariş: #{$order->order_number})");
        }

        if ($changed) {
            $order->save();
            
            // Gerçek kargo kodu kaydedildiyse ve sipariş henüz shipped değilse, otomatik shipped yap
            $cleanCode = trim((string)$order->cargo_tracking_code);
            if (
                !empty($cleanCode) 
                && !str_starts_with($cleanCode, '330')
                && $cleanCode !== trim((string)$order->order_number)
                && $cleanCode !== trim((string)$order->id)
                && in_array($order->status, ['pending', 'processing'])
            ) {
                $order->status = 'shipped';
                $order->save();
                Log::info("Gerçek kargo kodu geldi, sipariş otomatik shipped yapıldı. Sipariş: #{$order->order_number}, Kod: {$cleanCode}");
            }
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
     * Sipariş için Porego'dan canlı kargo takip bilgilerini çeker ve veritabanına kaydeder.
     */
    public function fetchAndSaveOrderTracking(Order $order): ?array
    {
        $apiKey = \App\Models\Setting::where('key', 'porego_api_key')->value('value') ?: $this->apiKey;
        $apiSecret = \App\Models\Setting::where('key', 'porego_api_secret')->value('value') ?: $this->apiSecret;
        $apiUrl = \App\Models\Setting::where('key', 'porego_api_url')->value('value') ?: $this->apiUrl;

        if (!$apiKey || !$apiSecret) {
            Log::warning("Porego API anahtarları tanımlı değil, kargo takip çekilemiyor.");
            return null;
        }

        try {
            // Porego API: /orders endpoint'i paginated 'content' array döndürür.
            // platformOrderNumber filtresi çalışmadığı için tüm sayfaları tarayıp
            // platformOrderNumber eşleşmesi yapıyoruz.
            $poregoOrder = null;
            $page = 0;

            while ($page < 10) {
                $response = Http::withHeaders([
                    'X-Api-Key' => $apiKey,
                    'X-Api-Secret' => $apiSecret,
                    'Accept' => 'application/json',
                ])->timeout(10)->get("{$apiUrl}/orders", ['page' => $page, 'size' => 50]);

                if (!$response->successful()) {
                    Log::warning("Porego sipariş listesi alınamadı. Status: " . $response->status());
                    break;
                }

                $body = $response->json();
                $content = $body['content'] ?? [];

                foreach ($content as $item) {
                    if (($item['platformOrderNumber'] ?? '') === $order->order_number) {
                        $poregoOrder = $item;
                        break 2; // Her iki döngüden de çık
                    }
                }

                // Son sayfa mı?
                if ($body['last'] ?? true) break;
                $page++;
            }

            if (!$poregoOrder) {
                Log::info("Porego'da sipariş bulunamadı: #{$order->order_number}");
                return null;
            }

            // ===== Porego OrderResponse alanları =====
            // trackingNumber: Porego'nun atadığı kargo takip numarası (ör: 330459070)
            // trackingUrl: Porego takip linki (ör: https://app.porego.com/tracking/330459070)
            // platformCargoTrackingNumber: Platform tarafından atanan kargo takip no (genelde null)
            // platformCargoCompany: Platform tarafından atanan kargo firması (genelde null)
            // status: NEW, READY, SHIPPED, IN_TRANSIT, COMPLETED, CANCELLED

            // Porego API'sinin yeni eklenen gerçek kargo alanları (carrierTrackingNumber, carrierName, carrierTrackingUrl)
            // Eğer bunlar boş gelirse (paket şubede okutulmadıysa), Porego'nun kendi alanlarına (trackingNumber) fallback yap
            $platformTracking = $poregoOrder['carrierTrackingNumber'] ?? ($poregoOrder['platformCargoTrackingNumber'] ?? null);
            $platformCompany = $poregoOrder['carrierName'] ?? ($poregoOrder['carrierCode'] ?? ($poregoOrder['platformCargoCompany'] ?? null));
            
            $trackingNumber = $platformTracking ?? $poregoOrder['trackingNumber'] ?? null;
            $cargoCompany = $platformCompany; 
            
            // Eğer gerçek kargo takip linki varsa (carrierTrackingUrl), Porego'nun linkini ez
            $trackingUrl = $poregoOrder['carrierTrackingUrl'] ?? ($poregoOrder['trackingUrl'] ?? null);
            $status = $poregoOrder['status'] ?? null;

            $cleanTrackingNumber = trim((string)$trackingNumber);
            $cleanOrderNumber = trim((string)$order->order_number);
            $cleanOrderId = trim((string)$order->id);

            $changed = false;
            $newStatus = null;

            // Durum eşleştirmesi
            if ($status) {
                $upperStatus = strtoupper((string)$status);
                $newStatus = match ($upperStatus) {
                    'SHIPPED', 'IN_TRANSIT', 'TRANSFER_STAGE', 'ON_THE_WAY', 'CARGO' => 'shipped',
                    'COMPLETED', 'DELIVERED', 'TESLİM EDİLDİ', 'TESLIM EDILDI', 'DELIVERED_TO_RECEIVER' => 'delivered',
                    'CANCELLED', 'CANCELED', 'CANCEL', 'VOID', 'REJECTED', 'FAILED', 'FAILED_DELIVERY', 'DELETED', 'REFUNDED', 'İPTAL', 'IPTAL', 'İPTAL EDİLDİ', 'IPTAL EDILDI' => 'cancelled',
                    'READY' => 'processing',
                    default => null
                };

                if ($newStatus && $order->status !== $newStatus) {
                    $order->status = $newStatus;
                    $changed = true;
                }
            }

            // Kapıda Ödeme siparişi teslim edildiğinde otomatik "Ödendi" yap
            $effectiveStatus = $newStatus ?: $order->status;
            if ($effectiveStatus === 'delivered' && $order->payment_method === 'cash_on_delivery' && $order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                $changed = true;
            }

            // Kargo takip kodu kaydet
            if (!empty($cleanTrackingNumber) && $cleanTrackingNumber !== $cleanOrderNumber && $cleanTrackingNumber !== $cleanOrderId && $cleanTrackingNumber !== '#' . $cleanOrderNumber) {
                // Eğer GERÇEK bir kargo numarası (carrierTrackingNumber) varsa KESİN GÜNCELLE
                // Yoksa, sadece DB'deki barkod BOŞSA güncelle (gerçek kodu ezmemek için)
                if ($platformTracking || empty($order->cargo_tracking_code)) {
                    if ($order->cargo_tracking_code !== $cleanTrackingNumber) {
                        $order->cargo_tracking_code = $cleanTrackingNumber;
                        $changed = true;
                    }
                }
                
                // Kargo firması için de aynısı
                if (!empty($cargoCompany) || empty($order->cargo_company)) {
                    $fallbackCompany = $cargoCompany ?: 'DHL eCommerce';
                    if ($order->cargo_company !== $fallbackCompany) {
                        $order->cargo_company = $fallbackCompany;
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                $order->save();
                Log::info("Porego Senkronizasyonu: Sipariş #{$order->order_number} güncellendi. Status: {$order->status}, Kargo: {$order->cargo_tracking_code}");
            }

            // Frontend için canlı veri döndür
            return [
                'tracking_code'  => $cleanTrackingNumber ?: $order->cargo_tracking_code,
                'cargo_name'     => $cargoCompany ?: ($order->cargo_company ?: 'DHL eCommerce'),
                'tracking_url'   => $trackingUrl,
                'status'         => $order->status,
                'payment_status' => $order->payment_status,
                'raw_status'     => $status,
                'porego_order_number' => $poregoOrder['orderNumber'] ?? null,
            ];

        } catch (\Throwable $e) {
            Log::error("Porego kargo takip çekme hatası (#{$order->order_number}): " . $e->getMessage());
        }

        return null;
    }

    /**
     * Porego'dan aktif/kargodaki ve ödemesi bekleyen Kapıda Ödeme siparişlerinin güncel durumlarını çeker.
     */
    public function syncOrderStatuses()
    {
        $apiKey = \App\Models\Setting::where('key', 'porego_api_key')->value('value') ?: $this->apiKey;
        $apiSecret = \App\Models\Setting::where('key', 'porego_api_secret')->value('value') ?: $this->apiSecret;

        if (!$apiKey || !$apiSecret) {
            return ['success' => false, 'message' => 'Porego API Anahtarları tanımlı değil.', 'updated_count' => 0];
        }

        try {
            $activeOrders = Order::whereNotIn('status', ['cancelled'])
                ->where(function($q) {
                    $q->whereNotIn('status', ['delivered'])
                      ->orWhere(function($subQ) {
                          $subQ->where('payment_method', 'cash_on_delivery')
                               ->where('payment_status', '!=', 'paid');
                      });
                })->get();
            $updatedCount = 0;

            foreach ($activeOrders as $order) {
                $result = $this->fetchAndSaveOrderTracking($order);
                if ($result) {
                    $updatedCount++;
                }
            }

            return [
                'success' => true,
                'message' => "{$updatedCount} adet siparişin Porego kargo/ödeme durumu güncellendi.",
                'updated_count' => $updatedCount
            ];
        } catch (\Throwable $e) {
            Log::error("Porego Sipariş Durumu Senkronizasyon İstisnası: " . $e->getMessage());
            return ['success' => false, 'message' => 'Hata: ' . $e->getMessage(), 'updated_count' => 0];
        }
    }

    /**
     * Porego Dashboard API üzerinden siparişin ürün bilgilerini günceller.
     *
     * Porego'nun Merchant API'si (POST /orders) items alanını kabul eder ama
     * backend veritabanındaki "products" JSON sütununa aktarmaz. Etiket motoru
     * bu sütundan ürün bilgisi okuduğu için kargo etiketlerinde "Ürün bilgisi yok"
     * hatası oluşur.
     *
     * Bu method Porego Dashboard API'sine (PUT /orders/{id}) erişerek
     * products alanını doğrudan günceller — tıpkı Porego panelinde
     * "İşlem Düzenle > Kaydet" akışının yaptığı gibi.
     *
     * Auth Yöntemleri (öncelik sırasıyla):
     * 1. POREGO_SESSION_COOKIE — Porego dashboard session cookie (withCredentials)
     * 2. X-Api-Key / X-Api-Secret — Merchant API key (fallback, genellikle 403 döner)
     */
    protected function updateOrderProductsViaDashboard(int $poregoOrderId, array $mappedItems, Order $order): bool
    {
        $dashboardUrl = \App\Models\Setting::where('key', 'porego_dashboard_api_url')->value('value')
            ?: $this->dashboardApiUrl;
        $sessionCookie = \App\Models\Setting::where('key', 'porego_session_cookie')->value('value')
            ?: $this->sessionCookie;

        // Products JSON string — Porego frontend'inin okuduğu format
        $productsJson = json_encode($mappedItems, JSON_UNESCAPED_UNICODE);

        $updatePayload = [
            'products' => $productsJson,
            'id' => $poregoOrderId,
        ];

        if (empty($sessionCookie)) {
            Log::info("Porego Dashboard API: Session cookie tanımlı değil, ürün güncelleme atlanıyor. Sipariş: #{$order->order_number}");
            return false;
        }

        // app_token JWT'yi cookie string'inden çıkar
        $jwtToken = $sessionCookie;
        if (preg_match('/app_token=([^;]+)/', $sessionCookie, $m)) {
            $jwtToken = trim($m[1]);
        }

        // Yöntem 1: Authorization: Bearer JWT ile Dashboard API
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$jwtToken}",
            ])->withOptions([
                'verify' => false,
            ])->timeout(10)->put("{$dashboardUrl}/orders/{$poregoOrderId}", $updatePayload);

            if ($response->successful()) {
                Log::info("Porego Dashboard API (Bearer): Ürün bilgileri güncellendi. Sipariş: #{$order->order_number}, Porego ID: {$poregoOrderId}");
                return true;
            }

            Log::info("Porego Dashboard API (Bearer): Status {$response->status()}, Sipariş: #{$order->order_number}");
        } catch (\Throwable $e) {
            Log::warning("Porego Dashboard API (Bearer) istisnası: " . $e->getMessage());
        }

        // Yöntem 2: Cookie header ile Dashboard API
        try {
            $cookieHeader = str_contains($sessionCookie, '=') ? $sessionCookie : "app_token={$sessionCookie}";

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Cookie' => $cookieHeader,
            ])->withOptions([
                'verify' => false,
            ])->timeout(10)->put("{$dashboardUrl}/orders/{$poregoOrderId}", $updatePayload);

            if ($response->successful()) {
                Log::info("Porego Dashboard API (Cookie): Ürün bilgileri güncellendi. Sipariş: #{$order->order_number}, Porego ID: {$poregoOrderId}");
                return true;
            }

            Log::warning("Porego Dashboard API (Cookie): Status {$response->status()}, Sipariş: #{$order->order_number}, Body: " . substr($response->body(), 0, 200));
        } catch (\Throwable $e) {
            Log::warning("Porego Dashboard API (Cookie) istisnası: " . $e->getMessage());
        }

        Log::warning(
            "Porego ürün bilgisi etiket üzerine aktarılamadı. Sipariş: #{$order->order_number}. " .
            "Çözüm: Porego panelinde siparişi açın, 'İşlem Düzenle' > 'Kaydet' yaparak ürün bilgisini etikete aktarın."
        );
        return false;
    }

    /**
     * Porego'da daha önce oluşturulmuş siparişin detaylarını ve Porego ID'sini bulur.
     */
    public function findPoregoOrderData(Order $order, ?string $apiKey = null, ?string $apiSecret = null, ?string $apiUrl = null): ?array
    {
        $apiKey = $apiKey ?: ($this->apiKey);
        $apiSecret = $apiSecret ?: ($this->apiSecret);
        $apiUrl = $apiUrl ?: ($this->apiUrl);
        $dashboardUrl = \App\Models\Setting::where('key', 'porego_dashboard_api_url')->value('value') ?: $this->dashboardApiUrl;
        $sessionCookie = \App\Models\Setting::where('key', 'porego_session_cookie')->value('value') ?: $this->sessionCookie;

        // 1. Merchant API GET /orders/{order_number}
        try {
            $resp = Http::withHeaders([
                'X-Api-Key' => $apiKey,
                'X-Api-Secret' => $apiSecret,
                'Accept' => 'application/json',
            ])->timeout(8)->get("{$apiUrl}/orders/{$order->order_number}");

            if ($resp->successful() && !empty($resp->json('id'))) {
                return $resp->json();
            }
        } catch (\Throwable $e) {
            Log::info("findPoregoOrderData Merchant API get: " . $e->getMessage());
        }

        // 2. Dashboard API GET /orders listesinden platformOrderNumber veya orderNumber ile ara
        if (!empty($sessionCookie)) {
            $jwtToken = $sessionCookie;
            if (preg_match('/app_token=([^;]+)/', $sessionCookie, $m)) {
                $jwtToken = trim($m[1]);
            }

            try {
                $resp = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$jwtToken}",
                ])->withOptions(['verify' => false])->timeout(8)->get("{$dashboardUrl}/orders?size=50");

                if ($resp->successful()) {
                    $body = $resp->json();
                    $list = $body['content'] ?? ($body['data'] ?? (is_array($body) ? $body : []));
                    foreach ($list as $item) {
                        $pNo = (string)($item['platformOrderNumber'] ?? '');
                        $pId = (string)($item['platformOrderId'] ?? '');
                        $oNo = (string)($item['orderNumber'] ?? '');

                        if (
                            $pNo === (string)$order->order_number
                            || $pId === (string)$order->id
                            || $oNo === (string)$order->order_number
                        ) {
                            return $item;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::info("findPoregoOrderData Dashboard API get: " . $e->getMessage());
            }
        }

        return null;
    }
}

