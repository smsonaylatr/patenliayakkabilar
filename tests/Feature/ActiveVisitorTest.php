<?php

namespace Tests\Feature;

use App\Models\ActiveVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveVisitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_heartbeat_api_creates_active_visitor_record(): void
    {
        $payload = [
            'visitor_token' => 'pa_vt_test_12345',
            'url' => 'https://patenliayakkabilar.com/patenli-ayakkabilar',
            'path' => '/patenli-ayakkabilar',
            'title' => 'Tüm Patenli Ayakkabılar',
            'referrer' => 'https://google.com',
            'screen' => '390x844',
            'action' => 'view',
        ];

        $response = $this->postJson('/api/presence/heartbeat', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
            ]);

        $this->assertDatabaseHas('active_visitors', [
            'visitor_token' => 'pa_vt_test_12345',
            'current_path' => '/patenli-ayakkabilar',
            'current_title' => 'Tüm Patenli Ayakkabılar',
            'is_online' => true,
        ]);

        $visitor = ActiveVisitor::where('visitor_token', 'pa_vt_test_12345')->first();
        $this->assertNotNull($visitor);
        $this->assertTrue($visitor->is_currently_online);
        $this->assertCount(1, $visitor->journey_trail);
    }

    public function test_behavior_analyzer_detects_checkout_hesitation(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_checkout_test',
            'current_url' => 'https://patenliayakkabilar.com/checkout',
            'current_path' => '/checkout',
            'current_title' => 'Ödeme Sayfası',
            'cart_items_count' => 2,
            'cart_total' => 1890.00,
            'first_seen_at' => now()->subMinutes(3),
            'last_heartbeat_at' => now(),
        ]);

        $payload = [
            'visitor_token' => 'pa_vt_checkout_test',
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme Sayfası',
            'action' => 'heartbeat',
        ];

        $response = $this->postJson('/api/presence/heartbeat', $payload);

        $response->assertStatus(200);

        $visitor->refresh();
        $this->assertEquals('hesitating', $visitor->intent_level);
        $this->assertGreaterThanOrEqual(80, $visitor->intent_score);
        $this->assertStringContainsString('Ödeme adımında bekliyor', $visitor->behavior_insight);
        $this->assertEquals('checkout_coupon', $visitor->recommended_strategy['key']);
    }

    public function test_queued_redirect_command_is_delivered_and_archived(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_redirect_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        // Admin kuyruğa yönlendirme ekler
        $visitor->queueRedirect('/checkout', 'Sizi sepetinize aktarıyoruz...', 3);

        $this->assertNotNull($visitor->pending_command);
        $this->assertEquals('redirect', $visitor->pending_command['action']);
        $this->assertEquals('/checkout', $visitor->pending_command['target_url']);

        // İstemci heartbeat gönderir
        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_redirect_test',
            'url' => 'https://patenliayakkabilar.com/',
            'path' => '/',
            'title' => 'Ana Sayfa',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'command' => [
                    'action' => 'redirect',
                    'target_url' => '/checkout',
                    'countdown' => 3,
                ],
            ]);

        // Komut pending'den temizlenip arşive geçmeli
        $visitor->refresh();
        $this->assertNull($visitor->pending_command);
        $this->assertNotNull($visitor->last_command_executed);
        $this->assertEquals('/checkout', $visitor->last_command_executed['target_url']);
    }

    public function test_queued_silent_redirect_command_is_delivered(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_silent_redirect_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        // Bildirimsiz sessiz yönlendirme kuyrukla
        $visitor->queueRedirect('/kampanya', null, 0, false);

        $this->assertNotNull($visitor->pending_command);
        $this->assertEquals('redirect', $visitor->pending_command['action']);
        $this->assertEquals('/kampanya', $visitor->pending_command['target_url']);
        $this->assertFalse($visitor->pending_command['show_notice']);
        $this->assertNull($visitor->pending_command['message']);
        $this->assertEquals(0, $visitor->pending_command['countdown']);

        // İstemci heartbeat gönderir
        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_silent_redirect_test',
            'url' => 'https://patenliayakkabilar.com/',
            'path' => '/',
            'title' => 'Ana Sayfa',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'command' => [
                    'action' => 'redirect',
                    'target_url' => '/kampanya',
                    'countdown' => 0,
                    'show_notice' => false,
                ],
            ]);
    }

    public function test_queued_offer_command_is_delivered(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_offer_test',
            'current_url' => 'https://patenliayakkabilar.com/urun/paten',
            'current_path' => '/urun/paten',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $visitor->queueOffer('Özel Fırsat!', 'Sepetinize özel %10 indirim:', 'CANLI10', 'Hemen Kullan', '/checkout');

        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_offer_test',
            'url' => 'https://patenliayakkabilar.com/urun/paten',
            'path' => '/urun/paten',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'command' => [
                    'action' => 'offer',
                    'title' => 'Özel Fırsat!',
                    'coupon_code' => 'CANLI10',
                ],
            ]);
    }

    public function test_behavior_analyzer_detects_size_confusion_and_suggests_whatsapp_help(): void
    {
        $payload = [
            'visitor_token' => 'pa_vt_size_test',
            'url' => 'https://patenliayakkabilar.com/urun/pembe-isikli-paten',
            'path' => '/urun/pembe-isikli-paten',
            'title' => 'Pembe Işıklı Paten',
            'action' => 'size_click',
            'action_detail' => 'Beden seçildi: 34',
        ];

        $response = $this->postJson('/api/presence/heartbeat', $payload);

        $response->assertStatus(200);

        $visitor = ActiveVisitor::where('visitor_token', 'pa_vt_size_test')->first();
        $this->assertNotNull($visitor);
        $this->assertEquals('hesitating', $visitor->intent_level);
        $this->assertEquals('size_help', $visitor->recommended_strategy['key']);
        $this->assertStringContainsString('beden/numara seçeneklerini inceliyor', $visitor->behavior_insight);
    }

    public function test_blocked_visitor_receives_blocked_status_and_command(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_blocked_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'is_blocked' => true,
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_blocked_test',
            'url' => 'https://patenliayakkabilar.com/',
            'path' => '/',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'blocked',
                'command' => [
                    'action' => 'blocked',
                ],
            ]);
    }

    public function test_unblock_visitor_restores_access_and_queues_reload(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_unblock_test',
            'ip_address' => '192.168.1.100',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'is_blocked' => true,
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        // Unblock
        $visitor->unblockVisitor();
        $visitor->refresh();

        $this->assertFalse($visitor->is_blocked);
        $this->assertNotNull($visitor->pending_command);
        $this->assertEquals('reload', $visitor->pending_command['action']);

        // Heartbeat should now succeed and return status ok with reload command
        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_unblock_test',
            'url' => 'https://patenliayakkabilar.com/',
            'path' => '/',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'command' => [
                    'action' => 'reload',
                ],
            ]);
    }

    public function test_queued_voice_command_is_delivered(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_voice_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $visitor->queueVoiceMessage(
            'Patenli Ayakkabılar mağazamıza hoş geldiniz!',
            '🎙️ Canlı Anons',
            'chime_and_speech',
            'SESLI10',
            'Fırsatları Gör',
            '/patenli-ayakkabilar'
        );

        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_voice_test',
            'url' => 'https://patenliayakkabilar.com/',
            'path' => '/',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'command' => [
                    'action' => 'voice',
                    'title' => '🎙️ Canlı Anons',
                    'sound_type' => 'chime_and_speech',
                    'coupon_code' => 'SESLI10',
                    'action_button' => 'Fırsatları Gör',
                    'action_url' => '/patenli-ayakkabilar',
                ],
            ]);
    }

    public function test_page_info_resolution_and_clean_titles(): void
    {
        // 1. Ana Sayfa (Site başlığı ile gelse bile Ana Sayfa olarak çözümlenmeli)
        $homeInfo = ActiveVisitor::resolvePageInfo('/', 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri');
        $this->assertEquals('Ana Sayfa', $homeInfo['title']);
        $this->assertEquals('Ana Sayfa', $homeInfo['badge']);
        $this->assertEquals('🏠', $homeInfo['icon']);

        // 2. Ödeme Sayfası (Boş veya genel başlık ile gelse bile Ödeme olarak çözümlenmeli)
        $checkoutInfo = ActiveVisitor::resolvePageInfo('/checkout', 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri');
        $this->assertEquals('Ödeme Sayfası (Checkout)', $checkoutInfo['title']);
        $this->assertEquals('Ödeme Ekranı', $checkoutInfo['badge']);
        $this->assertEquals('🛒', $checkoutInfo['icon']);

        // 3. Başlık Temizleme (Marka ekleri temizlenmeli)
        $cleanTitle = ActiveVisitor::cleanTitle('Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı - Patenli Ayakkabılar');
        $this->assertEquals('Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı', $cleanTitle);

        // 4. Kargo & Sipariş Takibi
        $trackingInfo = ActiveVisitor::resolvePageInfo('/siparis-takip');
        $this->assertEquals('Sipariş & Kargo Takibi', $trackingInfo['title']);
        $this->assertEquals('📦', $trackingInfo['icon']);
    }

    public function test_journey_modal_renders_safely_with_array_variant_colors_and_sizes(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_modal_render_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
            'cart_items_count' => 1,
            'cart_total' => 1499.00,
            'cart_summary' => [
                [
                    'product_name' => 'Kick Speed Pro',
                    'size' => ['34', '35'], // array variant size
                    'color' => ['Beyaz', 'Mavi'], // array variant color
                    'quantity' => 1,
                    'price' => 1499.00,
                ],
            ],
            'journey_trail' => [
                [
                    'path' => '/',
                    'title' => 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri',
                    'time' => '03:00:00',
                ],
            ],
        ]);

        $view = view('filament.pages.partials.visitor-journey-modal', ['record' => $visitor])->render();
        $this->assertStringContainsString('Beden: 34, 35', $view);
        $this->assertStringContainsString('Renk: Beyaz, Mavi', $view);
        $this->assertStringContainsString('Ana Sayfa', $view);
    }

    public function test_active_visitors_table_partial_renders_strategy_and_actions_underneath_log(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_table_layout_test',
            'ip_address' => '184.23.188.224',
            'device_type' => 'desktop',
            'browser' => 'Chrome',
            'first_seen_at' => now()->subMinutes(15),
            'last_heartbeat_at' => now(),
            'current_url' => 'https://patenliayakkabilar.com/patenli-ayakkabilar',
            'current_path' => '/patenli-ayakkabilar',
            'current_title' => 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı',
            'intent_score' => 65,
            'intent_level' => 'hesitating',
            'behavior_insight' => 'Beden tablosunu inceledi, tereddüt aşamasında.',
            'cart_items_count' => 0,
            'cart_total' => 0,
            'recommended_strategy' => [
                'title' => 'Hızlı Kargo & Güven Bildirimi',
                'action_type' => 'offer',
                'suggested_message' => 'Saat 16:00ya kadar sipariş verin bugün kargoda!',
                'suggested_coupon' => 'CANLI10',
            ],
        ]);

        $page = new \App\Filament\Pages\ActiveVisitors();
        $table = $page->table(new \Filament\Tables\Table($page));

        $this->assertNotEmpty($visitor->recommended_strategy);
        $this->assertEquals('Hızlı Kargo & Güven Bildirimi', $visitor->recommended_strategy['title']);

        $view = view('filament.pages.partials.active-visitors-table', [
            'records' => collect([$visitor]),
            'table' => $table,
        ])->render();

        // 1. Ziyaretçi bilgileri üst kısımda olmalı
        $this->assertStringContainsString('184.23.188.224', $view);
        $this->assertStringContainsString('Chrome', $view);
        $this->assertStringContainsString('Teredd', $view);
        $this->assertStringContainsString('Beden tablosunu inceledi', $view);

        // 2. Alt satırda (visitor-log-footer) strateji yer almalı
        $this->assertStringContainsString('visitor-log-footer', $view);
        $this->assertStringContainsString('Önerilen Strateji:', $view);
        $this->assertStringContainsString('Hızlı Kargo', $view);
        $this->assertStringContainsString('CANLI10', $view);

        // 3. Alt satırda hızlı aksiyon butonları yer almalı
        $this->assertStringContainsString('Strateji', $view);
        $this->assertStringContainsString('Yönlendir', $view);
        $this->assertStringContainsString('İncele', $view);
    }

    public function test_visitor_identity_updated_live_when_typing_name_and_contact(): void
    {
        $token = 'pa_vt_live_typing_' . uniqid();
        
        // 1. İsim yazıldığında anında misafir adı güncellenmeli
        $response1 = $this->postJson('/api/presence/identify', [
            'visitor_token' => $token,
            'guest_name' => 'Elif Demir',
        ]);

        $response1->assertOk();
        $response1->assertJson([
            'status' => 'ok',
            'display_name' => 'Elif Demir',
            'is_identified' => false,
        ]);

        $visitor = ActiveVisitor::where('visitor_token', $token)->first();
        $this->assertNotNull($visitor);
        $this->assertEquals('Elif Demir', $visitor->guest_name);
        $this->assertEquals('Elif Demir', $visitor->display_name);

        // 2. Numara ve e-posta girildiğinde müşteri tanımlanmalı
        $testEmail = 'elif_' . uniqid() . '@example.com';
        $response2 = $this->postJson('/api/presence/identify', [
            'visitor_token' => $token,
            'guest_name' => 'Elif Demir',
            'guest_email' => $testEmail,
            'guest_phone' => '05321234567',
        ]);

        $response2->assertOk();
        $response2->assertJson([
            'status' => 'ok',
            'display_name' => 'Elif Demir',
            'is_identified' => true,
        ]);

        $visitor->refresh();
        $this->assertTrue((bool)$visitor->is_identified);
        $this->assertNotNull($visitor->user_id);
        $this->assertEquals($testEmail, $visitor->user->email);

        // Temizlik
        if ($visitor->user) {
            $visitor->user->delete();
        }
        $visitor->delete();
    }

    public function test_active_visitors_admin_page_renders_successfully(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_' . uniqid() . '@patenli.com',
            'role' => 'admin',
        ]);

        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_admin_render_test',
            'ip_address' => '85.105.12.34',
            'device_type' => 'desktop',
            'browser' => 'Chrome',
            'first_seen_at' => now()->subMinutes(10),
            'last_heartbeat_at' => now(),
            'current_url' => 'https://patenliayakkabilar.com/patenli-ayakkabilar',
            'current_path' => '/patenli-ayakkabilar',
            'current_title' => 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı',
            'intent_score' => 85,
            'intent_level' => 'hot',
            'behavior_insight' => 'Ödeme adımına hazırlanıyor.',
            'cart_items_count' => 1,
            'cart_total' => 1890,
            'recommended_strategy' => [
                'title' => 'Sepet İndirimi',
                'action_type' => 'offer',
                'suggested_message' => 'Hemen tamamla %10 kazan',
                'suggested_coupon' => 'SEPET10',
            ],
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\ActiveVisitors::class)
            ->assertSuccessful()
            ->assertSee('85.105.12.34')
            ->assertSee('Sepet İndirimi')
            ->assertSee('SEPET10');
    }

    public function test_active_visitors_admin_page_unblocks_visitor_via_livewire(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_' . uniqid() . '@patenli.com',
            'role' => 'admin',
        ]);

        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_livewire_unblock_' . uniqid(),
            'ip_address' => '178.240.10.20',
            'is_blocked' => true,
            'first_seen_at' => now()->subMinutes(10),
            'last_heartbeat_at' => now(),
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\ActiveVisitors::class)
            ->assertSuccessful()
            ->assertSee('ENGELLENDİ')
            ->call('unblockVisitorById', $visitor->id)
            ->assertNotified();

        $visitor->refresh();
        $this->assertFalse($visitor->is_blocked);
    }

    public function test_journey_modal_renders_product_image_in_timeline_and_cart(): void
    {
        $product = \App\Models\Product::create([
            'name' => 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı',
            'slug' => 'kick-speed-sky-roll-isikli-patenli-spor-ayakkabi',
            'price' => 1999.00,
            'discount_price' => 1799.00,
            'status' => true,
        ]);

        \App\Models\ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/kick-speed-sky-roll.webp',
            'sort_order' => 1,
        ]);

        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_img_test_' . uniqid(),
            'current_url' => 'https://patenliayakkabilar.com/urun/kick-speed-sky-roll-isikli-patenli-spor-ayakkabi',
            'current_path' => '/urun/kick-speed-sky-roll-isikli-patenli-spor-ayakkabi',
            'current_title' => 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
            'cart_items_count' => 1,
            'cart_total' => 1799.00,
            'cart_summary' => [
                [
                    'product_name' => 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı',
                    'size' => '36',
                    'color' => 'Mavi',
                    'quantity' => 1,
                    'price' => 1799.00,
                ],
            ],
            'journey_trail' => [
                [
                    'path' => '/urun/kick-speed-sky-roll-isikli-patenli-spor-ayakkabi',
                    'title' => 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı',
                    'time' => '03:44:58',
                    'detail' => 'Sekmeye geri dönüldü',
                ],
            ],
        ]);

        $view = view('filament.pages.partials.visitor-journey-modal', ['record' => $visitor])->render();

        $this->assertStringContainsString('kick-speed-sky-roll.webp', $view);
        $this->assertStringContainsString('Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı', $view);
        $this->assertStringContainsString('1,799.00 ₺', $view);
        $this->assertStringContainsString('sekmeye geri dönüldü', $view);
    }

    public function test_voice_message_command_includes_audio_url_when_provided(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_voice_audio_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $visitor->queueVoiceMessage(
            'Özel ses kaydınız iletilmiştir!',
            '🎙️ Canlı Mağaza Anonsu',
            'chime_and_speech',
            'SES10',
            'İncele',
            '/patenli-ayakkabilar',
            'https://patenliayakkabilar.com/storage/voice-announcements/test_anons.mp3'
        );

        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_voice_audio_test',
            'url' => 'https://patenliayakkabilar.com/',
            'path' => '/',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'command' => [
                    'action' => 'voice',
                    'title' => '🎙️ Canlı Mağaza Anonsu',
                    'sound_type' => 'chime_and_speech',
                    'coupon_code' => 'SES10',
                    'audio_url' => 'https://patenliayakkabilar.com/storage/voice-announcements/test_anons.mp3',
                ],
            ]);
    }

    public function test_canned_voice_message_model_and_audio_helpers(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $canned = \App\Models\CannedVoiceMessage::create([
            'title' => 'Test Özel Anons',
            'message' => 'Test anons mesajı',
            'audio_path' => 'voice-announcements/demo.mp3',
            'coupon_code' => 'TEST10',
            'action_button' => 'Hemen Al',
            'action_url' => '/checkout',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertNotNull($canned->audio_url);
        $this->assertStringContainsString('voice-announcements/demo.mp3', $canned->audio_url);

        \Illuminate\Support\Facades\Storage::disk('public')->put('voice-announcements/demo.mp3', 'dummy-audio');
        $this->assertTrue($canned->hasAudio());

        $activeList = \App\Models\CannedVoiceMessage::active()->ordered()->get();
        $this->assertTrue($activeList->contains('id', $canned->id));
    }

    public function test_canned_voice_messages_admin_resource_renders_successfully(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_canned_' . uniqid() . '@patenli.com',
            'role' => 'admin',
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\CannedVoiceMessages\Pages\ListCannedVoiceMessages::class)
            ->assertSuccessful()
            ->assertSee('Sesli Anons');
    }

    public function test_active_visitor_source_info_detects_google_ads_instagram_direct_and_referral(): void
    {
        // 1. Google Ads (cpc)
        $vAds = new ActiveVisitor([
            'referrer' => 'https://www.google.com/',
            'referrer_host' => 'www.google.com',
            'utm_source' => 'google',
            'utm_campaign' => 'cpc_kampanyasi',
        ]);
        $this->assertEquals('Google Ads', $vAds->source_info['name']);
        $this->assertEquals('paid', $vAds->source_info['type']);

        // 2. Instagram
        $vInsta = new ActiveVisitor([
            'referrer' => 'https://l.instagram.com/',
            'referrer_host' => 'l.instagram.com',
        ]);
        $this->assertEquals('Instagram', $vInsta->source_info['name']);
        $this->assertEquals('social', $vInsta->source_info['type']);

        // 3. Google Organik
        $vGoogle = new ActiveVisitor([
            'referrer' => 'https://www.google.com.tr/',
            'referrer_host' => 'www.google.com.tr',
        ]);
        $this->assertEquals('Google Arama', $vGoogle->source_info['name']);
        $this->assertEquals('search', $vGoogle->source_info['type']);

        // 4. Doğrudan Giriş
        $vDirect = new ActiveVisitor([
            'referrer' => null,
            'referrer_host' => null,
            'utm_source' => null,
        ]);
        $this->assertEquals('Doğrudan Giriş', $vDirect->source_info['name']);
        $this->assertEquals('direct', $vDirect->source_info['type']);
    }

    public function test_presence_identify_endpoint_updates_guest_details(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_ident_test_' . uniqid(),
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
            'current_url' => 'https://patenliayakkabilar.com/odeme',
            'current_path' => '/odeme',
        ]);

        $response = $this->postJson('/api/presence/identify', [
            'visitor_token' => $visitor->visitor_token,
            'guest_name' => 'Ahmet Yılmaz',
            'guest_email' => 'ahmet@example.com',
            'guest_phone' => '05551234567',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'display_name' => 'Ahmet Yılmaz',
                'is_identified' => true,
            ]);

        $visitor->refresh();
        $this->assertEquals('Ahmet Yılmaz', $visitor->guest_name);
        $this->assertEquals('ahmet@example.com', $visitor->guest_email);
        $this->assertEquals('05551234567', $visitor->guest_phone);
        $this->assertTrue((bool)$visitor->is_identified);
    }

    public function test_active_visitors_table_and_journey_modal_render_traffic_source_badge(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_source_ui_' . uniqid(),
            'current_url' => 'https://patenliayakkabilar.com/odeme',
            'current_path' => '/odeme',
            'current_title' => 'Ödeme Sayfası',
            'referrer' => 'https://l.instagram.com/',
            'referrer_host' => 'l.instagram.com',
            'utm_source' => 'instagram',
            'utm_campaign' => 'hikaye_indirimi',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $viewModal = view('filament.pages.partials.visitor-journey-modal', ['record' => $visitor])->render();
        $this->assertStringContainsString('Instagram', $viewModal);
        $this->assertStringContainsString('hikaye_indirimi', $viewModal);

        $viewCard = view('filament.pages.partials.active-visitors-table', [
            'records' => [$visitor],
            'selectedVisitorId' => null,
            'presetCoupons' => [],
            'quickUrls' => [],
        ])->render();
        $this->assertStringContainsString('Instagram', $viewCard);
        $this->assertStringContainsString('hikaye_indirimi', $viewCard);
    }

    public function test_category_routes_redirect_variations_smartly_without_404(): void
    {
        \App\Models\Category::create(['name' => 'Kız Çocuk', 'slug' => 'kiz-cocuk', 'status' => true]);
        \App\Models\Category::create(['name' => 'Erkek Çocuk', 'slug' => 'erkek-cocuk', 'status' => true]);

        // 1. Doğru kategori URL'si (200 OK)
        $resp = $this->get('/kategori/kiz-cocuk');
        $resp->assertStatus(200);

        // 2. Yanlış ekli yönlendirme linki (/kategori/kiz-cocuk-patenli-ayakkabi -> 301 /kategori/kiz-cocuk)
        $resp2 = $this->get('/kategori/kiz-cocuk-patenli-ayakkabi');
        $resp2->assertStatus(301);
        $resp2->assertRedirect(route('category.show', ['slug' => 'kiz-cocuk']));

        // 3. Erkek çocuk yanlış yönlendirme linki -> 301 /kategori/erkek-cocuk
        $resp3 = $this->get('/kategori/erkek-cocuk-patenli-ayakkabi');
        $resp3->assertStatus(301);
        $resp3->assertRedirect(route('category.show', ['slug' => 'erkek-cocuk']));

        // 4. Alt tireli giriş -> 301 /kategori/kiz-cocuk
        $resp4 = $this->get('/kategori/kiz_cocuk');
        $resp4->assertStatus(301);
        $resp4->assertRedirect(route('category.show', ['slug' => 'kiz-cocuk']));

        // 5. Olmayan rastgele kategori -> 404 DEĞİL, 301 /patenli-ayakkabilar kataloğuna yönlenmeli
        $resp5 = $this->get('/kategori/olmayan-kategori-xyz-123');
        $resp5->assertStatus(301);
        $resp5->assertRedirect(route('products.index'));
    }

    public function test_active_visitors_page_provides_verified_target_url_options(): void
    {
        \App\Models\Category::create(['name' => 'Kız Çocuk', 'slug' => 'kiz-cocuk', 'status' => true]);
        \App\Models\Category::create(['name' => 'Erkek Çocuk', 'slug' => 'erkek-cocuk', 'status' => true]);

        $options = \App\Filament\Pages\ActiveVisitors::getTargetUrlOptions();
        $flatOptions = [];
        foreach ($options as $key => $val) {
            if (is_array($val)) {
                $flatOptions = array_merge($flatOptions, $val);
            } else {
                $flatOptions[$key] = $val;
            }
        }

        $this->assertArrayHasKey('/', $flatOptions);
        $this->assertArrayHasKey('/patenli-ayakkabilar', $flatOptions);
        $this->assertArrayHasKey('/checkout', $flatOptions);
        $this->assertArrayHasKey('/kategori/kiz-cocuk', $flatOptions);
        $this->assertArrayHasKey('/kategori/erkek-cocuk', $flatOptions);

        // Hatalı/geçersiz linkler kesinlikle olmamalı
        $this->assertArrayNotHasKey('/kategori/kiz-cocuk-patenli-ayakkabi', $flatOptions);
        $this->assertArrayNotHasKey('/kategori/erkek-cocuk-patenli-ayakkabi', $flatOptions);
    }

    public function test_voice_message_defaults_to_speech_only_mode(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_speech_only_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $visitor->queueVoiceMessage('Yalnızca ses çalınacak.');

        $this->assertEquals('speech_only', $visitor->pending_command['sound_type']);
    }

    public function test_voice_message_supports_optional_visual_card(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_optional_card_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        // Kart kapalı (showCard = false)
        $visitor->queueVoiceMessage(
            'Ses çalıyor.',
            'Başlık',
            'speech_only',
            null,
            null,
            null,
            'https://patenliayakkabilar.com/storage/voice-announcements/test.mp3',
            false
        );

        $this->assertFalse($visitor->pending_command['show_card']);

        $response = $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => 'pa_vt_optional_card_test',
            'url' => 'https://patenliayakkabilar.com/',
            'path' => '/',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'command' => [
                    'action' => 'voice',
                    'show_card' => false,
                ],
            ]);
    }

    public function test_daily_traffic_metric_is_recorded_and_aggregated_correctly(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_traffic_test_1',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'ip_address' => '176.240.10.55',
            'device_type' => 'mobile',
            'referrer' => 'https://instagram.com',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $service = app(\App\Services\TrafficAnalyticsService::class);
        $service->recordHit($visitor, [
            'action' => 'pageview',
            'path' => '/urun/test-paten',
        ]);

        $daily = $service->getMetricsForPeriod('daily');
        $this->assertGreaterThanOrEqual(1, $daily['unique_visitors']);
        $this->assertGreaterThanOrEqual(1, $daily['page_views']);
        $this->assertNotEmpty($daily['analysis']['headline']);
        $this->assertNotEmpty($daily['analysis']['summary']);

        $weekly = $service->getMetricsForPeriod('weekly');
        $this->assertGreaterThanOrEqual(1, $weekly['unique_visitors']);
        $this->assertEquals('Son 7 Gün (Haftalık Sinyal)', $weekly['period_label']);

        $monthly = $service->getMetricsForPeriod('monthly');
        $this->assertGreaterThanOrEqual(1, $monthly['unique_visitors']);
        $this->assertEquals('Son 30 Gün (Aylık Sinyal)', $monthly['period_label']);
    }

    public function test_active_visitors_page_renders_traffic_analytics_and_switches_periods(): void
    {
        $admin = \App\Models\User::factory()->create([
            'email' => 'traffic_admin_' . uniqid() . '@patenliayakkabilar.com',
        ]);

        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_panel_traffic',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\ActiveVisitors::class)
            ->assertSuccessful()
            ->assertSee('Ziyaretçi Trafik Sinyali', false)
            ->assertSee('Bugün (Günlük Sinyal)', false)
            ->call('setTrafficPeriod', 'weekly')
            ->assertSet('trafficPeriod', 'weekly')
            ->assertSee('Son 7 Gün (Haftalık Sinyal)', false)
            ->call('setTrafficPeriod', 'monthly')
            ->assertSet('trafficPeriod', 'monthly')
            ->assertSee('Son 30 Gün (Aylık Sinyal)', false);
    }

    public function test_active_visitor_save_resiliently_handles_missing_columns(): void
    {
        $visitor = new ActiveVisitor();
        $visitor->visitor_token = 'pa_vt_resilient_' . uniqid();
        $visitor->current_url = 'https://patenliayakkabilar.com/';
        $visitor->current_path = '/';
        $visitor->first_seen_at = now();
        $visitor->last_heartbeat_at = now();

        // Olmayan bir kolon ekleyip save çağıralım
        $visitor->non_existent_column_for_test = 'some_value';

        $saved = $visitor->save();
        $this->assertTrue($saved);
        $this->assertDatabaseHas('active_visitors', [
            'visitor_token' => $visitor->visitor_token,
        ]);
    }

    public function test_cockpit_cards_filter_and_calculate_metrics_flawlessly(): void
    {
        $admin = \App\Models\User::factory()->create([
            'email' => 'cards_admin_' . uniqid() . '@patenliayakkabilar.com',
        ]);
        $memberUser = \App\Models\User::factory()->create([
            'name' => 'Canan Kaya',
            'email' => 'canan_' . uniqid() . '@patenliayakkabilar.com',
        ]);

        // 1. Canlı sepetli ziyaretçi
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_card_cart',
            'current_url' => 'https://patenliayakkabilar.com/sepet',
            'current_path' => '/sepet',
            'cart_items_count' => 2,
            'cart_total' => 3598.00,
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        // 2. Sıcak satın alma adayı
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_card_hot',
            'current_url' => 'https://patenliayakkabilar.com/urun/kick-speed',
            'current_path' => '/urun/kick-speed',
            'intent_score' => 90,
            'intent_level' => 'ready_to_buy',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        // 3. Tereddüt yaşayan ziyaretçi
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_card_hesitating',
            'current_url' => 'https://patenliayakkabilar.com/urun/kick-speed',
            'current_path' => '/urun/kick-speed',
            'intent_score' => 65,
            'intent_level' => 'hesitating',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        // 4. Üye girişli ziyaretçi
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_card_member',
            'current_url' => 'https://patenliayakkabilar.com/hesabim',
            'current_path' => '/hesabim',
            'user_id' => $memberUser->id,
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\ActiveVisitors::class)
            ->assertSuccessful()
            ->assertSee('Bekleyen Sepetler')
            ->assertSee('3,598.00 ₺')
            ->assertSee('Sıcak Adaylar')
            ->assertSee('Tereddütte Olanlar')
            ->assertSee('Kullanıcı Segmenti')
            ->call('setCardFilter', 'cart')
            ->assertSet('activeCardFilter', 'cart')
            ->assertSee('Filtre: 🛒 Sepetinde Ürün Olanlar', false)
            ->call('setCardFilter', 'high_intent')
            ->assertSet('activeCardFilter', 'high_intent')
            ->assertSee('Filtre: 🔥 Sıcak Satın Alma Adayları', false)
            ->call('setCardFilter', 'hesitating')
            ->assertSet('activeCardFilter', 'hesitating')
            ->assertSee('Filtre: 🤔 Tereddütte Olanlar', false)
            ->call('setCardFilter', 'members')
            ->assertSet('activeCardFilter', 'members')
            ->assertSee('Filtre: 👤 Üye Girişi Yapanlar', false)
            ->call('setCardFilter', 'members') // Toggle off
            ->assertSet('activeCardFilter', 'all')
            ->assertDontSee('Filtre: 👤 Üye Girişi Yapanlar', false);
    }

    public function test_micro_interactions_and_keystrokes_tracked_in_lowercase_details(): void
    {
        $token = 'pa_vt_micro_test_888';

        // 1. İlk sayfa ziyareti (Checkout)
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme ve Sipariş',
            'action' => 'view',
        ])->assertStatus(200);

        // 2. Kullanıcı isim alanına harfler yazar
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme ve Sipariş',
            'action' => 'typing',
            'action_detail' => 'Ad Soyad: "Ahmet Yılmaz" yazdı',
        ])->assertStatus(200);

        // 3. Kullanıcı kupon kodunu yazar
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme ve Sipariş',
            'action' => 'typing',
            'action_detail' => 'Kupon Kodu: "PATEN10" yazdı',
        ])->assertStatus(200);

        // 4. Kullanıcı il (şehir) seçer
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme ve Sipariş',
            'action' => 'click',
            'action_detail' => 'İl (Şehir): "İstanbul" seçti',
        ])->assertStatus(200);

        // 5. Kullanıcı ilçe seçer
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme ve Sipariş',
            'action' => 'click',
            'action_detail' => 'İlçe: "Kadıköy" seçti',
        ])->assertStatus(200);

        // 6. Kullanıcı kapıda ödeme seçeneğini tıklar
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme ve Sipariş',
            'action' => 'click',
            'action_detail' => 'Ödeme Yöntemi: Kapıda Ödeme seçti',
        ])->assertStatus(200);

        $visitor = ActiveVisitor::where('visitor_token', $token)->first();
        $this->assertNotNull($visitor);

        // Aynı sayfada olduğu için 1 adet step olmalı, ancak içinde 5 mikro hareket olmalı
        $this->assertCount(1, $visitor->journey_trail);
        $step = $visitor->journey_trail[0];

        $this->assertArrayHasKey('interactions', $step);
        $this->assertCount(5, $step['interactions']);

        // Tüm metinler küçük harflerle saklanmalı
        $this->assertEquals('⌨️', $step['interactions'][0]['icon']);
        $this->assertEquals('ad soyad: "ahmet yılmaz" yazdı', $step['interactions'][0]['text']);

        $this->assertEquals('⌨️', $step['interactions'][1]['icon']);
        $this->assertEquals('kupon kodu: "paten10" yazdı', $step['interactions'][1]['text']);

        $this->assertEquals('📍', $step['interactions'][2]['icon']);
        $this->assertEquals('il (şehir): "istanbul" seçti', $step['interactions'][2]['text']);

        $this->assertEquals('📍', $step['interactions'][3]['icon']);
        $this->assertEquals('ilçe: "kadıköy" seçti', $step['interactions'][3]['text']);

        $this->assertEquals('💳', $step['interactions'][4]['icon']);
        $this->assertEquals('ödeme yöntemi: kapıda ödeme seçti', $step['interactions'][4]['text']);

        // Modal blade görünümünde mikro hareketlerin render edildiğini test et
        $view = view('filament.pages.partials.visitor-journey-modal', ['record' => $visitor])->render();
        $this->assertStringContainsString('mikro hareketler & tıklamalar', $view);
        $this->assertStringContainsString('ad soyad: &quot;ahmet yılmaz&quot; yazdı', $view);
        $this->assertStringContainsString('kupon kodu: &quot;paten10&quot; yazdı', $view);
        $this->assertStringContainsString('il (şehir): &quot;istanbul&quot; seçti', $view);
        $this->assertStringContainsString('ilçe: &quot;kadıköy&quot; seçti', $view);
        $this->assertStringContainsString('ödeme yöntemi: kapıda ödeme seçti', $view);
    }

    public function test_journey_partial_endpoint_returns_live_html(): void
    {
        $token = 'pa_vt_partial_test_' . uniqid();
        $visitor = ActiveVisitor::create([
            'visitor_token' => $token,
            'current_url' => 'https://patenliayakkabilar.com/checkout',
            'current_path' => '/checkout',
            'current_title' => 'Ödeme Sayfası',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
            'is_online' => true,
            'guest_name' => 'Canlı Test Kullanıcısı',
            'journey_trail' => [
                [
                    'path' => '/checkout',
                    'title' => 'Ödeme Sayfası',
                    'badge' => 'Ödeme Adımı',
                    'icon' => '💳',
                    'color' => '#10b981',
                    'time' => now()->format('H:i:s'),
                    'interactions' => [
                        [
                            'time' => now()->format('H:i:s'),
                            'type' => 'click',
                            'icon' => '📍',
                            'text' => 'il (şehir): "ankara" seçti',
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->get('/api/presence/visitor/' . $visitor->id . '/journey-partial');

        $response->assertStatus(200);
        $html = $response->getContent();
        $this->assertStringContainsString('visitor-journey-content-' . $visitor->id, $html);
        $this->assertStringContainsString('Canlı Test Kullanıcısı', $html);
        $this->assertStringContainsString('il (şehir): &quot;ankara&quot; seçti', $html);
        $this->assertStringContainsString('CANLI YAYIN AKTİF', $html);
    }

    public function test_microscopic_focus_and_single_letter_tracking(): void
    {
        $token = 'pa_vt_micro_test_' . uniqid();

        // 1. Sayfa açılışı
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme',
            'action' => 'view',
        ])->assertStatus(200);

        // 2. Ad Soyad alanına tıklandı (Focus)
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme',
            'action' => 'focus',
            'action_detail' => 'Ad Soyad Alanına Tıkladı',
        ])->assertStatus(200);

        // 3. Tek harf yazıldı ("A")
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme',
            'action' => 'typing',
            'action_detail' => 'Ad Soyad: "A" Yazdı',
        ])->assertStatus(200);

        // 4. İl seçimine tıklandı (Focus)
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme',
            'action' => 'focus',
            'action_detail' => 'İl (Şehir) Seçimine Tıkladı',
        ])->assertStatus(200);

        // 5. İl seçildi
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme',
            'action' => 'click',
            'action_detail' => 'İl (Şehir): "İzmir" Seçti',
        ])->assertStatus(200);

        // 6. İlçe seçildi
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme',
            'action' => 'click',
            'action_detail' => 'İlçe: "Karşıyaka" Seçti',
        ])->assertStatus(200);

        // 7. Mahalle seçildi
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com/checkout',
            'path' => '/checkout',
            'title' => 'Ödeme',
            'action' => 'click',
            'action_detail' => 'Mahalle: "Bostanlı Mah." Seçti',
        ])->assertStatus(200);

        $visitor = ActiveVisitor::where('visitor_token', $token)->first();
        $this->assertNotNull($visitor);
        $this->assertCount(1, $visitor->journey_trail);
        $step = $visitor->journey_trail[0];

        $interactions = $step['interactions'];
        $this->assertCount(6, $interactions);

        $this->assertEquals('🎯', $interactions[0]['icon']);
        $this->assertEquals('ad soyad alanına tıkladı', $interactions[0]['text']);

        $this->assertEquals('⌨️', $interactions[1]['icon']);
        $this->assertEquals('ad soyad: "a" yazdı', $interactions[1]['text']);

        $this->assertEquals('🎯', $interactions[2]['icon']);
        $this->assertEquals('il (şehir) seçimine tıkladı', $interactions[2]['text']);

        $this->assertEquals('📍', $interactions[3]['icon']);
        $this->assertEquals('il (şehir): "izmir" seçti', $interactions[3]['text']);

        $this->assertEquals('📍', $interactions[4]['icon']);
        $this->assertEquals('ilçe: "karşıyaka" seçti', $interactions[4]['text']);

        $this->assertEquals('📍', $interactions[5]['icon']);
        $this->assertEquals('mahalle: "bostanlı mah." seçti', $interactions[5]['text']);
    }

    public function test_force_redirect_action_has_both_buttons_and_correct_halt_behavior(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_redirect_btn_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $page = new \App\Filament\Pages\ActiveVisitors();
        $table = $page->table(new \Filament\Tables\Table($page));
        $action = $table->getAction('force_redirect');
        $this->assertNotNull($action);
        $action->livewire($page)->record($visitor);

        // 1. Buton etiketlerini doğrula
        $this->assertEquals('Şimdi Yönlendir', $action->getModalSubmitActionLabel());
        $footerActions = $action->getExtraModalFooterActions();
        $this->assertCount(1, $footerActions);
        $this->assertEquals('Şimdi Yönlendir ve Çık', $footerActions['force_redirect_and_close']->getLabel());

        // 2. 'Şimdi Yönlendir' tıklandığında (close => false) pop-up kapanmamalı (Halt fırlatmalı)
        $halted = false;
        try {
            $action->call([
                'record' => $visitor,
                'data' => [
                    'quick_target' => '/checkout',
                    'redirect_mode' => 'silent',
                ],
                'arguments' => ['close' => false],
                'action' => $action,
            ]);
        } catch (\Filament\Support\Exceptions\Halt $e) {
            $halted = true;
        }

        $this->assertTrue($halted, "'Şimdi Yönlendir' butonu tıklandığında pop-up kapanmamalı (Halt fırlatılmalı).");
        $visitor->refresh();
        $this->assertNotNull($visitor->pending_command);
        $this->assertEquals('/checkout', $visitor->pending_command['target_url']);

        // 3. 'Şimdi Yönlendir ve Çık' tıklandığında (close => true) işlem tamamlanıp pop-up kapanmalı (Halt fırlatılmamalı)
        $haltedOnClose = false;
        try {
            $action->call([
                'record' => $visitor,
                'data' => [
                    'quick_target' => '/patenli-ayakkabilar',
                    'redirect_mode' => 'silent',
                ],
                'arguments' => ['close' => true],
                'action' => $action,
            ]);
        } catch (\Filament\Support\Exceptions\Halt $e) {
            $haltedOnClose = true;
        }

        $this->assertFalse($haltedOnClose, "'Şimdi Yönlendir ve Çık' butonu tıklandığında pop-up kapanmalı (Halt fırlatılmamalı).");
        $visitor->refresh();
        $this->assertEquals('/patenli-ayakkabilar', $visitor->pending_command['target_url']);
    }

    public function test_bulk_redirect_action_has_both_buttons_and_correct_halt_behavior(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_bulk_btn_test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
        ]);

        $page = new \App\Filament\Pages\ActiveVisitors();
        $reflection = new \ReflectionMethod($page, 'getHeaderActions');
        $reflection->setAccessible(true);
        $headerActions = $reflection->invoke($page);
        $bulkAction = $headerActions[0];
        $this->assertNotNull($bulkAction);
        $bulkAction->livewire($page);

        // 1. Buton etiketlerini doğrula
        $this->assertEquals('Şimdi Yönlendir', $bulkAction->getModalSubmitActionLabel());
        $footerActions = $bulkAction->getExtraModalFooterActions();
        $this->assertCount(1, $footerActions);
        $this->assertEquals('Şimdi Yönlendir ve Çık', $footerActions['bulk_redirect_and_close']->getLabel());

        // 2. 'Şimdi Yönlendir' tıklandığında pop-up açık kalmalı (Halt)
        $halted = false;
        try {
            $bulkAction->call([
                'data' => [
                    'bulk_target' => '/checkout',
                    'redirect_mode' => 'silent',
                ],
                'arguments' => ['close' => false],
                'action' => $bulkAction,
            ]);
        } catch (\Filament\Support\Exceptions\Halt $e) {
            $halted = true;
        }

        $this->assertTrue($halted, "Toplu yönlendirmede 'Şimdi Yönlendir' pop-up'ı açık tutmalıdır.");

        // 3. 'Şimdi Yönlendir ve Çık' tıklandığında pop-up kapanmalı (normal tamamlanmalı)
        $haltedOnClose = false;
        try {
            $bulkAction->call([
                'data' => [
                    'bulk_target' => '/checkout',
                    'redirect_mode' => 'silent',
                ],
                'arguments' => ['close' => true],
                'action' => $bulkAction,
            ]);
        } catch (\Filament\Support\Exceptions\Halt $e) {
            $haltedOnClose = true;
        }

        $this->assertFalse($haltedOnClose, "Toplu yönlendirmede 'Şimdi Yönlendir ve Çık' pop-up'ı kapatmalıdır.");
    }

    public function test_product_page_clicks_and_interactions_tracked_in_journey_modal(): void
    {
        $token = 'pa_vt_product_clicks_' . uniqid();
        $productPath = '/urun/kick-speed-sky-roll-isikli-patenli-spor-ayakkabi';
        $productTitle = 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı';

        // 1. Ziyaretçi ürün sayfasına girdi (view)
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'view',
        ])->assertOk();

        $visitor = ActiveVisitor::where('visitor_token', $token)->first();
        $this->assertNotNull($visitor);
        $this->assertCount(1, $visitor->journey_trail);

        // 2. Ürün fotoğrafına ve zoom'a tıkladı
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'click',
            'action_detail' => 'küçük fotoğrafa tıkladı: 2. görsel',
        ])->assertOk();

        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'click',
            'action_detail' => 'ürün fotoğrafını büyüttü (zoom)',
        ])->assertOk();

        // 3. Beden menüsünü açtı ve 34 beden seçti
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'size_click',
            'action_detail' => 'beden menüsünü açtı',
        ])->assertOk();

        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'size_click',
            'action_detail' => 'beden: "34 beden" seçti',
        ])->assertOk();

        // 4. Adedi artırdı
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'click',
            'action_detail' => 'adedi artırdı (+)',
        ])->assertOk();

        // 5. Akordeon sekmesini açtı
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'tab',
            'action_detail' => '"kargo & iade" sekmesini açtı',
        ])->assertOk();

        // 6. Sepete ekle butonuna tıkladı
        $this->postJson('/api/presence/heartbeat', [
            'visitor_token' => $token,
            'url' => 'https://patenliayakkabilar.com' . $productPath,
            'path' => $productPath,
            'title' => $productTitle,
            'action' => 'cart_add',
            'action_detail' => '"sepete ekle" butonuna tıkladı',
        ])->assertOk();

        // Doğrulama: Tüm bu hareketler aynı sayfadaki satırın interactions dizisine eklenmeli
        $visitor->refresh();
        $trail = $visitor->journey_trail;
        $this->assertCount(1, $trail, "Tüm tıklamalar mevcut ürün sayfasının tek satırında toplanmalıdır.");

        $interactions = $trail[0]['interactions'];
        $this->assertCount(7, $interactions, "7 mikro tıklama da sırasıyla kaydedilmiş olmalıdır.");

        // İkonların ve metinlerin doğrulanması
        $this->assertEquals('🖼️', $interactions[0]['icon']);
        $this->assertStringContainsString('küçük fotoğrafa tıkladı: 2. görsel', $interactions[0]['text']);

        $this->assertEquals('🖼️', $interactions[1]['icon']);
        $this->assertStringContainsString('ürün fotoğrafını büyüttü (zoom)', $interactions[1]['text']);

        $this->assertEquals('👟', $interactions[2]['icon']);
        $this->assertStringContainsString('beden menüsünü açtı', $interactions[2]['text']);

        $this->assertEquals('👟', $interactions[3]['icon']);
        $this->assertStringContainsString('beden: "34 beden" seçti', $interactions[3]['text']);

        $this->assertEquals('🔢', $interactions[4]['icon']);
        $this->assertStringContainsString('adedi artırdı (+)', $interactions[4]['text']);

        $this->assertEquals('📑', $interactions[5]['icon']);
        $this->assertStringContainsString('"kargo & iade" sekmesini açtı', $interactions[5]['text']);

        $this->assertEquals('🛒', $interactions[6]['icon']);
        $this->assertStringContainsString('"sepete ekle" butonuna tıkladı', $interactions[6]['text']);

        // Journey Modal Render Testi
        $modalHtml = view('filament.pages.partials.visitor-journey-modal', ['record' => $visitor])->render();
        $this->assertStringContainsString('küçük fotoğrafa tıkladı: 2. görsel', $modalHtml);
        $this->assertStringContainsString('ürün fotoğrafını büyüttü (zoom)', $modalHtml);
        $this->assertStringContainsString('beden menüsünü açtı', $modalHtml);
        $this->assertStringContainsString('34 beden', $modalHtml);
        $this->assertStringContainsString('adedi artırdı (+)', $modalHtml);
        $this->assertStringContainsString('kargo &amp; iade', $modalHtml);
        $this->assertStringContainsString('sepete ekle', $modalHtml);
        $this->assertStringContainsString('7 işlem', $modalHtml);
    }

    public function test_view_mode_defaults_to_list_and_can_be_switched_to_grid(): void
    {
        $page = new \App\Filament\Pages\ActiveVisitors();
        $page->mount();

        $this->assertEquals('list', $page->viewMode);

        $page->setViewMode('grid');
        $this->assertEquals('grid', $page->viewMode);
        $this->assertEquals('grid', session('av_view_mode'));

        $page->setViewMode('list');
        $this->assertEquals('list', $page->viewMode);
        $this->assertEquals('list', session('av_view_mode'));
    }

    public function test_active_visitors_table_renders_both_list_and_grid_views(): void
    {
        $visitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_view_mode_' . uniqid(),
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'first_seen_at' => now(),
            'last_heartbeat_at' => now(),
            'guest_name' => 'Görünüm Test Kullanıcısı',
        ]);

        $records = collect([$visitor]);

        // 1. Liste Görünümü
        $listView = view('filament.pages.partials.active-visitors-table', [
            'records' => $records,
            'viewMode' => 'list',
        ])->render();

        $this->assertStringContainsString('av-table-header', $listView);
        $this->assertStringContainsString('av-card', $listView);
        $this->assertStringContainsString('Görünüm Test Kullanıcısı', $listView);

        // 2. Izgara (Grid) Görünümü
        $gridView = view('filament.pages.partials.active-visitors-table', [
            'records' => $records,
            'viewMode' => 'grid',
        ])->render();

        $this->assertStringContainsString('av-grid-container', $gridView);
        $this->assertStringContainsString('av-grid-card', $gridView);
        $this->assertStringContainsString('Görünüm Test Kullanıcısı', $gridView);
    }

    public function test_visitor_view_toggle_component_renders_buttons(): void
    {
        $toggleHtml = view('filament.pages.partials.visitor-view-toggle', [
            'viewMode' => 'list',
        ])->render();

        $this->assertStringContainsString('av-view-toggle-group', $toggleHtml);
        $this->assertStringContainsString('Liste', $toggleHtml);
        $this->assertStringContainsString('Izgara', $toggleHtml);
        $this->assertStringContainsString('is-active', $toggleHtml);
    }

    public function test_canli_ziyaretciler_page_lists_only_online_visitors_and_excludes_departed_ones(): void
    {
        $admin = User::factory()->create();

        // 1. Canlı Ziyaretçi (Sitede aktif)
        $liveVisitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_live_active_999',
            'guest_name' => 'Canlı Misafir MUJ0RC',
            'current_url' => 'https://patenliayakkabilar.com/patenli-ayakkabilar',
            'current_path' => '/patenli-ayakkabilar',
            'current_title' => 'Tüm Patenli Ayakkabı Modelleri',
            'is_online' => true,
            'first_seen_at' => now()->subMinutes(10),
            'last_heartbeat_at' => now(), // Anlık canlı
        ]);

        // 2. Ayrılmış Ziyaretçi (Osman Baba - 2 dakika önce çıkmış)
        $departedVisitor = ActiveVisitor::create([
            'visitor_token' => 'pa_vt_departed_osman',
            'guest_name' => 'Osman Baba',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'is_online' => false,
            'first_seen_at' => now()->subHours(1),
            'last_heartbeat_at' => now()->subMinutes(2), // 2 dk önce ayrılmış
        ]);

        // Canlı Ziyaretçiler sayfası testi
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\ActiveVisitors::class)
            ->assertSuccessful()
            ->assertSet('activeTab', 'live')
            // Canlı olan listelenmeli
            ->assertSee('Canlı Misafir MUJ0RC')
            ->assertSee('CANLI')
            // Ayrılmış olan CANLI listesinde YER ALMAMALI!
            ->assertDontSee('Osman Baba')
            // Sekme düğmeleri ve sayaçları görünmeli
            ->assertSee('Canlı Yayındakiler')
            ->assertSee('Son Ziyaret Edenler');
    }

    public function test_son_ziyaret_edenler_page_and_tab_lists_departed_visitors_and_excludes_online_ones(): void
    {
        $admin = User::factory()->create();

        // 1. Canlı Ziyaretçi
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_live_user_111',
            'guest_name' => 'Canlı Kullanıcı Zeynep',
            'current_url' => 'https://patenliayakkabilar.com/patenli-ayakkabilar',
            'current_path' => '/patenli-ayakkabilar',
            'current_title' => 'Tüm Patenli Ayakkabı Modelleri',
            'is_online' => true,
            'first_seen_at' => now()->subMinutes(5),
            'last_heartbeat_at' => now(),
        ]);

        // 2. Ayrılmış Ziyaretçi (Osman Baba)
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_departed_osman_2',
            'guest_name' => 'Osman Baba',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'current_title' => 'Ana Sayfa',
            'is_online' => false,
            'first_seen_at' => now()->subHours(2),
            'last_heartbeat_at' => now()->subMinutes(5),
        ]);

        // 1. Sekme üzerinden Son Ziyaret Edenler'e geçiş
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\ActiveVisitors::class)
            ->assertSuccessful()
            ->call('setActiveTab', 'recent')
            ->assertSet('activeTab', 'recent')
            ->assertSee('Osman Baba')
            ->assertSee('AYRILDI')
            ->assertDontSee('Canlı Kullanıcı Zeynep');

        // 2. Doğrudan RecentVisitors Filament Sayfası
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Pages\RecentVisitors::class)
            ->assertSuccessful()
            ->assertSet('activeTab', 'recent')
            ->assertSee('Osman Baba')
            ->assertSee('AYRILDI')
            ->assertDontSee('Canlı Kullanıcı Zeynep');
    }

    public function test_navigation_badges_for_active_and_recent_visitors(): void
    {
        // 1 Canlı
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_badge_live',
            'guest_name' => 'Canlı Test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'last_heartbeat_at' => now(),
        ]);

        // 1 Ayrılmış
        ActiveVisitor::create([
            'visitor_token' => 'pa_vt_badge_recent',
            'guest_name' => 'Ayrılan Test',
            'current_url' => 'https://patenliayakkabilar.com/',
            'current_path' => '/',
            'last_heartbeat_at' => now()->subMinutes(10),
        ]);

        $this->assertEquals('1', \App\Filament\Pages\ActiveVisitors::getNavigationBadge());
        $this->assertEquals('1', \App\Filament\Pages\RecentVisitors::getNavigationBadge());
    }
}




