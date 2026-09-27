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
        // 1. Kalıcı Misafir Profili & Ziyaret Geçmişi Tablosu
        if (!Schema::hasTable('guest_profiles')) {
            Schema::create('guest_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('guest_id', 20)->unique()->index(); // Örn: MUJORC veya G-MUJORC
                $table->string('visitor_token', 64)->unique()->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('guest_name', 191)->nullable()->index();
                $table->string('guest_email', 191)->nullable()->index();
                $table->string('guest_phone', 50)->nullable()->index();
                $table->integer('visit_count')->default(1)->index(); // 1. geliş (⭐), 2. geliş (⭐⭐), 3. geliş (⭐⭐⭐)
                $table->integer('total_page_views')->default(1);
                $table->integer('total_time_spent_seconds')->default(0);
                $table->timestamp('first_seen_at')->useCurrent();
                $table->timestamp('last_visit_at')->useCurrent()->index();
                $table->timestamp('last_heartbeat_at')->useCurrent()->index();
                $table->string('ip_address', 45)->nullable()->index();
                $table->text('user_agent')->nullable();
                $table->string('device_type', 20)->default('desktop');
                $table->string('browser', 50)->nullable();
                $table->string('operating_system', 50)->nullable();
                $table->text('referrer')->nullable();
                $table->string('referrer_host', 100)->nullable();
                $table->string('utm_source', 100)->nullable();
                $table->string('utm_campaign', 100)->nullable();
                $table->timestamps();
            });
        }

        // 2. Active Visitors tablosuna guest_id, visit_count ve guest_profile_id ekle
        if (Schema::hasTable('active_visitors')) {
            Schema::table('active_visitors', function (Blueprint $table) {
                if (!Schema::hasColumn('active_visitors', 'guest_id')) {
                    $table->string('guest_id', 20)->nullable()->after('visitor_token')->index();
                }
                if (!Schema::hasColumn('active_visitors', 'visit_count')) {
                    $table->integer('visit_count')->default(1)->after('guest_id')->index();
                }
                if (!Schema::hasColumn('active_visitors', 'guest_profile_id')) {
                    $table->foreignId('guest_profile_id')->nullable()->after('user_id')->constrained('guest_profiles')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('active_visitors')) {
            Schema::table('active_visitors', function (Blueprint $table) {
                if (Schema::hasColumn('active_visitors', 'guest_profile_id')) {
                    $table->dropForeign(['guest_profile_id']);
                    $table->dropColumn('guest_profile_id');
                }
                if (Schema::hasColumn('active_visitors', 'visit_count')) {
                    $table->dropColumn('visit_count');
                }
                if (Schema::hasColumn('active_visitors', 'guest_id')) {
                    $table->dropColumn('guest_id');
                }
            });
        }

        Schema::dropIfExists('guest_profiles');
    }
};
