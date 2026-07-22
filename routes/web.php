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

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return 'Harika! Sitenin tüm önbelleği (cache ve görünümler) başarıyla temizlendi. Lütfen ana sayfaya dönüp CTRL+F5 ile yenileyin.';
});

Route::get('/run-migrations', function () {
    $pages = [
        'hakkimizda' => [
            'title' => 'Hakkımızda',
            'content' => '<h2>Biz Kimiz?</h2><p>Patenli Ayakkabılar olarak, çocukların eğlenirken güvende olmasını sağlamak ve onlara unutulmaz bir deneyim yaşatmak için yola çıktık. 2026 yılında kurulan şirketimiz, yenilikçi ve kaliteli ışıklı, tekerlekli ayakkabı modellerini Türkiye\'nin dört bir yanındaki miniklerle buluşturmaktadır.</p><p>Misyonumuz, ebeveynlerin güvenle tercih edebileceği, çocukların ise yüzünde kocaman bir gülümsemeyle giyeceği ürünler sunmaktır. Ürünlerimizin tamamı uluslararası güvenlik standartlarına uygun olarak üretilmekte ve titiz testlerden geçirilmektedir.</p><h2>Neden Bizi Seçmelisiniz?</h2><ul><li><strong>Güvenlik Önceliğimiz:</strong> Tüm modellerimiz ekstra dayanıklı malzemeler ve güvenli frenleme sistemleri ile donatılmıştır.</li><li><strong>Hızlı Teslimat:</strong> Siparişlerinizi en hızlı şekilde, özenle paketleyerek kapınıza kadar ulaştırıyoruz.</li><li><strong>Müşteri Memnuniyeti:</strong> 7/24 aktif müşteri hizmetlerimizle satış öncesi ve sonrası her zaman yanınızdayız.</li></ul>'
        ],
        'gizlilik-politikasi' => [
            'title' => 'Gizlilik Politikası',
            'content' => '<h2>Kişisel Verilerin Korunması</h2><p>Patenli Ayakkabılar (Bundan böyle "Şirket" olarak anılacaktır), müşterilerimizin kişisel verilerinin gizliliğine ve güvenliğine son derece önem vermektedir. 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) uyarınca, web sitemiz (patenliayakkabilar.com) üzerinden bizimle paylaştığınız tüm kişisel verileriniz büyük bir hassasiyetle korunmakta ve yalnızca size daha iyi hizmet verebilmek amacıyla işlenmektedir.</p><h2>Toplanan Veriler ve Kullanım Amacı</h2><p>Sipariş süreçlerini yönetmek, kargo işlemlerini gerçekleştirmek ve kampanyalarımızdan sizleri haberdar etmek amacıyla ad, soyad, adres, e-posta ve telefon numarası gibi temel bilgilerinizi toplamaktayız. Kredi kartı ve ödeme bilgileriniz kesinlikle sunucularımızda saklanmaz, doğrudan güvenli ödeme altyapısı (BDDK onaylı kuruluşlar) üzerinden işlenir.</p><h2>Çerezler (Cookies)</h2><p>Alışveriş deneyiminizi iyileştirmek için çerezler kullanmaktayız. Tarayıcı ayarlarınızdan çerez kullanımını dilediğiniz zaman sınırlandırabilirsiniz.</p>'
        ],
        'iade-ve-degisim' => [
            'title' => 'İade ve Değişim Koşulları',
            'content' => '<h2>Kolay İade ve Değişim</h2><p>Satın almış olduğunuz ürünleri, teslimat tarihinden itibaren <strong>14 gün içerisinde</strong> hiçbir gerekçe göstermeksizin iade edebilir veya numara/model değişimi talep edebilirsiniz.</p><h2>İade/Değişim Şartları</h2><ul><li>Ürünün kullanılmamış, etiketlerinin koparılmamış ve orijinal kutusunun zarar görmemiş olması gerekmektedir.</li><li>Dışarıda (sokak, asfalt vb.) kullanılmış, tekerlekleri aşınmış veya çizilmiş ürünlerin iadesi hijyen ve yeniden satılabilirlik kuralları gereği kabul edilmemektedir. Sadece ev içinde (halı üzerinde) numara denemesi yapılmalıdır.</li><li>İade kargo ücretleri, anlaşmalı kargo kodumuz ile gönderildiği takdirde şirketimize aittir.</li></ul><p>İade veya değişim talebi oluşturmak için "Sipariş Takip" sayfasından işleminizi başlatabilir veya müşteri hizmetlerimizle iletişime geçebilirsiniz.</p>'
        ],
        'mesafeli-satis-sozlesmesi' => [
            'title' => 'Mesafeli Satış Sözleşmesi',
            'content' => '<h2>Madde 1 - Taraflar</h2><p>İşbu sözleşme, bir tarafta patenliayakkabilar.com web sitesini işleten Satıcı ile diğer tarafta site üzerinden sipariş veren Alıcı (Müşteri) arasında dijital ortamda onaylanarak yürürlüğe girmiştir.</p><h2>Madde 2 - Konu</h2><p>İşbu sözleşmenin konusu, Alıcı\'nın Satıcı\'ya ait web sitesinden elektronik ortamda siparişini yaptığı aşağıda nitelikleri ve satış fiyatı belirtilen ürünün satışı ve teslimi ile ilgili olarak 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği hükümleri gereğince tarafların hak ve yükümlülüklerinin saptanmasıdır.</p><h2>Madde 3 - Teslimat</h2><p>Ürün, Alıcı\'nın sipariş formunda belirttiği teslimat adresine, faturası ile birlikte paketlenmiş ve sağlam olarak en geç 3 iş günü içinde kargoya teslim edilir. Kargo firmasından kaynaklanan gecikmelerden Satıcı sorumlu tutulamaz.</p>'
        ],
        'sikca-sorulan-sorular' => [
            'title' => 'Sıkça Sorulan Sorular',
            'content' => '<h2>Sipariş ve Kargo</h2><p><strong>Siparişim ne zaman kargoya verilir?</strong><br>Saat 14:00\'e kadar verilen siparişler aynı gün, 14:00\'ten sonra verilen siparişler ise ertesi iş günü kargoya teslim edilmektedir.</p><p><strong>Hangi kargo şirketi ile çalışıyorsunuz?</strong><br>Türkiye\'nin her yerine Yurtiçi Kargo ve Aras Kargo güvencesiyle teslimat yapmaktayız.</p><h2>Ürün Kullanımı</h2><p><strong>Patenli ayakkabılar normal ayakkabı olarak kullanılabilir mi?</strong><br>Evet! Tüm modellerimizin tabanındaki gizli mekanizma sayesinde, tekerleği içeri gizleyerek ürünü günlük normal bir spor ayakkabı gibi kullanabilirsiniz. Arka kısımdaki butona basmanız yeterlidir.</p><p><strong>Işıkların şarjı ne kadar dayanır? Nasıl şarj edilir?</strong><br>Şarjlı modellerimiz kutu içerisinden çıkan çift uçlu USB kablo ile şarj edilir. Yaklaşık 2 saatlik şarj ile ortalama 6-8 saat boyunca aralıksız ışık yanabilmektedir.</p>'
        ]
    ];

    foreach ($pages as $slug => $data) {
        $page = \App\Models\Page::firstOrCreate(
            ['slug' => $slug],
            ['title' => $data['title']]
        );
        $page->content = $data['content'];
        $page->is_active = true;
        $page->save();
    }
    
    return 'Harika! Kurumsal sayfalar veritabanına başarıyla eklendi. Artık admin panelinden sayfaları görebilirsiniz.';
});

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/sifremi-unuttum', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/sifreyi-sifirla/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');
    
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

