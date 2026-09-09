<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('type'); // sale, refund, shipping, discount, adjustment
            $table->decimal('amount', 12, 2); // pozitif = gelir, negatif = gider
            $table->string('description')->nullable();
            $table->string('reference')->nullable(); // sipariş numarası
            $table->string('return_reason')->nullable(); // iade nedeni
            $table->text('note')->nullable(); // ek not
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_entries');
    }
};
