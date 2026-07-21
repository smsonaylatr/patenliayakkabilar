<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('orders', function (Blueprint $table) {
    if (!Schema::hasColumn('orders', 'ip_address')) {
        $table->string('ip_address', 45)->nullable()->after('status');
    }
});

Schema::table('products', function (Blueprint $table) {
    if (!Schema::hasColumn('products', 'is_cod_active')) {
        $table->boolean('is_cod_active')->default(true);
    }
});
