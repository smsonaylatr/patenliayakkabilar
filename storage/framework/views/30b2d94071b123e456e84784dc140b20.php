<div>
    <div class="mb-4">
        <h4 class="font-bold text-lg mb-2">Beden Stokları</h4>
        <div class="flex flex-wrap gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="p-2 border rounded dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-center min-w-[60px]">
                    <div class="text-xs text-gray-500"><?php echo e($variant->size); ?></div>
                    <div class="font-bold <?php echo e($variant->stock <= 0 ? 'text-danger-600' : ($variant->stock <= 3 ? 'text-warning-600' : 'text-success-600')); ?>">
                        <?php echo e($variant->stock); ?>

                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <div class="mt-2 font-bold">
            Toplam Stok: <?php echo e($product->stock); ?>

        </div>
    </div>

    <div>
        <h4 class="font-bold text-lg mb-2">Son 10 Stok Hareketi</h4>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($movements->isEmpty()): ?>
            <p class="text-sm text-gray-500">Henüz stok hareketi bulunmuyor.</p>
        <?php else: ?>
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="p-2 border dark:border-gray-700">Tarih</th>
                        <th class="p-2 border dark:border-gray-700">Beden</th>
                        <th class="p-2 border dark:border-gray-700">Değişim</th>
                        <th class="p-2 border dark:border-gray-700">Not</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td class="p-2 border dark:border-gray-700"><?php echo e($movement->created_at->format('d.m.Y H:i')); ?></td>
                            <td class="p-2 border dark:border-gray-700"><?php echo e($movement->variant?->size ?? '-'); ?></td>
                            <td class="p-2 border dark:border-gray-700 font-bold <?php echo e($movement->quantity > 0 && $movement->type === App\Models\StockMovement::TYPE_RESTOCK ? 'text-success-600' : ($movement->quantity < 0 ? 'text-danger-600' : '')); ?>">
                                <?php echo e($movement->new_stock - $movement->old_stock > 0 ? '+' : ''); ?><?php echo e($movement->new_stock - $movement->old_stock); ?>

                            </td>
                            <td class="p-2 border dark:border-gray-700"><?php echo e($movement->note); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\filament\pages\product-stock-detail-modal.blade.php ENDPATH**/ ?>