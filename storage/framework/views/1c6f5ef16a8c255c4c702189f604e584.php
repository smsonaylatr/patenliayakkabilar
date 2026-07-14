<div class="min-h-screen bg-gray-50 pt-8 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Güvenli Ödeme</h1>
            <p class="text-gray-500 mt-1">Siparişinizi tamamlamak için lütfen bilgilerinizi girin.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Sol Taraf: Form -->
            <div class="flex-1">
                <form wire:submit.prevent="placeOrder" class="space-y-8">
                    
                    <!-- İletişim Bilgileri -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white text-xs">1</span>
                            İletişim Bilgileri
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                                <input type="text" wire:model="customer_name" class="w-full px-4 py-3 text-base rounded-xl border-gray-200 focus:ring-0 focus:outline-none focus:border-black transition-colors" placeholder="Adınız ve Soyadınız">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['customer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                <input type="email" wire:model="customer_email" class="w-full px-4 py-3 text-base rounded-xl border-gray-200 focus:ring-0 focus:outline-none focus:border-black transition-colors" placeholder="ornek@email.com">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['customer_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon Numarası</label>
                                <input type="tel" wire:model="customer_phone" class="w-full px-4 py-3 text-base rounded-xl border-gray-200 focus:ring-0 focus:outline-none focus:border-black transition-colors" placeholder="0 (5XX) XXX XX XX">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['customer_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Teslimat Adresi -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white text-xs">2</span>
                            Teslimat Adresi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İl</label>
                                <select wire:model.live="shipping_city" class="w-full px-4 py-3 text-base rounded-xl border-gray-200 focus:ring-0 focus:outline-none focus:border-black transition-colors">
                                    <option value="">İl Seçiniz</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($city); ?>"><?php echo e($city); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipping_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İlçe</label>
                                <select wire:model="shipping_district" class="w-full px-4 py-3 text-base rounded-xl border-gray-200 focus:ring-0 focus:outline-none focus:border-black transition-colors" <?php echo e(empty($districts) ? 'disabled' : ''); ?>>
                                    <option value="">İlçe Seçiniz</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($district); ?>"><?php echo e($district); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipping_district'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Açık Adres</label>
                                <textarea wire:model="shipping_address" rows="3" class="w-full px-4 py-3 text-base rounded-xl border-gray-200 focus:ring-0 focus:outline-none focus:border-black transition-colors custom-scrollbar" placeholder="Mahalle, sokak, bina ve daire no..."></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sipariş Notu (Opsiyonel)</label>
                                <textarea wire:model="customer_note" rows="2" class="w-full px-4 py-3 text-base rounded-xl border-gray-200 focus:ring-0 focus:outline-none focus:border-black transition-colors custom-scrollbar" placeholder="Kuryeye veya mağazaya iletmek istedikleriniz..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Ödeme Yöntemi -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white text-xs">3</span>
                            Ödeme Yöntemi
                        </h2>
                        
                        <div class="space-y-3">
                            <!-- Kapıda Ödeme Seçeneği -->
                            <label class="flex items-start p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors <?php echo e($payment_method === 'cash_on_delivery' ? 'border-black bg-gray-50' : 'border-gray-200'); ?>">
                                <div class="flex items-center h-5">
                                    <input wire:model.live="payment_method" type="radio" value="cash_on_delivery" class="w-5 h-5 text-black border-gray-300 focus:ring-0 focus:outline-none">
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-gray-900">Kapıda Ödeme</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">Ürünü teslim alırken nakit veya kredi kartı ile ödeyin.</span>
                                </div>
                            </label>

                            <!-- Havale/EFT Seçeneği -->
                            <label class="flex items-start p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors <?php echo e($payment_method === 'wire_transfer' ? 'border-black bg-gray-50' : 'border-gray-200'); ?>">
                                <div class="flex items-center h-5">
                                    <input wire:model.live="payment_method" type="radio" value="wire_transfer" class="w-5 h-5 text-black border-gray-300 focus:ring-0 focus:outline-none">
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-gray-900">Havale / EFT</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">Ödemeyi banka hesabımıza doğrudan aktarın.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Mobil İçin Buton (Sadece Mobilde Görünür) -->
                    <div class="lg:hidden">
                        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold text-lg py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="placeOrder">Siparişi Onayla</span>
                            <span wire:loading wire:target="placeOrder">İşleniyor...</span>
                            <svg wire:loading.remove wire:target="placeOrder" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Sağ Taraf: Sipariş Özeti -->
            <div class="w-full lg:w-[400px]">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 border-b pb-4">Sipariş Özeti</h2>
                    
                    <!-- Ürünler -->
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-3 pt-2 custom-scrollbar">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex gap-4">
                                <div class="w-16 h-16 rounded-xl bg-gray-100 flex-shrink-0 border border-gray-200 relative">
                                    <span class="absolute -top-2 -right-2 bg-black text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full z-10 shadow-sm"><?php echo e($item->quantity); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product && $item->product->images->first()): ?>
                                        <img src="<?php echo e($item->product->images->first()->image_url); ?>" class="w-full h-full object-cover rounded-xl">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 rounded-xl">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="flex-1 flex flex-col justify-center">
                                    <h4 class="text-sm font-bold text-gray-900 line-clamp-1"><?php echo e($item->product ? $item->product->name : 'Bilinmeyen Ürün'); ?></h4>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant): ?>
                                        <span class="text-xs text-gray-500 mt-0.5">Beden/Seçenek: <?php echo e($item->variant->size); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="text-sm font-bold text-red-600 mt-1">
                                        <?php echo e(number_format($item->price, 2)); ?> ₺
                                    </div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p class="text-sm text-gray-500 text-center py-4">Sepetiniz boş.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Fiyat Toplamları -->
                    <div class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Ara Toplam</span>
                            <span><?php echo e(number_format($subtotal, 2)); ?> ₺</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Kargo <span class="text-[10px] text-gray-400 ml-1">(Ürün Başı 1 ₺)</span></span>
                            <span class="text-gray-900 font-medium"><?php echo e(number_format($shippingPrice, 2)); ?> ₺</span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-gray-900 pt-3 border-t border-gray-100 mt-3">
                            <span>Toplam</span>
                            <span class="text-red-600"><?php echo e(number_format($grandTotal, 2)); ?> ₺</span>
                        </div>
                    </div>

                    <!-- Desktop İçin Buton -->
                    <div class="mt-8 hidden lg:block">
                        <button wire:click="placeOrder" class="w-full bg-black hover:bg-gray-800 text-white font-bold text-lg py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="placeOrder">Siparişi Onayla</span>
                            <span wire:loading wire:target="placeOrder">İşleniyor...</span>
                            <svg wire:loading.remove wire:target="placeOrder" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        256-bit SSL ile güvenli ödeme
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\frontend\checkout.blade.php ENDPATH**/ ?>