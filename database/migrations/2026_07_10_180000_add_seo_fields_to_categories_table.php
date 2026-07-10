<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kategoriler tablosuna SEO ve görsel alanları ekle.
     * Mevcut veriyi korumak için tüm alanlar nullable veya default değerli.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->longText('seo_content')->nullable()->after('meta_description');
            $table->string('seo_h1')->nullable()->after('seo_content');
            $table->string('og_image')->nullable()->after('seo_h1');
            $table->boolean('is_indexable')->default(true)->after('og_image');
            $table->integer('sort_order')->default(0)->after('is_indexable');
            $table->string('image')->nullable()->after('sort_order');
        });
    }

    /**
     * Eklenen SEO ve görsel alanlarını kaldır.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->dropColumn('seo_content');
            $table->dropColumn('seo_h1');
            $table->dropColumn('og_image');
            $table->dropColumn('is_indexable');
            $table->dropColumn('sort_order');
            $table->dropColumn('image');
        });
    }
};
