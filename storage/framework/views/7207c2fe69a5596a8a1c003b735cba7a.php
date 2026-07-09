<div class="mt-8" x-data="{ selectedId: <?php if ((object) ('selectedVariantId') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedVariantId'->value()); ?>')<?php echo e('selectedVariantId'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedVariantId'); ?>')<?php endif; ?>.live }">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-900 tracking-wide">Beden Seçimi</h2>
        <a href="#" class="text-sm text-gray-500 hover:text-gray-900 underline decoration-gray-300 hover:decoration-gray-900 transition-colors">Beden Tablosu</a>
    </div>

    <div class="flex flex-wrap gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button 
                @click="selectedId = <?php echo e($variant->id); ?>"
                type="button" 
                class="flex items-center justify-center border-2 py-2.5 px-6 rounded-full text-sm font-medium transition-all duration-150 <?php echo e($variant->stock <= 0 ? 'opacity-40 cursor-not-allowed line-through' : ''); ?>"
                :class="selectedId == <?php echo e($variant->id); ?> 
                    ? 'border-gray-900 bg-gray-900 text-white shadow-md transform scale-105' 
                    : 'border-gray-200 bg-white text-gray-600 hover:border-gray-400 hover:bg-gray-50 hover:text-gray-900 hover:shadow-sm'"
                <?php echo e($variant->stock <= 0 ? 'disabled' : ''); ?>

            >
                <?php echo e($variant->size); ?>

            </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\product\variant-selector.blade.php ENDPATH**/ ?>