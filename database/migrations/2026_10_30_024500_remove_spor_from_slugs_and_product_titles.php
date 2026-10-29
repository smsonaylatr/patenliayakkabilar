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

        Product::cleanAllSlugsAndTitles(true);

        if (Schema::hasTable('settings')) {
            Setting::updateOrCreate(
                ['key' => 'shoe_remove_spor_and_seo_v2_applied'],
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
