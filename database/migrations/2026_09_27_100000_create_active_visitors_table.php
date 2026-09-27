<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('active_visitors')) {
            Schema::create('active_visitors', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_token', 64)->index();
                $table->string('session_id', 191)->nullable()->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('guest_name', 191)->nullable()->index();
                $table->string('guest_email', 191)->nullable()->index();
                $table->string('guest_phone', 50)->nullable()->index();
                $table->boolean('is_identified')->default(false)->index();
                $table->string('ip_address', 45)->nullable()->index();
                $table->text('user_agent')->nullable();
                $table->string('device_type', 20)->default('desktop'); // desktop, mobile, tablet
                $table->string('browser', 50)->nullable();
                $table->string('operating_system', 50)->nullable();
                $table->string('screen_resolution', 20)->nullable();
                $table->text('current_url');
                $table->string('current_path', 255)->index();
                $table->string('current_title', 255)->nullable();
                $table->text('referrer')->nullable();
                $table->string('referrer_host', 100)->nullable();
                $table->string('utm_source', 100)->nullable();
                $table->string('utm_campaign', 100)->nullable();
                $table->foreignId('cart_id')->nullable()->constrained('carts')->nullOnDelete();
                $table->decimal('cart_total', 10, 2)->default(0.00);
                $table->integer('cart_items_count')->default(0);
                $table->json('cart_summary')->nullable();
                $table->json('journey_trail')->nullable();
                $table->integer('intent_score')->default(15)->index();
                $table->string('intent_level', 30)->default('browsing')->index();
                $table->text('behavior_insight')->nullable();
                $table->json('recommended_strategy')->nullable();
                $table->integer('time_spent_seconds')->default(0);
                $table->integer('page_views_count')->default(1);
                $table->json('pending_command')->nullable();
                $table->json('last_command_executed')->nullable();
                $table->boolean('is_online')->default(true)->index();
                $table->boolean('is_blocked')->default(false)->index();
                $table->timestamp('first_seen_at')->useCurrent();
                $table->timestamp('last_heartbeat_at')->useCurrent()->index();
                $table->timestamps();

                $table->index(['last_heartbeat_at', 'is_online']);
                $table->index(['visitor_token', 'last_heartbeat_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('active_visitors');
    }
};
