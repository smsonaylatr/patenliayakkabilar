<?php

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        $products = Product::with(['categories', 'features', 'variants'])->get();

        foreach ($products as $product) {
            if ($product->applyShoeDescriptionAndSeo(true, true)) {
                $product->saveQuietly();
            }
        }

        Product::clearProductCaches();

        if (Schema::hasTable('settings')) {
            Setting::updateOrCreate(
                ['key' => 'shoe_seo_descriptions_v1_applied'],
                ['value' => '1']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Veri güncellemesi geri alınmaz
    }
};
