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
        if (Schema::hasTable('active_visitors')) {
            Schema::table('active_visitors', function (Blueprint $table) {
                if (!Schema::hasColumn('active_visitors', 'guest_name')) {
                    $table->string('guest_name', 191)->nullable()->after('user_id')->index();
                }
                if (!Schema::hasColumn('active_visitors', 'guest_email')) {
                    $table->string('guest_email', 191)->nullable()->after('guest_name')->index();
                }
                if (!Schema::hasColumn('active_visitors', 'guest_phone')) {
                    $table->string('guest_phone', 50)->nullable()->after('guest_email')->index();
                }
                if (!Schema::hasColumn('active_visitors', 'is_identified')) {
                    $table->boolean('is_identified')->default(false)->after('guest_phone')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('active_visitors', function (Blueprint $table) {
            $table->dropIndex(['guest_name']);
            $table->dropIndex(['guest_email']);
            $table->dropIndex(['guest_phone']);
            $table->dropIndex(['is_identified']);
            $table->dropColumn(['guest_name', 'guest_email', 'guest_phone', 'is_identified']);
        });
    }
};
