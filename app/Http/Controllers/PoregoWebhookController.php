<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PoregoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $signature = $request->header('X-Porego-Signature') ?: $request->header('x-porego-signature');
            $secret = config('services.porego.webhook_secret', env('POREGO_WEBHOOK_SECRET'));

            // İmza doğrulama kontrolü (Secret varsa doğrula, yoksa uyar ve devam et)
            if ($secret && $signature) {
                $payload = $request->getContent();
                $expectedSignature = 'sha256=' . base64_encode(hash_hmac('sha256', $payload, $secret, true));
                $expectedHex = hash_hmac('sha256', $payload, $secret);

                if (!hash_equals($expectedSignature, $signature) && !hash_equals($expectedHex, $signature) && !hash_equals('sha256=' . $expectedHex, $signature)) {
                    Log::warning('Porego Webhook Imza Doğrulama Uyarısı: Imzalar eşleşmedi.', [
                        'signature' => $signature,
                    ]);
                }
            }

            $data = $request->json()->all();
            if (empty($data)) {
                $data = $request->all();
            }

            Log::info('Porego Webhook Alındı:', $data);

            $event = $data['event'] ?? ($data['type'] ?? null);
            $orderData = $data['data'] ?? $data;

            // 1. Sipariş Durumu Değişikliği (ORDER_STATUS_CHANGED, SHIPMENT_STATUS_CHANGED)
            $platformOrderId = $orderData['platformOrderId'] ?? ($orderData['platform_order_id'] ?? ($orderData['order_id'] ?? ($orderData['id'] ?? null)));
            $platformOrderNumber = $orderData['platformOrderNumber'] ?? ($orderData['platform_order_number'] ?? ($orderData['order_number'] ?? ($orderData['orderNumber'] ?? null)));
            $status = $orderData['currentStatus'] ?? ($orderData['status'] ?? ($orderData['orderStatus'] ?? null));
            
            // Porego'nun yeni eklediği alanlar üzerinden gerçek takip kodunu almayı deniyoruz
            $trackingCode = $orderData['carrierTrackingNumber'] ?? ($orderData['platformCargoTrackingNumber'] ?? ($orderData['trackingNumber'] ?? ($orderData['tracking_number'] ?? ($orderData['trackingCode'] ?? null))));
            $cargoCompany = $orderData['carrierName'] ?? ($orderData['carrierCode'] ?? ($orderData['platformCargoCompany'] ?? null));

            if ($status && ($platformOrderId || $platformOrderNumber)) {
                $order = null;
                if ($platformOrderId) {
                    $order = \App\Models\Order::find($platformOrderId);
                }
                if (!$order && $platformOrderNumber) {
                    $order = \App\Models\Order::where('order_number', $platformOrderNumber)->first();
                }

                if ($order) {
                    $upperStatus = strtoupper((string)$status);
                    $newStatus = match ($upperStatus) {
                        'SHIPPED', 'IN_TRANSIT', 'TRANSFER_STAGE', 'ON_THE_WAY', 'CARGO' => 'shipped',
                        'COMPLETED', 'DELIVERED', 'TESLİM EDİLDİ', 'TESLIM EDILDI', 'DELIVERED_TO_RECEIVER' => 'delivered',
                        'CANCELLED', 'CANCELED', 'CANCEL', 'VOID', 'REJECTED', 'FAILED', 'FAILED_DELIVERY', 'DELETED', 'REFUNDED', 'İPTAL', 'IPTAL', 'İPTAL EDİLDİ', 'IPTAL EDILDI' => 'cancelled',
                        default => null
                    };

                    if ($newStatus && $order->status !== $newStatus) {
                        $order->status = $newStatus;
                        
                        // Gerçek kargo firması ve takip kodu varsa DB'yi güncelle
                        if ($trackingCode) {
                            // Eğer gelen kod gerçek barkod ise VEYA DB'de kayıtlı kod yoksa kaydet. (Gerçek kodu ezme)
                            if (!empty($orderData['carrierTrackingNumber']) || !empty($orderData['platformCargoTrackingNumber']) || empty($order->cargo_tracking_code)) {
                                $order->cargo_tracking_code = $trackingCode;
                            }
                        }
                        
                        if ($cargoCompany) {
                            if (!empty($orderData['carrierName']) || !empty($orderData['platformCargoCompany']) || empty($order->cargo_company)) {
                                $order->cargo_company = $cargoCompany;
                            }
                        }

                        // Kapıda Ödeme (COD) siparişi teslim edildiğinde ödeme durumunu 'paid' (Ödendi) yapıyoruz
                        if ($newStatus === 'delivered' && $order->payment_method === 'cash_on_delivery' && $order->payment_status !== 'paid') {
                            $order->payment_status = 'paid';
                        }

                        $order->save();

                        if ($newStatus === 'cancelled') {
                            foreach ($order->items as $item) {
                                if ($item->variant) {
                                    $item->variant->increment('stock', $item->quantity);
                                }
                                if ($item->product) {
                                    $item->product->increment('stock', $item->quantity);
                                }
                            }
                            Log::info("Porego Webhook: Sipariş (#{$order->order_number}) iptal edildiği için stoklar geri yüklendi.");
                        }

                        Log::info("Porego Webhook: Sipariş (#{$order->order_number}) durumu '{$newStatus}' olarak güncellendi.");
                    }
                } else {
                    Log::warning("Porego Webhook: Sipariş ID ({$platformOrderId}) bulunamadı.");
                }
            }

            // 2. Stok Değişikliği Bildirimi (STOCK_UPDATED, PRODUCT_STOCK_CHANGED)
            if (in_array($event, ['STOCK_UPDATED', 'PRODUCT_STOCK_CHANGED', 'STOCK_CHANGE'])) {
                $sku = $orderData['sku'] ?? ($orderData['productSku'] ?? null);
                $newStock = isset($orderData['stock']) ? (int)$orderData['stock'] : (isset($orderData['quantity']) ? (int)$orderData['quantity'] : null);

                if ($sku && $newStock !== null) {
                    $variant = \App\Models\ProductVariant::where('sku', $sku)->first();
                    if ($variant) {
                        $variant->update(['stock' => $newStock]);
                        $variant->product?->syncFromVariants();
                        Log::info("Porego Webhook: Varyant ({$sku}) stoğu '{$newStock}' olarak güncellendi.");
                    } else {
                        $product = \App\Models\Product::where('sku', $sku)->first();
                        if ($product) {
                            $product->update(['stock' => $newStock]);
                            Log::info("Porego Webhook: Ürün ({$sku}) stoğu '{$newStock}' olarak güncellendi.");
                        }
                    }
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Webhook processed successfully'], 200);

        } catch (\Throwable $e) {
            Log::error('Porego Webhook İşleme İstisnası: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            // Porego servisinin 503 almaması için her zaman 200 OK yanıtı veriyoruz
            return response()->json(['status' => 'received', 'message' => 'Webhook received with warning'], 200);
        }
    }
}
