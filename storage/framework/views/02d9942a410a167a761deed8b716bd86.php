<div style="display: flex; flex-direction: column; gap: 6px; width: max-content; min-width: 90px;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $getState(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div style="display: flex; flex-direction: row; align-items: center; gap: 8px; flex-wrap: nowrap; white-space: nowrap;">
            <img
                src="<?php echo e($item['image']); ?>"
                alt="Ürün"
                style="width: 50px; height: 50px; min-width: 50px; max-width: 50px; object-fit: cover; border: 1px solid rgba(255,255,255,0.18); border-radius: 8px; transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease; cursor: zoom-in; transform-origin: center left; position: relative; z-index: 10; flex-shrink: 0;"
                class="hover:scale-[2.6] hover:z-[9999] hover:shadow-2xl hover:rounded-xl hover:border-2 hover:border-sky-400"
            />
            <span style="background: rgba(99,102,241,0.95); color: #ffffff; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 6px; white-space: nowrap; flex-shrink: 0; display: inline-block; line-height: 1.4; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                x<?php echo e($item['quantity']); ?>

            </span>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\filament\columns\order-product-images.blade.php ENDPATH**/ ?>