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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('sms_consent')->default(false)->after('customer_note')->comment('Ticari elektronik ileti onayı');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->boolean('sms_consent')->default(false)->after('guest_phone')->comment('Ticari elektronik ileti onayı');
            $table->timestamp('abandoned_sms_sent_at')->nullable()->after('sms_consent')->comment('Sepeti terk SMS gönderim zamanı');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sms_consent');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['sms_consent', 'abandoned_sms_sent_at']);
        });
    }
};
