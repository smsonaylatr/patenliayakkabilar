<!DOCTYPE html>
<html lang="tr" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
        
        <title><?php echo e($title ?? 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri ve Fiyatları'); ?></title>
        <meta name="description" content="<?php echo e($description ?? 'Çocuklar için en güvenli ve eğlenceli patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçeneklerle ücretsiz kargo fırsatı.'); ?>">
        <meta name="robots" content="<?php echo e($robots ?? 'index, follow'); ?>">
        <link rel="canonical" href="<?php echo e($canonical ?? url()->current()); ?>">

        
        <?php
            $verifications = \App\Models\Setting::whereIn('key', ['seo_google_verification', 'seo_yandex_verification', 'seo_bing_verification'])->pluck('value', 'key');
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verifications->get('seo_google_verification')): ?>
            <meta name="google-site-verification" content="<?php echo e($verifications->get('seo_google_verification')); ?>">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verifications->get('seo_yandex_verification')): ?>
            <meta name="yandex-verification" content="<?php echo e($verifications->get('seo_yandex_verification')); ?>">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verifications->get('seo_bing_verification')): ?>
            <meta name="msvalidate.01" content="<?php echo e($verifications->get('seo_bing_verification')); ?>">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Locale -->
        <meta property="og:locale" content="tr_TR">
        <meta property="og:site_name" content="Patenli Ayakkabılar">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="<?php echo e($ogType ?? 'website'); ?>">
        <meta property="og:url" content="<?php echo e(url()->current()); ?>">
        <meta property="og:title" content="<?php echo e($title ?? 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri ve Fiyatları'); ?>">
        <meta property="og:description" content="<?php echo e($description ?? 'Çocuklar için en güvenli ve eğlenceli patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçeneklerle ücretsiz kargo fırsatı.'); ?>">
        <meta property="og:image" content="<?php echo e($ogImage ?? asset('favicon.png')); ?>">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="<?php echo e(url()->current()); ?>">
        <meta name="twitter:title" content="<?php echo e($title ?? 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri ve Fiyatları'); ?>">
        <meta name="twitter:description" content="<?php echo e($description ?? 'Çocuklar için en güvenli ve eğlenceli patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçeneklerle ücretsiz kargo fırsatı.'); ?>">
        <meta name="twitter:image" content="<?php echo e($ogImage ?? asset('favicon.png')); ?>">

        <!-- Structured Data -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($schema)): ?>
            <?php echo $schema; ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->bound(\App\Services\SchemaService::class)): ?>
            <?php echo app(\App\Services\SchemaService::class)->organization(); ?>

            <?php echo app(\App\Services\SchemaService::class)->website(); ?>

        <?php else: ?>
            <script type="application/ld+json">
            <?php echo json_encode([
                '<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Patenli Ayakkabılar',
                'url' => url('/'),
                'logo' => asset('favicon.png'),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>

            </script>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Analytics -->
        <?php if (isset($component)) { $__componentOriginalea2dce4014fb9bd77432d0a2cf5dc69b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalea2dce4014fb9bd77432d0a2cf5dc69b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.analytics','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('analytics'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalea2dce4014fb9bd77432d0a2cf5dc69b)): ?>
<?php $attributes = $__attributesOriginalea2dce4014fb9bd77432d0a2cf5dc69b; ?>
<?php unset($__attributesOriginalea2dce4014fb9bd77432d0a2cf5dc69b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalea2dce4014fb9bd77432d0a2cf5dc69b)): ?>
<?php $component = $__componentOriginalea2dce4014fb9bd77432d0a2cf5dc69b; ?>
<?php unset($__componentOriginalea2dce4014fb9bd77432d0a2cf5dc69b); ?>
<?php endif; ?>

        <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Alpine Plugins -->
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

        <style>
            @keyframes pageSlideHorizontal {
                0% { opacity: 0; transform: translateX(30px); }
                100% { opacity: 1; transform: none; }
            }
            .page-transition-effect {
                animation: pageSlideHorizontal 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
            }
        </style>
    </head>
    <body class="bg-brand-light text-brand-dark font-sans antialiased flex flex-col min-h-screen overflow-x-hidden">
        
        <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('header-wrapper'); ?>">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('frontend.header', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2887436534-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        </div>

        <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('cart-drawer-wrapper'); ?>">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('frontend.cart-drawer', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2887436534-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        </div>

        <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('search-modal-wrapper'); ?>">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('frontend.search-modal', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2887436534-2', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        </div>
        
        <main class="flex-grow page-transition-effect">
            <?php echo e($slot); ?>

        </main>

        <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('footer-wrapper'); ?>">
            <!-- Bottom Marquee -->
            <div class="bg-white border-y border-gray-200 py-5 sm:py-7 overflow-hidden w-full relative">
                <div class="marquee-content flex whitespace-nowrap items-center">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 24; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <span class="text-black font-black text-xl sm:text-2xl md:text-3xl tracking-[0.2em] uppercase mx-6 md:mx-12">HER YERDE KAY</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <footer class="bg-brand-black text-brand-white pt-16 pb-24 md:pb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                        <div class="col-span-2">
                            <a href="<?php echo e(route('home')); ?>" class="text-2xl font-black text-white tracking-tighter mb-4 inline-block" wire:navigate>
                                PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                            </a>
                            <p class="text-gray-400 max-w-md">Çocukların eğlenirken güvende olması için ürün seçimini, kargo sürecini ve satış sonrası desteği kolaylaştırıyoruz.</p>
                            
                            <div class="mt-6 flex space-x-4">
                                <!-- Social Icons -->
                                <a href="#" class="text-gray-400 hover:text-brand-orange transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-brand-orange transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                                </a>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-bold mb-4">Hızlı Menü</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li><a href="<?php echo e(route('home')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Ana Sayfa</a></li>
                                <li><a href="<?php echo e(route('products.index')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Tüm Ürünler</a></li>
                                <li><a href="<?php echo e(route('order.tracking')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Sipariş Takip</a></li>
                                <li><a href="<?php echo e(route('blog.index')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Rehber Merkezi</a></li>
                                <li><a href="<?php echo e(route('contact')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>İletişim</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold mb-4">Kurumsal</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li><a href="<?php echo e(route('pages.show', 'hakkimizda')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Hakkımızda</a></li>
                                <li><a href="<?php echo e(route('pages.show', 'sikca-sorulan-sorular')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Sıkça Sorulan Sorular</a></li>
                                <li><a href="<?php echo e(route('pages.show', 'iade-ve-degisim')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>İade ve Değişim</a></li>
                                <li><a href="<?php echo e(route('pages.show', 'mesafeli-satis-sozlesmesi')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Mesafeli Satış Sözleşmesi</a></li>
                                <li><a href="<?php echo e(route('pages.show', 'gizlilik-politikasi')); ?>" class="hover:text-brand-orange transition-colors" wire:navigate>Gizlilik Politikası</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Payment Icons -->
                    <div class="flex flex-wrap justify-center gap-2 mb-8">
                        <!-- Amex -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] overflow-hidden">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/amex.svg" alt="Amex" class="w-full h-full object-cover">
                        </div>
                        <!-- Apple Pay -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100">
                            <svg viewBox="0 0 384 512" class="w-2.5 h-2.5 mr-0.5 text-black" fill="currentColor"><path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/></svg>
                            <span class="font-bold text-black text-[10px] leading-none">Pay</span>
                        </div>
                        <!-- Troy -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100">
                            <span class="font-black text-[#00a8e1] tracking-tighter text-[11px] leading-none">TROY</span>
                        </div>
                        <!-- Visa -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1.5 overflow-hidden">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/visa.svg" alt="Visa" class="w-full h-full object-contain">
                        </div>
                        <!-- Google Pay -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100">
                            <span class="font-bold text-gray-600 text-[10px] leading-none flex items-center">
                                <span class="text-blue-500 mr-0.5 text-[11px]">G</span>Pay
                            </span>
                        </div>
                        <!-- Mastercard -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1.5 overflow-hidden">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/mastercard.svg" alt="Mastercard" class="w-full h-full object-contain">
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-8 flex flex-col items-center justify-center text-sm text-gray-500 text-center">
                        <p>&copy; <?php echo e(date('Y')); ?> Patenli Ayakkabılar. Tüm hakları saklıdır.</p>
                    </div>
                </div>
            </footer>
        </div>

        <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('mobile-bottom-nav'); ?>">
        <!-- Mobile Bottom Navigation Bar (Floating Action Button Design) -->
        <div class="md:hidden fixed inset-x-0 bottom-0 w-full bg-white border-t border-gray-200 z-[9999] shadow-[0_-10px_30px_rgba(0,0,0,0.08)]" style="padding-bottom: env(safe-area-inset-bottom); transform: translateZ(0);">
            <div class="flex justify-between items-center h-[64px] px-1 relative w-full max-w-full">
                
                <a href="<?php echo e(route('home')); ?>" wire:navigate class="flex flex-col items-center justify-center w-full h-full text-brand-black hover:text-brand-orange transition-colors">
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-[10px] font-medium leading-none tracking-wide mt-1">Ana Sayfa</span>
                </a>
                
                <button x-data @click="$dispatch('open-search')" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-brand-orange transition-colors">
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="text-[10px] font-medium leading-none tracking-wide mt-1">Ara</span>
                </button>
                
                <!-- Center Floating Button (Sepet) -->
                <div class="relative w-full flex justify-center h-full pointer-events-none">
                    <button x-data @click="$dispatch('toggle-cart')" class="pointer-events-auto absolute flex items-center justify-center w-[68px] h-[68px] bg-black text-white rounded-full border-[6px] border-white shadow-[0_8px_20px_rgba(0,0,0,0.2)] hover:bg-gray-900 hover:scale-105 transition-all duration-300" style="top: -32px;">
                        <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </button>
                    <!-- Text below the floating button -->
                    <span class="absolute text-[10px] font-medium text-gray-900 pointer-events-none leading-none tracking-wide" style="bottom: 11px;">Sepet</span>
                </div>
                
                <a href="<?php echo e(route('order.tracking')); ?>" wire:navigate class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-brand-orange transition-colors">
                    <!-- Package / Order tracking icon -->
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="text-[10px] font-medium whitespace-nowrap leading-none tracking-wide mt-1">Sipariş Takip</span>
                </a>
                
                <a href="<?php echo e(auth()->check() ? route('account.dashboard') : route('login')); ?>" wire:navigate class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-brand-orange transition-colors">
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-[10px] font-medium leading-none tracking-wide mt-1">Hesabım</span>
                </a>
            </div>
        </div>
        </div>

        <?php if (isset($component)) { $__componentOriginale5426c9fcdff9c1d7e17456801fe4d80 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5426c9fcdff9c1d7e17456801fe4d80 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.frontend.toast-notification','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('frontend.toast-notification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5426c9fcdff9c1d7e17456801fe4d80)): ?>
<?php $attributes = $__attributesOriginale5426c9fcdff9c1d7e17456801fe4d80; ?>
<?php unset($__attributesOriginale5426c9fcdff9c1d7e17456801fe4d80); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5426c9fcdff9c1d7e17456801fe4d80)): ?>
<?php $component = $__componentOriginale5426c9fcdff9c1d7e17456801fe4d80; ?>
<?php unset($__componentOriginale5426c9fcdff9c1d7e17456801fe4d80); ?>
<?php endif; ?>
        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('frontend.site-popup', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2887436534-3', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    </body>
</html>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\components\layouts\app.blade.php ENDPATH**/ ?>