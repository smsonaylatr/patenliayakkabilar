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
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                if (!Schema::hasColumn('carts', 'guest_name')) {
                    $table->string('guest_name')->nullable()->after('session_id');
                }
                if (!Schema::hasColumn('carts', 'guest_email')) {
                    $table->string('guest_email')->nullable()->after('guest_name');
                }
                if (!Schema::hasColumn('carts', 'guest_phone')) {
                    $table->string('guest_phone')->nullable()->after('guest_email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('carts', 'guest_phone')) {
                    $columns[] = 'guest_phone';
                }
                if (Schema::hasColumn('carts', 'guest_email')) {
                    $columns[] = 'guest_email';
                }
                if (Schema::hasColumn('carts', 'guest_name')) {
                    $columns[] = 'guest_name';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
