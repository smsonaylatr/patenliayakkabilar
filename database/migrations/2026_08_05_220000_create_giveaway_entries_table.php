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
        Schema::create('giveaway_entries', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 30)->unique()->comment('Çekiliş Kura Numarası');
            $table->string('name')->comment('Ad Soyad');
            $table->string('instagram_username', 100)->index()->comment('Instagram Kullanıcı Adı (@kullaniciadi)');
            $table->string('phone', 30)->index()->comment('Telefon Numarası');
            $table->string('email')->comment('E-posta Adresi');
            $table->string('shoe_size', 10)->comment('Tercih Edilen Beden/Numara');
            $table->string('city', 100)->comment('İl');
            $table->string('district', 100)->comment('İlçe');
            $table->text('address')->nullable()->comment('Açık Teslimat Adresi');
            $table->boolean('is_winner')->default(false)->comment('Kazanan Mı?');
            $table->string('won_prize')->nullable()->comment('Kazanılan Ödül');
            $table->boolean('kvkk_consent')->default(true)->comment('KVKK & Çekiliş Koşulları Onayı');
            $table->boolean('sms_consent')->default(false)->comment('Kampanya & Fırsat SMS/Eposta İzni');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giveaway_entries');
    }
};
