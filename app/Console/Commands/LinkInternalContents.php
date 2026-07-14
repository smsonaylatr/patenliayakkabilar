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

        $links = [];

        // 1. Her zaman aktif olan statik rotalar ve genişletilmiş eşanlamlılar
        $baseKeywords = [
            'patenli ayakkabı modelleri', 'patenli ayakkabılar', 'patenli ayakkabı', 'tekerlekli ayakkabı', 
            'ışıklı patenli ayakkabı', 'ışıklı tekerlekli ayakkabı', 'ledli paten', 'çocuk pateni', 
            'iki tekerlekli ayakkabı', 'tek tekerlekli ayakkabı', 'tekerlekli spor ayakkabı'
        ];
        foreach($baseKeywords as $kw) {
            $links[$kw] = '/patenli-ayakkabilar';
        }

        // 2. Sadece aktif Kategoriler
        $categories = Category::where('status', true)->get();
        foreach ($categories as $cat) {
            if ($cat->slug === 'patenli-ayakkabi-modelleri') continue;
            
            $links[mb_strtolower($cat->name)] = '/kategori/' . $cat->slug;
            
            // Kategori varyasyonları
            if (str_contains($cat->slug, 'cocuk-patenli')) {
                $links['çocuk patenli ayakkabı modelleri'] = '/kategori/' . $cat->slug;
                $links['çocuk patenli ayakkabı'] = '/kategori/' . $cat->slug;
                $links['çocuk tekerlekli ayakkabı'] = '/kategori/' . $cat->slug;
                $links['kız çocuk paten'] = '/kategori/' . $cat->slug;
                $links['erkek çocuk paten'] = '/kategori/' . $cat->slug;
            }
        }

        // 3. Sadece aktif Sayfalar
        $pages = Page::where('is_active', true)->get();
        foreach ($pages as $page) {
            $links[mb_strtolower($page->title)] = '/' . $page->slug;
            
            // Sayfa varyasyonları
            if (str_contains($page->slug, 'beden')) {
                $links['beden rehberi'] = '/' . $page->slug;
                $links['beden tablosu'] = '/' . $page->slug;
                $links['ayak uzunluğu'] = '/' . $page->slug;
                $links['doğru beden'] = '/' . $page->slug;
                $links['beden ölçüsü'] = '/' . $page->slug;
                $links['numara seçimi'] = '/' . $page->slug;
            }
            if (str_contains($page->slug, 'guvenlik')) {
                $links['güvenlik ekipmanları'] = '/' . $page->slug;
                $links['güvenlik ekipmanı'] = '/' . $page->slug;
                $links['kask'] = '/' . $page->slug;
                $links['dizlik'] = '/' . $page->slug;
                $links['dirseklik'] = '/' . $page->slug;
                $links['koruyucu set'] = '/' . $page->slug;
                $links['koruyucu ekipman'] = '/' . $page->slug;
            }
            if (str_contains($page->slug, 'iletisim')) {
                $links['iletişim'] = '/' . $page->slug;
                $links['bize ulaşın'] = '/' . $page->slug;
                $links['iletişime geçin'] = '/' . $page->slug;
                $links['müşteri hizmetleri'] = '/' . $page->slug;
            }
            if (str_contains($page->slug, 'iade')) {
                $links['iade ve değişim'] = '/' . $page->slug;
                $links['iade koşulları'] = '/' . $page->slug;
                $links['değişim'] = '/' . $page->slug;
                $links['iade politikası'] = '/' . $page->slug;
                $links['iade garantisi'] = '/' . $page->slug;
                $links['kargo ve iade koşulları'] = '/' . $page->slug;
                $links['kargo ve iade'] = '/' . $page->slug;
                $links['garanti bilgisi'] = '/' . $page->slug;
                $links['garanti şartları'] = '/' . $page->slug;
                $links['teslimat'] = '/' . $page->slug;
            }
            if (str_contains($page->slug, 'sorular')) {
                $links['sıkça sorulan sorular'] = '/' . $page->slug;
                $links['sss'] = '/' . $page->slug;
                $links['merak edilenler'] = '/' . $page->slug;
                $links['sık sorulan sorular'] = '/' . $page->slug;
            }
            if (str_contains($page->slug, 'hakkimizda')) {
                $links['hakkımızda'] = '/' . $page->slug;
                $links['biz kimiz'] = '/' . $page->slug;
            }
        }

        // 4. Sadece aktif Ürünler
        $products = Product::where('status', true)->get();
        foreach ($products as $prod) {
            $links[mb_strtolower($prod->name)] = '/urun/' . $prod->slug;
        }

        // 5. Sadece aktif Blog Yazıları (Kendi aralarında linklemeleri için)
        $blogPosts = BlogPost::where('status', true)->get();
        foreach ($blogPosts as $post) {
            $links[mb_strtolower($post->title)] = '/blog/' . $post->slug;
            
            // Zenginleştirilmiş Blog Varyasyonları
            if (str_contains($post->slug, 'nasil-temizlenir')) {
                $links['patenli ayakkabı nasıl temizlenir?'] = '/blog/' . $post->slug;
                $links['patenli ayakkabı nasıl temizlenir'] = '/blog/' . $post->slug;
                $links['nasıl temizlenir'] = '/blog/' . $post->slug;
                $links['paten bakımı'] = '/blog/' . $post->slug;
                $links['tekerlek temizliği'] = '/blog/' . $post->slug;
                $links['rulman temizliği'] = '/blog/' . $post->slug;
            }
            if (str_contains($post->slug, 'guvenli-mi')) {
                $links['patenli ayakkabı güvenli mi?'] = '/blog/' . $post->slug;
                $links['patenli ayakkabı güvenli mi'] = '/blog/' . $post->slug;
                $links['güvenli mi'] = '/blog/' . $post->slug;
                $links['güvenlik kuralları'] = '/blog/' . $post->slug;
                $links['güvenli kullanım'] = '/blog/' . $post->slug;
            }
            if (str_contains($post->slug, 'hata-ve-dogru-secim')) {
                $links['patenli ayakkabı alırken en sık yapılan 12 hata ve doğru seçim rehberi'] = '/blog/' . $post->slug;
                $links['doğru seçim'] = '/blog/' . $post->slug;
                $links['sık yapılan hatalar'] = '/blog/' . $post->slug;
            }
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

            // Önceki script çalışmalarından kalan otomatik eklenmiş TÜM linkleri temizle (sadece bizim eklediğimiz class'a sahip olanları)
            // Böylece sadece text kalır ve aşağıda güncel aktif listeye göre yeniden linklenir.
            $cleanPattern = '/<a href="[^"]*" class="text-teal-600 font-semibold hover:underline" title="[^"]*">(.*?)<\/a>/ui';
            $newContent = preg_replace($cleanPattern, '$1', $content);
            
            if ($newContent !== null && $newContent !== $content) {
                $content = $newContent;
                $modified = true;
            }

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
                }, $content, -1); // Tüm eşleşmeleri linkle
                
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
