<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Blog yazıları tablosuna yazar, yayın tarihi ve SEO alanları ekle.
     * Mevcut veriyi korumak için tüm alanlar nullable veya default değerli.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('meta_description');
            $table->timestamp('published_at')->nullable()->after('author_name');
            $table->string('og_image')->nullable()->after('published_at');
            $table->boolean('is_indexable')->default(true)->after('og_image');
        });
    }

    /**
     * Eklenen yazar, yayın tarihi ve SEO alanlarını kaldır.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('author_name');
            $table->dropColumn('published_at');
            $table->dropColumn('og_image');
            $table->dropColumn('is_indexable');
        });
    }
};
