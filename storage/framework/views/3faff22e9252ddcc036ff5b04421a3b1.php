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

     <?php $__env->slot('title', null, []); ?> <?php echo e($page->meta_title ?? $page->title . ' | Patenli Ayakkabılar'); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('description', null, []); ?> <?php echo e($page->meta_description ?? Str::limit(strip_tags($page->content), 155)); ?> <?php $__env->endSlot(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($page->is_indexable) && !$page->is_indexable): ?>
         <?php $__env->slot('robots', null, []); ?> noindex, follow <?php $__env->endSlot(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <style>
        .page-content { font-size: 17px; line-height: 1.8; color: #374151; text-align: justify; }
        .page-content h1 { font-size: 2rem; font-weight: 800; color: #111827; margin: 2.5rem 0 1rem; line-height: 1.3; }
        .page-content h2 { font-size: 1.6rem; font-weight: 800; color: #111827; margin: 2.5rem 0 0.8rem; padding-bottom: 0.6rem; border-bottom: 2px solid #f3f4f6; line-height: 1.3; }
        .page-content h3 { font-size: 1.3rem; font-weight: 700; color: #1f2937; margin: 2rem 0 0.6rem; line-height: 1.4; }
        .page-content h4 { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 1.5rem 0 0.5rem; }
        .page-content p { margin: 0 0 1.2rem; }
        .page-content a { color: #2563eb; font-weight: 500; text-decoration: none; }
        .page-content a:hover { text-decoration: underline; }
        .page-content strong, .page-content b { color: #111827; font-weight: 700; }
        .page-content ul, .page-content ol { margin: 1rem 0 1.5rem 1.5rem; }
        .page-content ul { list-style-type: disc; }
        .page-content ol { list-style-type: decimal; }
        .page-content li { margin-bottom: 0.4rem; line-height: 1.7; }
        .page-content blockquote { margin: 1.5rem 0; padding: 1rem 1.5rem; border-left: 4px solid #3b82f6; background: #eff6ff; border-radius: 0 12px 12px 0; color: #1e40af; font-style: normal; }
        .page-content img { max-width: 100%; height: auto; border-radius: 16px; margin: 1.5rem 0; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .page-content code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; font-family: monospace; }
        .page-content pre { background: #1f2937; color: #e5e7eb; padding: 1.2rem; border-radius: 12px; overflow-x: auto; margin: 1.5rem 0; }
        .page-content pre code { background: none; padding: 0; color: inherit; }
        .page-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .page-content th, .page-content td { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; text-align: left; }
        .page-content th { background: #f9fafb; font-weight: 700; color: #111827; }
        .page-content hr { border: none; border-top: 2px solid #f3f4f6; margin: 2rem 0; }
    </style>

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => $page->title],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => $page->title],
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

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-12">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-8 border-b border-gray-100 pb-6">
                    <?php echo e($page->title); ?>

                </h1>
                
                <div class="page-content">
                    <?php echo $page->content; ?>

                </div>
            </div>
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
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\pages\show.blade.php ENDPATH**/ ?>