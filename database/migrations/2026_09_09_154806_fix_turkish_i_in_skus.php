<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Türkçe İ harfinin SKU'da tire (-) olarak dönüştürülmesi sorununu düzelt.
     * TWİNGERS -> TW-NGERS olmuştu, TWINGERS olmalı.
     */
    public function up(): void
    {
        // product_variants tablosundaki hatalı SKU'ları düzelt
        DB::table('product_variants')
            ->where('sku', 'LIKE', '%TW-NGERS%')
            ->update([
                'sku' => DB::raw("REPLACE(sku, 'TW-NGERS', 'TWINGERS')"),
            ]);

        // products tablosundaki hatalı SKU'ları düzelt
        DB::table('products')
            ->where('sku', 'LIKE', '%TW-NGERS%')
            ->update([
                'sku' => DB::raw("REPLACE(sku, 'TW-NGERS', 'TWINGERS')"),
            ]);
    }

    /**
     * Rollback: Geri almaya gerek yok, düzeltme kalıcı.
     */
    public function down(): void
    {
        // Intentionally left empty - this is a data fix
    }
};
