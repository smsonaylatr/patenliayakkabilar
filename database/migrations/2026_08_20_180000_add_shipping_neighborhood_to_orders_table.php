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
            if (!Schema::hasColumn('orders', 'shipping_neighborhood')) {
                $table->string('shipping_neighborhood')->nullable()->after('shipping_district');
            }
            if (!Schema::hasColumn('orders', 'billing_neighborhood')) {
                $table->string('billing_neighborhood')->nullable()->after('billing_district');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_neighborhood', 'billing_neighborhood']);
        });
    }
};
