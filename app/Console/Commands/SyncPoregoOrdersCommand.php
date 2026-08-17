<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PoregoApiService;

class SyncPoregoOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'porego:sync-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Porego API üzerinden kargodaki ve teslim edilen sipariş durumlarını senkronize eder.';

    /**
     * Execute the console command.
     */
    public function handle(PoregoApiService $poregoService)
    {
        $this->info('Porego sipariş durumları senkronizasyonu başlatılıyor...');
        $result = $poregoService->syncOrderStatuses();

        if ($result['success']) {
            $this->info("✅ " . $result['message']);
        } else {
            $this->error("❌ " . $result['message']);
        }

        return 0;
    }
}
