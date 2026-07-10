<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sayfalar tablosuna SEO alanları ekle.
     * Mevcut veriyi korumak için tüm alanlar nullable veya default değerli.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('title');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
            $table->boolean('is_indexable')->default(true)->after('og_image');
        });
    }

    /**
     * Eklenen SEO alanlarını kaldır.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->dropColumn('og_image');
            $table->dropColumn('is_indexable');
        });
    }
};
