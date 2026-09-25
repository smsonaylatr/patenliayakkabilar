<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Tests\TestCase;

class OrderKickSpeedTest extends TestCase
{
    public function test_order_detects_kick_speed_by_product_brand(): void
    {
        $order = new Order();
        $item = new OrderItem(['product_name' => 'Klasik Paten']);
        $product = new Product(['brand' => 'Kick Speed', 'name' => 'Klasik Paten']);
        $item->setRelation('product', $product);
        $order->setRelation('items', collect([$item]));

        $this->assertTrue($order->hasKickSpeedProducts());
    }

    public function test_order_detects_kick_speed_case_insensitively_and_variations(): void
    {
        $variations = ['KICK SPEED', 'kick speed', 'KickSpeed', 'kickspeed', 'Kick_Speed', 'Kick-Speed'];

        foreach ($variations as $brand) {
            $order = new Order();
            $item = new OrderItem(['product_name' => 'Işıklı Model']);
            $product = new Product(['brand' => $brand, 'name' => 'Işıklı Model']);
            $item->setRelation('product', $product);
            $order->setRelation('items', collect([$item]));

            $this->assertTrue($order->hasKickSpeedProducts(), "Brand '{$brand}' should be recognized as Kick Speed.");
        }
    }

    public function test_order_detects_kick_speed_by_order_item_product_name(): void
    {
        $order = new Order();
        $item = new OrderItem(['product_name' => 'Kick Speed 4 Tekerlekli Paten']);
        $order->setRelation('items', collect([$item]));

        $this->assertTrue($order->hasKickSpeedProducts());
    }

    public function test_order_detects_kick_speed_by_sku(): void
    {
        $order = new Order();
        $item = new OrderItem(['product_name' => 'Spor Paten']);
        $product = new Product(['sku' => 'KS-38-RED']);
        $item->setRelation('product', $product);
        $order->setRelation('items', collect([$item]));

        $this->assertTrue($order->hasKickSpeedProducts());
    }

    public function test_order_returns_false_for_non_kick_speed_products(): void
    {
        $order = new Order();
        $item = new OrderItem(['product_name' => 'Standart Patenli Ayakkabı']);
        $product = new Product(['brand' => 'Patenli Ayakkabılar', 'name' => 'Standart Patenli Ayakkabı', 'sku' => 'PA-01']);
        $item->setRelation('product', $product);
        $order->setRelation('items', collect([$item]));

        $this->assertFalse($order->hasKickSpeedProducts());
    }

    public function test_order_returns_false_when_empty(): void
    {
        $order = new Order();
        $order->setRelation('items', collect([]));

        $this->assertFalse($order->hasKickSpeedProducts());
    }
}
