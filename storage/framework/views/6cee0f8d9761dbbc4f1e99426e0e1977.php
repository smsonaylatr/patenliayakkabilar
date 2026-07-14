<?php
    $categoryName = isset($category) && $category ? $category->name : 'Tüm Modeller';
    $pageTitle = isset($category) && $category 
        ? ($category->meta_title ?? $categoryName . ' | Patenli Ayakkabılar')
        : 'Tüm Patenli Ayakkabı Modelleri | Patenli Ayakkabılar';
    $pageDesc = isset($category) && $category 
        ? ($category->meta_description ?? $categoryName . ' kategorisindeki en çok tercih edilen patenli ayakkabı modelleri. Güvenli alışveriş ve hızlı kargo.')
        : 'Tüm ışıklı ve tekerlekli patenli ayakkabı modellerimizi keşfedin. Çocuk ve genç modelleri, uygun fiyatlarla.';
    $canonicalUrl = isset($category) && $category ? url('/kategori/' . $category->slug) : url('/patenli-ayakkabilar');
?>

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

     <?php $__env->slot('title', null, []); ?> <?php echo e($pageTitle); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('description', null, []); ?> <?php echo e($pageDesc); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('canonical', null, []); ?> <?php echo e($canonicalUrl); ?> <?php $__env->endSlot(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($category) && $category): ?>
         <?php $__env->slot('schema', null, []); ?> 
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->bound(\App\Services\SchemaService::class)): ?>
                <?php echo app(\App\Services\SchemaService::class)->categoryPage($category, null); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php $__env->endSlot(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            
            <div class="mb-8">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => array_filter([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Patenli Ayakkabılar', 'url' => route('products.index')],
                    isset($category) && $category ? ['name' => $category->name] : null,
                ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(array_filter([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Patenli Ayakkabılar', 'url' => route('products.index')],
                    isset($category) && $category ? ['name' => $category->name] : null,
                ]))]); ?>
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

            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    <?php echo e(isset($category) && $category ? ($category->seo_h1 ?? $category->name) : 'Tüm Modeller'); ?>

                </h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    <?php echo e(isset($category) && $category ? $category->name . ' kategorisindeki en çok tercih edilen patenli ayakkabı modellerimiz.' : 'En çok tercih edilen patenli ayakkabı ve tekerlekli sneaker modellerimiz.'); ?>

                </p>
            </div>

            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product.product-grid', ['category' => isset($category) && $category ? $category->slug : '']);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2385946463-0', $__key);

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
            
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($category) && $category && $category->seo_content): ?>
                <div class="mt-16 max-w-4xl mx-auto">
                    <div class="prose prose-gray max-w-none text-gray-600">
                        <?php echo $category->seo_content; ?>

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
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\products\index.blade.php ENDPATH**/ ?>