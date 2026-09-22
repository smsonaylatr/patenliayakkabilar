<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CustomerEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DetectAbandonedCarts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Terk edilen sepetleri kontrol et ve event oluştur.
     * Sepet güncellendikten 2 saat sonra hala aktifse ve siparişe dönüşmediyse tetiklenir.
     */
    public function handle(): void
    {
        // 1. 2 Saatlik Kuponsuz Sepet Hatırlatması
        $this->processTwoHourCarts();

        // 2. 24 Saatlik %10 Kuponlu Geri Kazanım Maili
        $this->processTwentyFourHourCarts();
    }

    /**
     * 2 saat sonra kuponsuz hatırlatma maili gönderir.
     */
    private function processTwoHourCarts(): void
    {
        $carts = Cart::with(['items.product', 'user'])
            ->where('updated_at', '<=', now()->subHours(2))
            ->where('updated_at', '>', now()->subHours(24))
            ->whereHas('items')
            ->whereNull('reminder_mail_sent_at') // Daha önce gönderilmemişlere
            ->where(function ($q) {
                $q->whereNotNull('user_id')
                  ->orWhereNotNull('guest_email');
            })
            ->get();

        foreach ($carts as $cart) {
            $email = $cart->user?->email ?? $cart->guest_email;
            if (!$email) {
                continue;
            }

            // Aynı session için duplikasyon kontrolü
            $existingEvent = CustomerEvent::where('event_type', 'cart_abandoned_2h')
                ->where('session_id', $cart->session_id)
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if ($existingEvent) {
                continue;
            }

            try {
                // Event oluştur
                CustomerEvent::create([
                    'user_id' => $cart->user_id,
                    'session_id' => $cart->session_id,
                    'event_type' => 'cart_abandoned_2h',
                    'event_data' => ['cart_id' => $cart->id],
                ]);

                // Kuponsuz hatırlatma maili gönder
                Mail::to($email)->send(new \App\Mail\AbandonedCartReminderMail($cart));

                // Gönderim zamanını kaydet
                $cart->update(['reminder_mail_sent_at' => now()]);

                Log::info("Sepet hatırlatma maili gönderildi: {$email} (Sepet #{$cart->id})");
            } catch (\Exception $e) {
                Log::error("Sepet hatırlatma maili gönderilemedi: {$email} - " . $e->getMessage());
            }
        }
    }

    /**
     * 24 saat sonra %10 kuponlu geri kazanım maili gönderir.
     */
    private function processTwentyFourHourCarts(): void
    {
        $carts = Cart::with(['items.product', 'user'])
            ->where('updated_at', '<=', now()->subHours(24))
            ->where('updated_at', '>', now()->subHours(48))
            ->whereHas('items')
            ->whereNotNull('reminder_mail_sent_at') // Önce kuponsuz mail gitmiş olmalı
            ->whereNull('coupon_mail_sent_at')       // Kuponlu mail henüz gitmemiş olmalı
            ->where(function ($q) {
                $q->whereNotNull('user_id')
                  ->orWhereNotNull('guest_email');
            })
            ->get();

        foreach ($carts as $cart) {
            $email = $cart->user?->email ?? $cart->guest_email;
            if (!$email) {
                continue;
            }

            // Aynı session için duplikasyon kontrolü
            $existingEvent = CustomerEvent::where('event_type', 'cart_abandoned_24h')
                ->where('session_id', $cart->session_id)
                ->where('created_at', '>=', now()->subHours(48))
                ->exists();

            if ($existingEvent) {
                continue;
            }

            try {
                // Kişiye özel %10 kupon oluştur
                do {
                    $couponCode = 'PATEN10-' . random_int(1000, 9999);
                } while (Coupon::where('code', $couponCode)->exists());

                $expiresAt = now()->addHours(48);

                Coupon::create([
                    'code' => $couponCode,
                    'type' => 'percentage',
                    'value' => 10.00,
                    'usage_limit' => 1,
                    'used_count' => 0,
                    'expires_at' => $expiresAt,
                    'status' => true,
                ]);

                // Event oluştur
                CustomerEvent::create([
                    'user_id' => $cart->user_id,
                    'session_id' => $cart->session_id,
                    'event_type' => 'cart_abandoned_24h',
                    'event_data' => [
                        'cart_id' => $cart->id,
                        'coupon_code' => $couponCode,
                    ],
                ]);

                // %10 kuponlu mail gönder
                Mail::to($email)->send(
                    new \App\Mail\AbandonedCartCouponMail($cart, $couponCode, $expiresAt->format('d.m.Y H:i'))
                );

                // Gönderim zamanını kaydet
                $cart->update(['coupon_mail_sent_at' => now()]);

                Log::info("Kuponlu geri kazanım maili gönderildi: {$email} (Kupon: {$couponCode}, Sepet #{$cart->id})");
            } catch (\Exception $e) {
                Log::error("Kuponlu mail gönderilemedi: {$email} - " . $e->getMessage());
            }
        }
    }
}
