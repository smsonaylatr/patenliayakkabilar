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
        // Kredi kartı ve Havale/EFT ödemelerinde, ödeme PayTR üzerinden tamamlanana kadar (webhook gelene kadar) bildirim göndermiyoruz
        if (!in_array($order->payment_method, ['credit_card', 'wire_transfer'])) {
            app()->terminating(function () use ($order) {
                $order->refresh();
                $this->sendTelegramNotification($order);
                
                // Kapıda ödemeli siparişi doğrudan Porego'ya aktar
                app(\App\Services\PoregoApiService::class)->sendOrder($order);
            });
        }
    }

    private function sendTelegramNotification(Order $order): void
    {
        try {
            $isActive = filter_var(\App\Models\Setting::where('key', 'telegram_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
            $token = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value');
            $chatId = \App\Models\Setting::where('key', 'telegram_chat_id')->value('value');

            if ($isActive && !empty($token) && !empty($chatId)) {
                $paymentMethods = [
                    'credit_card' => 'Kredi Kartı',
                    'cash_on_delivery' => 'Kapıda Ödeme',
                    'wire_transfer' => 'Havale / EFT'
                ];
                
                $paymentMethod = $paymentMethods[$order->payment_method] ?? $order->payment_method;
                
                $message = "📦 *YENİ SİPARİŞ GELDİ!*\n\n";
                $message .= "🛒 *Sipariş No:* {$order->order_number}\n";
                $message .= "👤 *Müşteri:* {$order->customer_name}\n";
                $message .= "📞 *Telefon:* {$order->customer_phone}\n";
                $message .= "💰 *Tutar:* " . number_format((float)$order->grand_total, 2) . " ₺\n";
                $message .= "💳 *Ödeme:* {$paymentMethod}\n\n";

                $message .= "📦 *Ürünler:*\n";
                foreach ($order->items as $item) {
                    $sku = $item->variant?->sku ?? '-';
                    $variantText = $item->variant_info ? " ({$item->variant_info})" : "";
                    $message .= "- {$item->quantity}x {$item->product_name}{$variantText} | SKU: {$sku}\n";
                }
                $message .= "\n";

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
                    if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
                        $imageUrl = asset($imageUrl);
                    }
                }

                if (!empty($imageUrl)) {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown',
                        'link_preview_options' => [
                            'url' => $imageUrl,
                            'prefer_small_media' => true
                        ]
                    ]);

                    // Fallback in case link_preview_options fails or URL is invalid for preview
                    if ($response->failed()) {
                        \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                            'chat_id' => $chatId,
                            'text' => $message,
                            'parse_mode' => 'Markdown',
                            'link_preview_options' => [
                                'is_disabled' => true
                            ]
                        ]);
                    }
                } else {
                    \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown',
                        'link_preview_options' => [
                            'is_disabled' => true
                        ]
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Sessizce hatayı yutalım, sipariş akışını ve Porego entegrasyonunu bozmamak için
            \Illuminate\Support\Facades\Log::error('Telegram notification failed: ' . $e->getMessage());
        }
    }


    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // PayTR üzerinden Kredi kartı veya Havale ödemesi onaylanıp "paid" statüsüne geçince Telegram bildirimi gönder
        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid' && in_array($order->payment_method, ['credit_card', 'wire_transfer'])) {
            app()->terminating(function () use ($order) {
                $order->refresh();
                $this->sendTelegramNotification($order);
                
                // Siparişi Porego'ya aktar
                app(\App\Services\PoregoApiService::class)->sendOrder($order);
            });
        }

        if ($order->wasChanged('status')) {
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
