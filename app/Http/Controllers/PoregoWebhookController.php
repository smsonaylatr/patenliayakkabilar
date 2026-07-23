<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PoregoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('X-Porego-Signature');
        $secret = config('services.porego.webhook_secret', env('POREGO_WEBHOOK_SECRET'));

        if (!$secret) {
            Log::error('Porego Webhook Secret is not configured.');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        if (!$signature) {
            Log::error('Porego Webhook Signature is missing.');
            return response()->json(['error' => 'Signature missing'], 401);
        }

        $payload = $request->getContent();
        // Porego sends the signature as 'sha256=...' and uses Base64 encoding for the hash
        $expectedSignature = 'sha256=' . base64_encode(hash_hmac('sha256', $payload, $secret, true));

        if (!hash_equals($expectedSignature, $signature)) {
            Log::error('Porego Webhook Signature verification failed.', [
                'expected' => $expectedSignature,
                'actual' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();

        // Process the webhook data here
        Log::info('Porego Webhook Received:', $data);

        $event = $data['event'] ?? null;
        $orderData = $data['data'] ?? [];
        
        $platformOrderId = $orderData['platformOrderId'] ?? null;
        $status = $orderData['currentStatus'] ?? null;
        $trackingCode = $orderData['trackingNumber'] ?? null;

        if ($platformOrderId && $status && in_array($event, ['ORDER_STATUS_CHANGED', 'SHIPMENT_STATUS_CHANGED'])) {
            $order = \App\Models\Order::find($platformOrderId);
            
            if ($order) {
                // Porego statüleri ile sitemizdeki statüleri eşleştiriyoruz
                // Porego Statuses: NEW, READY, SHIPPED, IN_TRANSIT, COMPLETED, CANCELLED
                $newStatus = match (strtoupper($status)) {
                    'SHIPPED', 'IN_TRANSIT' => 'shipped',
                    'COMPLETED' => 'delivered',
                    'CANCELLED' => 'cancelled',
                    default => null
                };

                if ($newStatus && $order->status !== $newStatus) {
                    $order->status = $newStatus;
                    
                    if ($trackingCode) {
                        $order->cargo_tracking_code = $trackingCode;
                    }

                    $order->save();

                    // İptal edildiyse stokları geri yükle
                    if ($newStatus === 'cancelled') {
                        foreach ($order->items as $item) {
                            if ($item->variant) {
                                $variant = clone $item->variant;
                                $variant->increment('stock', $item->quantity);
                            }
                            
                            $product = clone $item->product;
                            if ($product) {
                                $product->increment('stock', $item->quantity);
                            }
                        }
                        Log::info("Porego Webhook: Sipariş (#{$order->order_number}) iptal edildiği için stoklar iade edildi.");
                    }
                    
                    Log::info("Sipariş (#{$order->order_number}) durumu Porego webhook aracılığıyla '{$newStatus}' olarak güncellendi.");
                }
            } else {
                Log::warning("Porego Webhook: '{$platformOrderId}' ID'li sipariş bulunamadı.");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
