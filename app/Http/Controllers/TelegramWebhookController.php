<?php

namespace App\Http\Controllers;

use App\Models\PotentialCustomer;
use App\Models\Setting;
use App\Services\VatanSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Telegram Bot Webhook — callback_query handler.
     * Inline buton basıldığında bu endpoint çağrılır.
     */
    public function handle(Request $request)
    {
        $data = $request->all();

        // callback_query kontrolü
        if (!isset($data['callback_query'])) {
            return response()->json(['ok' => true]);
        }

        $callbackQuery = $data['callback_query'];
        $callbackData = $callbackQuery['data'] ?? '';
        $callbackQueryId = $callbackQuery['id'];
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $messageId = $callbackQuery['message']['message_id'] ?? null;

        // İletişim mesajı "Okundu Olarak İşaretle" işlemi
        if (str_starts_with($callbackData, 'contact_read_')) {
            $contactId = (int) str_replace('contact_read_', '', $callbackData);
            $contact = \App\Models\ContactMessage::find($contactId);

            if (!$contact) {
                $this->answerCallbackQuery($callbackQueryId, '❌ İletişim mesajı bulunamadı.');
                return response()->json(['ok' => true]);
            }

            if ($contact->is_read) {
                $this->answerCallbackQuery($callbackQueryId, 'ℹ️ Bu mesaj zaten okundu olarak işaretlenmiş.');
                return response()->json(['ok' => true]);
            }

            $contact->update(['is_read' => true]);
            $this->answerCallbackQuery($callbackQueryId, '✅ Mesaj okundu olarak işaretlendi!');

            if ($chatId && $messageId) {
                $originalText = $callbackQuery['message']['text'] ?? '';
                $updatedText = $originalText . "\n\n✅ <b>OKUNDU OLARAK İŞARETLENDİ</b> (" . now()->format('d.m.Y H:i') . ')';

                // URL butonlarını koru, sadece callback butonunu kaldır
                $replyMarkup = null;
                if (!empty($callbackQuery['message']['reply_markup']['inline_keyboard'])) {
                    $newKeyboard = [];
                    foreach ($callbackQuery['message']['reply_markup']['inline_keyboard'] as $row) {
                        $newRow = [];
                        foreach ($row as $btn) {
                            if (!isset($btn['callback_data']) || !str_starts_with($btn['callback_data'], 'contact_read_')) {
                                $newRow[] = $btn;
                            }
                        }
                        if (!empty($newRow)) {
                            $newKeyboard[] = $newRow;
                        }
                    }
                    if (!empty($newKeyboard)) {
                        $replyMarkup = ['inline_keyboard' => $newKeyboard];
                    }
                }

                $this->editMessageTextAndMarkup($chatId, $messageId, $updatedText, $replyMarkup);
            }

            return response()->json(['ok' => true]);
        }

        // Sadece "sms_send_" ile başlayan callback'leri işle
        if (!str_starts_with($callbackData, 'sms_send_')) {
            $this->answerCallbackQuery($callbackQueryId, '❌ Bilinmeyen işlem.');
            return response()->json(['ok' => true]);
        }

        $customerId = (int) str_replace('sms_send_', '', $callbackData);
        $customer = PotentialCustomer::with('product')->find($customerId);

        if (!$customer) {
            $this->answerCallbackQuery($callbackQueryId, '❌ Müşteri bulunamadı.');
            return response()->json(['ok' => true]);
        }

        if (!$customer->phone) {
            $this->answerCallbackQuery($callbackQueryId, '❌ Müşterinin telefon numarası yok.');
            return response()->json(['ok' => true]);
        }

        // Zaten iletişime geçilmişse tekrar göndermekten sakın
        if ($customer->status === 'contacted') {
            $this->answerCallbackQuery($callbackQueryId, 'ℹ️ Bu müşteriye zaten ulaşıldı.');
            return response()->json(['ok' => true]);
        }

        // SMS mesajını oluştur
        $productName = $customer->product?->name ?? 'ürünümüz';
        $productUrl = $customer->product
            ? route('products.show', $customer->product->slug)
            : url('/patenli-ayakkabilar');

        $smsMessage = "Merhaba, ilgilendiğiniz {$productName} hakkında bilgi vermek için ulaşıyoruz. İncelemek ve sipariş vermek için tıklayın: {$productUrl}";

        // WaMessage SMS ile gönder
        try {
            $vatanService = app(VatanSmsService::class);
            $result = $vatanService->send($customer->phone, $smsMessage, 'turkce', 'bilgi');

            if ($result) {
                $customer->update(['status' => 'contacted']);

                $this->answerCallbackQuery($callbackQueryId, '✅ SMS başarıyla gönderildi!');

                // Telegram mesajını güncelle — butonu kaldır, onay notu ekle
                if ($chatId && $messageId) {
                    $originalText = $callbackQuery['message']['text'] ?? '';
                    $updatedText = $originalText . "\n\n✅ <b>SMS GÖNDERİLDİ</b> (" . now()->format('d.m.Y H:i') . ')';

                    $this->editMessageText($chatId, $messageId, $updatedText);
                }
            } else {
                $err = $vatanService->getLastError() ?: 'API hatası';
                $this->answerCallbackQuery($callbackQueryId, '❌ SMS gönderilemedi: ' . mb_substr($err, 0, 150));
            }
        } catch (\Throwable $e) {
            Log::error('Telegram webhook SMS gönderim hatası: ' . $e->getMessage());
            $this->answerCallbackQuery($callbackQueryId, '❌ Hata: ' . mb_substr($e->getMessage(), 0, 100));
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Telegram answerCallbackQuery — kullanıcıya toast mesajı gösterir.
     */
    private function answerCallbackQuery(string $callbackQueryId, string $text): void
    {
        $token = Setting::where('key', 'telegram_bot_token')->value('value');
        if (!$token) return;

        Http::timeout(5)->post("https://api.telegram.org/bot{$token}/answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => true,
        ]);
    }

    /**
     * Telegram editMessageText — mesajı günceller ve inline keyboard'u kaldırır.
     */
    private function editMessageText(int|string $chatId, int $messageId, string $text): void
    {
        $token = Setting::where('key', 'telegram_bot_token')->value('value');
        if (!$token) return;

        Http::timeout(5)->post("https://api.telegram.org/bot{$token}/editMessageText", [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Telegram editMessageTextAndMarkup — mesajı günceller ve belirtilen reply_markup'ı ayarlar.
     */
    private function editMessageTextAndMarkup(int|string $chatId, int $messageId, string $text, ?array $replyMarkup = null): void
    {
        $token = Setting::where('key', 'telegram_bot_token')->value('value');
        if (!$token) return;

        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = $replyMarkup;
        }

        Http::timeout(5)->asJson()->post("https://api.telegram.org/bot{$token}/editMessageText", $payload);
    }
}
