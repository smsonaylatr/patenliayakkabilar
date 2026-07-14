<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Product;
use App\Models\Category;

class LinkInternalContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:link-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blog ve Sayfa içeriklerine otomatik iç bağlantılar (internal links) ekler.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('İç bağlantı ekleme işlemi başlatılıyor...');

        // Sabit anahtar kelime eşleşmeleri
        $links = [
            'patenli ayakkabı beden rehberi' => '/beden-rehberi',
            'patenli ayakkabı güvenlik ekipmanları' => '/guvenlik-ekipmanlari',
            'çocuk patenli ayakkabı modelleri' => '/kategori/cocuk-patenli-ayakkabi-modelleri',
            'patenli ayakkabı modelleri' => '/kategori/patenli-ayakkabi-modelleri',
            'çocuk patenli ayakkabı' => '/kategori/cocuk-patenli-ayakkabi-modelleri',
            'çocuk tekerlekli ayakkabı' => '/kategori/cocuk-patenli-ayakkabi-modelleri',
            'patenli ayakkabı' => '/kategori/patenli-ayakkabi-modelleri',
            'tekerlekli ayakkabı' => '/kategori/patenli-ayakkabi-modelleri',
            'ışıklı patenli ayakkabı' => '/kategori/patenli-ayakkabi-modelleri',
            'ışıklı tekerlekli ayakkabı' => '/kategori/patenli-ayakkabi-modelleri',
            'erkek çocuk' => '/kategori/erkek-cocuk',
            'kız çocuk' => '/kategori/kiz-cocuk',
            'beden rehberi' => '/beden-rehberi',
            'güvenlik ekipmanları' => '/guvenlik-ekipmanlari',
            'güvenlik ekipmanı' => '/guvenlik-ekipmanlari',
            'iade ve değişim' => '/iade-ve-degisim',
            'hakkımızda' => '/hakkimizda',
            'sıkça sorulan sorular' => '/sikca-sorulan-sorular',
            'iletişim' => '/iletisim',
        ];

        // Ürünleri dinamik ekle
        $products = Product::pluck('slug', 'name');
        foreach ($products as $name => $slug) {
            $links[mb_strtolower($name)] = '/urun/' . $slug;
        }

        // Kategorileri dinamik ekle (zaten yukarıda genel olanlar var ama özel isimler için)
        $categories = Category::pluck('slug', 'name');
        foreach ($categories as $name => $slug) {
            $links[mb_strtolower($name)] = '/kategori/' . $slug;
        }

        // Anahtar kelimeleri uzunluklarına göre azalan şekilde sırala 
        // (Böylece uzun kelimeler kısa kelimelerin içinde kaybolmaz)
        uksort($links, function($a, $b) {
            return mb_strlen($b) - mb_strlen($a);
        });

        // Blog Postlarını İşle
        $blogCount = $this->processModel(BlogPost::class, $links);
        $this->info("{$blogCount} adet Blog yazısına iç bağlantılar eklendi.");

        // Kurumsal Sayfaları İşle
        $pageCount = $this->processModel(Page::class, $links);
        $this->info("{$pageCount} adet Sayfaya iç bağlantılar eklendi.");

        $this->info('İşlem başarıyla tamamlandı!');
    }

    /**
     * Modeli işler ve regex ile ilk eşleşen kelimelere link verir.
     */
    private function processModel($modelClass, $links)
    {
        $records = $modelClass::all();
        $modifiedCount = 0;

        foreach ($records as $record) {
            $content = $record->content;
            if (empty($content)) {
                continue;
            }

            $modified = false;

            foreach ($links as $keyword => $url) {
                // Regex açıklaması:
                // (?!(?:[^<]+>|[^>]+<\/a>)) -> <a> veya başka HTML etiketleri içinde değilsek
                // (?<![\p{L}\p{N}]) -> Kelime başlangıcı (harf veya rakam öncesi olmamalı)
                // (keyword) -> Aranacak tam kelime
                // (?![\p{L}\p{N}]) -> Kelime bitişi (harf veya rakam sonrası olmamalı)
                
                $escapedKeyword = preg_quote($keyword, '/');
                $pattern = '/(?!(?:[^<]+>|[^>]+<\/a>))(?<![\p{L}\p{N}])(' . $escapedKeyword . ')(?![\p{L}\p{N}])/iu';
                
                $newContent = preg_replace_callback($pattern, function($matches) use ($url) {
                    $matchedText = $matches[1];
                    // Link oluştur
                    return '<a href="' . $url . '" class="text-teal-600 font-semibold hover:underline" title="' . e($matchedText) . '">' . $matchedText . '</a>';
                }, $content, 1); // 1 eşleşme ile sınırla
                
                if ($newContent !== null && $newContent !== $content) {
                    $content = $newContent;
                    $modified = true;
                }
            }
            
            if ($modified) {
                $record->content = $content;
                // updated_at vb ezilmemesi veya ezilmesi tercihe bağlı, şimdilik kaydediyoruz.
                $record->save();
                $modifiedCount++;
            }
        }

        return $modifiedCount;
    }
}
