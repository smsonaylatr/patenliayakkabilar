<div class="mt-8" x-data="{ selectedId: <?php if ((object) ('selectedVariantId') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedVariantId'->value()); ?>')<?php echo e('selectedVariantId'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('selectedVariantId'); ?>')<?php endif; ?>.live }">
    <div class="relative" x-data="{ 
        open: false,
        variants: [
            <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                { id: <?php echo e($variant->id); ?>, size: '<?php echo e(addslashes($variant->size)); ?>', stock: <?php echo e($variant->stock); ?> }<?php echo e(!$loop->last ? ',' : ''); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        get selectedSize() {
            if (!this.selectedId) return 'Beden';
            let v = this.variants.find(v => v.id == this.selectedId);
            return v ? v.size : 'Beden';
        }
    }" @click.away="open = false" @open-variant-selector.window="open = true; setTimeout(() => $el.scrollIntoView({behavior: 'smooth', block: 'center'}), 100)">
        
        <!-- Dropdown Butonu -->
        <button 
            type="button"
            @click="open = !open"
            class="flex items-center justify-between w-full h-14 rounded-full border border-gray-200 bg-white px-5 text-base font-medium text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 sm:text-sm transition-colors cursor-pointer"
        >
            <span x-text="selectedSize"></span>
            <svg class="w-5 h-5 text-gray-800 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown Menü İçeriği -->
        <ul 
            x-show="open" 
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="mt-2 w-full rounded-2xl bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 py-2 focus:outline-none"
            style="display: none;"
        >
            <template x-for="variant in variants" :key="variant.id">
                <li>
                    <button 
                        type="button"
                        @click="if(variant.stock > 0) { selectedId = variant.id; open = false; }"
                        class="w-full text-left px-5 py-3 text-base font-medium transition-colors"
                        :class="{
                            'text-gray-900 hover:bg-gray-50': variant.stock > 0,
                            'text-gray-400 cursor-not-allowed': variant.stock <= 0,
                            'bg-gray-50 font-bold': selectedId == variant.id
                        }"
                        :disabled="variant.stock <= 0"
                    >
                        <span x-text="variant.size"></span>
                        <span x-show="variant.stock <= 0" class="ml-2 text-sm text-red-500 font-semibold">(Stokta Yok)</span>
                    </button>
                </li>
            </template>
        </ul>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\product\variant-selector.blade.php ENDPATH**/ ?>