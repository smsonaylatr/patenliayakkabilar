<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class OptimizeGeoContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geo:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sistemdeki tum urunleri ve blog yazilarini GEO (Yapay Zeka) standartlarina gore optimize eder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("GEO Optimizasyonu Baslatiliyor...");

        $this->optimizeProducts();
        $this->optimizeBlogPosts();

        $this->info("Islem basariyla tamamlandi.");
        return 0;
    }

    private function optimizeProducts()
    {
        $products = Product::whereNull('aio_summary')->orWhereNull('faq_schema')->get();
        $count = 0;

        foreach ($products as $product) {
            $updated = false;

            // AIO Summary
            if (empty($product->aio_summary)) {
                $product->aio_summary = $this->generateProductSummary($product);
                $updated = true;
            }

            // FAQ Schema
            if (empty($product->faq_schema)) {
                $product->faq_schema = $this->generateProductFaq($product);
                $updated = true;
            }

            if ($updated) {
                // Sadece belirtilen alanlari guncelle (events tetiklenmemesi icin saveQuietly kullanılabilir ama model SEO autoFill yapıyor)
                $product->saveQuietly();
                $count++;
            }
        }

        $this->info("{$count} adet Urun optimize edildi.");
    }

    private function optimizeBlogPosts()
    {
        $posts = BlogPost::whereNull('aio_summary')->orWhereNull('faq_schema')->get();
        $count = 0;

        foreach ($posts as $post) {
            $updated = false;

            // AIO Summary
            if (empty($post->aio_summary)) {
                $source = $post->excerpt ?: strip_tags($post->content);
                $post->aio_summary = mb_substr(trim($source), 0, 160);
                $updated = true;
            }

            // FAQ Schema
            if (empty($post->faq_schema)) {
                $post->faq_schema = [
                    [
                        'question' => $post->title . ' hakkinda bilinmesi gerekenler nelerdir?',
                        'answer' => $post->aio_summary
                    ]
                ];
                $updated = true;
            }

            if ($updated) {
                $post->saveQuietly();
                $count++;
            }
        }

        $this->info("{$count} adet Blog Yazisi optimize edildi.");
    }

    private function generateProductSummary(Product $product): string
    {
        $name = $product->name;
        $category = $product->categories()->first()?->name ?? 'patenli ayakkabi';
        $desc = mb_substr(strip_tags($product->short_description), 0, 80);

        return trim("{$name}, güvenli tasarimi ve rahat kalibi ile öne çikan bir {$category} modelidir. {$desc}");
    }

    private function generateProductFaq(Product $product): array
    {
        return [
            [
                'question' => "{$product->name} kac yas icin uygundur?",
                'answer' => "Denge kurabilen 5 yas ve uzeri cocuklar ile gencler icin uygundur. Urun kalibi dardir, bu nedenle ayak numarasindan tam 1 numara buyuk alinmasi onerilir."
            ],
            [
                'question' => "{$product->name} tekerlekleri cikarilabilir mi?",
                'answer' => "Evet, modelin tekerlekleri kolayca iceri gizlenebilir veya sökülebilir. Boylece istenildiginde normal bir spor ayakkabi olarak da guvenle kullanilabilir."
            ]
        ];
    }
}
