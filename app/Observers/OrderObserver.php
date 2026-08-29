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
                
                // Telegram Bildirimi Gönder
                try {
                    $this->sendTelegramNotification($order, 'new');
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Telegram notification error: ' . $e->getMessage());
                }
                
                // Kapıda ödemeli siparişi doğrudan Porego'ya aktar
                try {
                    app(\App\Services\PoregoApiService::class)->sendOrder($order);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Porego API error: ' . $e->getMessage());
                }

                
                // Müşteriye SMS Gönder
                try {
                    $this->sendCustomerSms($order, 'new_order');
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('SMS notification error: ' . $e->getMessage());
                }
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
                $order->loadMissing(['items.product.images', 'items.variant']);

                $paymentMethods = [
                    'credit_card' => 'Kredi Kartı',
                    'cash_on_delivery' => 'Kapıda Ödeme',
                    'wire_transfer' => 'Havale / EFT'
                ];
                
                $paymentMethod = $paymentMethods[$order->payment_method] ?? $order->payment_method;
                
                if ($type === 'pending') {
                    $htmlMessage = "⏳ <b>ÖDEME AŞAMASINDA (Yarım Kalabilir)</b>\n";
                    $htmlMessage .= "<i>Müşteri şu an ödeme sayfasında.</i>\n\n";
                } elseif ($type === 'failed') {
                    $htmlMessage = "❌ <b>ÖDEME BAŞARISIZ / YARIM KALAN SİPARİŞ</b>\n\n";
                } elseif ($type === 'paid') {
                    $htmlMessage = "✅ <b>ÖDEME BAŞARILI (Sipariş Onaylandı)</b>\n\n";
                } else {
                    $htmlMessage = "📦 <b>YENİ SİPARİŞ GELDİ!</b>\n\n";
                }
                $htmlMessage .= "🛒 <b>Sipariş No:</b> " . htmlspecialchars($order->order_number) . "\n";
                $htmlMessage .= "👤 <b>Müşteri:</b> " . htmlspecialchars($order->customer_name) . "\n";
                $htmlMessage .= "📞 <b>Telefon:</b> " . htmlspecialchars($order->customer_phone) . "\n";
                $htmlMessage .= "💰 <b>Tutar:</b> " . number_format((float)$order->grand_total, 2) . " ₺\n";
                $htmlMessage .= "💳 <b>Ödeme:</b> " . htmlspecialchars($paymentMethod) . "\n\n";

                $htmlMessage .= "📦 <b>Ürünler:</b>\n";
                foreach ($order->items as $item) {
                    $sku = $item->variant?->sku ?? '-';
                    $variantText = $item->variant_info ? " ({$item->variant_info})" : "";
                    $htmlMessage .= "- {$item->quantity}x " . htmlspecialchars($item->product_name . $variantText) . " | SKU: " . htmlspecialchars($sku) . "\n";
                }
                $htmlMessage .= "\n";

                $htmlMessage .= "📍 <b>Teslimat Adresi:</b>\n" . htmlspecialchars($order->shipping_address) . "\n" . htmlspecialchars($order->shipping_district) . " / " . htmlspecialchars($order->shipping_city) . "\n\n";
                
                if (!empty($order->customer_note)) {
                    $htmlMessage .= "📝 <b>Sipariş Notu:</b>\n" . htmlspecialchars($order->customer_note) . "\n\n";
                }
                
                $htmlMessage .= "Detaylar için admin panelini kontrol edebilirsiniz.";

                // Her ürünün ilk görselini topla
                $imageUrls = [];
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->images->count() > 0) {
                        $imgUrl = $item->product->images->first()->raw_image_url;
                        if (!empty($imgUrl)) {
                            $imageUrls[] = $imgUrl;
                        }
                    }
                }

                if (count($imageUrls) > 1) {
                    // Birden fazla ürün görseli varsa → sendMediaGroup ile hepsini gönder
                    $media = [];
                    foreach ($imageUrls as $index => $url) {
                        $photoItem = [
                            'type' => 'photo',
                            'media' => $url,
                        ];
                        // İlk fotoğrafa caption olarak sipariş bilgisini ekle
                        if ($index === 0) {
                            $photoItem['caption'] = $htmlMessage;
                            $photoItem['parse_mode'] = 'HTML';
                        }
                        $media[] = $photoItem;
                    }

                    $response = \Illuminate\Support\Facades\Http::timeout(10)->asJson()->post("https://api.telegram.org/bot{$token}/sendMediaGroup", [
                        'chat_id' => $chatId,
                        'media' => $media,
                    ]);

                    if ($response->failed()) {
                        \Illuminate\Support\Facades\Log::warning('Telegram sendMediaGroup failed: ' . $response->body());
                        // Fallback: sadece metin gönder
                        \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                            'chat_id' => $chatId,
                            'text' => $htmlMessage,
                            'parse_mode' => 'HTML',
                        ]);
                    }
                } elseif (count($imageUrls) === 1) {
                    // Tek ürün görseli → sendPhoto ile gönder
                    $response = \Illuminate\Support\Facades\Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                        'chat_id' => $chatId,
                        'photo' => $imageUrls[0],
                        'caption' => $htmlMessage,
                        'parse_mode' => 'HTML',
                    ]);

                    if ($response->failed()) {
                        \Illuminate\Support\Facades\Log::warning('Telegram sendPhoto failed: ' . $response->body());
                        \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                            'chat_id' => $chatId,
                            'text' => $htmlMessage,
                            'parse_mode' => 'HTML',
                        ]);
                    }
                } else {
                    // Görsel yok → sadece metin gönder
                    $response = \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $htmlMessage,
                        'parse_mode' => 'HTML',
                    ]);

                    if ($response->failed()) {
                        \Illuminate\Support\Facades\Log::warning('Telegram sendMessage failed: ' . $response->body());
                        $plainText = strip_tags($htmlMessage);
                        \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                            'chat_id' => $chatId,
                            'text' => $plainText,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Telegram notification failed: ' . $e->getMessage());
        }
    }


    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // PayTR veya Admin panel üzerinden ödeme 'paid' (ödendi) durumuna geçtiğinde
        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid') {
            $order->loadMissing(['items.product', 'items.variant']);

            // 1. Telegram Bildirimi Gönder
            try {
                $this->sendTelegramNotification($order, 'paid');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Telegram notification error on paid: ' . $e->getMessage());
            }
            
            // 2. Tam Otomatik Porego Kargo Aktarımı
            try {
                app(\App\Services\PoregoApiService::class)->sendOrder($order);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Porego API error on paid: ' . $e->getMessage());
            }

            
            // 4. Müşteriye SMS Gönder
            try {
                $this->sendCustomerSms($order, 'new_order');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SMS notification error on paid: ' . $e->getMessage());
            }
        } elseif ($order->wasChanged('payment_status') && $order->payment_status === 'failed') {
            try {
                $this->sendTelegramNotification($order, 'failed');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Telegram notification error on failed: ' . $e->getMessage());
            }
        }

        if ($order->wasChanged('status')) {
            if ($order->status === 'shipped') {
                app()->terminating(function () use ($order) {
                    $order->refresh();
                    $this->sendCustomerSms($order, 'shipped');
                });
            } elseif ($order->status === 'delivered') {
                app()->terminating(function () use ($order) {
                    $order->refresh();
                    try {
                        app(\App\Services\GibEArsivService::class)->autoInvoiceAndSendMail($order);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('GİB E-Arşiv error on delivered: ' . $e->getMessage());
                    }
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
