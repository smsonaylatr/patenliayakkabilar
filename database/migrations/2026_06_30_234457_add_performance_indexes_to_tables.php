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
        $productsIndexes = array_column(\Illuminate\Support\Facades\Schema::getIndexes('products'), 'name');
        
        Schema::table('products', function (Blueprint $table) use ($productsIndexes) {
            if (!in_array('products_status_featured_index', $productsIndexes)) {
                $table->index(['status', 'featured']);
            }
            if (!in_array('products_price_index', $productsIndexes)) {
                $table->index('price');
            }
        });

        $cartsIndexes = array_column(\Illuminate\Support\Facades\Schema::getIndexes('carts'), 'name');
        Schema::table('carts', function (Blueprint $table) use ($cartsIndexes) {
            if (!in_array('carts_updated_at_index', $cartsIndexes)) {
                $table->index('updated_at');
            }
        });
        
        $eventsIndexes = array_column(\Illuminate\Support\Facades\Schema::getIndexes('customer_events'), 'name');
        Schema::table('customer_events', function (Blueprint $table) use ($eventsIndexes) {
            if (!in_array('customer_events_event_type_created_at_index', $eventsIndexes)) {
                $table->index(['event_type', 'created_at']);
            }
        });
    }

    public function down(): void
    {
        // Safe down
    }
};
