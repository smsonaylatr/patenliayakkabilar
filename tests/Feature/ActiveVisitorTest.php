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
}


