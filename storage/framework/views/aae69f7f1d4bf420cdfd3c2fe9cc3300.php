
<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('title', null, []); ?> <?php echo e($product->meta_title ?? $product->name . ' | Patenli Ayakkabılar'); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('description', null, []); ?> <?php echo e($product->meta_description ?? Str::limit($product->short_description ?? 'Çocuklar için güvenli ve eğlenceli patenli ayakkabılar.', 155)); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('ogType', null, []); ?> product <?php $__env->endSlot(); ?>
     <?php $__env->slot('ogImage', null, []); ?> <?php echo e($product->images->first()?->image_url ?? asset('favicon.png')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('canonical', null, []); ?> <?php echo e($product->canonical_url ?? url('/urun/' . $product->slug)); ?> <?php $__env->endSlot(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($product->is_indexable) && !$product->is_indexable): ?>
         <?php $__env->slot('robots', null, []); ?> noindex, follow <?php $__env->endSlot(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php $__env->slot('schema', null, []); ?> 
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->bound(\App\Services\SchemaService::class)): ?>
            <?php echo app(\App\Services\SchemaService::class)->product($product); ?>

        <?php else: ?>
            <script type="application/ld+json">
            <?php echo json_encode([
                '<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>' => 'https://schema.org/',
                '@type' => 'Product',
                'name' => $product->name,
                'image' => [$product->images->first()?->image_url ?? asset('favicon.png')],
                'description' => Str::limit(strip_tags($product->short_description), 200),
                'sku' => $product->sku ?? (string)$product->id,
                'offers' => [
                    '@type' => 'Offer',
                    'url' => url()->current(),
                    'priceCurrency' => 'TRY',
                    'price' => number_format((float)($product->discount_price ?? $product->price), 2, '.', ''),
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>

            </script>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php $__env->endSlot(); ?>

    <style>
        .accordion-content {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.4s ease, opacity 0.3s ease;
            opacity: 0;
        }
        .accordion-content.open {
            grid-template-rows: 1fr;
            opacity: 1;
        }
        .accordion-content > div {
            overflow: hidden;
        }
    </style>

    <div class="pt-4 lg:pt-6 pb-10 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Patenli Ayakkabılar', 'url' => route('products.index')],
                    ...($product->category ? [['name' => $product->category->name, 'url' => url('/kategori/' . $product->category->slug)]] : []),
                    ['name' => $product->name],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Patenli Ayakkabılar', 'url' => route('products.index')],
                    ...($product->category ? [['name' => $product->category->name, 'url' => url('/kategori/' . $product->category->slug)]] : []),
                    ['name' => $product->name],
                ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
            </div>
            <div class="flex flex-col lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start" style="grid-template-rows: max-content 1fr;">
                <!-- 1. Image gallery (Left Column, Top) -->
                <div class="order-1 lg:col-span-1 lg:row-span-1">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product.product-gallery', ['product' => $product]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-978724737-0', $__key);

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

                <!-- 2. Product info (Right Column, spans both rows) -->
                <div class="order-2 lg:col-span-1 lg:row-span-2 mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                    <!-- Desktop Title -->
                    <h1 class="hidden md:block text-3xl md:text-4xl font-bold tracking-tight text-gray-900"><?php echo e($product->name); ?></h1>
                    
                    <!-- Mobile Marquee Title -->
                    <div class="md:hidden w-full overflow-hidden bg-white pb-2 relative">
                        <style>
                            @keyframes marquee-mobile {
                                0% { transform: translate3d(0, 0, 0); }
                                100% { transform: translate3d(-50%, 0, 0); }
                            }
                            .animate-marquee-mobile {
                                animation: marquee-mobile 25s linear infinite;
                                will-change: transform;
                                backface-visibility: hidden;
                                -webkit-backface-visibility: hidden;
                            }
                        </style>
                        <div class="flex w-max animate-marquee-mobile">
                            <!-- Grup 1 -->
                            <div class="flex shrink-0">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <h1 class="font-bold tracking-tight text-gray-900" style="font-size: 40px; margin-right: 140px;">
                                        <?php echo e($product->name); ?>

                                    </h1>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <!-- Grup 2 (Grup 1'in birebir kopyası) -->
                            <div class="flex shrink-0" aria-hidden="true">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <h1 class="font-bold tracking-tight text-gray-900" style="font-size: 40px; margin-right: 140px;">
                                        <?php echo e($product->name); ?>

                                    </h1>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mt-4">
                        <h2 class="sr-only">Product information</h2>
                        <div class="flex items-center gap-3">
                            <?php
                                $displayPrice = $product->price;
                                $displayDiscount = $product->discount_price;
                                if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                                    $displayPrice = $product->discount_price;
                                    $displayDiscount = $product->price;
                                }
                            ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayDiscount): ?>
                                <p class="text-3xl font-bold text-red-600"><?php echo e(number_format($displayDiscount, 2)); ?> ₺</p>
                                <p class="text-lg text-gray-400 line-through"><?php echo e(number_format($displayPrice, 2)); ?> ₺</p>
                                <?php $percent = round(($displayPrice - $displayDiscount) / $displayPrice * 100); ?>
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-red-500 text-white">%<?php echo e($percent); ?></span>
                            <?php else: ?>
                                <p class="text-3xl font-bold text-red-600"><?php echo e(number_format($displayPrice, 2)); ?> ₺</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->short_description): ?>
                    <div class="mt-5">
                        <p class="text-sm text-gray-500 leading-relaxed"><?php echo e($product->short_description); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->variants->count() > 0): ?>
                        <div class="mt-6">
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product.variant-selector', ['product' => $product]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-978724737-1', $__key);

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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="mt-3">
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product.add-to-cart-button', ['product' => $product]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-978724737-2', $__key);

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

                    
                    <?php $signals = $product->getTrustSignals(); ?>
                    <div class="mt-8 grid grid-cols-2 gap-2">
                        <?php
                            $iconMap = [
                                '🚚' => 'fa-solid fa-truck-fast',
                                '⚡' => 'fa-solid fa-bolt',
                                '🔒' => 'fa-solid fa-lock',
                                '↩️' => 'fa-solid fa-rotate-left',
                                '🏷️' => 'fa-solid fa-tag',
                                '🔥' => 'fa-solid fa-fire',
                                '⭐' => 'fa-solid fa-star',
                            ];
                            $colorMap = [
                                'green'  => 'text-emerald-500',
                                'blue'   => 'text-blue-500',
                                'purple' => 'text-violet-500',
                                'orange' => 'text-amber-500',
                                'red'    => 'text-red-500',
                                'yellow' => 'text-yellow-500',
                            ];
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $signals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $signal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex items-center gap-3.5 px-4 py-3 rounded-lg border border-gray-100 bg-gray-50/50">
                                <i class="<?php echo e($iconMap[$signal['icon']] ?? 'fa-solid fa-check'); ?> <?php echo e($colorMap[$signal['color']] ?? 'text-gray-500'); ?> text-sm w-5 flex-shrink-0 text-center"></i>
                                <span class="text-xs font-medium text-gray-700"><?php echo e($signal['text']); ?></span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    
                    <div class="mt-8 divide-y divide-gray-100" x-data="{ openPanel: window.innerWidth < 1024 ? 'description' : '' }">

                        
                        <?php $featureLabels = $product->getFeatureLabels(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($featureLabels) > 0): ?>
                        <div>
                            <button
                                @click="openPanel = openPanel === 'features' ? '' : 'features'"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-4">
                                    <i class="fa-solid fa-list-check text-gray-400 text-sm w-6 flex-shrink-0 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Öne Çıkan Özellikler</span>
                                    <span class="text-[11px] font-medium text-gray-400"><?php echo e(count($featureLabels)); ?></span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'features' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'features' ? 'open' : ''">
                                <div class="pb-4 grid grid-cols-2 gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $featureLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="flex items-center gap-3.5 px-3.5 py-2.5 bg-gray-50 rounded-lg">
                                            <i class="fa-solid fa-check text-emerald-500 text-[10px] w-5 flex-shrink-0 text-center"></i>
                                            <span class="text-xs font-medium text-gray-700"><?php echo e($feature['label']); ?></span>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php $specs = $product->getSpecifications(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($specs) > 0): ?>
                        <div>
                            <button
                                @click="openPanel = openPanel === 'specs' ? '' : 'specs'"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-4">
                                    <i class="fa-solid fa-info-circle text-gray-400 text-sm w-6 flex-shrink-0 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Teknik Bilgiler</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'specs' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'specs' ? 'open' : ''">
                                <div class="pb-4">
                                    <div class="rounded-lg border border-gray-100 overflow-hidden">
                                        <dl>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $specs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <div class="flex justify-between items-center px-4 py-2.5 text-xs <?php echo e(!$loop->last ? 'border-b border-gray-50' : ''); ?> <?php echo e($loop->even ? 'bg-gray-50/50' : 'bg-white'); ?>">
                                                    <dt class="text-gray-500"><?php echo e($label); ?></dt>
                                                    <dd class="text-gray-900 font-semibold"><?php echo e($value); ?></dd>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div>
                            <button
                                @click="openPanel = openPanel === 'shipping' ? '' : 'shipping'"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-4">
                                    <i class="fa-solid fa-truck text-gray-400 text-sm w-6 flex-shrink-0 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Kargo & İade</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'shipping' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="accordion-content" :class="openPanel === 'shipping' ? 'open' : ''">
                                <div class="pb-4 space-y-3">
                                    <div class="flex items-start gap-4">
                                        <i class="fa-solid fa-truck-fast text-emerald-500 text-sm mt-0.5 w-5 flex-shrink-0 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Sabit 1 TL Kargo</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">Ürün başı kargo ücreti sadece 1 TL. Türkiye'nin her yerine 1-3 iş günü içinde kargoya verilir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4">
                                        <i class="fa-solid fa-shield-halved text-blue-500 text-sm mt-0.5 w-5 flex-shrink-0 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Güvenli Paketleme</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">Özel kutusunda, hasar görmeyecek şekilde paketlenir.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4">
                                        <i class="fa-solid fa-rotate-left text-amber-500 text-sm mt-0.5 w-5 flex-shrink-0 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">14 Gün İade Garantisi</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">Kullanılmamış ve orijinal ambalajında koşulsuz iade.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4">
                                        <i class="fa-solid fa-lock text-violet-500 text-sm mt-0.5 w-5 flex-shrink-0 text-center"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Güvenli Ödeme</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">256-bit SSL şifreleme. Kapıda ödeme seçeneği mevcuttur.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->description): ?>
                        <div>
                            <button
                                @click="window.innerWidth >= 1024 ? $dispatch('toggle-tanitim') : (openPanel = openPanel === 'description' ? '' : 'description')"
                                class="w-full flex items-center justify-between py-4 text-left group"
                            >
                                <div class="flex items-center gap-4">
                                    <i class="fa-solid fa-file-lines text-gray-400 text-sm w-6 flex-shrink-0 text-center"></i>
                                    <span class="text-sm font-semibold text-gray-900">Ürün Tanıtımı</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-300"
                                   :class="openPanel === 'description' && window.innerWidth < 1024 ? 'rotate-180' : ''"
                                   @toggle-tanitim.window="$el.classList.toggle('rotate-180')"></i>
                            </button>
                            <div class="accordion-content lg:hidden" :class="openPanel === 'description' ? 'open' : ''">
                                <div class="pb-4">
                                    <div class="prose prose-sm prose-gray max-w-none text-gray-700 leading-relaxed prose-img:rounded-2xl prose-img:w-full prose-img:shadow-sm prose-headings:font-bold prose-a:text-emerald-600">
                                        <?php echo $product->description; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
                </div>
                
                <!-- 3. Product Reviews (Left Column, Bottom on Desktop | Bottom on Mobile) -->
                <div class="order-3 lg:col-span-1 lg:row-span-1 mt-0 lg:-mt-50 px-4 sm:px-0 static lg:relative lg:z-20 pointer-events-none">
                    <div class="flex flex-col md:flex-row gap-4 lg:gap-6">
                        <!-- Spacer to align with thumbnails -->
                        <div class="hidden md:block w-full md:w-24 lg:w-28 flex-shrink-0"></div>
                        <!-- Reviews Container aligned with main image -->
                        <div class="flex-1 pointer-events-auto">
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product.review-list', ['product' => $product]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-978724737-3', $__key);

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
                    </div>
                </div>
            </div>

            <!-- 4. TANITIM (Masaüstü Tam Genişlik) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->description): ?>
            <style>
                @media (min-width: 1024px) {
                    #tanitim-bolumu .prose img,
                    #tanitim-bolumu .prose video {
                        margin-left: 6rem !important;
                        border-radius: 1rem;
                        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                        width: calc(100% - 6rem);
                        max-width: 100%;
                    }
                }
            </style>
            <div x-data="{ showTanitim: false }" @toggle-tanitim.window="showTanitim = !showTanitim; if(showTanitim) { setTimeout(() => { $el.scrollIntoView({behavior: 'smooth'}) }, 250) }">
                <div id="tanitim-bolumu" class="hidden lg:block mt-0 pt-10 border-t border-gray-100 px-4 sm:px-0 relative z-10 accordion-content" :class="showTanitim ? 'open' : ''">
                    <div>
                        <div class="prose prose-lg prose-gray max-w-none text-gray-700 leading-relaxed prose-headings:font-bold prose-a:text-emerald-600 pb-10">
                            <?php echo $product->description; ?>

                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\products\show.blade.php ENDPATH**/ ?>