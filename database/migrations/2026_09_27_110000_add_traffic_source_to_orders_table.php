<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('traffic_source', 100)->nullable()->after('status')->index();
            $table->string('device_type', 50)->nullable()->after('traffic_source');
            $table->string('utm_source', 100)->nullable()->after('traffic_source');
            $table->string('utm_medium', 100)->nullable()->after('utm_source');
            $table->string('utm_campaign', 150)->nullable()->after('utm_medium');
            $table->string('utm_term', 150)->nullable()->after('utm_campaign');
            $table->string('utm_content', 150)->nullable()->after('utm_term');
            $table->text('referrer')->nullable()->after('utm_content');
        });

        // Mevcut siparişler için varsayılan kaynak belirle
        DB::table('orders')->whereNull('traffic_source')->update([
            'traffic_source' => DB::raw("CASE WHEN gclid IS NOT NULL AND gclid != '' THEN 'Google Ads' ELSE 'Doğrudan' END"),
            'device_type' => 'Mobil',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['traffic_source']);
            $table->dropColumn([
                'traffic_source',
                'device_type',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_term',
                'utm_content',
                'referrer',
            ]);
        });
    }
};
