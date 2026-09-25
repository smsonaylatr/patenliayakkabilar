<?php $__env->startSection('title', 'Beklediğiniz Ürün Stokta!'); ?>

<?php $__env->startSection('content'); ?>
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="color: #ff4e00; margin-top: 0;">🎉 Beklediğiniz Ürün Stokta!</h2>
</div>

<p style="font-size: 15px; text-align: center; margin-bottom: 25px;">Harika haber! Daha önce stok bildirimi istediğiniz ürünün stokları yenilendi.</p>

<div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 25px; margin: 20px 0; text-align: center;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->images->first()): ?>
        <img src="<?php echo e($product->images->first()->image_url); ?>" alt="<?php echo e($product->name); ?>" style="max-width: 220px; height: auto; border-radius: 8px; margin-bottom: 15px;">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <div style="font-size: 18px; font-weight: bold; margin-bottom: 10px;"><?php echo e($product->name); ?></div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant): ?>
        <div style="display: inline-block; background-color: #ffe4d6; color: #ff4e00; font-weight: bold; font-size: 13px; padding: 6px 14px; border-radius: 20px; margin-bottom: 15px;"><?php echo e($variant->size); ?> Beden</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <div style="font-size: 24px; font-weight: bold; color: #ff4e00; margin-bottom: 25px;"><?php echo e(number_format($product->discount_price ?? $product->price, 2)); ?> ₺</div>
    
    <a href="<?php echo e(url('/urun/' . $product->slug)); ?>" style="display: inline-block; background-color: #ff4e00; color: #ffffff; font-weight: bold; font-size: 15px; padding: 14px 32px; border-radius: 8px; text-decoration: none;">Hemen İncele & Satın Al</a>
</div>

<p style="font-size: 14px; color: #6b7280; text-align: center;">Stoklar tükenmeden siparişinizi vermek için acele edin!</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\emails\stock_back.blade.php ENDPATH**/ ?>