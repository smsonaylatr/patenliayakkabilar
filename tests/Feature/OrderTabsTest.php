<?php

namespace Tests\Feature;

use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OrderTabsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('orders_tab_counts');
    }

    public function test_cancelled_and_returned_orders_are_only_in_cancelled_returned_tab(): void
    {
        // 1. Geçerli Ödenmiş Sipariş (Kredi Kartı) -> Sadece valid ve all
        $validPaid = Order::create([
            'order_number' => 'ORD-VALID-PAID',
            'customer_name' => 'Havva Elif',
            'customer_email' => 'havva@test.com',
            'customer_phone' => '05551112233',
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        // 2. Geçerli Kapıda Ödeme Sipariş -> Sadece valid ve all
        $validCod = Order::create([
            'order_number' => 'ORD-VALID-COD',
            'customer_name' => 'Gönül Kahveci',
            'customer_email' => 'gonul@test.com',
            'customer_phone' => '05552223344',
            'status' => 'processing',
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        // 3. İptal Edilmiş Kapıda Ödeme Sipariş (Yusuf Ayan vakası) -> SADECE cancelled_returned ve all
        $cancelledCod = Order::create([
            'order_number' => 'ORD-CANCELLED-COD',
            'customer_name' => 'Yusuf Ayan',
            'customer_email' => 'yusuf@test.com',
            'customer_phone' => '05553334455',
            'status' => 'cancelled',
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        // 4. İade Edilmiş Kredi Kartı Sipariş -> SADECE cancelled_returned ve all
        $returnedOrder = Order::create([
            'order_number' => 'ORD-RETURNED',
            'customer_name' => 'Ayşe Demir',
            'customer_email' => 'ayse@test.com',
            'customer_phone' => '05554445566',
            'status' => 'returned',
            'payment_status' => 'refunded',
            'payment_method' => 'credit_card',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        // 5. İadesi Başlatılmış Sipariş -> SADECE cancelled_returned ve all
        $returnStartedOrder = Order::create([
            'order_number' => 'ORD-RETURN-STARTED',
            'customer_name' => 'Mehmet Kaya',
            'customer_email' => 'mehmet@test.com',
            'customer_phone' => '05555556677',
            'status' => 'return_started',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        // 6. Yarım Kalan / Başarısız Sipariş (Ödenmemiş Kredi Kartı) -> Sadece abandoned ve all
        $abandonedOrder = Order::create([
            'order_number' => 'ORD-ABANDONED',
            'customer_name' => 'Ali Veli',
            'customer_email' => 'ali@test.com',
            'customer_phone' => '05556667788',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        // 7. Yarım Kalan ama sonradan İptal Edilmiş Sipariş -> SADECE cancelled_returned ve all
        $abandonedThenCancelled = Order::create([
            'order_number' => 'ORD-ABANDONED-CANCELLED',
            'customer_name' => 'Fatma Can',
            'customer_email' => 'fatma@test.com',
            'customer_phone' => '05557778899',
            'status' => 'cancelled',
            'payment_status' => 'failed',
            'payment_method' => 'credit_card',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        $page = new ListOrders();
        $tabs = $page->getTabs();

        // Query'leri test et
        // 1. Valid tab (Geçerli Siparişler): Sadece 1 ve 2 olmalı (2 adet)
        $validQuery = Order::query();
        $tabs['valid']->modifyQuery($validQuery);
        $validResults = $validQuery->pluck('order_number')->toArray();

        $this->assertContains('ORD-VALID-PAID', $validResults);
        $this->assertContains('ORD-VALID-COD', $validResults);
        $this->assertNotContains('ORD-CANCELLED-COD', $validResults, 'İptal kapıda ödeme siparişi geçerli sekmesinde OLMAMALIDIR');
        $this->assertNotContains('ORD-RETURNED', $validResults, 'İade siparişi geçerli sekmesinde OLMAMALIDIR');
        $this->assertNotContains('ORD-RETURN-STARTED', $validResults, 'İade başlatılmış sipariş geçerli sekmesinde OLMAMALIDIR');
        $this->assertNotContains('ORD-ABANDONED', $validResults);
        $this->assertNotContains('ORD-ABANDONED-CANCELLED', $validResults);
        $this->assertCount(2, $validResults);

        // 2. Abandoned tab (Yarım Kalan): Sadece 6 olmalı (1 adet)
        $abandonedQuery = Order::query();
        $tabs['abandoned']->modifyQuery($abandonedQuery);
        $abandonedResults = $abandonedQuery->pluck('order_number')->toArray();

        $this->assertContains('ORD-ABANDONED', $abandonedResults);
        $this->assertNotContains('ORD-ABANDONED-CANCELLED', $abandonedResults, 'İptal edilmiş sipariş yarım kalan sekmesinde OLMAMALIDIR');
        $this->assertNotContains('ORD-CANCELLED-COD', $abandonedResults);
        $this->assertCount(1, $abandonedResults);

        // 3. Cancelled/Returned tab (İptal / İade): 3, 4, 5, 7 olmalı (4 adet)
        $cancelledQuery = Order::query();
        $tabs['cancelled_returned']->modifyQuery($cancelledQuery);
        $cancelledResults = $cancelledQuery->pluck('order_number')->toArray();

        $this->assertContains('ORD-CANCELLED-COD', $cancelledResults);
        $this->assertContains('ORD-RETURNED', $cancelledResults);
        $this->assertContains('ORD-RETURN-STARTED', $cancelledResults);
        $this->assertContains('ORD-ABANDONED-CANCELLED', $cancelledResults);
        $this->assertNotContains('ORD-VALID-PAID', $cancelledResults);
        $this->assertNotContains('ORD-VALID-COD', $cancelledResults);
        $this->assertNotContains('ORD-ABANDONED', $cancelledResults);
        $this->assertCount(4, $cancelledResults);

        // Badge count'ları kontrol et
        $this->assertEquals(2, $tabs['valid']->getBadge());
        $this->assertEquals(1, $tabs['abandoned']->getBadge());
        $this->assertEquals(4, $tabs['cancelled_returned']->getBadge());
        $this->assertEquals(7, $tabs['all']->getBadge());
    }
}
