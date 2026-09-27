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
        $this->assertStringContainsString('TEREDDÜTTE', $view);
        $this->assertStringContainsString('Beden tablosunu inceledi', $view);

        // 2. Alt satırda (visitor-log-footer) strateji yer almalı
        $this->assertStringContainsString('visitor-log-footer', $view);
        $this->assertStringContainsString('Önerilen Strateji:', $view);
        $this->assertStringContainsString('Hızlı Kargo', $view);
        $this->assertStringContainsString('CANLI10', $view);

        // 3. Alt satırda hızlı aksiyon butonları yer almalı
        $this->assertStringContainsString('Strateji Uygula', $view);
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
        $this->assertStringContainsString('Sekmeye geri dönüldü', $view);
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

        $this->assertArrayHasKey('/', $options);
        $this->assertArrayHasKey('/patenli-ayakkabilar', $options);
        $this->assertArrayHasKey('/checkout', $options);
        $this->assertArrayHasKey('/kategori/kiz-cocuk', $options);
        $this->assertArrayHasKey('/kategori/erkek-cocuk', $options);

        // Hatalı/geçersiz linkler kesinlikle olmamalı
        $this->assertArrayNotHasKey('/kategori/kiz-cocuk-patenli-ayakkabi', $options);
        $this->assertArrayNotHasKey('/kategori/erkek-cocuk-patenli-ayakkabi', $options);
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
}



