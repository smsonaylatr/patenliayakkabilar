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

        // TODO: Porego'dan gelen gerçek veri yapısına göre alan isimleri güncellenmelidir.
        // Aşağıdaki kod genel (standart) bir API veri modeline göre yazılmıştır.
        $orderNumber = $data['data']['order_number'] ?? $data['order_number'] ?? null;
        $status = $data['data']['status'] ?? $data['status'] ?? null;
        $trackingCode = $data['data']['tracking_code'] ?? $data['tracking_code'] ?? null;

        if ($orderNumber && $status) {
            $order = \App\Models\Order::where('order_number', $orderNumber)->first();
            
            if ($order) {
                // Porego statüleri ile sitemizdeki statüleri eşleştiriyoruz
                // Örnek Porego statüleri: "Shipped", "Delivered", "Cancelled" vs.
                $newStatus = match (strtolower($status)) {
                    'shipped', 'kargoya_verildi' => 'shipped',
                    'delivered', 'teslim_edildi' => 'delivered',
                    'cancelled', 'iptal_edildi' => 'cancelled',
                    default => null
                };

                if ($newStatus && $order->status !== $newStatus) {
                    $order->status = $newStatus;
                    
                    if ($trackingCode) {
                        $order->cargo_tracking_code = $trackingCode;
                    }

                    $order->save();
                    
                    Log::info("Sipariş (#{$order->order_number}) durumu Porego webhook aracılığıyla '{$newStatus}' olarak güncellendi.");
                }
            } else {
                Log::warning("Porego Webhook: '{$orderNumber}' numaralı sipariş bulunamadı.");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
