<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TelegramAlertService
{
    public function sendAlert(string $message, string $parseMode = 'HTML'): void
    {
        try {
            $active = Setting::where('key', 'telegram_active')->value('value');
            if ($active !== '1' && $active !== 'true' && $active !== true && $active !== 1) {
                return;
            }

            $botToken = Setting::where('key', 'telegram_bot_token')->value('value');
            $chatId = Setting::where('key', 'telegram_chat_id')->value('value');

            if (empty($botToken) || empty($chatId)) {
                return;
            }

            // Rate limiting: 10 messages per minute
            $cacheKey = 'telegram_alert_rate_limit';
            $requestsCount = Cache::get($cacheKey, 0);

            if ($requestsCount >= 10) {
                Log::warning('Telegram alert rate limit reached.');
                return;
            }

            Cache::put($cacheKey, $requestsCount + 1, now()->addMinute());

            // Truncate message to 4000 characters
            if (mb_strlen($message) > 4000) {
                $message = mb_substr($message, 0, 3997) . '...';
            }

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            Http::timeout(5)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);
        } catch (\Throwable $e) {
            Log::error('TelegramAlertService error: ' . $e->getMessage());
        }
    }
}