// PayTR Ödeme Rotaları
Route::any('/payment/paytr/success', [\App\Http\Controllers\Payment\PaytrWebhookController::class, 'success'])->name('payment.paytr.success');
Route::any('/payment/paytr/fail', [\App\Http\Controllers\Payment\PaytrWebhookController::class, 'fail'])->name('payment.paytr.fail');
Route::post('/payment/paytr/webhook', [\App\Http\Controllers\Payment\PaytrWebhookController::class, 'webhook'])->name('payment.paytr.webhook');

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

// Ürün Yorumları (Tümünü Gör)
Route::get('/urun/{slug}/yorumlar', function ($slug) {
    $product = \App\Models\Product::where('slug', $slug)->firstOrFail();
    $reviews = $product->reviews()->where('status', true)->latest('id')->paginate(20);
    $averageRating = $product->reviews()->where('status', true)->avg('rating') ?? 5.0;
    
    return view('products.reviews', compact('product', 'reviews', 'averageRating'));
})->name('products.reviews');
// ========================
// SİPARİŞ TAKİP
// ========================
Route::get('/siparis-takip', function (\Illuminate\Http\Request $request) {
    $order = null;
    $error = null;
    
    if ($request->has('order_number')) {
        $orderNumber = trim($request->input('order_number'));
        $order = \App\Models\Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            $error = 'Girdiğiniz sipariş numarasına ait bir kayıt bulunamadı.';
        }
    }
    
    return view('order.tracking', compact('order', 'error'));
})->name('order.tracking');

