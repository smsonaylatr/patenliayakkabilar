<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Influencers ─────────────────────────────────────────
        Schema::create('influencers', function (Blueprint $table) {
            $table->id();
            $table->string('channel_name');
            $table->string('channel_url')->nullable();
            $table->string('platform')->default('youtube');
            $table->unsignedInteger('subscriber_count')->nullable();
            $table->unsignedInteger('avg_views')->nullable();
            $table->decimal('engagement_rate', 5, 2)->nullable();
            $table->string('category')->default('kids_vlog');
            $table->string('tier')->default('micro'); // nano/micro/mid/macro/mega
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_instagram')->nullable();
            $table->string('child_name')->nullable();
            $table->unsignedTinyInteger('child_age')->nullable();
            $table->string('parent_name')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('fit_score')->default(0); // 0-100
            $table->string('status')->default('discovered'); // discovered/contacted/negotiating/agreed/active/paused/rejected
            $table->string('affiliate_code')->nullable()->unique();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->unsignedInteger('total_videos')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['status', 'tier', 'fit_score']);
        });

        // ── Influencer Campaigns ────────────────────────────────
        Schema::create('influencer_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('influencer_id')->constrained()->cascadeOnDelete();
            $table->string('campaign_name');
            $table->string('package_type'); // super_hero/youtube_star/mega_star/custom
            $table->decimal('offered_amount', 10, 2)->nullable();
            $table->json('products_sent')->nullable();
            $table->unsignedTinyInteger('expected_videos')->default(1);
            $table->unsignedTinyInteger('delivered_videos')->default(0);
            $table->json('video_urls')->nullable();
            $table->string('status')->default('draft'); // draft/sent/accepted/in_progress/completed/cancelled
            $table->unsignedInteger('total_views')->default(0);
            $table->unsignedInteger('total_clicks')->default(0);
            $table->unsignedInteger('total_sales')->default(0);
            $table->decimal('revenue_generated', 12, 2)->default(0);
            $table->decimal('roi', 8, 2)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('ai_generated_proposal')->nullable();
            $table->timestamps();
        });

        // ── Influencer Outreach Logs ────────────────────────────
        Schema::create('influencer_outreach_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('influencer_id')->constrained()->cascadeOnDelete();
            $table->string('channel'); // email/dm/whatsapp/phone
            $table->string('direction')->default('outgoing');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('ai_generated')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->boolean('response_received')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('influencer_outreach_logs');
        Schema::dropIfExists('influencer_campaigns');
        Schema::dropIfExists('influencers');
    }
};
