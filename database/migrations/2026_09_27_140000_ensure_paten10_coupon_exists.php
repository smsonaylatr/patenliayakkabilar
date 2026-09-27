<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('coupons')) {
            return;
        }

        // PATEN10 kuponunun veritabanında aktif ve %10 indirimli olarak bulunmasını sağla
        $existing = DB::table('coupons')->where('code', 'PATEN10')->first();

        if ($existing) {
            DB::table('coupons')->where('code', 'PATEN10')->update([
                'type' => 'percentage',
                'value' => 10.00,
                'status' => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('coupons')->insert([
                'code' => 'PATEN10',
                'type' => 'percentage',
                'value' => 10.00,
                'min_cart_total' => 0,
                'usage_limit' => null,
                'used_count' => 0,
                'expires_at' => null,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma durumunda kuponu silmek yerine pasife al
        if (Schema::hasTable('coupons')) {
            DB::table('coupons')->where('code', 'PATEN10')->update(['status' => false]);
        }
    }
};