// ========================
// KURUMSAL SAYFALAR
// ========================
// (Kurumsal sayfalar rotası seo açısından en alta taşındı)

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
// GELİŞTİRİCİ (Canlıda ve lokalde kullanılabilir)
// ========================
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

// Sayfaları canlı sunucuda oluşturmak için
Route::get('/deploy-add-pages', function () {
    // --- 1. KURUMSAL SAYFALAR ---
    
    $bedenRehberi = '<div class="space-y-6 text-gray-700 leading-relaxed">
    <h2>Doğru Bedeni Seçmek Neden Önemli?</h2>
    <p>Patenli ayakkabılarda doğru bedeni seçmek, hem çocuğunuzun ayak sağlığı hem de sürüş güvenliği açısından büyük önem taşır. Ayakkabının çok bol veya çok dar olması, denge kaybına ve rahatsızlığa neden olabilir.</p>

    <h3>Ayak Ölçümü Nasıl Yapılır?</h3>
    <ol class="list-decimal pl-5 space-y-2">
        <li>Çocuğunuzun topuğunu düz bir duvara dayayarak temiz bir kağıdın üzerine bastırın.</li>
        <li>En uzun parmağının ucunu kağıt üzerinde işaretleyin.</li>
        <li>Duvar ile işaretlediğiniz nokta arasındaki mesafeyi bir cetvel yardımıyla ölçün (cm cinsinden).</li>
    </ol>

    <div class="bg-blue-50 p-4 rounded-lg my-6 border border-blue-100">
        <strong>💡 Önemli İpucu:</strong> Patenli ayakkabılarda kalın çorap kullanımı ve ayak şişme payı göz önüne alınarak, ölçtüğünüz ayak uzunluğuna <strong>0.5 cm - 1 cm ekleyerek</strong> beden seçimi yapmanızı tavsiye ederiz. Genellikle normal spor ayakkabı numarasından <strong>1 numara büyük</strong> tercih edilmesi önerilir.
    </div>

    <h3>Örnek Beden Tablosu</h3>
    <div class="overflow-x-auto my-4">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="py-3 px-4 border-b text-left font-semibold text-gray-600">Ayak Uzunluğu (cm)</th>
                    <th class="py-3 px-4 border-b text-left font-semibold text-gray-600">Önerilen Beden (EU)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="py-2 px-4 border-b">17.5 cm - 18.0 cm</td><td class="py-2 px-4 border-b">28</td></tr>
                <tr><td class="py-2 px-4 border-b">18.1 cm - 18.7 cm</td><td class="py-2 px-4 border-b">29</td></tr>
                <tr><td class="py-2 px-4 border-b">18.8 cm - 19.3 cm</td><td class="py-2 px-4 border-b">30</td></tr>
                <tr><td class="py-2 px-4 border-b">19.4 cm - 20.0 cm</td><td class="py-2 px-4 border-b">31</td></tr>
                <tr><td class="py-2 px-4 border-b">20.1 cm - 20.6 cm</td><td class="py-2 px-4 border-b">32</td></tr>
                <tr><td class="py-2 px-4 border-b">20.7 cm - 21.3 cm</td><td class="py-2 px-4 border-b">33</td></tr>
                <tr><td class="py-2 px-4 border-b">21.4 cm - 22.0 cm</td><td class="py-2 px-4 border-b">34</td></tr>
                <tr><td class="py-2 px-4 border-b">22.1 cm - 22.6 cm</td><td class="py-2 px-4 border-b">35</td></tr>
                <tr><td class="py-2 px-4 border-b">22.7 cm - 23.3 cm</td><td class="py-2 px-4 border-b">36</td></tr>
            </tbody>
        </table>
    </div>
</div>';

    $guvenlikEkipmanlari = '<div class="space-y-6 text-gray-700 leading-relaxed">
    <h2>Güvenli Bir Sürüş İçin Olmazsa Olmazlar</h2>
    <p>Patenli ayakkabı kullanırken, özellikle öğrenme aşamasında çocukların düşmesi veya dengelerini kaybetmesi son derece doğaldır. Olası yaralanmaların önüne geçmek ve çocukların korkmadan eğlenebilmesini sağlamak için doğru güvenlik ekipmanlarının kullanılması şarttır.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-teal-600 mb-2">🛡️ Kask</h3>
            <p class="text-sm">En kritik koruyucu ekipmandır. Düşme anında baş bölgesini darbelere karşı korur. Kaskın çocuğun kafasına tam oturduğundan ve çene altı kayışının sıkıca bağlandığından emin olun.</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-teal-600 mb-2">🧤 Bileklik (Avuç İçi Koruyucu)</h3>
            <p class="text-sm">Düşerken refleks olarak ilk ellerimizi yere koyarız. Bileklikler, bilek burkulmalarını ve avuç içi yaralanmalarını önleyen sert plastik desteklere sahiptir.</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-teal-600 mb-2">🦵 Dizlik</h3>
            <p class="text-sm">Diz kapakları düşmelerde en çok darbe alan bölgelerden biridir. Sünger destekli ve sert plastik dış yüzeyli dizlikler, dizleri sürtünme ve çarpmalardan korur.</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-teal-600 mb-2">💪 Dirseklik</h3>
            <p class="text-sm">Denge kaybında geriye veya yana düşmelerde dirsekler zarar görebilir. Dirseklikler, hareket kabiliyetini kısıtlamadan eklem bölgesini güvende tutar.</p>
        </div>
    </div>

    <h3>Ekipman Seçiminde Dikkat Edilmesi Gerekenler</h3>
    <ul class="list-disc pl-5 space-y-2">
        <li><strong>Beden Uyumu:</strong> Koruyucular vücudu çok sıkmamalı ama aynı zamanda kayıp düşmeyecek kadar tam oturmalıdır.</li>
        <li><strong>CE Sertifikası:</strong> Ürünlerin Avrupa güvenlik standartlarına (CE) uygun olduğunu kontrol edin.</li>
        <li><strong>Havalandırma:</strong> Terlemeyi önlemek için hava alabilen malzemeden üretilmiş koruyucuları tercih edin.</li>
    </ul>

    <div class="bg-red-50 text-red-700 p-4 rounded-lg my-6 border border-red-100">
        <strong>Uyarı:</strong> Çocuklar patenli ayakkabılarını sürüş modunda (tekerlekleri açıkken) mutlaka düz, güvenli ve trafiğe kapalı alanlarda, yetişkin gözetiminde kullanmalıdır.
    </div>
</div>';

    $kargoIade = '<div class="space-y-6 text-gray-700 leading-relaxed">
    <h2>Kargo Teslimat Süreçleri</h2>
    <p>Siparişleriniz onaylandıktan sonra en geç <strong>2 iş günü</strong> içerisinde özenle paketlenerek anlaşmalı kargo firmamıza teslim edilir. Kargo takip numaranız, paketiniz yola çıktığında SMS ve E-posta yoluyla size bildirilir.</p>
    
    <ul class="list-disc pl-5 space-y-2">
        <li>Standart teslimat süresi, bulunduğunuz şehre bağlı olarak 1-3 iş günüdür.</li>
        <li>Hafta sonu (Cumartesi - Pazar) ve resmi tatillerde verilen siparişler, takip eden ilk iş günü işleme alınır.</li>
    </ul>

    <h2 class="mt-8">İade ve Değişim Koşulları</h2>
    <p>Müşteri memnuniyeti bizim için önceliktir. Satın aldığınız ürünü, teslimat tarihinden itibaren <strong>14 gün içerisinde</strong> iade edebilir veya beden/renk değişimi talep edebilirsiniz.</p>
    
    <div class="bg-orange-50 p-4 rounded-lg my-6 border border-orange-100">
        <h4 class="font-bold text-orange-800 mb-2">İade ve Değişim Şartları:</h4>
        <ul class="list-decimal pl-5 space-y-2 text-sm text-orange-900">
            <li>Ürünün kullanılmamış, etiketleri koparılmamış ve orijinal kutusu/ambalajı bozulmamış olmalıdır.</li>
            <li>Patenli ayakkabı tekerleklerinde sürüş kaynaklı aşınma olmamasına dikkat edilmelidir. Ürünleri dışarıda (asfalt vs.) test etmeden önce ev içinde halı üzerinde denemenizi rica ederiz.</li>
            <li>İade kargo masrafları, tarafımızca sağlanan anlaşmalı kargo kodu ile gönderildiğinde firmamıza aittir.</li>
        </ul>
    </div>
    
    <h3>Nasıl İade Edebilirim?</h3>
    <p>İade veya değişim talebinizi oluşturmak için <a href="/iletisim" class="text-teal-600 underline">iletişim sayfamızdan</a> veya WhatsApp destek hattımızdan bize ulaşabilirsiniz. Size verilecek kargo kodu ile ürünü en yakın şubeye teslim etmeniz yeterlidir.</p>
</div>';

    \App\Models\Page::updateOrCreate(
        ['slug' => 'beden-rehberi'],
        ['title' => 'Patenli Ayakkabı Beden Rehberi', 'content' => $bedenRehberi, 'is_active' => true]
    );

    \App\Models\Page::updateOrCreate(
        ['slug' => 'guvenlik-ekipmanlari'],
        ['title' => 'Patenli Ayakkabı Güvenlik Ekipmanları', 'content' => $guvenlikEkipmanlari, 'is_active' => true]
    );

    \App\Models\Page::updateOrCreate(
        ['slug' => 'kargo-ve-iade-kosullari'],
        ['title' => 'Kargo ve İade Koşulları', 'content' => $kargoIade, 'is_active' => true]
    );

    // --- 2. KATEGORİLER ---

    \App\Models\Category::updateOrCreate(
        ['slug' => 'patenli-ayakkabi-modelleri'],
        ['name' => 'Patenli Ayakkabı Modelleri', 'status' => true]
    );

    \App\Models\Category::updateOrCreate(
        ['slug' => 'cocuk-patenli-ayakkabi-modelleri'],
        ['name' => 'Çocuk Patenli Ayakkabı Modelleri', 'status' => true]
    );

    return '<html><head><title>İçerikler Eklendi</title></head><body style="font-family:monospace;padding:40px;background:#111;color:#eee;font-size:16px;line-height:2;">'
         . '<h1 style="color:#0d9488;">✅ Sayfalar ve Kategoriler Başarıyla Eklendi!</h1>'
         . '<p>Şu anda aşağıdaki içerikler sisteme dahil edildi:</p>'
         . '<ul style="padding-left:20px;margin:20px 0;">'
         . '<li>Sayfa: Patenli Ayakkabı Beden Rehberi</li>'
         . '<li>Sayfa: Patenli Ayakkabı Güvenlik Ekipmanları</li>'
         . '<li>Sayfa: Kargo ve İade Koşulları</li>'
         . '<li>Kategori: Patenli Ayakkabı Modelleri</li>'
         . '<li>Kategori: Çocuk Patenli Ayakkabı Modelleri</li>'
         . '</ul>'
         . '<br><a href="/admin/pages" style="color:#0d9488;font-size:16px;margin-right:20px;">👉 Admin Sayfalar</a>'
         . '<a href="/admin/categories" style="color:#0d9488;font-size:16px;">👉 Admin Kategoriler</a>'
         . '</body></html>';
})->middleware('auth');

