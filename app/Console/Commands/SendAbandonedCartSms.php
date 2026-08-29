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

    protected $description = 'Sepeti terk eden ve SMS izni veren kullanıcılara %5 indirim kuponlu hatırlatma SMSi gönderir.';

    public function handle(VatanSmsService $smsService)
    {
        $isActive = filter_var(Setting::where('key', 'vatansms_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
        
        if (!$isActive) {
            $this->info('VatanSMS aktif değil. İşlem iptal edildi.');
            return;
        }

        $messageTemplate = Setting::where('key', 'vatansms_abandoned_cart_message')->value('value');
        if (empty($messageTemplate)) {
            $this->error('Sepet hatırlatma mesaj şablonu bulunamadı.');
            return;
        }

        // 1 saatten eski, 24 saatten yeni olan ve henüz SMS atılmamış sepetleri bul
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

            // Müşterinin son 24 saatte sipariş verdiyse atla
            $hasRecentOrder = \App\Models\Order::where('customer_phone', $cart->guest_phone)
                ->where('created_at', '>', Carbon::now()->subHours(24))
                ->exists();

            if ($hasRecentOrder) {
                continue;
            }

            // Son 7 günde bu telefona zaten SEPET kuponu üretilmiş mi?
            $existingCoupon = Coupon::where('code', 'like', 'SEPET-%')
                ->where('created_at', '>', Carbon::now()->subDays(7))
                ->where('used_count', 0)
                ->where('status', true)
                ->whereRaw("code IN (
                    SELECT code FROM coupons 
                    WHERE code LIKE 'SEPET-%' 
                    AND created_at > ? 
                    AND id IN (
                        SELECT MAX(id) FROM coupons 
                        WHERE code LIKE 'SEPET-%' 
                        GROUP BY code
                    )
                )", [Carbon::now()->subDays(7)])
                ->first();

            // Basit yaklaşım: her sepet için yeni kupon üret (çift gönderim abandoned_sms_sent_at ile engelleniyor)
            $couponCode = $this->generateUniqueCouponCode();
            
            Coupon::create([
                'code' => $couponCode,
                'type' => 'percentage',
                'value' => 5,
                'min_cart_total' => null,
                'usage_limit' => 1,
                'used_count' => 0,
                'expires_at' => Carbon::now()->addDays(3),
                'status' => true,
            ]);

            // Mesaja kupon kodunu ekle
            $message = $messageTemplate;

            if (str_contains($message, '{kupon}')) {
                $message = str_replace('{kupon}', $couponCode, $message);
            } else {
                $message .= " Size ozel %5 indirim kodunuz: " . $couponCode . " (3 gun gecerli)";
            }

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
     * Benzersiz kupon kodu üret: SEPET-XXXXX
     */
    private function generateUniqueCouponCode(): string
    {
        do {
            $code = 'SEPET-' . strtoupper(Str::random(5));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }
}
