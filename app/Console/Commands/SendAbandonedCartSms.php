<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Setting;
use App\Services\VatanSmsService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SendAbandonedCartSms extends Command
{
    protected $signature = 'app:send-abandoned-cart-sms';

    protected $description = 'Sepeti terk eden kullanıcılara kişiye özel %10 indirim kuponuyla SMS gönderir.';

    public function handle(VatanSmsService $smsService)
    {
        $isActive = filter_var(Setting::where('key', 'vatansms_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
        
        if (!$isActive) {
            $this->info('VatanSMS aktif değil. İşlem iptal edildi.');
            return;
        }

        // 1 saatten eski, 24 saatten yeni, henüz SMS atılmamış sepetler
        $carts = Cart::where('sms_consent', true)
            ->whereNotNull('guest_phone')
            ->whereNull('abandoned_sms_sent_at')
            ->where('updated_at', '<', Carbon::now()->subHours(1))
            ->where('updated_at', '>', Carbon::now()->subHours(24))
            ->get();

        $count = 0;
        foreach ($carts as $cart) {
            if ($cart->items()->count() === 0) {
                continue;
            }

            // Son 24 saatte sipariş verdiyse atla
            $hasRecentOrder = \App\Models\Order::where('customer_phone', $cart->guest_phone)
                ->where('created_at', '>', Carbon::now()->subHours(24))
                ->exists();

            if ($hasRecentOrder) {
                continue;
            }

            // Kişiye özel %10 kupon oluştur
            $couponCode = $this->generatePersonalCoupon($cart->guest_phone);

            // Mesajı oluştur — kupon her zaman dahil
            $name = $cart->guest_name ?? '';
            $greeting = $name ? "Sayin {$name}, sepetinizdeki" : "Merhaba, sepetinizdeki";

            $message = "{$greeting} urunler sizi bekliyor! "
                     . "Size ozel %10 indirim kodunuz: {$couponCode} "
                     . "(3 gun gecerli, tek kullanimlik). "
                     . "Alisverisi tamamlamak icin: https://patenliayakkabilar.com/checkout";

            $success = $smsService->send($cart->guest_phone, $message, 'turkce', 'ticari');

            if ($success) {
                $cart->update(['abandoned_sms_sent_at' => Carbon::now()]);
                $count++;
                $this->info("✅ {$cart->guest_phone} → Kupon: {$couponCode}");
            } else {
                $this->warn("❌ SMS gönderilemedi: {$cart->guest_phone}");
            }
        }

        $this->info("Toplam {$count} adet sepet terk SMS'i kuponla gönderildi.");
    }

    /**
     * Basit ve kolay okunur benzersiz kupon kodu üretir.
     * Format: PATEN10-XXXX (4 haneli rakam)
     */
    private function generatePersonalCoupon(string $phone): string
    {
        do {
            $code = 'PATEN10-' . random_int(1000, 9999);
        } while (Coupon::where('code', $code)->exists());

        Coupon::create([
            'code' => $code,
            'type' => 'percentage',
            'value' => 10,
            'min_cart_total' => null,
            'usage_limit' => 1,
            'used_count' => 0,
            'expires_at' => Carbon::now()->addDays(3),
            'status' => true,
        ]);

        return $code;
    }
}
