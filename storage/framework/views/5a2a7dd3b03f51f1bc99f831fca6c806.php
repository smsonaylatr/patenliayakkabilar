<div class="w-full flex flex-col gap-6" x-data="{ qty: <?php if ((object) ('quantity') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('quantity'->value()); ?>')<?php echo e('quantity'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('quantity'); ?>')<?php endif; ?>, maxStock: <?php if ((object) ('maxStock') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('maxStock'->value()); ?>')<?php echo e('maxStock'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('maxStock'); ?>')<?php endif; ?> }">
    <?php
        $isOutOfStock = !$product->inStock();
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isOutOfStock): ?>
    <!-- Adet Seçici Kutusu (Shopier Style) -->
    <div class="flex items-center justify-between border border-gray-200 rounded-full h-12 sm:h-14 px-4 sm:px-5 bg-white">
        <span class="text-sm sm:text-base font-medium text-gray-900">Adet</span>
        <div class="flex items-center gap-1">
            <button type="button" @click="qty > 1 ? qty-- : null" aria-label="Adedi Azalt" class="text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-full focus:outline-none w-11 h-11 sm:w-9 sm:h-9 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-minus text-[10px]"></i>
            </button>
            <span class="text-base font-medium text-gray-900 w-10 text-center" x-text="qty"></span>
            <button type="button" @click="qty < maxStock ? qty++ : null" :disabled="qty >= maxStock" aria-label="Adedi Artır" :class="qty >= maxStock ? 'text-gray-300 bg-gray-50 cursor-not-allowed' : 'text-gray-500 bg-gray-50 hover:bg-gray-100'" class="rounded-full focus:outline-none w-11 h-11 sm:w-9 sm:h-9 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-plus text-[10px]"></i>
            </button>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Sepete Ekle / Tükendi Butonu -->
    <button 
        <?php if(!$isOutOfStock): ?> wire:click="addToCart" <?php endif; ?> 
        wire:loading.attr="disabled" 
        type="button" 
        <?php if($isOutOfStock): ?> disabled <?php endif; ?>
        class="group relative flex w-full h-12 sm:h-14 items-center justify-center gap-3 overflow-hidden rounded-full <?php echo e($isOutOfStock ? 'bg-gray-200 text-gray-500 cursor-not-allowed shadow-none' : 'bg-gray-900 text-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:scale-[1.02] hover:bg-black hover:shadow-[0_8px_30px_rgb(0,0,0,0.2)]'); ?> px-4 sm:px-8 text-sm sm:text-base font-bold transition-all duration-300 disabled:cursor-not-allowed disabled:opacity-70">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isOutOfStock): ?>
        <!-- Shine effect on hover -->
        <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
            <div class="relative h-full w-8 bg-white/20"></div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <span class="flex items-center gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOutOfStock): ?>
                <i class="fa-solid fa-ban text-sm"></i>
                Tükendi
            <?php else: ?>
                <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                
                <svg wire:loading wire:target="addToCart" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                Sepete Ekle
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
    </button>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOutOfStock): ?>
    <!-- Gelince Haber Ver Butonu -->
    <button 
        @click="$dispatch('open-stock-modal', { productId: <?php echo e($product->id); ?>, variantId: '<?php echo e($variantId); ?>' })"
        type="button" 
        class="w-full h-12 sm:h-14 flex items-center justify-center gap-2.5 rounded-full bg-brand-orange hover:bg-[#e56a10] text-white font-bold text-sm sm:text-base shadow-[0_8px_25px_rgba(255,122,26,0.3)] transition-all duration-300 hover:scale-[1.02] cursor-pointer">
        <i class="fa-solid fa-bell"></i>
        <span>Gelince Haber Ver</span>
    </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\product\add-to-cart-button.blade.php ENDPATH**/ ?>