<div x-data="{ 
        images: [
            <?php $__empty_1 = true; $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                '<?php echo e($image->image_url); ?>',
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
            <?php endif; ?>
        ],
        currentIndex: 0,
        get mainImage() { return this.images[this.currentIndex]; },
        touchStartX: 0,
        touchEndX: 0,
        isZoomed: false,
        zoomX: 50,
        zoomY: 50,
        handleSwipe() {
            if (this.isZoomed) return; // Disable swiping between images when zoomed in
            let swipeDistance = this.touchEndX - this.touchStartX;
            if (swipeDistance > 50) {
                this.currentIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.images.length - 1;
            } else if (swipeDistance < -50) {
                this.currentIndex = this.currentIndex < this.images.length - 1 ? this.currentIndex + 1 : 0;
            }
        },
        toggleZoom(e) {
            this.isZoomed = !this.isZoomed;
            if(this.isZoomed) {
                this.updatePan(e);
            }
        },
        updatePan(e) {
            if (!this.isZoomed) return;
            const clientX = e.clientX ?? (e.touches ? e.touches[0].clientX : null);
            const clientY = e.clientY ?? (e.touches ? e.touches[0].clientY : null);
            
            if (clientX === null || clientY === null) return;

            const rect = this.$refs.mainImageContainer.getBoundingClientRect();
            let x = ((clientX - rect.left) / rect.width) * 100;
            let y = ((clientY - rect.top) / rect.height) * 100;
            this.zoomX = Math.max(0, Math.min(100, x));
            this.zoomY = Math.max(0, Math.min(100, y));
        }
    }" 
    class="flex flex-col md:flex-row gap-4 lg:gap-6 items-start relative z-10">
    
    <!-- Thumbnails (Bottom on Mobile, Left on Desktop) -->
    <div class="order-2 md:order-1 flex md:flex-col gap-3 overflow-x-auto md:overflow-visible pb-2 md:pb-0 hide-scrollbar w-full md:w-24 lg:w-28 flex-shrink-0">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <button @click="currentIndex = <?php echo e($index); ?>; isZoomed = false;" 
                    type="button" 
                    :class="currentIndex === <?php echo e($index); ?> ? 'border-b-4 border-black shadow-sm opacity-100' : 'border border-transparent hover:border-gray-200 opacity-70 hover:opacity-100'"
                    class="relative flex-shrink-0 w-20 h-20 md:w-full md:h-24 lg:h-28 cursor-pointer items-center justify-center rounded-xl bg-gray-50 overflow-hidden transition-all duration-200">
                <img src="<?php echo e($image->image_url); ?>" alt="" class="h-full w-full object-contain p-0">
            </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <button type="button" class="relative flex-shrink-0 w-20 h-20 md:w-full md:h-24 lg:h-28 cursor-pointer items-center justify-center rounded-xl bg-gray-50 overflow-hidden border-b-4 border-black shadow-sm opacity-100">
                <img src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="" class="h-full w-full object-contain p-0">
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Main Image (Top on Mobile, Right on Desktop) -->
    <div class="order-1 md:order-2 flex-1 relative overflow-hidden rounded-2xl bg-gray-50 aspect-square flex items-center justify-center select-none"
         :class="isZoomed ? 'cursor-zoom-out' : 'cursor-zoom-in'"
         x-ref="mainImageContainer"
         @click="toggleZoom($event)"
         @mousemove="updatePan($event)"
         @mouseleave="isZoomed = false"
         @touchstart="touchStartX = $event.changedTouches[0].screenX"
         @touchmove="if(isZoomed) { $event.preventDefault(); updatePan($event); }"
         @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()"
         >
        <img src="<?php echo e($product->images->first() ? $product->images->first()->image_url : 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'); ?>"
             :src="mainImage" 
             alt="<?php echo e($product->name); ?>"
             fetchpriority="high"
             loading="eager"
             decoding="sync"
             class="h-full w-full object-contain p-0 transition-transform duration-300 ease-out"
             :style="isZoomed ? `transform: scale(2.5); transform-origin: ${zoomX}% ${zoomY}%;` : 'transform: scale(1); transform-origin: center center;'"
        >

        <!-- Instruction Overlay (Appears briefly or on hover before click) -->
        <div x-show="!isZoomed" class="absolute bottom-4 right-4 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm pointer-events-none hidden lg:block opacity-70 transition-opacity">
            Büyütmek için tıklayın
        </div>

        <!-- Mobile Swipe Indicators (Dots) -->
        <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 lg:hidden" x-show="images.length > 1">
            <template x-for="(img, idx) in images" :key="idx">
                <div class="h-1.5 rounded-full transition-all duration-300" 
                     :class="currentIndex === idx ? 'w-4 bg-black' : 'w-1.5 bg-gray-400'"></div>
            </template>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\product\product-gallery.blade.php ENDPATH**/ ?>