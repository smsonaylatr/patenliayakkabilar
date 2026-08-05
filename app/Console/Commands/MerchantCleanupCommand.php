<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\GoogleMerchantService;

class MerchantCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merchant:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google Merchant Center\'daki hayalet urunleri (silinmis/pasif) temizler.';

    /**
     * Execute the console command.
     */
    public function handle(GoogleMerchantService $merchantService)
    {
        $this->info("Google Merchant Center'a baglaniliyor...");
        
        $googleProducts = $merchantService->listProducts();
        
        if (empty($googleProducts)) {
            $this->warn("Google Merchant Center'da hic urun bulunamadi veya baglanti hatasi olustu.");
            return Command::FAILURE;
        }

        $this->info("Google'da toplam " . count($googleProducts) . " urun bulundu.");

        // Sitemizdeki aktif urunlerin ID'lerini (ve varyantlari varsa parent_id'leri) bulalim.
        // Google Content API offerId alanina ya "product_id" ya da "product_id_size" koyar.
        // OfferId formatimiz: "1", "1_32", "1_33" vs.
        
        $activeProductIds = Product::where('status', true)
            ->where('stock', '>', 0)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        $deletedCount = 0;
        $activeCount = 0;

        foreach ($googleProducts as $googleProduct) {
            $offerId = $googleProduct->getOfferId(); // ornegin: "1", "1_32", "2"
            
            // "1_32" gibi varyant ID'sinden asil urun ID'sini cikar
            $parts = explode('_', $offerId);
            $mainProductId = (int) $parts[0];

            if (!in_array($mainProductId, $activeProductIds)) {
                $this->info("Siliniyor (Aktif degil/Bulunamadi): OfferId {$offerId}");
                // Benzersiz kimlik ile sil (Google tarafindaki orjinal OfferID'yi yollamaliyiz)
                $merchantService->deleteProduct($offerId);
                $deletedCount++;
            } else {
                $activeCount++;
            }
        }

        $this->info("-------------------");
        $this->info("Temizlik Tamamlandi!");
        $this->info("Gereksiz Silinen Urun Sayisi: {$deletedCount}");
        $this->info("Gecerli/Aktif Birakilan Urun Sayisi: {$activeCount}");

        return Command::SUCCESS;
    }
}
