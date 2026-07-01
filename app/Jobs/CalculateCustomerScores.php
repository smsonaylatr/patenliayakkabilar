<?php

namespace App\Jobs;

use App\Services\CustomerScoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateCustomerScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CustomerScoreService $service): void
    {
        $count = $service->calculateAll();
        Log::info("Customer scores calculated for {$count} customers.");
    }
}
