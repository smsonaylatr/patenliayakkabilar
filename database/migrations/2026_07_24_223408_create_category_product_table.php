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
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['category_id', 'product_id']);
        });

        // Mevcut products tablosundaki category_id'leri pivot tabloya taşıyalım
        $products = \Illuminate\Support\Facades\DB::table('products')->whereNotNull('category_id')->get();
        $inserts = [];
        foreach ($products as $product) {
            $inserts[] = [
                'category_id' => $product->category_id,
                'product_id' => $product->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($inserts)) {
            \Illuminate\Support\Facades\DB::table('category_product')->insert($inserts);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
