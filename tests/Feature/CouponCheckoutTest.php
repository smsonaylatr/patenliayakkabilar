<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CouponCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function createTestProductWithVariant(float $price = 1000.00): array
    {
        $product = Product::create([
            'name' => 'Kick Speed Klasik Patenli Ayakkabı',
            'slug' => 'kick-speed-klasik-patenli-ayakkabi-' . uniqid(),
            'price' => $price,
            'discount_price' => null,
            'stock' => 10,
            'status' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Siyah',
            'size' => '34',
            'stock' => 10,
            'price_extra' => 0,
        ]);

        return [$product, $variant];
    }

    public function test_paten10_coupon_can_be_retrieved_or_auto_created(): void
    {
        $coupon = Coupon::firstOrCreate(
            ['code' => 'PATEN10'],
            [
                'type' => 'percentage',
                'value' => 10.00,
                'status' => true,
            ]
        );

        $this->assertNotNull($coupon);
        $this->assertEquals('PATEN10', $coupon->code);
        $this->assertEquals('percentage', $coupon->type);
        $this->assertEquals(10, (float) $coupon->value);
        $this->assertTrue((bool) $coupon->status);
    }

    public function test_api_coupon_apply_endpoint_validates_and_stores_in_session(): void
    {
        $response = $this->postJson('/api/coupon/apply', [
            'code' => 'PATEN10',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'coupon' => [
                    'code' => 'PATEN10',
                    'type' => 'percentage',
                    'value' => 10.0,
                ],
            ]);

        $this->assertEquals('PATEN10', session('applied_coupon_code'));
    }

    public function test_api_coupon_apply_handles_invalid_code(): void
    {
        $response = $this->postJson('/api/coupon/apply', [
            'code' => 'GECERSIZKOD99',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_api_coupon_remove_endpoint_clears_session(): void
    {
        session(['applied_coupon_code' => 'PATEN10']);

        $response = $this->postJson('/api/coupon/remove');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNull(session('applied_coupon_code'));
    }

    public function test_checkout_component_auto_applies_coupon_from_query_parameter(): void
    {
        [$product, $variant] = $this->createTestProductWithVariant(1000);

        $cartService = app(CartService::class);
        $cartService->addItem($product->id, $variant->id, 1);

        Coupon::firstOrCreate(
            ['code' => 'PATEN10'],
            [
                'type' => 'percentage',
                'value' => 10.00,
                'status' => true,
            ]
        );

        // URL üzerinden ?coupon=PATEN10 ile Checkout açıldığında
        $component = Livewire::withQueryParams(['coupon' => 'PATEN10'])
            ->test(\App\Livewire\Frontend\Checkout::class);

        $component->assertSet('coupon_code', 'PATEN10');
        $this->assertNotNull($component->get('applied_coupon'));
        $this->assertGreaterThan(0, $component->get('coupon_discount'));
        $this->assertEquals('PATEN10', session('applied_coupon_code'));
    }

    public function test_checkout_component_auto_applies_coupon_from_session(): void
    {
        [$product, $variant] = $this->createTestProductWithVariant(2000);

        $cartService = app(CartService::class);
        $cartService->addItem($product->id, $variant->id, 1);

        session(['applied_coupon_code' => 'PATEN10']);

        $component = Livewire::test(\App\Livewire\Frontend\Checkout::class);

        $component->assertSet('coupon_code', 'PATEN10');
        $this->assertNotNull($component->get('applied_coupon'));
        $this->assertGreaterThan(0, $component->get('coupon_discount'));
    }

    public function test_checkout_component_applies_coupon_from_livewire_event(): void
    {
        [$product, $variant] = $this->createTestProductWithVariant(1500);

        $cartService = app(CartService::class);
        $cartService->addItem($product->id, $variant->id, 1);

        $component = Livewire::test(\App\Livewire\Frontend\Checkout::class);

        $this->assertNull($component->get('applied_coupon'));

        // Modal'dan Livewire apply-coupon olayı tetiklendiğinde
        $component->dispatch('apply-coupon', code: 'PATEN10');

        $component->assertSet('coupon_code', 'PATEN10');
        $this->assertNotNull($component->get('applied_coupon'));
        $this->assertGreaterThan(0, $component->get('coupon_discount'));
        $this->assertEquals('PATEN10', session('applied_coupon_code'));
    }

    public function test_checkout_component_can_remove_applied_coupon(): void
    {
        [$product, $variant] = $this->createTestProductWithVariant(1000);

        $cartService = app(CartService::class);
        $cartService->addItem($product->id, $variant->id, 1);

        $component = Livewire::withQueryParams(['coupon' => 'PATEN10'])
            ->test(\App\Livewire\Frontend\Checkout::class);

        $this->assertNotNull($component->get('applied_coupon'));

        $component->call('removeCoupon');

        $this->assertNull($component->get('applied_coupon'));
        $component->assertSet('coupon_discount', 0);
        $component->assertSet('coupon_code', '');
        $this->assertNull(session('applied_coupon_code'));
    }
}
