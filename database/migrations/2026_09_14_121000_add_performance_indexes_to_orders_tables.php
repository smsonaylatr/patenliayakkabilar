<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sipariş listesini hızlandırmak için kritik indexler.
 *
 * Sorun: Admin panelde siparişler sayfası yavaş açılıyor.
 * Neden: status, payment_status, payment_method, created_at gibi
 *        sürekli filtrelenen/sıralanan kolonlarda index yok.
 *
 * Eklenen indexler ve nedenleri:
 * 1. status                    → Navigation badge (pending count), durum filtresi
 * 2. payment_status            → Tab filtresi, ödeme durumu filtresi
 * 3. payment_method            → Tab filtresi, ödeme yöntemi filtresi
 * 4. created_at                → Varsayılan sıralama (DESC)
 * 5. (payment_status, payment_method) → Tab composite query optimizasyonu
 * 6. (status, created_at)      → Durum filtresi + sıralama composite
 * 7. customer_name             → Global arama + tablo sıralaması
 * 8. customer_phone            → Global arama
 * 9. customer_email            → Global arama
 * 10. invoice_type             → Fatura tipi filtresi
 * 11. order_items.order_id     → Zaten FK ile var, ancak composite ekliyoruz
 * 12. order_items.(order_id, product_id) → Eager loading optimizasyonu
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tek kolon indexleri - filtre ve sıralama
            $table->index('status', 'idx_orders_status');
            $table->index('payment_status', 'idx_orders_payment_status');
            $table->index('payment_method', 'idx_orders_payment_method');
            $table->index('created_at', 'idx_orders_created_at');

            // Composite indexler - tab filtreleri ve birleşik sorgular
            $table->index(['payment_status', 'payment_method'], 'idx_orders_payment_composite');
            $table->index(['status', 'created_at'], 'idx_orders_status_created');

            // Global arama indexleri
            $table->index('customer_name', 'idx_orders_customer_name');
            $table->index('customer_phone', 'idx_orders_customer_phone');
            $table->index('customer_email', 'idx_orders_customer_email');

            // Fatura tipi filtresi
            if (Schema::hasColumn('orders', 'invoice_type')) {
                $table->index('invoice_type', 'idx_orders_invoice_type');
            }

            // GİB fatura durumu (bulk action'larda sık sorgulanıyor)
            if (Schema::hasColumn('orders', 'gib_invoice_status')) {
                $table->index('gib_invoice_status', 'idx_orders_gib_invoice_status');
            }

            // is_invoiced + status composite (fatura kesme bulk action)
            if (Schema::hasColumn('orders', 'is_invoiced')) {
                $table->index(['is_invoiced', 'status'], 'idx_orders_invoiced_status');
            }
        });

        // order_items tablosu - eager loading optimizasyonu
        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_payment_status');
            $table->dropIndex('idx_orders_payment_method');
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_payment_composite');
            $table->dropIndex('idx_orders_status_created');
            $table->dropIndex('idx_orders_customer_name');
            $table->dropIndex('idx_orders_customer_phone');
            $table->dropIndex('idx_orders_customer_email');
        });

        // Koşullu indexleri güvenli silme
        try { Schema::table('orders', fn (Blueprint $t) => $t->dropIndex('idx_orders_invoice_type')); } catch (\Exception $e) {}
        try { Schema::table('orders', fn (Blueprint $t) => $t->dropIndex('idx_orders_gib_invoice_status')); } catch (\Exception $e) {}
        try { Schema::table('orders', fn (Blueprint $t) => $t->dropIndex('idx_orders_invoiced_status')); } catch (\Exception $e) {}

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_product');
        });
    }
};
