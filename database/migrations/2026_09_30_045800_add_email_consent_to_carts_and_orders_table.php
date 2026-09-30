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
            $table->boolean('email_consent')->default(false)->after('sms_consent')->comment('E-posta ticari ileti onayı');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->boolean('email_consent')->default(false)->after('sms_consent')->comment('E-posta ticari ileti onayı');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('email_consent');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('email_consent');
        });
    }
};
