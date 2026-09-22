<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('stock_decremented')->default(false)->after('payment_status');
        });

        // Backfill: Mevcut siparişlerde stok düşürülmüş olanları işaretle
        // 1. Kapıda ödeme + aktif sipariş durumları (processing, shipped, delivered) → stok düşürülmüştü
        DB::table('orders')
            ->where('payment_method', 'cash_on_delivery')
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->update(['stock_decremented' => true]);

        // 2. KK/Havale + payment_status=paid + aktif sipariş durumları → stok düşürülmüştü
        DB::table('orders')
            ->whereIn('payment_method', ['credit_card', 'wire_transfer'])
            ->where('payment_status', 'paid')
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->update(['stock_decremented' => true]);

        // 3. İptal/iade olmuş siparişlerde stok zaten geri yüklendi → false kalmalı (default)
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_decremented');
        });
    }
};
