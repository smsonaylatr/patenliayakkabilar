<?php $__env->startSection('title', 'Size Özel %10 İndirim!'); ?>

<?php $__env->startSection('content'); ?>
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">Size Özel %10 İndirim Fırsatı! 🎁</h2>
</div>

<p style="font-size: 16px;">Merhaba <?php echo e($customerName); ?>,</p>
<p style="font-size: 15px; line-height: 1.6;">Sepetinize eklediğiniz harika ürünler hâlâ sizi bekliyor! Alışverişinizi tamamlamanız için size özel bir <strong>%10 indirim kuponu</strong> hazırladık.</p>

<div style="background: linear-gradient(135deg, #ff4e00, #ff7b3d); border-radius: 12px; padding: 25px; text-align: center; margin: 30px 0; color: #ffffff;">
    <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Kupon Kodunuz</div>
    <div style="font-size: 28px; font-weight: bold; letter-spacing: 3px; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 8px; display: inline-block; margin: 10px 0;"><?php echo e($couponCode); ?></div>
    <div style="font-size: 13px; opacity: 0.9; margin-top: 8px;">⏰ <?php echo e($expiresAt); ?> tarihine kadar geçerlidir</div>
</div>

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
    <a href="<?php echo e(url('/checkout')); ?>" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: bold; font-size: 16px;">Kuponu Kullan ve Satın Al</a>
</div>

<p style="text-align: center; color: #6b7280; font-size: 13px; margin-top: 20px;">Checkout sayfasında kupon kodunuzu girerek indirimi uygulayabilirsiniz.</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\emails\abandoned_cart_coupon.blade.php ENDPATH**/ ?>