<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('daily_traffic_metrics')) {
            Schema::create('daily_traffic_metrics', function (Blueprint $table) {
                $table->id();
                $table->date('date')->unique()->index();
                $table->unsignedInteger('unique_visitors_count')->default(0);
                $table->unsignedInteger('page_views_count')->default(0);
                $table->unsignedInteger('sessions_count')->default(0);
                $table->unsignedInteger('cart_additions_count')->default(0);
                $table->unsignedInteger('checkout_starts_count')->default(0);
                $table->unsignedInteger('orders_count')->default(0);
                $table->decimal('orders_revenue', 12, 2)->default(0.00);
                $table->json('device_stats')->nullable(); // ['mobile' => 80, 'desktop' => 15, 'tablet' => 5]
                $table->json('source_stats')->nullable(); // ['Instagram' => 40, 'Google Ads' => 30, ...]
                $table->json('top_paths')->nullable(); // ['/patenli-ayakkabilar' => 120, ...]
                $table->unsignedInteger('avg_duration_seconds')->default(0);
                $table->text('analysis_summary')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_traffic_metrics');
    }
};
