<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PoregoApiService;

class SyncPoregoProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'porego:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm aktif ürünleri ve stok miktarlarını Porego/Paketfy sistemine senkronize eder.';

    /**
     * Execute the console command.
     */
    public function handle(PoregoApiService $service): int
    {
        $this->info('Porego stok ve ürün senkronizasyonu başlatılıyor...');

        $result = $service->syncProducts();

        if ($result['success']) {
            $this->info("✅ Başarılı: " . $result['message']);
            return Command::SUCCESS;
        } else {
            $this->error("❌ Hata: " . $result['message']);
            return Command::FAILURE;
        }
    }
}
