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
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'is_invoiced')) {
                    $table->boolean('is_invoiced')->default(false)->after('status');
                }
                if (!Schema::hasColumn('orders', 'invoice_url')) {
                    $table->string('invoice_url')->nullable()->after('is_invoiced');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('orders', 'is_invoiced')) {
                    $columns[] = 'is_invoiced';
                }
                if (Schema::hasColumn('orders', 'invoice_url')) {
                    $columns[] = 'invoice_url';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
