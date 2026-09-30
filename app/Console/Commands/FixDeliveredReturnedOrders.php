<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\PoregoApiService;

class FixDeliveredReturnedOrders extends Command
{
    protected $signature = 'orders:fix-delivered-returned';
    protected $description = 'Yanlışlıkla Teslim Edildi olarak işaretlenen iade/iptal ve transfer aşamasındaki kargoları düzeltir.';

    public function handle(PoregoApiService $poregoService): int
    {
        $this->info('Yanlış statüdeki siparişler taranıyor ve düzeltiliyor...');

        $repairedCount = $poregoService->autoRepairErroneousDeliveries();

        $this->info("✅ Toplam {$repairedCount} sipariş otomatik olarak gerçek durumuna (Kargoda / İade Edildi) geri döndürüldü.");

        return Command::SUCCESS;
    }
}
