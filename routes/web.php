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
    
    // 5. livewire-tmp durumu
    $tmpFiles = \Illuminate\Support\Facades\Storage::disk('public')->files('livewire-tmp');
    $result['livewire_tmp_files'] = count($tmpFiles);
    
    return response()->json($result, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});
