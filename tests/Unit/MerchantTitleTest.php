<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\GoogleMerchantService;
use Tests\TestCase;

class MerchantTitleTest extends TestCase
{
    private GoogleMerchantService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GoogleMerchantService();
    }

    public function test_cleans_duplicate_kick_speed_when_both_in_brand_and_name(): void
    {
        $product = new Product([
            'name' => 'Kick Speed Pink Glow Işıklı Patenli Spor Ayakkabı',
            'brand' => 'KICK SPEED',
            'gender' => null,
        ]);

        $title = $this->service->buildSeoTitle($product);
        $this->assertEquals('Kick Speed Pink Glow Işıklı Patenli Spor Ayakkabı', $title);
    }

    public function test_cleans_already_doubled_name_in_database(): void
    {
        $product = new Product([
            'name' => 'KICK SPEED Kick Speed Pink Glow Işıklı Patenli Spor Ayakkabı',
            'brand' => 'KICK SPEED',
            'gender' => null,
        ]);

        $title = $this->service->buildSeoTitle($product);
        $this->assertEquals('Kick Speed Pink Glow Işıklı Patenli Spor Ayakkabı', $title);
    }

    public function test_cleans_twingers_duplicate(): void
    {
        $product = new Product([
            'name' => 'TWINGERS Twingers Pink Star Işıklı Patenli Spor Ayakkabı Kız Çocuk',
            'brand' => 'TWINGERS',
            'gender' => 'kiz_cocuk',
        ]);

        $title = $this->service->buildSeoTitle($product);
        $this->assertEquals('Twingers Pink Star Işıklı Patenli Spor Ayakkabı Kız Çocuk', $title);
    }

    public function test_prepends_brand_when_not_in_name(): void
    {
        $product = new Product([
            'name' => 'Pink Glow Işıklı Patenli Spor Ayakkabı',
            'brand' => 'Kick Speed',
            'gender' => null,
        ]);

        $title = $this->service->buildSeoTitle($product);
        $this->assertEquals('Kick Speed Pink Glow Işıklı Patenli Spor Ayakkabı', $title);
    }

    public function test_appends_gender_when_missing(): void
    {
        $product = new Product([
            'name' => 'Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı',
            'brand' => 'Kick Speed',
            'gender' => 'erkek_cocuk',
        ]);

        $title = $this->service->buildSeoTitle($product);
        $this->assertEquals('Kick Speed Sky Roll Işıklı Patenli Spor Ayakkabı Erkek Çocuk', $title);
    }
}
