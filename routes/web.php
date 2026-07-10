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
// DEPLOY HELPER (Sunucu komutları + teşhis)
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

    // 2. Cache temizle
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        $results[] = '✅ config + view + route cache temizlendi';
    } catch (\Exception $e) {
        $results[] = '⚠️ cache: ' . $e->getMessage();
    }

    // 3. Symlink kontrol
    $symlinkPath = public_path('storage');
    if (is_link($symlinkPath)) {
        $target = readlink($symlinkPath);
        $results[] = "✅ public/storage → symlink → {$target}";
    } elseif (is_dir($symlinkPath)) {
        $results[] = '⚠️ public/storage → gerçek klasör (symlink değil)';
    } else {
        $results[] = '❌ public/storage YOK!';
    }

    // 4. Storage yolları
    $results[] = '';
    $results[] = '=== YOLLAR ===';
    $results[] = 'storage_path: ' . storage_path();
    $results[] = 'storage/app/public: ' . storage_path('app/public');
    $results[] = 'public_path: ' . public_path();
    $results[] = 'public/storage var mı: ' . (file_exists(public_path('storage')) ? 'EVET' : 'HAYIR');

    // 5. Disk config
    $results[] = '';
    $results[] = '=== FILESYSTEM ===';
    $defaultDisk = config('filesystems.default');
    $results[] = 'Default disk: ' . $defaultDisk;
    $publicRoot = config('filesystems.disks.public.root');
    $results[] = 'Public disk root: ' . ($publicRoot ?? 'TANIMLANMAMIŞ');

    // 6. Storage içeriği
    $results[] = '';
    $results[] = '=== STORAGE İÇERİĞİ ===';
    $storagePub = storage_path('app/public');
    if (is_dir($storagePub)) {
        $dirs = array_filter(scandir($storagePub), fn($f) => $f !== '.' && $f !== '..');
        foreach ($dirs as $d) {
            $full = $storagePub . '/' . $d;
            if (is_dir($full)) {
                $fileCount = count(array_diff(scandir($full), ['.', '..']));
                $results[] = "📁 {$d}/ → {$fileCount} dosya";
            } else {
                $results[] = "📄 {$d} → " . filesize($full) . ' byte';
            }
        }
    } else {
        $results[] = '❌ storage/app/public YOK!';
    }

    // 7. Blog post teşhis
    $results[] = '';
    $results[] = '=== BLOG POST TEŞHİS ===';
    $posts = \App\Models\BlogPost::all(['id', 'title', 'image_path']);
    foreach ($posts as $p) {
        $results[] = "Post #{$p->id}: {$p->title}";
        $results[] = "  image_path: " . ($p->image_path ?: '(BOŞ)');
        if ($p->image_path) {
            $diskPath = storage_path('app/public/' . $p->image_path);
            $exists = file_exists($diskPath);
            $results[] = "  Dosya yolu: {$diskPath}";
            $results[] = "  Dosya var mı: " . ($exists ? '✅ EVET (' . filesize($diskPath) . ' byte)' : '❌ HAYIR');
            $results[] = "  URL: " . \Storage::disk('public')->url($p->image_path);
        }
    }

    // 8. Livewire-tmp kontrol
    $tmpDir = storage_path('app/public/livewire-tmp');
    if (is_dir($tmpDir)) {
        $tmpFiles = array_diff(scandir($tmpDir), ['.', '..']);
        $results[] = '';
        $results[] = '=== LIVEWIRE-TMP ===';
        $results[] = count($tmpFiles) . ' geçici dosya var';
        foreach (array_slice($tmpFiles, 0, 5) as $f) {
            $results[] = "  {$f}";
        }
    }

    return '<html><head><title>Deploy Helper</title></head><body style="font-family:monospace;padding:40px;background:#111;color:#eee;font-size:14px;line-height:1.8;">'
         . '<h1 style="color:#0d9488;">🚀 Deploy Helper - Tam Teşhis</h1>'
         . '<pre style="white-space:pre-wrap;word-break:break-all;">' . implode("\n", $results) . '</pre>'
         . '<br><a href="/deploy-fix-storage" style="color:#0d9488;font-size:16px;">👉 Görselleri private → public taşı</a>'
         . '</body></html>';
})->middleware('auth');

// Dosyaları private → public taşı
Route::get('/deploy-fix-storage', function () {
    $results = [];
    $privatePath = storage_path('app/private');
    $publicPath = storage_path('app/public');

    if (!is_dir($privatePath)) {
        return 'private klasörü yok, taşınacak dosya bulunamadı.';
    }

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($privatePath, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $relativePath = str_replace($privatePath . DIRECTORY_SEPARATOR, '', $item->getPathname());
        $targetPath = $publicPath . DIRECTORY_SEPARATOR . $relativePath;

        if ($item->isDir()) {
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
                $results[] = "📁 Klasör oluşturuldu: {$relativePath}";
            }
        } else {
            $targetDir = dirname($targetPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            copy($item->getPathname(), $targetPath);
            $results[] = "✅ Taşındı: {$relativePath} (" . round($item->getSize() / 1024) . " KB)";
        }
    }

    if (empty($results)) {
        $results[] = 'Taşınacak dosya bulunamadı.';
    }

    return '<html><head><title>Storage Fix</title></head><body style="font-family:monospace;padding:40px;background:#111;color:#eee;font-size:14px;line-height:1.8;">'
         . '<h1 style="color:#0d9488;">📦 Private → Public Taşıma</h1>'
         . '<pre>' . implode("\n", $results) . '</pre>'
         . '<br><a href="/deploy-helper" style="color:#0d9488;">← Teşhis sayfasına dön</a>'
         . '</body></html>';
})->middleware('auth');
