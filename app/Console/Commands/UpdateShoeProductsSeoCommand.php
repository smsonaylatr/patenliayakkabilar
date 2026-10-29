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
        $this->info('Ürün başlıklarından ve slug\'lardan "Spor" kelimesi temizleniyor ve SEO verileri güncelleniyor...');

        $force = !$this->option('only-missing');
        $updatedCount = Product::cleanAllSlugsAndTitles($force);

        try {
            Setting::updateOrCreate(
                ['key' => 'shoe_remove_spor_and_seo_v2_applied'],
                ['value' => '1']
            );
        } catch (\Throwable) {
        }

        $this->info("Tamamlandı: {$updatedCount} adet ürünün başlık, slug, açıklama ve SEO verileri güncellendi.");

        return self::SUCCESS;
    }
}
