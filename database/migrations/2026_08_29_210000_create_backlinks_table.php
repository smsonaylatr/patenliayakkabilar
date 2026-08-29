<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backlinks', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Kaynak Başlığı / Site Adı (örn. "Medium Patenli Ayakkabı Rehberi")
            $table->string('domain'); // Domain (örn. "medium.com")
            $table->string('target_url')->default('/'); // Sitemizdeki hedef URL (örn. "https://patenliayakkabilar.com")
            $table->string('backlink_url')->nullable(); // Canlı backlink URL'si
            $table->string('category')->default('directory'); // directory, social_profile, parenting_blog, sports_lifestyle, forum_community, digital_pr, gift_guide, review
            $table->string('anchor_text')->nullable(); // Bağlantı metni
            $table->string('link_type')->default('dofollow'); // dofollow, nofollow, ugc, sponsored
            $table->string('status')->default('pending'); // pending, contacted, published, rejected, active_verified
            $table->integer('domain_authority')->nullable(); // DA Puanı (1-100)
            $table->string('contact_email')->nullable();
            $table->string('contact_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlinks');
    }
};
