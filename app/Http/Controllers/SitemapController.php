<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

/**
 * Dinamik XML Sitemap Üreteci
 *
 * Google Search Console ve diğer arama motorları için
 * otomatik güncellenen XML sitemap dosyaları üretir.
 *
 * Sitemap Index yapısı:
 *   /sitemap.xml              → Ana index
 *   /sitemap-products.xml     → Ürünler (görseller dahil)
 *   /sitemap-categories.xml   → Kategoriler
 *   /sitemap-pages.xml        → Statik sayfalar
 *   /sitemap-blog.xml         → Blog yazıları
 */
class SitemapController extends Controller
{
    /**
     * Sitemap Index — Ana sitemap dosyası
     *
     * Tüm alt sitemap'leri ve son güncelleme tarihlerini listeler.
     * GET /sitemap.xml
     */
    public function index(): Response
    {
        $appUrl = config('app.url');

        // Her içerik tipinin son güncelleme tarihi
        $productLastmod  = Product::where('status', true)->whereNull('deleted_at')->max('updated_at');
        $categoryLastmod = Category::where('status', true)->max('updated_at');
        $pageLastmod     = Page::where('is_active', true)->max('updated_at');
        $blogLastmod     = BlogPost::where('status', true)->max('updated_at');

        $sitemaps = [];

        // Ürün sitemap'i (veri varsa)
        if ($productLastmod) {
            $sitemaps[] = [
                'loc'     => $appUrl . '/sitemap-products.xml',
                'lastmod' => \Carbon\Carbon::parse($productLastmod)->toW3cString(),
            ];
        }

        // Kategori sitemap'i (veri varsa)
        if ($categoryLastmod) {
            $sitemaps[] = [
                'loc'     => $appUrl . '/sitemap-categories.xml',
                'lastmod' => \Carbon\Carbon::parse($categoryLastmod)->toW3cString(),
            ];
        }

        // Sayfa sitemap'i (veri varsa)
        if ($pageLastmod) {
            $sitemaps[] = [
                'loc'     => $appUrl . '/sitemap-pages.xml',
                'lastmod' => \Carbon\Carbon::parse($pageLastmod)->toW3cString(),
            ];
        }

        // Blog sitemap'i (veri varsa)
        if ($blogLastmod) {
            $sitemaps[] = [
                'loc'     => $appUrl . '/sitemap-blog.xml',
                'lastmod' => \Carbon\Carbon::parse($blogLastmod)->toW3cString(),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sitemaps as $sitemap) {
            $xml .= '  <sitemap>' . "\n";
            $xml .= '    <loc>' . $sitemap['loc'] . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $sitemap['lastmod'] . '</lastmod>' . "\n";
            $xml .= '  </sitemap>' . "\n";
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Ürün Sitemap'i — Aktif ürünler (görseller dahil)
     *
     * image:image namespace kullanarak Google Image aramasında
     * ürün görsellerinin indexlenmesini sağlar.
     *
     * GET /sitemap-products.xml
     */
    public function products(): Response
    {
        $products = Product::where('status', true)
            ->whereNull('deleted_at')
            ->with('images')
            ->orderBy('updated_at', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($products as $product) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . url('/urun/' . $product->slug) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $product->updated_at->toW3cString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";

            // Ürün görselleri (Google Image indexleme)
            foreach ($product->images as $image) {
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . htmlspecialchars($image->image_url, ENT_XML1) . '</image:loc>' . "\n";
                $xml .= '      <image:title>' . htmlspecialchars($image->alt_text ?: $product->name, ENT_XML1) . '</image:title>' . "\n";
                $xml .= '    </image:image>' . "\n";
            }

            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Kategori Sitemap'i — Aktif kategoriler
     *
     * GET /sitemap-categories.xml
     */
    public function categories(): Response
    {
        $categories = Category::where('status', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($categories as $category) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . url('/kategori/' . $category->slug) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $category->updated_at->toW3cString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.7</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Sayfa Sitemap'i — Aktif statik sayfalar
     *
     * GET /sitemap-pages.xml
     */
    public function pages(): Response
    {
        $pages = Page::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($pages as $page) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . url('/sayfa/' . $page->slug) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $page->updated_at->toW3cString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.5</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Blog Sitemap'i — Aktif blog yazıları
     *
     * GET /sitemap-blog.xml
     */
    public function blog(): Response
    {
        $posts = BlogPost::where('status', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($posts as $post) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . url('/blog/' . $post->slug) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $post->updated_at->toW3cString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
