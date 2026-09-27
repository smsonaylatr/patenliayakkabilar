<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\CalculateCustomerScores;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('visitors:sanitize-cart', function () {
    $visitors = \App\Models\ActiveVisitor::whereNotNull('cart_summary')->get();
    $count = 0;
    foreach ($visitors as $v) {
        $summary = $v->cart_summary;
        if (is_array($summary)) {
            $changed = false;
            foreach ($summary as &$item) {
                if (isset($item['color']) && is_array($item['color'])) {
                    $item['color'] = implode(', ', $item['color']);
                    $changed = true;
                }
                if (isset($item['size']) && is_array($item['size'])) {
                    $item['size'] = implode(', ', $item['size']);
                    $changed = true;
                }
            }
            if ($changed) {
                $v->cart_summary = $summary;
                $v->save();
                $count++;
            }
        }
    }
    $this->info("Sanitized {$count} active visitor records.");
});

// ─── Müşteri Skorlarını Hesapla (Her gece 03:00) ─────────────────────────
Schedule::job(new CalculateCustomerScores)->dailyAt('03:00');

// ─── Sepet Terk Tespiti (Her saat başı) ──────────────────────────────────
Schedule::job(new \App\Jobs\DetectAbandonedCarts)->hourly();

// ─── Müşteri Skoru: Manuel çalıştırma komutu ─────────────────────────────
Artisan::command('scores:calculate', function () {
    $service = app(\App\Services\CustomerScoreService::class);
    $count = $service->calculateAll();
    $this->info("✅ {$count} müşteri skoru hesaplandı.");
})->purpose('Calculate all customer scores');

// ─── Segment Sync: Müşterileri Segmentlere Eşleştir (Her gece 03:30) ───
Schedule::job(new \App\Jobs\SyncSegmentCustomers)->dailyAt('03:30');

Artisan::command('segments:sync', function () {
    dispatch_sync(new \App\Jobs\SyncSegmentCustomers);
    $this->info("✅ Segment eşleştirme tamamlandı.");
})->purpose('Sync customers into dynamic segments');


// ─── WaMessage SMS: Sepeti Terk Edenlere SMS Gönder (Her saat başı) ───────────
Schedule::command('app:send-abandoned-cart-sms')->hourly();

// ─── Porego: Sipariş & Kargo Durumlarını Otomatik Senkronize Et (Her 5 dk) ───
Schedule::command('porego:sync-orders')->everyFiveMinutes()->withoutOverlapping();

// ─── Site Sağlık Kontrolü (Her 5 dakikada bir) ──────────────────────────
Schedule::command('site:health-check')->everyFiveMinutes()->withoutOverlapping();
