<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Console\Command;

class SyncSearchConsoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:sync-search-console {--skip-indexing : Indexing API tekil URL bildirimlerini atla}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ürün başlık/slug güncellemelerini, Sitemap <lastmod> tarihlerini, iç linkleri, Google Search Console Sitemap gönderimini ve Google Merchant (Satıcı Girişleri) eşitlemesini çalıştırır.';

    /**
     * Execute the console command.
     */
    public function handle(GoogleSearchConsoleService $service): int
    {
        $this->info('Google Search Console, Sitemap ve SEO senkronizasyonu başlatılıyor...');

        $result = $service->syncAll(!$this->option('skip-indexing'));

        $this->info("• Başlık/Slug/SEO güncellenen ürün sayısı: {$result['cleaned_products']}");
        $this->info("• Sitemap <lastmod> yenilenen aktif ürün sayısı: {$result['touched_products']}");

        if (!empty($result['sitemaps_submitted'])) {
            $this->info('• Search Console API\'ye gönderilen Sitemap\'ler: ' . implode(', ', $result['sitemaps_submitted']));
        }

        if ($result['urls_notified'] > 0) {
            $this->info("• Google Indexing API ile bildirilen URL sayısı: {$result['urls_notified']}");
        }

        if ($result['merchant_dispatched']) {
            $this->info('• Google Merchant Center (Satıcı Girişleri) senkronizasyonu kuyruğa eklendi.');
        }

        foreach ($result['warnings'] as $warning) {
            $this->warn('⚠️ ' . $warning);
        }

        try {
            Setting::updateOrCreate(
                ['key' => 'search_console_last_synced_at'],
                ['value' => now()->toDateTimeString()]
            );
        } catch (\Throwable) {
        }

        $this->info('Tamamlandı!');

        return self::SUCCESS;
    }
}
