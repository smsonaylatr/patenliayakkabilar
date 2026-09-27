<?php

namespace Tests\Feature;

use App\Http\Middleware\CaptureTrafficSource;
use App\Models\Order;
use App\Services\TrafficSourceDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class OrderTrafficSourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TrafficSourceDetector doğru kaynakları ve cihazları çözümler mi?
     */
    public function test_detector_identifies_google_ads_via_gclid(): void
    {
        $detector = app(TrafficSourceDetector::class);

        $request = Request::create('https://patenliayakkabilar.com/?gclid=test_gclid_123', 'GET');
        $data = $detector->detect($request);

        $this->assertEquals('Google Ads', $data['traffic_source']);
        $this->assertEquals('test_gclid_123', $data['gclid']);
    }

    public function test_detector_identifies_meta_ads_and_instagram(): void
    {
        $detector = app(TrafficSourceDetector::class);

        // Instagram CPC
        $request = Request::create('https://patenliayakkabilar.com/?utm_source=instagram&utm_medium=cpc&utm_campaign=yaz2026', 'GET');
        $data = $detector->detect($request);

        $this->assertEquals('Instagram Ads', $data['traffic_source']);
        $this->assertEquals('yaz2026', $data['utm_campaign']);

        // Instagram Organik referer
        $requestOrganic = Request::create('https://patenliayakkabilar.com/', 'GET', [], [], [], [
            'HTTP_REFERER' => 'https://l.instagram.com/',
        ]);
        $dataOrganic = $detector->detect($requestOrganic);

        $this->assertEquals('Instagram', $dataOrganic['traffic_source']);
    }

    public function test_detector_identifies_google_organic_referer(): void
    {
        $detector = app(TrafficSourceDetector::class);

        $request = Request::create('https://patenliayakkabilar.com/', 'GET', [], [], [], [
            'HTTP_REFERER' => 'https://www.google.com.tr/',
        ]);
        $data = $detector->detect($request);

        $this->assertEquals('Google Organik', $data['traffic_source']);
    }

    public function test_detector_identifies_direct_traffic_and_mobile_device(): void
    {
        $detector = app(TrafficSourceDetector::class);

        $request = Request::create('https://patenliayakkabilar.com/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1',
        ]);
        $data = $detector->detect($request);

        $this->assertEquals('Doğrudan', $data['traffic_source']);
        $this->assertEquals('Mobil', $data['device_type']);
    }

    public function test_middleware_captures_traffic_source_in_session(): void
    {
        $middleware = new CaptureTrafficSource(app(TrafficSourceDetector::class));

        $request = Request::create('https://patenliayakkabilar.com/iletisim?utm_source=tiktok&utm_medium=cpc&utm_campaign=kesfet', 'GET');
        $request->setLaravelSession(session()->driver());

        $response = $middleware->handle($request, function ($req) {
            return response('ok');
        });

        $this->assertEquals('TikTok Ads', session('traffic_source'));
        $this->assertEquals('kesfet', session('traffic_attribution.utm_campaign'));
    }

    public function test_order_creation_stores_traffic_source_and_attribution(): void
    {
        $order = Order::create([
            'order_number' => 'TEST-' . uniqid(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'traffic_source' => 'Google Ads',
            'device_type' => 'Mobil',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'paten_kelimeleri',
            'gclid' => 'google_click_id_999',
            'subtotal' => 1000,
            'shipping_price' => 0,
            'grand_total' => 1000,
            'customer_name' => 'Ahmet Yılmaz',
            'customer_email' => 'ahmet@example.com',
            'customer_phone' => '05551234567',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'traffic_source' => 'Google Ads',
            'device_type' => 'Mobil',
            'utm_campaign' => 'paten_kelimeleri',
            'gclid' => 'google_click_id_999',
        ]);

        // scopeSource çalışıyor mu?
        $count = Order::source('Google Ads')->where('id', $order->id)->count();
        $this->assertEquals(1, $count);

        $order->delete();
    }

    public function test_marketing_links_admin_page_renders_successfully(): void
    {
        \Livewire\Livewire::test(\App\Filament\Pages\MarketingLinks::class)
            ->assertSuccessful();
    }

    public function test_products_admin_page_renders_with_tracking_links_action(): void
    {
        \Livewire\Livewire::test(\App\Filament\Resources\Products\Pages\ListProducts::class)
            ->assertSuccessful()
            ->assertTableActionExists('trackingLinks');
    }

    public function test_order_details_accordion_renders_traffic_source_card_and_link_cleanly(): void
    {
        $order = Order::create([
            'order_number' => 'TR' . rand(100000, 999999),
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'traffic_source' => 'Google Ads',
            'device_type' => 'Mobil',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'sonbahar_indirimi',
            'gclid' => 'Cj0KCQjwlNPVBHCMARIsAPZ5RqIC_test',
            'landing_url' => 'https://patenliayakkabilar.com/?gclid=Cj0KCQjwlNPVBHCMARIsAPZ5RqIC_test',
            'ip_address' => '172.69.250.184',
            'subtotal' => 1999,
            'shipping_price' => 0,
            'grand_total' => 1999,
            'customer_name' => 'Selçuk Yılmaz',
            'customer_email' => 'selcuk@example.com',
            'customer_phone' => '05413247585',
        ]);

        $view = view('filament.orders.order-details-accordion', [
            'getRecord' => fn () => $order,
        ])->render();

        $this->assertStringContainsString('Google Ads Reklamı', $view);
        $this->assertStringContainsString('GELDİĞİ KAYNAK LİNKİ:', $view);
        $this->assertStringContainsString('gclid=Cj0KCQjwlNPVBHCMARIsAPZ5RqIC_test', $view);
        $this->assertStringContainsString('172.69.250.184', $view);
        $this->assertStringContainsString('traffic-url-box', $view);
        $this->assertStringContainsString('traffic-action-btn', $view);

        $order->delete();
    }

    public function test_checkout_create_order_resiliently_ignores_missing_database_columns(): void
    {
        $checkout = new \App\Livewire\Frontend\Checkout();
        $reflector = new \ReflectionClass($checkout);
        $method = $reflector->getMethod('createOrderResiliently');
        $method->setAccessible(true);

        $orderData = [
            'order_number' => 'TR' . rand(100000, 999999),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'subtotal' => 1499,
            'shipping_price' => 0,
            'grand_total' => 1499,
            'customer_name' => 'Test Müşteri',
            'customer_email' => 'test@example.com',
            'customer_phone' => '05551112233',
            // Gerçek tabloda kesinlikle olmayan hayali/eksik sütunlar verelim
            'non_existing_column_foo' => 'bar_baz',
            'another_fake_telemetry_field' => 'test_123',
        ];

        /** @var Order $order */
        $order = $method->invoke($checkout, $orderData);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('Test Müşteri', $order->customer_name);
        $this->assertEquals(1499, $order->grand_total);

        $order->delete();
    }
}
