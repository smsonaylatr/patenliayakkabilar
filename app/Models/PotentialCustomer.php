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

                $productName = $potentialCustomer->product ? htmlspecialchars($potentialCustomer->product->name) : 'Bilinmeyen Ürün';
                $purpose = htmlspecialchars($potentialCustomer->buying_for ?? '-');
                $phone = $potentialCustomer->phone ?? '-';

                $message = "🚨 <b>YENİ POTANSİYEL MÜŞTERİ!</b>\n\n";
                $message .= "🛒 <b>İlgilendiği Ürün:</b> {$productName}\n";
                $message .= "🎯 <b>Alım Amacı:</b> {$purpose}\n";
                $message .= "📱 <b>Telefon:</b> {$phone}\n";
                
                // Inline keyboard — "SMS Gönder" butonu
                $replyMarkup = [
                    'inline_keyboard' => [
                        [
                            [
                                'text' => '📩 SMS Bilgilendir',
                                'callback_data' => 'sms_send_' . $potentialCustomer->id,
                            ],
                            [
                                'text' => '💬 WhatsApp',
                                'url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $phone),
                            ],
                        ],
                    ],
                ];

                $response = \Illuminate\Support\Facades\Http::timeout(5)->asJson()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'reply_markup' => $replyMarkup,
                ]);

                if (!$response->successful() || !($response->json('ok') ?? false)) {
                    \Illuminate\Support\Facades\Log::error('Telegram sendMessage failed: ' . $response->body());
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Potential Customer Telegram notification failed: ' . $e->getMessage());
            }
        });
    }
}
