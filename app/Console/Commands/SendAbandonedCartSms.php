<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\Setting;
use App\Services\VatanSmsService;
use Carbon\Carbon;

class SendAbandonedCartSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-abandoned-cart-sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sepeti terk eden ve SMS izni veren kullanıcılara hatırlatma SMSi gönderir.';

    /**
     * Execute the console command.
     */
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
            // Eğer sepetin içindeki ürünler boşsa atla
            if ($cart->items()->count() === 0) {
                continue;
            }

            // Müşterinin son 24 saatte verdiği başarılı bir sipariş var mı kontrol et (Sipariş verilmişse sepet eski olabilir)
            $hasRecentOrder = \App\Models\Order::where('customer_phone', $cart->guest_phone)
                ->where('created_at', '>', Carbon::now()->subHours(24))
                ->exists();

            if ($hasRecentOrder) {
                continue;
            }

            $success = $smsService->send($cart->guest_phone, $messageTemplate, 'turkce', 'ticari');

            if ($success) {
                $cart->update(['abandoned_sms_sent_at' => Carbon::now()]);
                $count++;
            }
        }

        $this->info("Toplam {$count} adet sepet terk SMS'i başarıyla gönderildi.");
    }
}
