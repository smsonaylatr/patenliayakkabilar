<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Frontend\Contact;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Account\Dashboard;
use App\Livewire\Account\Profile;
use App\Livewire\Account\Orders;

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

Route::get('/patenli-ayakkabilar', function () {
    return view('products.index');
})->name('products.index');

Route::redirect('/urunler', '/patenli-ayakkabilar', 301);

Route::get('/urun/{slug}', function ($slug) {
    $product = \App\Models\Product::where('slug', $slug)->with(['variants', 'images'])->firstOrFail();
    return view('products.show', ['product' => $product]);
})->name('products.show');

Route::get('/siparis-takip', function () {
    return view('order.tracking'); // TODO: Create Livewire tracking component later
})->name('order.tracking');

Route::get('/sayfa/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    return view('pages.show', ['page' => $page]);
})->name('pages.show');

Route::view('/lansman', 'lansman')->name('lansman');

Route::get('/debug-images', function() {
    $result = [];
    
    // 1. Symlink durumu
    $publicStorage = public_path('storage');
    $result['symlink'] = [
        'path' => $publicStorage,
        'exists' => file_exists($publicStorage),
        'is_link' => is_link($publicStorage),
        'target' => is_link($publicStorage) ? readlink($publicStorage) : 'NOT A SYMLINK',
    ];
    
    // 2. Son 5 ProductImage kaydı
    $images = \App\Models\ProductImage::orderBy('id', 'desc')->take(5)->get();
    $result['images'] = $images->map(function($img) {
        $diskExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($img->image_path);
        $publicPath = public_path('storage/' . $img->image_path);
        return [
            'id' => $img->id,
            'product_id' => $img->product_id,
            'image_path' => $img->image_path,
            'image_url' => $img->image_url,
            'file_on_disk' => $diskExists,
            'file_in_public' => file_exists($publicPath),
            'file_size' => $diskExists ? \Illuminate\Support\Facades\Storage::disk('public')->size($img->image_path) : 0,
        ];
    });
    
    // 3. storage/app/public/products/ içeriği
    $files = \Illuminate\Support\Facades\Storage::disk('public')->files('products');
    $result['storage_files'] = $files;
    
    // 4. PHP upload limits
    $result['php_limits'] = [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
    ];
    
    // 5. livewire-tmp dosyaları - detaylı
    $tmpFiles = \Illuminate\Support\Facades\Storage::disk('public')->files('livewire-tmp');
    $result['livewire_tmp'] = collect($tmpFiles)->map(function($f) {
        return [
            'name' => $f,
            'size' => \Illuminate\Support\Facades\Storage::disk('public')->size($f),
        ];
    })->values();
    
    // 6. Dizin izinleri
    $productsDir = storage_path('app/public/products');
    $result['permissions'] = [
        'storage_app_public' => substr(sprintf('%o', fileperms(storage_path('app/public'))), -4),
        'products_dir_exists' => is_dir($productsDir),
        'products_dir_writable' => is_writable($productsDir),
        'products_dir_perms' => is_dir($productsDir) ? substr(sprintf('%o', fileperms($productsDir)), -4) : 'N/A',
        'storage_owner' => function_exists('posix_getpwuid') ? posix_getpwuid(fileowner(storage_path('app/public')))['name'] ?? 'unknown' : fileowner(storage_path('app/public')),
        'current_user' => function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] ?? 'unknown' : get_current_user(),
    ];
    
    // 7. Yazma testi
    try {
        $testFile = 'products/_write_test_' . time() . '.txt';
        \Illuminate\Support\Facades\Storage::disk('public')->put($testFile, 'test');
        $writeOk = \Illuminate\Support\Facades\Storage::disk('public')->exists($testFile);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($testFile);
        $result['write_test'] = $writeOk ? 'SUCCESS' : 'FAILED';
    } catch (\Exception $e) {
        $result['write_test'] = 'ERROR: ' . $e->getMessage();
    }

    // 8. Livewire-tmp'den kurtarma denemesi — kırık görselleri bul ve eşleştir
    $brokenImages = \App\Models\ProductImage::orderBy('id', 'desc')
        ->get()
        ->filter(fn($img) => !\Illuminate\Support\Facades\Storage::disk('public')->exists($img->image_path));
    $result['broken_images_count'] = $brokenImages->count();
    $result['broken_image_ids'] = $brokenImages->pluck('id')->values();
    
    return response()->json($result, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// Geçici: Kırık görsel kayıtlarını temizle
Route::get('/fix-broken-images', function() {
    $broken = \App\Models\ProductImage::all()
        ->filter(fn($img) => !\Illuminate\Support\Facades\Storage::disk('public')->exists($img->image_path));
    
    $deleted = [];
    foreach ($broken as $img) {
        $deleted[] = ['id' => $img->id, 'path' => $img->image_path, 'product_id' => $img->product_id];
        $img->delete();
    }
    
    // livewire-tmp temizle
    $tmpFiles = \Illuminate\Support\Facades\Storage::disk('public')->files('livewire-tmp');
    foreach ($tmpFiles as $f) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($f);
    }
    // local disk'teki livewire-tmp de temizle
    $localTmpFiles = \Illuminate\Support\Facades\Storage::disk('local')->files('livewire-tmp');
    foreach ($localTmpFiles as $f) {
        \Illuminate\Support\Facades\Storage::disk('local')->delete($f);
    }
    
    return response()->json([
        'deleted_records' => $deleted,
        'tmp_cleaned' => count($tmpFiles) + count($localTmpFiles),
        'message' => 'Kırık kayıtlar silindi. Şimdi yeniden görsel yükleyebilirsiniz.',
    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});
