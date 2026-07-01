<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('activity_score')->default(0);
            $table->integer('purchase_score')->default(0);
            $table->integer('loyalty_score')->default(0);
            $table->integer('engagement_score')->default(0);
            $table->integer('risk_score')->default(0); // 0=safe, 100=high churn risk
            $table->decimal('lifetime_value', 12, 2)->default(0);
            $table->decimal('avg_order_value', 10, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->integer('days_since_last_order')->nullable();
            $table->integer('days_since_last_activity')->nullable();
            $table->decimal('predicted_churn_probability', 5, 2)->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_scores');
    }
};
