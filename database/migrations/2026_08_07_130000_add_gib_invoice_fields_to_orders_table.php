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
            if (!Schema::hasColumn('orders', 'is_invoiced')) {
                $table->boolean('is_invoiced')->default(false)->after('status');
            }
            if (!Schema::hasColumn('orders', 'invoice_url')) {
                $table->string('invoice_url')->nullable()->after('is_invoiced');
            }
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
                $table->string('gib_invoice_uuid')->nullable()->unique();
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_number')) {
                $table->string('gib_invoice_number')->nullable();
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_date')) {
                $table->dateTime('gib_invoice_date')->nullable();
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_status')) {
                $table->string('gib_invoice_status')->default('none');
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_html')) {
                $table->longText('gib_invoice_html')->nullable();
            }
            if (!Schema::hasColumn('orders', 'gib_invoice_error')) {
                $table->text('gib_invoice_error')->nullable();
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
                'is_invoiced',
                'invoice_url',
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
