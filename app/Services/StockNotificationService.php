<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockNotification;
use App\Mail\StockBackMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StockNotificationService
{
    /**
     * Stok yenilendiğinde bekleyen bildirim taleplerini işler.
     */
    public static function processNotifications(Product $product, ?ProductVariant $variant = null): int
    {
        if (!$product->status) {
            return 0;
        }

        $query = StockNotification::where('product_id', $product->id)
            ->where('is_notified', false);

        if ($variant) {
            $query->where('product_variant_id', $variant->id);
        }

        $pendingNotifications = $query->get();

        if ($pendingNotifications->isEmpty()) {
            return 0;
        }

        $notifiedCount = 0;
        $vatanSms = app(VatanSmsService::class);

        foreach ($pendingNotifications as $notification) {
            try {
                // 1. E-Posta Bildirimi
                if (!empty($notification->email)) {
                    Mail::to($notification->email)->queue(new StockBackMail($product, $variant));
                }

                // 2. SMS Bildirimi (Telefon girilmişse)
                if (!empty($notification->phone)) {
                    $sizeText = $variant ? " ({$variant->size} Beden)" : '';
                    $message = "Müjde! Patenli Ayakkabilar'da beklediğiniz {$product->name}{$sizeText} ürünü stoklarımıza girmiştir. İncelemek için: " . url('/urun/' . $product->slug);
                    $vatanSms->send($notification->phone, $message);
                }

                // Kaydı bildirildi olarak güncelle
                $notification->update([
                    'is_notified' => true,
                    'notified_at' => now(),
                ]);

                $notifiedCount++;
            } catch (\Throwable $th) {
                Log::error("Stok Bildirim Hatası [ID: {$notification->id}]: " . $th->getMessage());
            }
        }

        return $notifiedCount;
    }
}
