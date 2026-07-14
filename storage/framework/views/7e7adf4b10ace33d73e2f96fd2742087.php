<?php if (isset($component)) { $__componentOriginalcf324bfcd6feee0107a23f728e62ecac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcf324bfcd6feee0107a23f728e62ecac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.account-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('account-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="max-w-4xl">
        <h1 class="text-3xl font-black text-gray-900 mb-8">Siparişlerim</h1>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->count() > 0): ?>
            <div class="space-y-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="bg-gray-50/80 px-8 py-5 flex flex-wrap items-center justify-between border-b border-gray-200 gap-4">
                            <div class="flex items-center space-x-6">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-1">Sipariş Tarihi</p>
                                    <p class="text-sm font-medium text-gray-900"><?php echo e($order->created_at->format('d M Y')); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-1">Toplam Tutar</p>
                                    <p class="text-sm font-black text-gray-900"><?php echo e(number_format($order->grand_total, 2, ',', '.')); ?> ₺</p>
                                </div>
                            </div>
                            <div class="text-right flex-1 sm:flex-none">
                                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-1">Sipariş No</p>
                                <p class="text-sm font-black text-gray-900">#<?php echo e($order->order_number); ?></p>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                <div class="flex items-center space-x-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->status === 'pending'): ?>
                                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Ödeme Bekliyor</h3>
                                            <p class="text-sm text-gray-500">Siparişiniz onay bekliyor.</p>
                                        </div>
                                    <?php elseif($order->status === 'processing'): ?>
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Hazırlanıyor</h3>
                                            <p class="text-sm text-gray-500">Siparişiniz paketleniyor.</p>
                                        </div>
                                    <?php elseif($order->status === 'shipped'): ?>
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Kargoya Verildi</h3>
                                            <p class="text-sm text-gray-500">Siparişiniz yola çıktı.</p>
                                        </div>
                                    <?php elseif($order->status === 'delivered'): ?>
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Teslim Edildi</h3>
                                            <p class="text-sm text-gray-500">Siparişiniz başarıyla teslim edildi.</p>
                                        </div>
                                    <?php elseif($order->status === 'cancelled'): ?>
                                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">İptal Edildi</h3>
                                            <p class="text-sm text-gray-500">Bu sipariş iptal edilmiştir.</p>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->cargo_company && $order->cargo_tracking_code): ?>
                                    <?php
                                        $cargoName = $order->cargo_company;
                                        if (strtolower($cargoName) === 'aras') {
                                            $cargoName = 'Aras Kargo';
                                        } elseif (strtolower($cargoName) === 'yurtici' || strtolower($cargoName) === 'yurtiçi') {
                                            $cargoName = 'Yurtiçi Kargo';
                                        }
                                        
                                        $trackingUrl = '#';
                                        if (strtolower($cargoName) === 'aras kargo') {
                                            $trackingUrl = 'https://kargotakip.araskargo.com.tr/mainpage.aspx?code=' . $order->cargo_tracking_code;
                                        } elseif (strtolower($cargoName) === 'yurtiçi kargo') {
                                            $trackingUrl = 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=' . $order->cargo_tracking_code;
                                        }
                                    ?>
                                    <div class="mt-4 sm:mt-0 flex items-center gap-3 sm:gap-4 bg-gray-50/80 rounded-xl px-4 py-3 border border-gray-200">
                                        <div class="flex flex-col">
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5"><?php echo e($cargoName); ?></p>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-bold text-gray-900 font-mono tracking-tight"><?php echo e($order->cargo_tracking_code); ?></p>
                                                <button onclick="navigator.clipboard.writeText('<?php echo e($order->cargo_tracking_code); ?>'); alert('Kopyalandı!');" class="text-gray-400 hover:text-gray-700 transition-colors focus:outline-none" title="Kodu Kopyala">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                                        
                                        <a href="<?php echo e($trackingUrl); ?>" target="_blank" class="shrink-0 text-gray-400 hover:text-black transition-colors p-2 -mr-2" title="Kargo Sayfasına Git">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            
            <div class="pt-8">
                <?php echo e($orders->links()); ?>

            </div>
        <?php else: ?>
            <div class="bg-gray-50/50 border border-gray-100 rounded-3xl p-16 text-center">
                <div class="w-20 h-20 bg-white shadow-sm border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3">Henüz Siparişiniz Yok</h3>
                <p class="text-gray-500 text-base mb-8 max-w-sm mx-auto leading-relaxed">Sipariş geçmişiniz boş görünüyor. Hemen koleksiyonumuzu inceleyip ilk siparişinizi oluşturabilirsiniz.</p>
                <a href="/" class="inline-flex items-center justify-center bg-black hover:bg-gray-800 shadow-lg shadow-black/10 text-white font-bold text-sm px-8 py-4 rounded-xl transition-colors">
                    Alışverişe Başla
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcf324bfcd6feee0107a23f728e62ecac)): ?>
<?php $attributes = $__attributesOriginalcf324bfcd6feee0107a23f728e62ecac; ?>
<?php unset($__attributesOriginalcf324bfcd6feee0107a23f728e62ecac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcf324bfcd6feee0107a23f728e62ecac)): ?>
<?php $component = $__componentOriginalcf324bfcd6feee0107a23f728e62ecac; ?>
<?php unset($__componentOriginalcf324bfcd6feee0107a23f728e62ecac); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\account\orders.blade.php ENDPATH**/ ?>