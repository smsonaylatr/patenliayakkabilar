<?php

namespace App\Observers;

use App\Models\Order;
use Filament\Notifications\Notification;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Send a database notification to admins when a new order is placed.
        Notification::make()
            ->title('Yeni Sipariş Geldi')
            ->body("{$order->customer_name} adlı müşteri {$order->grand_total} ₺ tutarında yeni bir sipariş verdi (Sipariş No: {$order->order_number}).")
            ->icon('heroicon-o-shopping-bag')
            ->color('success')
            ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());

        // Send Telegram Notification after request finishes so items are definitely attached
        // Kredi kartı ödemelerinde, ödeme PayTR üzerinden tamamlanana kadar (webhook gelene kadar) bildirim göndermiyoruz
        if ($order->payment_method !== 'credit_card') {
            app()->terminating(function () use ($order) {
                $order->refresh();
                $this->sendTelegramNotification($order);
            });
        }
    }

    private function sendTelegramNotification(Order $order): void
    {
        $isActive = filter_var(\App\Models\Setting::where('key', 'telegram_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
        $token = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value');
        $chatId = \App\Models\Setting::where('key', 'telegram_chat_id')->value('value');

        if ($isActive && !empty($token) && !empty($chatId)) {
            $paymentMethods = [
                'credit_card' => 'Kredi Kartı',
                'cash_on_delivery' => 'Kapıda Ödeme',
                'bank_transfer' => 'Havale / EFT'
            ];
            
            $paymentMethod = $paymentMethods[$order->payment_method] ?? $order->payment_method;
            
            $message = "📦 *YENİ SİPARİŞ GELDİ!*\n\n";
            $message .= "🛒 *Sipariş No:* {$order->order_number}\n";
            $message .= "👤 *Müşteri:* {$order->customer_name}\n";
            $message .= "📞 *Telefon:* {$order->customer_phone}\n";
            $message .= "💰 *Tutar:* " . number_format($order->grand_total, 2) . " ₺\n";
            $message .= "💳 *Ödeme:* {$paymentMethod}\n\n";
            $message .= "📍 *Teslimat Adresi:*\n{$order->shipping_address}\n{$order->shipping_district} / {$order->shipping_city}\n\n";
            
            if (!empty($order->customer_note)) {
                $message .= "📝 *Sipariş Notu:*\n{$order->customer_note}\n\n";
            }
            
            $message .= "Detaylar için admin panelini kontrol edebilirsiniz.";

            $imageUrl = null;
            $firstItem = $order->items()->first();
            if ($firstItem && $firstItem->product && $firstItem->product->images->count() > 0) {
                // Telegram'ın fotoğrafı indirebilmesi için tam URL olması gerekir
                $imageUrl = $firstItem->product->images->first()->image_url;
                if (!str_starts_with($imageUrl, 'http')) {
                    $imageUrl = asset($imageUrl);
                }
            }

            try {
                if ($imageUrl) {
                    \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendPhoto", [
                        'chat_id' => $chatId,
                        'photo' => $imageUrl,
                        'caption' => $message,
                        'parse_mode' => 'Markdown'
                    ]);
                } else {
                    \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown'
                    ]);
                }
            } catch (\Exception $e) {
                // Sessizce hatayı yutalım, sipariş akışını bozmamak için
                \Illuminate\Support\Facades\Log::error('Telegram notification failed: ' . $e->getMessage());
            }
        }
    }


    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Kredi kartı ödemesi PayTR tarafından onaylanıp "paid" statüsüne geçince Telegram bildirimi gönder
        if ($order->isDirty('payment_status') && $order->payment_status === 'paid' && $order->payment_method === 'credit_card') {
            app()->terminating(function () use ($order) {
                $order->refresh();
                $this->sendTelegramNotification($order);
            });
        }

        if ($order->isDirty('status')) {
            // Audit Log: Durum değişikliği
            \App\Models\OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $order->status,
                'changed_by' => auth()->id(),
                'note' => 'Sipariş durumu sistem veya yetkili tarafından güncellendi.',
            ]);

            if ($order->status === 'cancelled') {
                Notification::make()
                    ->title('Sipariş İptal Edildi')
                    ->body("{$order->order_number} numaralı sipariş iptal edildi.")
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
            } elseif ($order->status === 'shipped') {
                Notification::make()
                    ->title('Sipariş Kargoya Verildi')
                    ->body("{$order->order_number} numaralı sipariş kargolandı. Kargo Kodu: {$order->cargo_tracking_code}")
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
            }
        }
    }
}
