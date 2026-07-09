<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mevcut string renk verilerini JSON array'e dönüştür
        $variants = DB::table('product_variants')->whereNotNull('color')->get();
        foreach ($variants as $variant) {
            $color = $variant->color;
            // Zaten JSON ise dokunma
            if (str_starts_with($color, '[')) {
                continue;
            }
            // String rengi JSON array'e çevir
            DB::table('product_variants')
                ->where('id', $variant->id)
                ->update(['color' => json_encode([$color])]);
        }

        // Kolon tipini JSON'a çevir
        Schema::table('product_variants', function (Blueprint $table) {
            $table->json('color')->nullable()->change();
        });
    }

    public function down(): void
    {
        // JSON'dan tekrar string'e çevir
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('color')->nullable()->change();
        });

        // JSON array'leri tekrar string'e dönüştür (ilk elemanı al)
        $variants = DB::table('product_variants')->whereNotNull('color')->get();
        foreach ($variants as $variant) {
            $color = $variant->color;
            $decoded = json_decode($color, true);
            if (is_array($decoded)) {
                DB::table('product_variants')
                    ->where('id', $variant->id)
                    ->update(['color' => $decoded[0] ?? null]);
            }
        }
    }
};
