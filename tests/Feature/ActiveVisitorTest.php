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
}

