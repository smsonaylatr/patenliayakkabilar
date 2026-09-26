<?php

namespace Tests\Unit;

use App\Models\ContactMessage;
use App\Models\Setting;
use App\Observers\ContactMessageObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContactMessageTelegramNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        Setting::updateOrCreate(['key' => 'telegram_active'], ['value' => '1']);
        Setting::updateOrCreate(['key' => 'telegram_bot_token'], ['value' => '123456:TEST_TOKEN']);
        Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['value' => '-100123456789']);
        Setting::updateOrCreate(['key' => 'telegram_contact_active'], ['value' => '1']);
    }

    public function test_telegram_notification_is_sent_when_contact_message_is_created(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 999]], 200),
        ]);

        $message = ContactMessage::create([
            'name' => 'Ahmet Yılmaz',
            'email' => 'ahmet@example.com',
            'subject' => '05321234567',
            'message' => 'Merhaba, 38 numara patenli ayakkabı stoğu ne zaman yenilenecek?',
        ]);

        Http::assertSent(function ($request) use ($message) {
            $data = $request->data();

            $isCorrectUrl = str_contains($request->url(), '123456:TEST_TOKEN/sendMessage');
            $isCorrectChat = ($data['chat_id'] ?? null) === '-100123456789';
            $hasName = str_contains($data['text'] ?? '', 'Ahmet Yılmaz');
            $hasEmail = str_contains($data['text'] ?? '', 'ahmet@example.com');
            $hasPhone = str_contains($data['text'] ?? '', '05321234567');
            $hasBody = str_contains($data['text'] ?? '', '38 numara patenli ayakkabı');
            $hasHtml = ($data['parse_mode'] ?? '') === 'HTML';

            // Inline buttons check
            $keyboard = $data['reply_markup']['inline_keyboard'] ?? [];
            $hasWhatsApp = false;
            $hasReadCallback = false;

            foreach ($keyboard as $row) {
                foreach ($row as $btn) {
                    if (str_contains($btn['url'] ?? '', 'https://wa.me/905321234567')) {
                        $hasWhatsApp = true;
                    }
                    if (($btn['callback_data'] ?? '') === 'contact_read_' . $message->id) {
                        $hasReadCallback = true;
                    }
                }
            }

            return $isCorrectUrl && $isCorrectChat && $hasName && $hasEmail && $hasPhone && $hasBody && $hasHtml && $hasWhatsApp && $hasReadCallback;
        });
    }

    public function test_telegram_notification_is_not_sent_when_telegram_is_disabled(): void
    {
        Http::fake();

        Setting::updateOrCreate(['key' => 'telegram_active'], ['value' => '0']);

        ContactMessage::create([
            'name' => 'Mehmet Demir',
            'email' => 'mehmet@example.com',
            'subject' => 'Bilgi Talebi',
            'message' => 'Kargo süresi kaç gündür?',
        ]);

        Http::assertNothingSent();
    }

    public function test_duplicate_notification_is_prevented_by_cache_dedup(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $message = ContactMessage::create([
            'name' => 'Ayşe Kaya',
            'email' => 'ayse@example.com',
            'subject' => '05439876543',
            'message' => 'Sipariş durumunu öğrenmek istiyorum.',
        ]);

        $this->assertEquals(1, count(Http::recorded()));

        // Call observer manually again for the same message
        $observer = new ContactMessageObserver();
        $observer->created($message);

        // Still only 1 call should have been made
        $this->assertEquals(1, count(Http::recorded()));
    }

    public function test_webhook_controller_marks_contact_message_as_read_on_callback(): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $contactMessage = ContactMessage::create([
            'name' => 'Fatma Şahin',
            'email' => 'fatma@example.com',
            'subject' => '05551112233',
            'message' => 'Ürün değişimi yapabilir miyiz?',
            'is_read' => false,
        ]);

        $this->assertFalse($contactMessage->fresh()->is_read);

        $controller = new \App\Http\Controllers\TelegramWebhookController();
        $request = Request::create('/api/telegram/webhook', 'POST', [
            'callback_query' => [
                'id' => 'cb_query_123',
                'data' => 'contact_read_' . $contactMessage->id,
                'message' => [
                    'message_id' => 777,
                    'chat' => ['id' => -100123456789],
                    'text' => 'Orijinal mesaj',
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                ['text' => '💬 WhatsApp', 'url' => 'https://wa.me/905551112233'],
                                ['text' => '🔍 Panelde Gör', 'url' => 'https://example.com/admin'],
                            ],
                            [
                                ['text' => '✅ Okundu İşaretle', 'callback_data' => 'contact_read_' . $contactMessage->id],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response = $controller->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        // Assert DB was updated
        $this->assertTrue($contactMessage->fresh()->is_read);
    }
}
