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
        \Illuminate\Support\Facades\DB::table('categories')
            ->where('slug', 'kadin-yetişkin')
            ->update(['slug' => 'kadin-yetiskin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('categories')
            ->where('slug', 'kadin-yetiskin')
            ->update(['slug' => 'kadin-yetişkin']);
    }
};
