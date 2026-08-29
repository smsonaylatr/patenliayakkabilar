<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ai_recommendations');
    }

    public function down(): void
    {
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index();
            $table->string('priority', 20)->default('medium');
            $table->string('title');
            $table->text('description');
            $table->json('action_data')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
        });
    }
};
