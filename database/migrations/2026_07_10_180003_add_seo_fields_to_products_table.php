<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ürünler tablosuna SEO alanları ekle.
     * Mevcut veriyi korumak için tüm alanlar nullable veya default değerli.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('og_image')->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('og_image');
            $table->boolean('is_indexable')->default(true)->after('canonical_url');
        });
    }

    /**
     * Eklenen SEO alanlarını kaldır.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('og_image');
            $table->dropColumn('canonical_url');
            $table->dropColumn('is_indexable');
        });
    }
};
