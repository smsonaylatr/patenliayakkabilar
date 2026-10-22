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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'audience_signals')) {
                $table->json('audience_signals')->nullable()->after('customer_segment_id');
            }
            if (!Schema::hasColumn('campaigns', 'conversion_goal')) {
                $table->string('conversion_goal')->default('purchase')->after('channel');
            }
            if (!Schema::hasColumn('campaigns', 'tracking_settings')) {
                $table->json('tracking_settings')->nullable()->after('conversion_goal');
            }
            if (!Schema::hasColumn('campaigns', 'rich_assets')) {
                $table->json('rich_assets')->nullable()->after('message_template');
            }
            if (!Schema::hasColumn('campaigns', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->after('customer_segment_id')->constrained('coupons')->nullOnDelete();
            }
            if (!Schema::hasColumn('campaigns', 'budget')) {
                $table->decimal('budget', 12, 2)->default(0)->after('scheduled_at');
            }
            if (!Schema::hasColumn('campaigns', 'spent_amount')) {
                $table->decimal('spent_amount', 12, 2)->default(0)->after('budget');
            }
            if (!Schema::hasColumn('campaigns', 'target_roas')) {
                $table->decimal('target_roas', 6, 2)->nullable()->after('spent_amount');
            }
            if (!Schema::hasColumn('campaigns', 'target_cpa')) {
                $table->decimal('target_cpa', 10, 2)->nullable()->after('target_roas');
            }
            if (!Schema::hasColumn('campaigns', 'conversions_count')) {
                $table->integer('conversions_count')->default(0)->after('clicked_count');
            }
            if (!Schema::hasColumn('campaigns', 'conversion_revenue')) {
                $table->decimal('conversion_revenue', 12, 2)->default(0)->after('conversions_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'audience_signals',
                'conversion_goal',
                'tracking_settings',
                'rich_assets',
                'coupon_id',
                'budget',
                'spent_amount',
                'target_roas',
                'target_cpa',
                'conversions_count',
                'conversion_revenue',
            ] as $col) {
                if (Schema::hasColumn('campaigns', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
