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
        if (Schema::hasTable('mail_logs')) {
            return;
        }

        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mailable_class')->nullable(); // App\Mail\OrderConfirmationMail gibi
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('subject')->nullable();
            $table->enum('status', ['queued', 'sending', 'sent', 'failed'])->default('queued');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('metadata')->nullable(); // ek bilgiler
            $table->nullableMorphs('loggable'); // polymorphic (order, user vb.)
            $table->timestamps();

            // İndeksler
            $table->index('to_email');
            $table->index('status');
            $table->index('mailable_class');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
