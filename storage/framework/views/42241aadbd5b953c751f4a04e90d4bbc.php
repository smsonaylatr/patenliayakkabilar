<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('rating_success_' . $order->id)): ?>
        <div class="mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            <?php echo e(session('rating_success_' . $order->id)); ?>

        </div>
    <?php else: ?>
        
        <div class="mt-4 p-5 rounded-2xl bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200/60 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-500 flex-shrink-0">
                        <i class="fa-solid fa-star text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Siparişinizi Değerlendirin</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Aldığınız ürünlere puan vererek diğer müşterilere yardımcı olun.</p>
                    </div>
                </div>
                <button
                    wire:click="$set('showModal', true)"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-md shadow-amber-200 transition-all active:scale-95"
                >
                    <i class="fa-solid fa-star"></i>
                    Değerlendir
                </button>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal): ?>
        <div
            class="fixed inset-0 z-[9999] overflow-y-auto"
            x-data="{ open: true }"
            x-show="open"
            x-on:rating-submitted.window="open = false"
        >
            
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4">
                
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl w-full max-w-lg border border-gray-100"
                >
                    
                    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 px-5 py-4 flex justify-between items-center border-b border-amber-100/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-500">
                                <i class="fa-solid fa-star text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-gray-900">Siparişi Değerlendir</h3>
                                <p class="text-[10px] text-gray-500 mt-0.5">Sipariş #<?php echo e($order->order_number); ?></p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="$set('showModal', false)"
                            aria-label="Kapat"
                            class="text-gray-400 hover:text-gray-600 transition-colors bg-white w-7 h-7 rounded-full flex items-center justify-center shadow-sm border border-gray-200"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    
                    <div class="px-5 py-5 space-y-5 max-h-[60vh] overflow-y-auto">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product): ?>
                                <div class="flex flex-col sm:flex-row gap-4 p-4 rounded-xl bg-gray-50/80 border border-gray-100">
                                    
                                    <div class="flex-shrink-0">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product->images->count() > 0): ?>
                                            <img
                                                src="<?php echo e(Storage::url($item->product->images->first()->image_path)); ?>"
                                                alt="<?php echo e($item->product_name); ?>"
                                                class="w-16 h-16 rounded-lg object-cover border border-gray-200"
                                            >
                                        <?php else: ?>
                                            <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400">
                                                <i class="fa-solid fa-shoe-prints"></i>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 truncate"><?php echo e($item->product_name); ?></h4>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant_info): ?>
                                            <p class="text-xs text-gray-500 mt-0.5"><?php echo e($item->variant_info); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <div class="mt-3">
                                            <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Puanınız <span class="text-red-400">*</span></label>
                                            <div class="flex gap-1 text-2xl">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                    <button
                                                        type="button"
                                                        wire:click="setRating(<?php echo e($item->product_id); ?>, <?php echo e($i); ?>)"
                                                        aria-label="<?php echo e($i); ?> yıldız"
                                                        class="focus:outline-none transition-transform hover:scale-110 active:scale-90 <?php echo e(($ratings[$item->product_id] ?? 5) >= $i ? 'text-yellow-400' : 'text-gray-300'); ?>"
                                                    >
                                                        <i class="fa-solid fa-star drop-shadow-sm"></i>
                                                    </button>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="mt-3">
                                            <label class="block text-[11px] font-bold text-gray-600 mb-1">
                                                Yorumunuz
                                                <span class="text-gray-400 font-normal text-[9px]">(İsteğe Bağlı)</span>
                                            </label>
                                            <textarea
                                                wire:model="comments.<?php echo e($item->product_id); ?>"
                                                rows="2"
                                                class="w-full rounded-lg border-gray-200 bg-white shadow-inner focus:border-amber-400 focus:ring-2 focus:ring-amber-100 text-xs px-3 py-2 transition-all resize-none"
                                                placeholder="Bu ürün hakkında ne düşünüyorsunuz?"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    
                    <div class="bg-gray-50 px-5 py-4 flex flex-col sm:flex-row-reverse gap-2 border-t border-gray-100">
                        <button
                            type="button"
                            wire:click="submitRatings"
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl px-6 py-2.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-200 transition-all active:scale-95 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="submitRatings">
                                <i class="fa-solid fa-paper-plane"></i>
                                Değerlendirmeyi Gönder
                            </span>
                            <span wire:loading wire:target="submitRatings">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                Gönderiliyor...
                            </span>
                        </button>
                        <button
                            type="button"
                            wire:click="$set('showModal', false)"
                            class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-white px-6 py-2.5 text-xs font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all active:scale-95"
                        >
                            Daha Sonra
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\account\order-rating.blade.php ENDPATH**/ ?>