<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canned_voice_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('audio_path')->nullable();
            $table->string('coupon_code', 50)->nullable();
            $table->string('action_button')->nullable();
            $table->string('action_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
        });

        // Seed default templates
        DB::table('canned_voice_messages')->insert([
            [
                'title' => '👋 Hoş Geldiniz & İndirim Fırsatı',
                'message' => "Patenli Ayakkabılar'a hoş geldiniz! Beğendiğiniz modellerde bugün geçerli özel fırsatları kaçırmayın, keyifli alışverişler dileriz!",
                'audio_path' => null,
                'coupon_code' => null,
                'action_button' => 'Tüm Modelleri Gör',
                'action_url' => '/patenli-ayakkabilar',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '🛒 Sepetiniz Sizi Bekliyor & Ücretsiz Kargo',
                'message' => 'Sepetinizdeki ürünler tükenmeden siparişinizi hemen tamamlayabilirsiniz. Ücretsiz kargo fırsatınız devam ediyor!',
                'audio_path' => null,
                'coupon_code' => null,
                'action_button' => 'Sepetime Git',
                'action_url' => '/checkout',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '🎁 Size Özel Sürpriz İndirim (CANLI10)',
                'message' => 'Alışverişinize özel anında geçerli %10 indirim kuponu tanımladık! İndiriminizi hemen kullanabilirsiniz.',
                'audio_path' => null,
                'coupon_code' => 'CANLI10',
                'action_button' => 'Kuponla Sepete Git',
                'action_url' => '/checkout',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '👟 Beden / Numara Canlı Desteği',
                'message' => 'Doğru paten numarasını seçmekte kararsız kaldıysanız WhatsApp destek hattımızdan uzman ekibimize danışabilirsiniz.',
                'audio_path' => null,
                'coupon_code' => null,
                'action_button' => 'WhatsApp Destek',
                'action_url' => 'https://wa.me/908503073164?text=' . urlencode('Merhaba, patenli ayakkabılar hakkında beden ve model desteği almak istiyorum.'),
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '⚡ Aynı Gün Hızlı Kargo Bildirimi',
                'message' => "Seçtiğiniz patenli ayakkabı modelleri bugün saat 16:00'a kadar vereceğiniz siparişlerde aynı gün kargoya teslim edilir!",
                'audio_path' => null,
                'coupon_code' => null,
                'action_button' => 'Modelleri İncele',
                'action_url' => '/patenli-ayakkabilar',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('canned_voice_messages');
    }
};
