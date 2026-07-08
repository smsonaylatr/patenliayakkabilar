<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('wheel_type');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
        });

        // Mevcut varyantların fiyatlarını ürün fiyatından + price_extra olarak doldur
        $variants = \App\Models\ProductVariant::with('product')->get();
        foreach ($variants as $variant) {
            if ($variant->product) {
                $variant->price = $variant->product->price + ($variant->price_extra ?? 0);
                $variant->discount_price = $variant->product->discount_price;
                $variant->save();
            }
        }

        // product tablosundaki price ve stock'u nullable yap
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->integer('stock')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['price', 'discount_price']);
        });
    }
};
