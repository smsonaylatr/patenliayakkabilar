<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['static', 'dynamic'])->default('dynamic');
            $table->json('conditions')->nullable(); // Dynamic segment rules
            $table->string('color', 20)->default('#6b7280');
            $table->string('icon', 50)->default('users');
            $table->integer('customer_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_segment_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_segment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('added_at')->useCurrent();

            $table->unique(['customer_segment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_segment_user');
        Schema::dropIfExists('customer_segments');
    }
};
