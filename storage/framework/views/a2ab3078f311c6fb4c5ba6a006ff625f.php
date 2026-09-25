<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                <!-- Close Button -->
                <button type="button" wire:click="closeModal" aria-label="Kapat" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>

                <div class="p-6 sm:p-8">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuccess): ?>
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl mb-4 shadow-xs animate-bounce">
                                <i class="fa-solid fa-bell-circle-check"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Talebiniz Alındı!</h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-6"><?php echo e($message); ?></p>
                            <button type="button" wire:click="closeModal" class="w-full h-12 bg-gray-900 text-white font-bold rounded-full hover:bg-black transition-colors shadow-md">
                                Tamam
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 rounded-2xl bg-orange-100 text-brand-orange flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Gelince Haber Ver</h3>
                                <p class="text-xs text-gray-500">Stoklar yenilendiğinde anında bilgilendirileceksiniz.</p>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product): ?>
                        <div class="mb-6 p-3.5 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->images->first()): ?>
                                <img src="<?php echo e($product->images->first()->image_url); ?>" alt="<?php echo e($product->name); ?>" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-gray-900 truncate"><?php echo e($product->name); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedVariant): ?>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 text-[11px] font-semibold bg-orange-100 text-orange-800 rounded">
                                        <?php echo e($selectedVariant->size); ?> Beden
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <form wire:submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">E-Posta Adresi <span class="text-red-500">*</span></label>
                                <input type="email" wire:model="email" placeholder="ornek@email.com" class="w-full h-12 rounded-xl border border-gray-200 px-4 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block font-medium"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Telefon Numarası (SMS için isteğe bağlı)</label>
                                <input type="tel" wire:model="phone" placeholder="05XX XXX XX XX" class="w-full h-12 rounded-xl border border-gray-200 px-4 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block font-medium"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="flex items-start gap-2 pt-1">
                                <input type="checkbox" id="kvkkConsent" wire:model="kvkkConsent" class="mt-1 rounded text-brand-orange focus:ring-brand-orange">
                                <label for="kvkkConsent" class="text-xs text-gray-500 leading-tight">
                                    Stok durumu hakkında e-posta/SMS bilgilendirmesi almayı kabul ediyorum.
                                </label>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['kvkkConsent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 block font-medium"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <button type="submit" wire:loading.attr="disabled" class="w-full h-13 mt-2 bg-brand-orange text-white font-bold rounded-full hover:bg-[#e56a10] transition-colors shadow-lg flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="submit"><i class="fa-solid fa-paper-plane text-xs"></i> Haber Ver</span>
                                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Kaydediliyor...
                                </span>
                            </button>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\product\stock-notification-modal.blade.php ENDPATH**/ ?>