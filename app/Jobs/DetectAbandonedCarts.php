<?php

namespace App\Jobs;

use App\Models\Cart;
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
        // Otomatik: 2 Saatlik Kuponsuz Sepet Hatırlatması (Mail + SMS)
        $this->processTwoHourCarts();

        // NOT: %10 Kuponlu geri kazanım (Mail + SMS) admin panelden
        // manuel olarak tetiklenir. Otomatik gönderim yapılmaz.
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
}

