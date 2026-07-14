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

     <?php $__env->slot('title', null, []); ?> Sayfa Bulunamadı | Patenli Ayakkabılar <?php $__env->endSlot(); ?>
     <?php $__env->slot('description', null, []); ?> Aradığınız sayfa bulunamadı veya kaldırılmış olabilir. <?php $__env->endSlot(); ?>
     <?php $__env->slot('robots', null, []); ?> noindex, follow <?php $__env->endSlot(); ?>

    <section class="min-h-[70vh] flex items-center justify-center bg-gray-50 px-4 py-16 sm:py-24">
        <div class="max-w-2xl mx-auto text-center">
            
            <h1 class="text-[10rem] sm:text-[12rem] font-black leading-none tracking-tight bg-gradient-to-br from-brand-orange via-orange-400 to-amber-500 bg-clip-text text-transparent select-none">
                404
            </h1>

            
            <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900">
                Aradığınız sayfa bulunamadı
            </h2>
            <p class="mt-3 text-base sm:text-lg text-gray-500 max-w-md mx-auto">
                Sayfa taşınmış, kaldırılmış veya hiç var olmamış olabilir.
            </p>

            
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <a href="<?php echo e(route('home')); ?>" wire:navigate
                   class="group flex flex-col items-center gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-brand-orange/30">
                    <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-brand-orange/10 text-brand-orange transition group-hover:bg-brand-orange group-hover:text-white">
                        <i class="fa-solid fa-house text-lg"></i>
                    </span>
                    <span class="text-sm font-semibold text-gray-900">Ana Sayfa</span>
                </a>

                
                <a href="<?php echo e(route('products.index')); ?>" wire:navigate
                   class="group flex flex-col items-center gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-brand-orange/30">
                    <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-brand-orange/10 text-brand-orange transition group-hover:bg-brand-orange group-hover:text-white">
                        <i class="fa-solid fa-bag-shopping text-lg"></i>
                    </span>
                    <span class="text-sm font-semibold text-gray-900">Ürünler</span>
                </a>

                
                <a href="<?php echo e(route('contact')); ?>" wire:navigate
                   class="group flex flex-col items-center gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-brand-orange/30">
                    <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-brand-orange/10 text-brand-orange transition group-hover:bg-brand-orange group-hover:text-white">
                        <i class="fa-solid fa-envelope text-lg"></i>
                    </span>
                    <span class="text-sm font-semibold text-gray-900">İletişim</span>
                </a>
            </div>

            
            <div class="mt-8">
                <button type="button"
                        x-data
                        @click="$dispatch('open-search')"
                        class="inline-flex items-center gap-2 text-sm font-medium text-brand-orange hover:text-orange-600 transition-colors cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Ürünlerimizde arama yapmak için tıklayın
                </button>
            </div>
        </div>
    </section>
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
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\errors\404.blade.php ENDPATH**/ ?>