// Canlı sunucuya (veya lokale) sahte yorumları eklemek için
Route::get('/deploy-add-reviews', function () {
    $products = \App\Models\Product::all();
    if ($products->count() === 0) {
        return "Ürün bulunamadı.";
    }

    $names = [
        'Ayşe Y.', 'Fatma K.', 'Zeynep D.', 'Elif Ç.', 'Merve Ş.', 'Esra Y.', 'Büşra Y.', 'Ceren Ö.', 'Selin A.', 'Tuğba Ö.',
        'Gizem A.', 'Berna D.', 'Gamze K.', 'Derya A.', 'Bahar Ç.', 'Yasemin K.', 'Dilek K.', 'Burcu K.', 'Aylin Ö.', 'Cansu Ş.',
        'Melis P.', 'Ece Ö.', 'Hande K.', 'İrem Ç.', 'Pelin E.', 'Sedef Y.', 'Tuğçe C.', 'Pınar A.', 'Özge Y.', 'Sibel G.',
        'Mehmet K.', 'Mustafa D.', 'Ahmet Ç.', 'Ali Ş.', 'Hüseyin Y.', 'Hasan Y.', 'İbrahim Ö.', 'Murat A.', 'Volkan Ö.', 'Emre A.',
        'Burak D.', 'Gökhan K.', 'Fatih A.', 'Hakan Ç.', 'Tolga K.', 'Oğuzhan K.', 'Kemal K.', 'Enes Ö.', 'Yasin Ş.', 'Serkan P.'
    ];

    $comments = [
        "Kızım bayıldı, ayağından çıkarmak istemiyor. Renkleri de görseldeki gibi çok canlı.",
        "Tekerlek mekanizması çok pratik. Çocuğum tek hamlede patenden ayakkabıya çevirebiliyor.",
        "Oğlum için aldım. Kargolama hızı çok iyiydi. Teşekkür ederiz.",
        "Ayakkabının kalitesi beklediğimden çok daha iyi çıktı. Sadece kalıpları biraz dar, 1 numara büyük alınabilir.",
        "Hem ayakkabı hem paten olması harika bir tasarım. AVM'lerde gezerken çok pratik oluyor.",
        "Işıkları çok canlı yanıyor. Akşamları parkta kullanırken hem çok eğlenceli hem de güvenli.",
        "Kızımın doğum günü için hediye almıştım. Hayatında aldığı en güzel hediye olduğunu söyledi. Satıcıya çok teşekkürler.",
        "Ürün elime iki günde ulaştı. Paketleme özenliydi. Kullanımı da anlatıldığı kadar kolaymış.",
        "Tekerlekler gayet sağlam. Asfaltta sürerken hiç sarsmıyor.",
        "Dışarıda paten olarak, okulda ayakkabı olarak kullanıyor. Gerçekten tam bir fiyat performans ürünü.",
        "Beklediğimizden biraz daha ağır ama sanırım paten mekanizmasından kaynaklı. Oğlum çok sevdi.",
        "Rengi ve modeli fotoğraftakinden daha güzel duruyor canlıda. Tavsiye ederim.",
        "Alırken biraz tereddüt etmiştim ama geldiğinde kalitesini görünce iyiki almışım dedim.",
        "İç astarı yumuşacık, çocuğun ayağını vurmuyor. Konforlu bir ayakkabı.",
        "Yeğenime hediye aldım. Paketi açar açmaz evin içinde sürmeye başladı. Çok eğlenceli.",
        "Kargolama inanılmaz hızlıydı. Siparişimin ertesi günü elimdeydi.",
        "Biraz pratik yapmak gerekiyor tabi ama tekerlek sistemi çok sağlam. Güvenle kullanıyoruz.",
        "Kızım o kadar çok sevdi ki yatağa bile bunlarla girmek istiyor. Kesinlikle her çocuğun hayali.",
        "Malzeme kalitesi muazzam. Daha önce başka bir marka denemiştik, hemen bozulmuştu. Bu çok dayanıklı.",
        "Çocuklar için inanılmaz bir eğlence aracı. Herkese tavsiye ediyorum.",
        "Ağırlığı bir tık fazla gibi ama çocuklar alıştığında sorun olmuyor. Işıkları şahane.",
        "Kalıpları tam, çocuğunuzun tam ayak numarasını alabilirsiniz.",
        "Düğmesine basıp tekerleği içeri sokmak çok zevkli. Çok havalı duruyor.",
        "Görselliği ve ışıkları çok dikkat çekiyor. Sokakta gören herkes nereden aldığımızı soruyor.",
        "Oğlumun paten kullanmayı öğrenmesi için harika bir başlangıç oldu. Çok dengeli.",
        "İki çocuğuma da birer tane aldım. İkisi de çok memnun. Kesinlikle pişman olmazsınız.",
        "Paten kısmı biraz ses yapıyor ama rahatsız edici boyutta değil. Kullanımı kolay.",
        "Satıcı sorularıma anında dönüş yaptı, çok ilgililer. Ürün de efsane.",
        "Hızlı kargo, özenli paketleme, kaliteli ürün. Başka söze gerek yok.",
        "Tabanı kalın olduğu için ayakları da üşütmüyor. Malzemesi tok duruyor.",
        "Ayakkabı kısmı biraz sert ilk giyildiğinde ama sonradan yumuşadı. Gayet rahat.",
        "Mekanizması hiç takılmıyor, yağ gibi kayıyor. Tekerlekler çok kaliteli.",
        "Çocukları dışarı çıkarmak için harika bir bahane oldu, sürekli kaymak istiyorlar.",
        "Bence piyasadaki en kaliteli ışıklı paten ayakkabısı. Diğer uyduruk ürünlerle kıyaslamayın.",
        "Güvenlik önlemi olarak sadece düz ve pürüzsüz zeminlerde kullanılmasını tavsiye ederim."
    ];

    shuffle($names);
    shuffle($comments);

    $nameIndex = 0;
    $commentIndex = 0;
    $totalAdded = 0;

    foreach ($products as $product) {
        $existingCount = $product->reviews()->count();
        $neededReviews = 5 - $existingCount;

        if ($neededReviews <= 0) {
            continue;
        }

        for ($i = 0; $i < $neededReviews; $i++) {
            $rating = rand(1, 100) > 20 ? 5 : 4;
            $createdAt = \Carbon\Carbon::now()->subDays(rand(1, 180))->subHours(rand(1, 24));

            $product->reviews()->create([
                'user_id' => null,
                'name' => $names[$nameIndex % count($names)],
                'email' => 'customer' . $nameIndex . '@example.com',
                'rating' => $rating,
                'comment' => $comments[$commentIndex % count($comments)],
                'status' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $nameIndex++;
            $commentIndex++;
            $totalAdded++;
        }
    }

    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');

    return "<html><head><title>Yorumlar Eklendi</title></head><body style=\"font-family:monospace;padding:40px;background:#111;color:#eee;font-size:16px;line-height:2;\">
            <h1 style=\"color:#0d9488;\">✅ Gerçekçi Yorumlar Başarıyla Üretildi!</h1>
            <p>Eksik olan toplam <b>{$totalAdded}</b> adet yeni yorum eklendi. Cache temizlendi.</p>
            <br><a href=\"/\" style=\"color:#0d9488;font-size:16px;margin-right:20px;\">👉 Ana Sayfaya Dön</a>
            </body></html>";
})->middleware('auth');

// ========================
// SEO TOOLS
// ========================
Route::get('/run-seo-links', function () {
    if (!auth()->check()) {
        return 'Giriş yapmalısınız.';
    }
    \Illuminate\Support\Facades\Artisan::call('seo:link-content');
    return '<pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
})->middleware('auth');

// Mevcut yorumlardaki soyisimleri baş harf + nokta (Örn: Ayşe Y.) formatına çevirmek için
Route::get('/deploy-fix-names', function () {
    $reviews = \App\Models\Review::all();
    $count = 0;
    foreach ($reviews as $review) {
        $parts = explode(' ', trim($review->name));
        if (count($parts) >= 2) {
            $lastName = array_pop($parts);
            if (strpos($lastName, '.') === false) {
                $lastNameInitial = mb_strtoupper(mb_substr($lastName, 0, 1, 'UTF-8'), 'UTF-8') . '.';
                $parts[] = $lastNameInitial;
                $review->name = implode(' ', $parts);
                $review->save();
                $count++;
            }
        }
    }
    return "<html><head><title>İsimler Güncellendi</title></head><body style=\"font-family:monospace;padding:40px;background:#111;color:#eee;font-size:16px;line-height:2;\">
            <h1 style=\"color:#0d9488;\">✅ Soyisimler Başarıyla Gizlendi!</h1>
            <p>Veritabanındaki toplam <b>{$count}</b> adet yorum güncellendi. Artık soyisimler tam okunmayacak (Örn: Ayşe Y.).</p>
            <br><a href=\"/\" style=\"color:#0d9488;font-size:16px;margin-right:20px;\">👉 Ana Sayfaya Dön</a>
            </body></html>";
})->middleware('auth');

// ========================
// N8N Webhook (AI Blog Oto-Yayınlama)
// ========================
Route::post('api/n8n/blog-publish', [\App\Http\Controllers\N8nWebhookController::class, 'publishBlog']);

// ========================
// DİNAMİK KURUMSAL SAYFALAR (Catch-all)
// ========================
Route::get('/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    return view('pages.show', ['page' => $page]);
})->name('pages.show');
