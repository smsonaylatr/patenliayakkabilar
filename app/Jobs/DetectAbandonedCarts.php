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
use App\Services\VatanSmsService;

class DetectAbandonedCarts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Terk edilen sepetleri kontrol et ve event oluştur.
     * Sepet güncellendikten 2 saat sonra hala aktifse ve siparişe dönüşmediyse tetiklenir.
     */
    public function handle(): void
    {
        // 1. 2 Saatlik Kuponsuz Sepet Hatırlatması (Mail + SMS)
        $this->processTwoHourCarts();

        // 2. 24 Saatlik %10 Kuponlu Geri Kazanım (Mail + SMS)
        $this->processTwentyFourHourCarts();
    }

    /**
     * 2 saat sonra kuponsuz hatırlatma maili + SMS gönderir.
     */
    private function processTwoHourCarts(): void
    {
        $carts = Cart::with(['items.product', 'user'])
            ->where('updated_at', '<=', now()->subHours(2))
            ->where('updated_at', '>', now()->subHours(24))
            ->whereHas('items')
            ->whereNull('reminder_mail_sent_at')
            ->where(function ($q) {
                $q->whereNotNull('user_id')
                  ->orWhereNotNull('guest_email')
                  ->orWhereNotNull('guest_phone');
            })
            ->get();

        $smsService = app(VatanSmsService::class);

        foreach ($carts as $cart) {
            $email = $cart->user?->email ?? $cart->guest_email;
            $phone = $cart->user?->phone ?? $cart->guest_phone;

            if (!$email && !$phone) {
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

            // Event oluştur
            CustomerEvent::create([
                'user_id' => $cart->user_id,
                'session_id' => $cart->session_id,
                'event_type' => 'cart_abandoned_2h',
                'event_data' => ['cart_id' => $cart->id],
            ]);

            // ─── Mail Gönderimi ────────────────────────────────────
            if ($email) {
                try {
                    Mail::to($email)->send(new \App\Mail\AbandonedCartReminderMail($cart));
                    $cart->update(['reminder_mail_sent_at' => now()]);
                    Log::info("Kuponsuz hatırlatma maili gönderildi: {$email} (Sepet #{$cart->id})");
                } catch (\Exception $e) {
                    Log::error("Hatırlatma maili gönderilemedi: {$email} - " . $e->getMessage());
                }
            }

            // ─── SMS Gönderimi (Kuponsuz) ──────────────────────────
            if ($phone) {
                try {
                    $name = $cart->user?->name ?? $cart->guest_name ?? '';
                    $greeting = $name ? "Sayin {$name}, sepetinizdeki" : "Merhaba, sepetinizdeki";

                    $smsMessage = "{$greeting} urunler sizi bekliyor! "
                                . "Alisverisi tamamlamak icin: https://patenliayakkabilar.com/checkout";

                    $result = $smsService->send($phone, $smsMessage, 'turkce', 'bilgi');

                    if ($result) {
                        Log::info("Kuponsuz hatırlatma SMS gönderildi: {$phone} (Sepet #{$cart->id})");
                    } else {
                        Log::warning("Hatırlatma SMS gönderilemedi: {$phone} - " . ($smsService->getLastError() ?? 'Bilinmeyen hata'));
                    }
                } catch (\Exception $e) {
                    Log::error("Hatırlatma SMS hatası: {$phone} - " . $e->getMessage());
                }
            }

            // Mail olmasa bile SMS gittiyse takip alanını güncelle
            if (!$email && $phone) {
                $cart->update(['reminder_mail_sent_at' => now()]);
            }
        }
    }

    /**
     * 24 saat sonra %10 kuponlu geri kazanım maili + SMS gönderir.
     */
    private function processTwentyFourHourCarts(): void
    {
        $carts = Cart::with(['items.product', 'user'])
            ->where('updated_at', '<=', now()->subHours(24))
            ->where('updated_at', '>', now()->subHours(48))
            ->whereHas('items')
            ->whereNotNull('reminder_mail_sent_at')  // Önce kuponsuz gönderim yapılmış olmalı
            ->whereNull('coupon_mail_sent_at')        // Kuponlu henüz gitmemiş olmalı
            ->where(function ($q) {
                $q->whereNotNull('user_id')
                  ->orWhereNotNull('guest_email')
                  ->orWhereNotNull('guest_phone');
            })
            ->get();

        $smsService = app(VatanSmsService::class);

        foreach ($carts as $cart) {
            $email = $cart->user?->email ?? $cart->guest_email;
            $phone = $cart->user?->phone ?? $cart->guest_phone;

            if (!$email && !$phone) {
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

                // ─── Kuponlu Mail Gönderimi ────────────────────────
                if ($email) {
                    try {
                        Mail::to($email)->send(
                            new \App\Mail\AbandonedCartCouponMail($cart, $couponCode, $expiresAt->format('d.m.Y H:i'))
                        );
                        Log::info("Kuponlu mail gönderildi: {$email} (Kupon: {$couponCode}, Sepet #{$cart->id})");
                    } catch (\Exception $e) {
                        Log::error("Kuponlu mail gönderilemedi: {$email} - " . $e->getMessage());
                    }
                }

                // ─── Kuponlu SMS Gönderimi ─────────────────────────
                if ($phone) {
                    try {
                        $name = $cart->user?->name ?? $cart->guest_name ?? '';
                        $greeting = $name ? "Sayin {$name}, sepetinizdeki" : "Merhaba, sepetinizdeki";

                        $smsMessage = "{$greeting} urunler sizi bekliyor! "
                                    . "Size ozel %10 indirim kodunuz: {$couponCode} "
                                    . "(48 saat gecerli, tek kullanimlik). "
                                    . "Alisverisi tamamlamak icin: https://patenliayakkabilar.com/checkout";

                        $result = $smsService->send($phone, $smsMessage, 'turkce', 'bilgi');

                        if ($result) {
                            $cart->update(['abandoned_sms_sent_at' => now()]);
                            Log::info("Kuponlu SMS gönderildi: {$phone} (Kupon: {$couponCode}, Sepet #{$cart->id})");
                        } else {
                            Log::warning("Kuponlu SMS gönderilemedi: {$phone} - " . ($smsService->getLastError() ?? 'Bilinmeyen hata'));
                        }
                    } catch (\Exception $e) {
                        Log::error("Kuponlu SMS hatası: {$phone} - " . $e->getMessage());
                    }
                }

                // Gönderim zamanını kaydet
                $cart->update(['coupon_mail_sent_at' => now()]);

            } catch (\Exception $e) {
                Log::error("Kuponlu geri kazanım hatası (Sepet #{$cart->id}): " . $e->getMessage());
            }
        }
    }
}
