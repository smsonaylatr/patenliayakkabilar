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

        // Kredi kartı ve Havale/EFT ödemelerinde, müşteri ödeme sayfasına yönlendirildiğinde "Ödeme Aşamasında" bildirimi gönderiyoruz
        if (in_array($order->payment_method, ['credit_card', 'wire_transfer'])) {
            app()->terminating(function () use ($order) {
                $order->refresh();
                $this->sendTelegramNotification($order, 'pending');
            });
        } else {
            app()->terminating(function () use ($order) {
                $order->refresh();
                $this->sendTelegramNotification($order, 'new');
                
                // Kapıda ödemeli siparişi doğrudan Porego'ya aktar
                app(\App\Services\PoregoApiService::class)->sendOrder($order);
                
                // Müşteriye SMS Gönder
                $this->sendCustomerSms($order, 'new_order');
            });
        }
    }

    private function sendCustomerSms(Order $order, string $type): void
    {
        if (empty($order->customer_phone)) return;

        $isActive = filter_var(\App\Models\Setting::where('key', 'vatansms_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
        if (!$isActive) return;

        $messageTemplate = '';
        if ($type === 'new_order') {
            $messageTemplate = \App\Models\Setting::where('key', 'vatansms_new_order_message')->value('value');
        } elseif ($type === 'shipped') {
            $messageTemplate = \App\Models\Setting::where('key', 'vatansms_shipped_message')->value('value');
        }

        if (empty($messageTemplate)) return;

        $message = str_replace(
            ['{isim}', '{siparis_no}', '{tutar}'],
            [$order->customer_name, $order->order_number, number_format((float)$order->grand_total, 2) . ' TL'],
            $messageTemplate
        );

        app(\App\Services\VatanSmsService::class)->send($order->customer_phone, $message, 'turkce', 'bilgi');
    }

    private function sendTelegramNotification(Order $order, string $type = 'new'): void
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
                
                if ($type === 'pending') {
                    $message = "⏳ *ÖDEME AŞAMASINDA (Yarım Kalabilir)*\n";
                    $message .= "_Müşteri şu an ödeme sayfasında._\n\n";
                } elseif ($type === 'failed') {
                    $message = "❌ *ÖDEME BAŞARISIZ / YARIM KALAN SİPARİŞ*\n\n";
                } elseif ($type === 'paid') {
                    $message = "✅ *ÖDEME BAŞARILI (Sipariş Onaylandı)*\n\n";
                } else {
                    $message = "📦 *YENİ SİPARİŞ GELDİ!*\n\n";
                }
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
        // PayTR üzerinden Kredi kartı veya Havale ödemesi durumu değiştiğinde
        if ($order->wasChanged('payment_status') && in_array($order->payment_method, ['credit_card', 'wire_transfer'])) {
            if ($order->payment_status === 'paid') {
                app()->terminating(function () use ($order) {
                    $order->refresh();
                    $this->sendTelegramNotification($order, 'paid');
                    
                    // Siparişi Porego'ya aktar
                    app(\App\Services\PoregoApiService::class)->sendOrder($order);
                    
                    // Müşteriye SMS Gönder
                    $this->sendCustomerSms($order, 'new_order');
                });
            } elseif ($order->payment_status === 'failed') {
                app()->terminating(function () use ($order) {
                    $order->refresh();
                    $this->sendTelegramNotification($order, 'failed');
                });
            }
        }

        if ($order->wasChanged('status')) {
            if ($order->status === 'shipped') {
                app()->terminating(function () use ($order) {
                    $order->refresh();
                    $this->sendCustomerSms($order, 'shipped');
                });
            }

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
