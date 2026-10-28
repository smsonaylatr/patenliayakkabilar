<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\GoogleMerchantService;

class SyncGoogleMerchantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merchant:sync {--product= : Belirli bir ürün ID\'si senkronize et}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktif ve stoklu tüm ürünleri Google Merchant Center ile senkronize eder.';

    /**
     * Execute the console command.
     */
    public function handle(GoogleMerchantService $merchantService): int
    {
        $productId = $this->option('product');

        if ($productId) {
            $product = Product::with(['images', 'variants', 'categories'])->find($productId);
            if (!$product) {
                $this->error("Ürün ID: {$productId} bulunamadı.");
                return Command::FAILURE;
            }

            $this->info("Ürün ID: {$product->id} - {$product->name} Google Merchant'a gönderiliyor...");
            $result = $merchantService->syncProduct($product);
            
            if ($result) {
                $this->info("✓ Başarıyla senkronize edildi.");
            } else {
                $this->error("✕ Senkronizasyon başarısız oldu.");
            }

            return Command::SUCCESS;
        }

        $products = Product::where('status', true)
            ->where('stock', '>', 0)
            ->whereNull('deleted_at')
            ->with(['images', 'variants', 'categories'])
            ->get();

        $count = $products->count();
        $this->info("Toplam {$count} aktif ürün Google Merchant Center'a senkronize ediliyor...");

        $success = 0;
        $failed = 0;
        $failedList = [];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($products as $product) {
            usleep(150000); // Google API kota koruması
            $res = $merchantService->syncProduct($product);
            if ($res) {
                $success++;
            } else {
                $failed++;
                $failedList[] = "ID {$product->id} ({$product->name}): " . ($merchantService->getLastError() ?: 'Bilinmeyen hata');
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Senkronizasyon Tamamlandı! Başarılı: {$success}, Başarısız: {$failed}");
        if (!empty($failedList)) {
            $this->warn("Aktarılamayan Ürünler:");
            foreach ($failedList as $err) {
                $this->error(" - {$err}");
            }
        }

        return Command::SUCCESS;
    }
}
