<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Frontend\Contact;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Account\Dashboard;
use App\Livewire\Account\Profile;
use App\Livewire\Account\Orders;

// ========================
// STORAGE FILE SERVE (RoadRunner symlink desteği olmadığı için)
// ========================
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mime = mime_content_type($fullPath);
    $size = filesize($fullPath);

    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Content-Length' => $size,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    
    // Google OAuth Routes
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/hesabim', Dashboard::class)->name('account.dashboard');
    Route::get('/hesabim/profil', Profile::class)->name('account.profile');
    Route::get('/hesabim/siparisler', Orders::class)->name('account.orders');
});

Route::get('/checkout', App\Livewire\Frontend\Checkout::class)->name('checkout');
Route::get('/order/success/{order_number}', App\Livewire\Frontend\OrderSuccess::class)->name('order.success');
Route::get('/iletisim', App\Livewire\Frontend\Contact::class)->name('contact');

// ========================
// ÜRÜN & KATEGORİ SAYFALARI
// ========================

// Tüm ürünler
Route::get('/patenli-ayakkabilar', function (\Illuminate\Http\Request $request) {
    $category = $request->has('category') ? \App\Models\Category::where('slug', $request->category)->first() : null;
    return view('products.index', compact('category'));
})->name('products.index');

// Kategori sayfası (SEO-friendly URL)
Route::get('/kategori/{slug}', function ($slug) {
    $category = \App\Models\Category::where('slug', $slug)->where('status', true)->firstOrFail();
    return view('products.index', compact('category'));
})->name('category.show');

Route::redirect('/urunler', '/patenli-ayakkabilar', 301);

// Ürün detay
Route::get('/urun/{slug}', function ($slug) {
    $product = \App\Models\Product::where('slug', $slug)
        ->with(['variants', 'images', 'category', 'reviews' => function ($q) { $q->where('status', true); }, 'features'])
        ->firstOrFail();
    return view('products.show', ['product' => $product]);
})->name('products.show');

// ========================
// SİPARİŞ TAKİP
// ========================
Route::get('/siparis-takip', function () {
    return view('order.tracking');
})->name('order.tracking');

// ========================
// KURUMSAL SAYFALAR
// ========================
Route::get('/sayfa/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    return view('pages.show', ['page' => $page]);
})->name('pages.show');

// ========================
// BLOG / REHBER MERKEZİ
// ========================
Route::get('/blog', function () {
    $posts = \App\Models\BlogPost::where('status', true)
        ->orderByDesc('created_at')
        ->paginate(12);
    return view('blog.index', compact('posts'));
})->name('blog.index');

Route::get('/blog/{slug}', function ($slug) {
    $post = \App\Models\BlogPost::where('slug', $slug)->where('status', true)->firstOrFail();
    return view('blog.show', compact('post'));
})->name('blog.show');

// ========================
// LANSMAN
// ========================
Route::view('/lansman', 'lansman')->name('lansman');

// ========================
// SEO: SİTEMAP & FEED
// ========================
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-products.xml', [\App\Http\Controllers\SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/sitemap-categories.xml', [\App\Http\Controllers\SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-pages.xml', [\App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-blog.xml', [\App\Http\Controllers\SitemapController::class, 'blog'])->name('sitemap.blog');

// ==========================================
// SEO: Google Merchant Center Feed
// ==========================================
Route::get('/feeds/google-merchant.xml', [\App\Http\Controllers\MerchantFeedController::class, 'index'])->name('feed.merchant');

// ========================
// GELİŞTİRİCİ (Sadece local)
// ========================
if (app()->environment('local')) {
    Route::get('/run-migrate', function (\Illuminate\Http\Request $request) {
        try {
            $params = ['--force' => true];
            if ($request->query('path')) {
                $params['--path'] = $request->query('path');
            }
            \Illuminate\Support\Facades\Artisan::call('migrate', $params);
            $output = \Illuminate\Support\Facades\Artisan::output();
            return response()->json([
                'status' => 'success',
                'output' => $output,
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
    });
}

// ========================
// DEPLOY HELPER (Sunucu komutları)
// ========================
Route::get('/deploy-helper', function () {
    $results = [];

    // 1. Storage Link
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $results[] = '✅ storage:link başarılı';
    } catch (\Exception $e) {
        $results[] = '⚠️ storage:link: ' . $e->getMessage();
    }

    // 2. Config Cache temizle
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        $results[] = '✅ config:clear başarılı';
    } catch (\Exception $e) {
        $results[] = '⚠️ config:clear: ' . $e->getMessage();
    }

    // 3. View Cache temizle
    try {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $results[] = '✅ view:clear başarılı';
    } catch (\Exception $e) {
        $results[] = '⚠️ view:clear: ' . $e->getMessage();
    }

    // 4. Route Cache temizle
    try {
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        $results[] = '✅ route:clear başarılı';
    } catch (\Exception $e) {
        $results[] = '⚠️ route:clear: ' . $e->getMessage();
    }

    // 5. Symlink kontrol
    $symlinkPath = public_path('storage');
    if (is_link($symlinkPath) || is_dir($symlinkPath)) {
        $results[] = '✅ public/storage symlink mevcut';
    } else {
        $results[] = '❌ public/storage symlink YOK!';
    }

    // 6. Blog dosya kontrol
    $blogDir = storage_path('app/public/blog');
    if (is_dir($blogDir)) {
        $files = scandir($blogDir);
        $count = count(array_diff($files, ['.', '..']));
        $results[] = "✅ storage/app/public/blog/ → {$count} dosya var";
    } else {
        $results[] = '❌ storage/app/public/blog/ klasörü YOK!';
    }

    return '<html><head><title>Deploy Helper</title></head><body style="font-family:monospace;padding:40px;background:#111;color:#eee;font-size:16px;line-height:2;">'
         . '<h1 style="color:#0d9488;">🚀 Deploy Helper</h1>'
         . '<pre>' . implode("\n", $results) . '</pre>'
         . '<br><p style="color:#666;">Bu sayfayı deploy sonrası bir kez ziyaret edin, sonra silebilirsiniz.</p>'
         . '</body></html>';
})->middleware('auth');
