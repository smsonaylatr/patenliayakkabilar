<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('products', function (Blueprint $table) {
    if (!Schema::hasColumn('products', 'has_installments')) {
        $table->boolean('has_installments')->default(false);
    }
});
