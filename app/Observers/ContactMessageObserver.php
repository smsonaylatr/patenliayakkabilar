<?php

namespace App\Observers;

use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactMessageObserver
{
    /**
     * Handle the ContactMessage "created" event.
     */
    public function created(ContactMessage $contactMessage): void
    {
        $this->sendTelegramNotification($contactMessage);
    }

    /**
     * Send Telegram notification for incoming contact message.
     */
    public function sendTelegramNotification(ContactMessage $contactMessage): void
    {
        try {
            // Check global telegram notification status
            $isActive = filter_var(Setting::where('key', 'telegram_active')->value('value'), FILTER_VALIDATE_BOOLEAN);
            if (!$isActive) {
                return;
            }

            // Check contact form specific notification toggle (defaults to true if not set)
            $contactActiveSetting = Setting::where('key', 'telegram_contact_active')->value('value');
            $isContactActive = $contactActiveSetting !== null ? filter_var($contactActiveSetting, FILTER_VALIDATE_BOOLEAN) : true;
            if (!$isContactActive) {
                return;
            }

            $token = Setting::where('key', 'telegram_bot_token')->value('value');
            $chatId = Setting::where('key', 'telegram_contact_chat_id')->value('value') ?: Setting::where('key', 'telegram_chat_id')->value('value');

            if (empty($token) || empty($chatId)) {
                return;
            }

            // Deduplication check: prevent duplicate notifications within 10 minutes
            $dedupKey = "telegram_sent_contact_{$contactMessage->id}";
            if (Cache::has($dedupKey)) {
                Log::info("Telegram contact notification duplicate prevented: #{$contactMessage->id}");
                return;
            }
            Cache::put($dedupKey, true, now()->addMinutes(10));

            // Sanitize customer data for Telegram HTML mode
            $name = htmlspecialchars($contactMessage->name ?? 'İsimsiz', ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($contactMessage->email ?? '-', ENT_QUOTES, 'UTF-8');
            $rawSubject = trim((string)($contactMessage->subject ?? ''));
            $createdAt = $contactMessage->created_at ? $contactMessage->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i');

            // Detect whether subject field contains a phone number
            $cleanedDigits = preg_replace('/[^0-9]/', '', $rawSubject);
            $isPhone = strlen($cleanedDigits) >= 10;

            $messageText = "📬 <b>YENİ İLETİŞİM FORMU MESAJI!</b>\n\n";
            $messageText .= "👤 <b>Gönderen:</b> {$name}\n";
            $messageText .= "📧 <b>E-Posta:</b> {$email}\n";

            if ($isPhone) {
                $messageText .= "📱 <b>Telefon:</b> " . htmlspecialchars($rawSubject, ENT_QUOTES, 'UTF-8') . "\n";
            } elseif (!empty($rawSubject)) {
                $messageText .= "📝 <b>Konu:</b> " . htmlspecialchars($rawSubject, ENT_QUOTES, 'UTF-8') . "\n";
            }

            $messageText .= "📅 <b>Tarih:</b> {$createdAt}\n\n";

            // Truncate long messages if necessary to avoid exceeding Telegram's 4096 character limit
            $rawContent = (string)($contactMessage->message ?? '');
            if (mb_strlen($rawContent) > 3000) {
                $rawContent = mb_substr($rawContent, 0, 2997) . '...';
            }
            $escapedBody = htmlspecialchars($rawContent, ENT_QUOTES, 'UTF-8');

            $messageText .= "💬 <b>Mesaj İçeriği:</b>\n{$escapedBody}";

            // Prepare inline action buttons
            $actionRow = [];

            if ($isPhone) {
                // Normalize Turkish phone number for WhatsApp
                $waDigits = $cleanedDigits;
                if (str_starts_with($waDigits, '0')) {
                    $waDigits = '90' . substr($waDigits, 1);
                } elseif (!str_starts_with($waDigits, '90') && strlen($waDigits) === 10) {
                    $waDigits = '90' . $waDigits;
                }

                $actionRow[] = [
                    'text' => '💬 WhatsApp',
                    'url' => 'https://wa.me/' . $waDigits,
                ];
            }

            // Panel view link
            $adminUrl = url('/admin/contact-messages/' . $contactMessage->id);
            $actionRow[] = [
                'text' => '🔍 Panelde Gör',
                'url' => $adminUrl,
            ];

            $keyboard = [$actionRow];

            // Mark as read callback button (interactive via Webhook)
            $keyboard[] = [
                [
                    'text' => '✅ Okundu İşaretle',
                    'callback_data' => 'contact_read_' . $contactMessage->id,
                ],
            ];

            $response = Http::timeout(5)->asJson()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $messageText,
                'parse_mode' => 'HTML',
                'reply_markup' => [
                    'inline_keyboard' => $keyboard,
                ],
            ]);

            if (!$response->successful() || !($response->json('ok') ?? false)) {
                Log::warning('Telegram contact notification failed: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Telegram contact notification exception: ' . $e->getMessage());
        }
    }
}
