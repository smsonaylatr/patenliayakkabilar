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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'tax_number')) {
                $table->string('tax_number')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('orders', 'tax_office')) {
                $table->string('tax_office')->nullable()->after('tax_number');
            }
            if (!Schema::hasColumn('orders', 'company_name')) {
                $table->string('company_name')->nullable()->after('tax_office');
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_uuid')) {
                $table->string('gib_invoice_uuid')->nullable()->unique()->after('invoice_url');
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_number')) {
                $table->string('gib_invoice_number')->nullable()->after('gib_invoice_uuid');
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_date')) {
                $table->dateTime('gib_invoice_date')->nullable()->after('gib_invoice_number');
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_status')) {
                $table->string('gib_invoice_status')->default('none')->after('gib_invoice_date');
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_html')) {
                $table->longText('gib_invoice_html')->nullable()->after('gib_invoice_status');
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_error')) {
                $table->text('gib_invoice_error')->nullable()->after('gib_invoice_html');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tax_number',
                'tax_office',
                'company_name',
                'gib_invoice_uuid',
                'gib_invoice_number',
                'gib_invoice_date',
                'gib_invoice_status',
                'gib_invoice_html',
                'gib_invoice_error',
            ]);
        });
    }
};
