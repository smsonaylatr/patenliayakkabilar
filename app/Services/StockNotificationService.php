<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockNotification;
use App\Models\Setting;
use App\Mail\StockBackMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StockNotificationService
{
    /**
     * Tek bir stok bildirim talebini işler ve müşteriye e-posta/SMS gönderir.
     */
    public static function notifySingle(StockNotification $notification): bool
    {
        try {
            $product = $notification->product;
            if (!$product) {
                return false;
            }

            $variant = $notification->variant;
            $mailSent = false;
            $smsSent = false;

            // 1. E-Posta Bildirimi
            if (!empty($notification->email)) {
                $isMailEnabled = filter_var(Setting::where('key', 'mail_stock_notification')->value('value') ?? true, FILTER_VALIDATE_BOOLEAN);
                if ($isMailEnabled) {
                    try {
                        Mail::to($notification->email)->send(new StockBackMail($product, $variant));
                        $mailSent = true;
                    } catch (\Throwable $mEx) {
                        Log::error("Stok Bildirimi E-Posta Hatası [ID: {$notification->id}]: " . $mEx->getMessage());
                    }
                }
            }

            // 2. SMS Bildirimi (Telefon girilmişse)
            if (!empty($notification->phone)) {
                try {
                    $vatanSms = app(VatanSmsService::class);
                    $sizeText = $variant ? " ({$variant->size} Beden)" : '';
                    $message = "Müjde! Patenli Ayakkabılar'da beklediğiniz {$product->name}{$sizeText} ürünü stoklarımıza girmiştir. İncelemek için: " . url('/urun/' . $product->slug);
                    $smsResult = $vatanSms->send($notification->phone, $message);
                    if ($smsResult) {
                        $smsSent = true;
                    }
                } catch (\Throwable $sEx) {
                    Log::error("Stok Bildirimi SMS Hatası [ID: {$notification->id}]: " . $sEx->getMessage());
                }
            }

            if (!$mailSent && !$smsSent) {
                Log::warning("Stok bildirimi hiçbir kanaldan iletilemedi [ID: {$notification->id}]: {$notification->email}");
                return false;
            }

            // Kaydı bildirildi olarak güncelle
            $notification->update([
                'is_notified' => true,
                'notified_at' => now(),
            ]);

            Log::info("Stok bildirimi tamamlandı [ID: {$notification->id}]: {$notification->email}" . ($mailSent ? ' (E-posta iletildi)' : '') . ($smsSent ? ' (SMS iletildi)' : ''));

            return true;
        } catch (\Throwable $th) {
            Log::error("Stok Bildirim Hatası [ID: {$notification->id}]: " . $th->getMessage());
            return false;
        }
    }

    /**
     * Stok yenilendiğinde bekleyen bildirim taleplerini işler.
     */
    public static function processNotifications(Product $product, ?ProductVariant $variant = null): int
    {
        try {
            if (!Schema::hasTable('stock_notifications')) {
                return 0;
            }

            // Stok kontrolü: İlgili varyant veya ürün stokta olmalı
            if ($variant) {
                if ((int) $variant->stock <= 0) {
                    return 0;
                }
            } else {
                if ((int) $product->stock <= 0) {
                    return 0;
                }
            }

            // Ürün pasife alınmışsa bildirim gönderilmez ve ürün zorla aktifleştirilmez
            if (!$product->status) {
                return 0;
            }

            $query = StockNotification::where('product_id', $product->id)
                ->where('is_notified', false);

            if ($variant) {
                $query->where(function ($q) use ($variant) {
                    $q->where('product_variant_id', $variant->id)
                      ->orWhereNull('product_variant_id');
                });
            }

            $pendingNotifications = $query->get();

            if ($pendingNotifications->isEmpty()) {
                return 0;
            }

            $notifiedCount = 0;

            foreach ($pendingNotifications as $notification) {
                if (static::notifySingle($notification)) {
                    $notifiedCount++;
                }
            }

            if ($notifiedCount > 0) {
                Log::info("Gelince Haber Ver: {$product->name} için {$notifiedCount} bildirim başarıyla iletildi.");
            }

            return $notifiedCount;
        } catch (\Throwable $e) {
            Log::error('StockNotificationService error: ' . $e->getMessage());
            return 0;
        }
    }
}
