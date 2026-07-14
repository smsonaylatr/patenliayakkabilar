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

    <div class="min-h-[70vh] bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center">
                <h1 class="text-3xl font-black text-gray-900 mb-4">Sipariş Takip</h1>
                <p class="text-gray-500 mb-8">Sipariş durumunuzu öğrenmek için sipariş numaranızı girin.</p>
                
                <form action="<?php echo e(route('order.tracking')); ?>" method="GET" class="space-y-4">
                    <div>
                        <input type="text" name="order_number" value="<?php echo e(request('order_number')); ?>" class="w-full rounded-xl border-gray-200 focus:ring-black focus:border-black text-center text-lg py-3 uppercase tracking-wider" placeholder="Örn: TR123456" required>
                    </div>
                    <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98]">
                        Sorgula
                    </button>
                </form>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($error)): ?>
                    <div class="mt-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 text-sm">
                        <?php echo e($error); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($order)): ?>
                    <div class="mt-8 pt-8 border-t border-gray-100 text-left">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Sipariş Detayları</h2>
                        
                        <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Tarih:</span>
                                <span class="font-medium text-gray-900 text-sm"><?php echo e($order->created_at->format('d.m.Y H:i')); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Ödeme Yöntemi:</span>
                                <span class="font-medium text-gray-900 text-sm">
                                    <?php echo e($order->payment_method === 'cash_on_delivery' ? 'Kapıda Ödeme' : 'Havale / EFT'); ?>

                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Toplam Tutar:</span>
                                <span class="font-bold text-red-600 text-sm"><?php echo e(number_format($order->grand_total, 2)); ?> ₺</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-200 pt-3 mt-3">
                                <span class="text-gray-500 text-sm">Sipariş Durumu:</span>
                                <div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->status === 'pending'): ?>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Bekliyor</span>
                                    <?php elseif($order->status === 'processing'): ?>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Hazırlanıyor</span>
                                    <?php elseif($order->status === 'shipped'): ?>
                                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Kargoya Verildi</span>
                                    <?php elseif($order->status === 'completed'): ?>
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Tamamlandı</span>
                                    <?php elseif($order->status === 'cancelled'): ?>
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">İptal Edildi</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider"><?php echo e($order->status); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-8 pt-8 border-t border-gray-100 text-sm text-gray-500">
                        <p>Sipariş numaranız e-posta adresinize ve SMS ile telefonunuza gönderilmiştir.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\order\tracking.blade.php ENDPATH**/ ?>