<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Console\Command;

class UpdateShoeProductsSeoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-shoe-seo {--only-missing : Sadece Patenli Ayakkabı kelimesi eksik olanları güncelle}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm ayakkabı modellerinin ürün açıklamalarını (kısa/detaylı) ve SEO (meta title, meta description, AIO) alanlarını Patenli Ayakkabı kelimesiyle günceller.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Ayakkabı modellerinin açıklama ve SEO verileri güncelleniyor...');

        $force = !$this->option('only-missing');
        $products = Product::with(['categories', 'features', 'variants'])->get();
        $updatedCount = 0;

        foreach ($products as $product) {
            if ($product->applyShoeDescriptionAndSeo($force, $force)) {
                $product->saveQuietly();
                $updatedCount++;
            }
        }

        Product::clearProductCaches();

        try {
            Setting::updateOrCreate(
                ['key' => 'shoe_seo_descriptions_v1_applied'],
                ['value' => '1']
            );
        } catch (\Throwable) {
        }

        $this->info("Tamamlandı: {$updatedCount} adet ayakkabı modelinin açıklama ve SEO verileri güncellendi.");

        return self::SUCCESS;
    }
}
