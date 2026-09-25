<?php $__env->startSection('title', 'E-Faturanız Oluşturuldu'); ?>

<?php $__env->startSection('content'); ?>
<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="margin-top: 0;">Merhaba <?php echo e($order->customer_name); ?>,</h2>
</div>

<p style="font-size: 15px; line-height: 1.6;"><strong><?php echo e($order->order_number); ?></strong> numaralı siparişinize ait e-faturanız/e-arşiv belgeniz başarıyla oluşturulmuştur.</p>

<p style="font-size: 15px; line-height: 1.6;">Faturanızı bu e-postanın ekinde PDF formatında bulabilirsiniz.</p>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pdfUrl)): ?>
    <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
        <a href="<?php echo e($pdfUrl); ?>" style="display: inline-block; padding: 14px 28px; background-color: #ff4e00; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px;">Faturayı Tarayıcıda Görüntüle</a>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<p style="font-size: 15px; line-height: 1.6;">Bizi tercih ettiğiniz için teşekkür ederiz.</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\emails\order-invoice.blade.php ENDPATH**/ ?>