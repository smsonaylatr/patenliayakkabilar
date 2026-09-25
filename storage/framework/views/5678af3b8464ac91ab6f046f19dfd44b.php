<?php $__env->startSection('title', 'E-Arşiv Faturanız'); ?>

<?php $__env->startSection('content'); ?>
<div style="font-size: 16px; font-weight: 600; margin-bottom: 20px;">Sayın <?php echo e($order->customer_name); ?>,</div>

<p style="font-size: 15px; line-height: 1.6; margin-bottom: 25px;">
    Patenli Ayakkabılar mağazamızdan vermiş olduğunuz <strong>#<?php echo e($order->order_number); ?></strong> numaralı siparişinize ait resmi GİB E-Arşiv faturanız başarıyla oluşturulmuştur. Faturanız bu e-postanın ekinde ve aşağıdaki bağlantıda yer almaktadır.
</p>

<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="font-size: 14px; color: #475569;">Sipariş Numarası:</td>
            <td style="font-size: 14px; text-align: right; font-weight: 600; color: #0f172a;">#<?php echo e($order->order_number); ?></td>
        </tr>
        <tr>
            <td style="font-size: 14px; color: #475569;">Sipariş Tarihi:</td>
            <td style="font-size: 14px; text-align: right; font-weight: 600; color: #0f172a;"><?php echo e($order->created_at ? $order->created_at->format('d.m.Y H:i') : date('d.m.Y')); ?></td>
        </tr>
        <tr>
            <td style="font-size: 14px; color: #475569;">Fatura Tarihi:</td>
            <td style="font-size: 14px; text-align: right; font-weight: 600; color: #0f172a;"><?php echo e($order->gib_invoice_date ? $order->gib_invoice_date->format('d.m.Y H:i') : date('d.m.Y')); ?></td>
        </tr>
        <tr>
            <td style="font-size: 14px; color: #475569;">Toplam Tutar:</td>
            <td style="text-align: right; font-weight: 600; color: #ff4e00; font-size: 16px;">₺<?php echo e(number_format($order->grand_total, 2, ',', '.')); ?></td>
        </tr>
    </table>
</div>

<div style="text-align: center; margin: 30px 0 15px;">
    <a href="<?php echo e($invoiceUrl); ?>" target="_blank" style="display: inline-block; background-color: #ff4e00; color: #ffffff; font-weight: 600; font-size: 15px; text-decoration: none; padding: 14px 32px; border-radius: 8px;">📄 Faturayı Görüntüle ve Yazdır</a>
</div>

<p style="font-size: 13px; color: #64748b; text-align: center; margin-top: 15px;">
    Faturanızı dilediğiniz zaman bilgisayarınıza indirebilir veya yazdırabilirsiniz.
</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layouts.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\emails\gib-invoice.blade.php ENDPATH**/ ?>