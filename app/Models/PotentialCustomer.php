<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotentialCustomer extends Model
{
    protected $fillable = [
        'product_id',
        'buying_for',
        'phone',
        'email',
        'status',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (PotentialCustomer $potentialCustomer) {
            try {
                $isActive = filter_var(\App\Models\Setting::where('key', 'telegram_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
                if (!$isActive) return;

                $token = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value');
                $chatId = \App\Models\Setting::where('key', 'telegram_chat_id')->value('value');

                if (!$token || !$chatId) return;

                $productName = $potentialCustomer->product ? $potentialCustomer->product->name : 'Bilinmeyen Ürün';
                $purpose = $potentialCustomer->buying_for ?? '-';
                $phone = $potentialCustomer->phone ?? '-';

                $message = "🚨 *YENİ POTANSİYEL MÜŞTERİ!*\n\n";
                $message .= "🛒 *İlgilendiği Ürün:* {$productName}\n";
                $message .= "🎯 *Alım Amacı:* {$purpose}\n";
                $message .= "📱 *Telefon:* {$phone}\n";
                
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Potential Customer Telegram notification failed: ' . $e->getMessage());
            }
        });
    }
}
