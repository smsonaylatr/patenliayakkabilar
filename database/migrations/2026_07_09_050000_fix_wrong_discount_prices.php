<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hatalı indirim fiyatlarını düzelt.
     * discount_price >= price olan ürünlerde fiyatları swap et.
     */
    public function up(): void
    {
        // Ürün tablosunda: discount_price >= price ise → fiyatları swap et
        // Büyük olan price olmalı, küçük olan discount_price olmalı
        DB::statement('
            UPDATE products
            SET price = discount_price,
                discount_price = price
            WHERE discount_price IS NOT NULL
              AND discount_price >= price
        ');
    }

    /**
     * Reverse: aynı swap tekrar yapılır
     */
    public function down(): void
    {
        DB::statement('
            UPDATE products
            SET price = discount_price,
                discount_price = price
            WHERE discount_price IS NOT NULL
              AND discount_price >= price
        ');
    }
};
