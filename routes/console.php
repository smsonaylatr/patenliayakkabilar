<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\CalculateCustomerScores;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Phoenix AI: Müşteri Skorlarını Hesapla (Her gece 03:00) ─────────────
Schedule::job(new CalculateCustomerScores)->dailyAt('03:00');

// ─── Phoenix AI: Önerileri Üret (Her 2 saatte bir) ──────────────────────
Schedule::call(function () {
    $service = app(\App\Services\PhoenixAIService::class);
    $count = $service->generateRecommendations();
    \Illuminate\Support\Facades\Log::info("Phoenix AI generated {$count} recommendations.");
})->everyTwoHours();

// ─── Sepet Terk Tespiti (Her saat başı) ──────────────────────────────────
Schedule::job(new \App\Jobs\DetectAbandonedCarts)->hourly();

// ─── Phoenix AI: Manuel çalıştırma komutu ────────────────────────────────
Artisan::command('phoenix:scores', function () {
    $service = app(\App\Services\CustomerScoreService::class);
    $count = $service->calculateAll();
    $this->info("✅ {$count} müşteri skoru hesaplandı.");
})->purpose('Calculate all customer scores');

Artisan::command('phoenix:recommendations', function () {
    $service = app(\App\Services\PhoenixAIService::class);
    $count = $service->generateRecommendations();
    $this->info("✅ {$count} yeni öneri oluşturuldu.");
})->purpose('Generate AI recommendations');

// ─── Segment Sync: Müşterileri Segmentlere Eşleştir (Her gece 03:30) ───
Schedule::job(new \App\Jobs\SyncSegmentCustomers)->dailyAt('03:30');

Artisan::command('phoenix:sync-segments', function () {
    dispatch_sync(new \App\Jobs\SyncSegmentCustomers);
    $this->info("✅ Segment eşleştirme tamamlandı.");
})->purpose('Sync customers into dynamic segments');
