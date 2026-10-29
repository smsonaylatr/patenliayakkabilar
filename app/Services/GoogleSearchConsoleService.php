<?php

namespace App\Services;

use App\Jobs\SyncAllProductsToGoogleMerchant;
use App\Models\Category;
use App\Models\Product;
use Google\Client;
use Google\Service\Indexing;
use Google\Service\Indexing\UrlNotification;
use Google\Service\SearchConsole;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleService
{
    private ?SearchConsole $searchConsole = null;
    private ?Indexing $indexing = null;
    private string $siteUrl = 'https://patenliayakkabilar.com';

    public function __construct()
    {
        $configuredUrl = rtrim((string) config('app.url', 'https://patenliayakkabilar.com'), '/');
        if (!str_contains($configuredUrl, 'localhost') && !str_contains($configuredUrl, '127.0.0.1')) {
            $this->siteUrl = $configuredUrl;
        }

        $keyFilePath = storage_path('app/google-merchant.json');
        if (file_exists($keyFilePath)) {
            try {
                $client = new Client();
                $client->setAuthConfig($keyFilePath);
                $client->addScope([
                    SearchConsole::WEBMASTERS,
                    Indexing::INDEXING,
                ]);
                $this->searchConsole = new SearchConsole($client);
                $this->indexing = new Indexing($client);
            } catch (\Throwable $e) {
                Log::warning('Google Search Console istemcisi başlatılamadı: ' . $e->getMessage());
            }
        }
    }

    /**
     * Tüm SEO, Sitemap, İç Linkleme, Search Console ve Merchant Center güncellemelerini çalıştırır.
     *
     * @return array{
     *   cleaned_products: int,
     *   touched_products: int,
     *   sitemaps_submitted: array<int, string>,
     *   urls_notified: int,
     *   merchant_dispatched: bool,
     *   warnings: array<int, string>
     * }
     */
    public function syncAll(bool $notifyIndexingUrls = true): array
    {
        $result = [
            'cleaned_products'    => 0,
            'touched_products'    => 0,
            'sitemaps_submitted'  => [],
            'urls_notified'       => 0,
            'merchant_dispatched' => false,
            'warnings'            => [],
        ];

        // 1. Tüm ürün başlıklarından ve slug'lardan "Spor" kelimesini temizle + SEO/AIO alanlarını güncelle
        try {
            $result['cleaned_products'] = Product::cleanAllSlugsAndTitles(true);
        } catch (\Throwable $e) {
            $result['warnings'][] = 'Ürün başlık/slug temizliği sırasında uyarı: ' . $e->getMessage();
        }

        // 2. Sitemap <lastmod> zaman damgalarının güncellenmesi için aktif ürün ve kategorilerin updated_at değerini yenile
        try {
            $now = now();
            $result['touched_products'] = DB::table('products')
                ->where('status', true)
                ->whereNull('deleted_at')
                ->update(['updated_at' => $now]);

            DB::table('categories')
                ->where('status', true)
                ->update(['updated_at' => $now]);
        } catch (\Throwable $e) {
            $result['warnings'][] = 'Sitemap lastmod güncellemesi uyarısı: ' . $e->getMessage();
        }

        // 3. Blog ve kurumsal sayfalardaki iç linkleri (internal links) yeni ürün slug'larıyla güncelle
        try {
            Artisan::call('seo:link-content');
        } catch (\Throwable $e) {
            $result['warnings'][] = 'İç linkleme güncellemesi uyarısı: ' . $e->getMessage();
        }

        // 4. Google Search Console API üzerinden Sitemap'leri yeniden gönder
        $sitemapSubmission = $this->submitSitemaps();
        $result['sitemaps_submitted'] = $sitemapSubmission['submitted'];
        if (!empty($sitemapSubmission['warnings'])) {
            $result['warnings'] = array_merge($result['warnings'], $sitemapSubmission['warnings']);
        }

        // 5. Google Indexing API ile güncel ürün URL'lerini bildir
        if ($notifyIndexingUrls) {
            $result['urls_notified'] = $this->notifyUpdatedProductUrls();
        }

        // 6. Google Merchant Center (Search Console Alışveriş / Satıcı Girişleri) senkronizasyonunu başlat
        try {
            SyncAllProductsToGoogleMerchant::dispatch();
            $result['merchant_dispatched'] = true;
        } catch (\Throwable $e) {
            $result['warnings'][] = 'Google Merchant kuyruğu başlatılamadı: ' . $e->getMessage();
        }

        Product::clearProductCaches();

        return $result;
    }

    /**
     * Google Search Console API üzerinden tüm site haritalarını (sitemap) gönderir.
     *
     * @return array{submitted: array<int, string>, warnings: array<int, string>}
     */
    public function submitSitemaps(): array
    {
        $submitted = [];
        $warnings = [];

        $sitemapUrls = [
            $this->siteUrl . '/sitemap.xml',
            $this->siteUrl . '/sitemap-products.xml',
            $this->siteUrl . '/sitemap-categories.xml',
            $this->siteUrl . '/sitemap-pages.xml',
            $this->siteUrl . '/sitemap-blog.xml',
        ];

        if (!$this->searchConsole) {
            return [
                'submitted' => [],
                'warnings'  => ['Google servis hesabı (google-merchant.json) bulunamadığı için Search Console API atlandı.'],
            ];
        }

        $candidateProperties = $this->resolveSearchConsoleProperties();

        foreach ($candidateProperties as $property) {
            $propertySuccess = false;
            foreach ($sitemapUrls as $feedpath) {
                try {
                    $this->searchConsole->sitemaps->submit($property, $feedpath);
                    $submitted[] = $feedpath;
                    $propertySuccess = true;
                } catch (\Throwable $e) {
                    // İlk mülk formatı (örn. sc-domain vs https://) yetkisizse diğer mülk formatını dene
                    Log::debug("Search Console sitemap gönderimi ({$property} -> {$feedpath}): " . $e->getMessage());
                }
            }
            if ($propertySuccess) {
                break;
            }
        }

        $submitted = array_values(array_unique($submitted));

        if (empty($submitted)) {
            $warnings[] = 'Search Console API mülk erişimi doğrulanamadı (Servis hesabı e-postasının Search Console\'da mülk sahibi/kullanıcı olarak ekli olduğundan emin olun). Sitemap dosyaları canlı olarak güncellendi.';
        }

        return [
            'submitted' => $submitted,
            'warnings'  => $warnings,
        ];
    }

    /**
     * Google Indexing API üzerinden güncellenen ürün URL'lerini bildirir.
     */
    public function notifyUpdatedProductUrls(): int
    {
        if (!$this->indexing) {
            return 0;
        }

        $urls = [
            $this->siteUrl . '/',
            $this->siteUrl . '/patenli-ayakkabilar',
        ];

        try {
            $products = Product::where('status', true)
                ->whereNull('deleted_at')
                ->orderByDesc('updated_at')
                ->limit(40)
                ->get(['id', 'slug']);

            foreach ($products as $product) {
                $cleanSlug = Product::stripSporFromSlug($product->slug);
                $urls[] = $this->siteUrl . '/urun/' . $cleanSlug;
            }
        } catch (\Throwable) {
            return 0;
        }

        $notified = 0;
        foreach (array_unique($urls) as $url) {
            try {
                $notification = new UrlNotification();
                $notification->setUrl($url);
                $notification->setType('URL_UPDATED');
                $this->indexing->urlNotifications->publish($notification);
                $notified++;
            } catch (\Throwable) {
                // Indexing API yetkisi veya kotası yoksa sessizce çık
                break;
            }
        }

        return $notified;
    }

    /**
     * Search Console'da kayıtlı mülk (property) adreslerini tespit eder.
     *
     * @return array<int, string>
     */
    private function resolveSearchConsoleProperties(): array
    {
        $domain = parse_url($this->siteUrl, PHP_URL_HOST) ?: 'patenliayakkabilar.com';
        $defaults = [
            $this->siteUrl . '/',
            'sc-domain:' . $domain,
        ];

        if (!$this->searchConsole) {
            return $defaults;
        }

        try {
            $sitesList = $this->searchConsole->sites->listSites();
            $entries = $sitesList->getSiteEntry() ?: [];
            $found = [];
            foreach ($entries as $entry) {
                $siteUrl = $entry->getSiteUrl();
                if ($siteUrl && str_contains($siteUrl, $domain)) {
                    $found[] = $siteUrl;
                }
            }
            if (!empty($found)) {
                return array_values(array_unique(array_merge($found, $defaults)));
            }
        } catch (\Throwable) {
        }

        return $defaults;
    }
}
