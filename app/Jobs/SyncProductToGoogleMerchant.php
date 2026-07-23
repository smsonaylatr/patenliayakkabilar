<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\GoogleMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProductToGoogleMerchant
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Product
     */
    public $product;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleMerchantService $merchantService): void
    {
        // Servis sınıfını kullanarak ürünü senkronize et
        $merchantService->syncProduct($this->product);
    }
}
