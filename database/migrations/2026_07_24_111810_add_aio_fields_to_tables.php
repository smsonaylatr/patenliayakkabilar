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
        $tables = ['products', 'blog_posts', 'pages'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('aio_summary')->nullable()->after('is_indexable')->comment('AI için özet bilgi (TL;DR)');
                    $table->text('aio_target_keywords')->nullable()->after('aio_summary')->comment('AI hedef soruları/promptları');
                    $table->json('faq_schema')->nullable()->after('aio_target_keywords')->comment('FAQPage JSON verisi');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['products', 'blog_posts', 'pages'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['aio_summary', 'aio_target_keywords', 'faq_schema']);
                });
            }
        }
    }
};
