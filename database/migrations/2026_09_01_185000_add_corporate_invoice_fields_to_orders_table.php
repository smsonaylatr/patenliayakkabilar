<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'invoice_type')) {
                $table->string('invoice_type', 20)->default('individual')->after('billing_address');
            }
            if (!Schema::hasColumn('orders', 'company_name')) {
                $table->string('company_name')->nullable()->after('invoice_type');
            }
            if (!Schema::hasColumn('orders', 'tax_office')) {
                $table->string('tax_office')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('orders', 'tax_number')) {
                $table->string('tax_number', 20)->nullable()->after('tax_office');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_type', 'company_name', 'tax_office', 'tax_number']);
        });
    }
};
