<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->text('note');
            $table->enum('type', ['info', 'warning', 'important'])->default('info');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index(); // stock_alert, campaign_suggest, customer_retention, revenue_opportunity
            $table->string('priority', 20)->default('medium'); // low, medium, high, critical
            $table->string('title');
            $table->text('description');
            $table->json('action_data')->nullable(); // What action to take
            $table->string('status', 20)->default('pending'); // pending, accepted, dismissed, completed
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Related customer
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
        Schema::dropIfExists('customer_notes');
    }
};
