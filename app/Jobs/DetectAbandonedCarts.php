<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\CustomerEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectAbandonedCarts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Terk edilen sepetleri kontrol et ve event oluştur.
     * Sepet güncellendikten 2 saat sonra hala aktifse ve siparişe dönüşmediyse tetiklenir.
     */
    public function handle(): void
    {
        // 1. 2 Saatlik Sepet Hatırlatması
        $this->processTwoHourCarts();

        // 2. 24 Saatlik Kuponlu Geri Kazanım
        $this->processTwentyFourHourCarts();
    }

    private function processTwoHourCarts(): void
    {
        $carts = Cart::with(['items.product', 'user'])
            ->where('updated_at', '<=', now()->subHours(2))
            ->where('updated_at', '>', now()->subHours(24))
            ->whereHas('items')
            ->whereNotNull('user_id') // Sadece kayıtlı kullanıcılara mail atabiliriz
            ->get();

        foreach ($carts as $cart) {
            $existingEvent = CustomerEvent::where('event_type', 'cart_abandoned_2h')
                ->where('session_id', $cart->session_id)
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if (!$existingEvent && $cart->user) {
                CustomerEvent::create([
                    'user_id' => $cart->user_id,
                    'session_id' => $cart->session_id,
                    'event_type' => 'cart_abandoned_2h',
                    'event_data' => ['cart_id' => $cart->id]
                ]);

                $cart->user->notify(new \App\Notifications\AbandonedCartNotification());
            }
        }
    }

    private function processTwentyFourHourCarts(): void
    {
        $carts = Cart::with(['items.product', 'user'])
            ->where('updated_at', '<=', now()->subHours(24))
            ->where('updated_at', '>', now()->subHours(48))
            ->whereHas('items')
            ->whereNotNull('user_id')
            ->get();

        foreach ($carts as $cart) {
            $existingEvent = CustomerEvent::where('event_type', 'cart_abandoned_24h')
                ->where('session_id', $cart->session_id)
                ->where('created_at', '>=', now()->subHours(48))
                ->exists();

            if (!$existingEvent && $cart->user) {
                CustomerEvent::create([
                    'user_id' => $cart->user_id,
                    'session_id' => $cart->session_id,
                    'event_type' => 'cart_abandoned_24h',
                    'event_data' => ['cart_id' => $cart->id]
                ]);

                // Kupon oluştur
                $couponCode = 'GERIGEL10-' . strtoupper(\Illuminate\Support\Str::random(5));
                \App\Models\Coupon::create([
                    'code' => $couponCode,
                    'type' => 'percentage',
                    'value' => 10.00,
                    'usage_limit' => 1,
                    'expires_at' => now()->addHours(48),
                    'status' => true,
                ]);

                $cart->user->notify(new \App\Notifications\CartRecoveryCouponNotification($couponCode));
            }
        }
    }
}
