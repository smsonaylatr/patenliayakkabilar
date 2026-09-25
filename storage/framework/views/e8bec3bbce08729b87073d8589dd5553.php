<?php $__env->startSection('title', 'Sepetinizde Ürünler Unuttunuz!'); ?>

<?php $__env->startSection('content'); ?>
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">Sepetinizde Ürünler Sizi Bekliyor! 🎁</h2>
</div>

<p style="font-size: 16px;">Merhaba <?php echo e($cart->user->name ?? 'Değerli Müşterimiz'); ?>,</p>
<p style="font-size: 15px; line-height: 1.6;">Patenli Ayakkabılar'da gezinirken sepetinize harika ürünler eklemiştiniz ama siparişinizi henüz tamamlamadınız. Stoklarımız tükenmeden sepetinizdeki ürünleri almak için hala bir şansınız var!</p>

<div style="margin-top: 30px; border-top: 1px solid #e5e7eb;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div style="display: flex; padding: 20px 0; border-bottom: 1px solid #e5e7eb;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product && $item->product->images->first()): ?>
                <img src="<?php echo e(url(Storage::url($item->product->images->first()->image_path))); ?>" alt="<?php echo e($item->product->name); ?>" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; margin-right: 20px; background-color: #f3f4f6;">
            <?php else: ?>
                <div style="width: 80px; height: 80px; border-radius: 8px; margin-right: 20px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #9ca3af; float: left;">Görsel Yok</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div style="flex-grow: 1;">
                <h3 style="font-size: 16px; font-weight: bold; margin: 0 0 5px 0;"><?php echo e($item->product->name ?? 'Ürün'); ?></h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant): ?>
                    <p style="font-size: 14px; color: #6b7280; margin: 0 0 5px 0;">Renk: <?php echo e(is_array($item->variant->color) ? implode(', ', $item->variant->color) : $item->variant->color); ?> | Beden: <?php echo e($item->variant->size); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <p style="font-size: 14px; color: #6b7280; margin: 0 0 5px 0;">Adet: <?php echo e($item->quantity); ?></p>
                <p style="font-size: 16px; font-weight: bold; color: #ff4e00; margin: 0;"><?php echo e(number_format($item->product->discount_price ?? $item->product->price ?? 0, 2)); ?> ₺</p>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>

<div style="text-align: center; margin-top: 40px;">
    <a href="<?php echo e(url('/checkout')); ?>" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: bold; font-size: 16px;">Sepetime Git ve Satın Al</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\emails\abandoned_cart_reminder.blade.php ENDPATH**/ ?>