<div x-data="{ 
        images: [
            @forelse($product->images as $image)
                '{{ $image->image_url }}',
            @empty
                asset('img/placeholder.svg')
            @endforelse
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
    <div class="order-2 md:order-1 grid grid-cols-5 md:flex md:flex-col gap-2 md:gap-3 w-full md:w-24 lg:w-28 flex-shrink-0">
        @forelse($product->images as $index => $image)
            <button @click="currentIndex = {{ $index }}; isZoomed = false;" 
                    type="button" 
                    :class="currentIndex === {{ $index }} ? 'border-b-4 border-black shadow-sm opacity-100' : 'border border-transparent hover:border-gray-200 opacity-70 hover:opacity-100'"
                    class="relative flex-shrink-0 w-full aspect-square md:h-24 lg:h-28 cursor-pointer items-center justify-center rounded-xl bg-gray-50 overflow-hidden transition-all duration-200">
                <img src="{{ $image->image_url }}" alt="" class="h-full w-full object-contain p-0">
            </button>
        @empty
            <button type="button" class="relative flex-shrink-0 w-full aspect-square md:h-24 lg:h-28 cursor-pointer items-center justify-center rounded-xl bg-gray-50 overflow-hidden border-b-4 border-black shadow-sm opacity-100">
                <img src="{{ asset('img/placeholder.svg') }}" alt="" class="h-full w-full object-contain p-0">
            </button>
        @endforelse
    </div>

    <!-- Main Image and Desktop Reviews Column (Top on Mobile, Right on Desktop) -->
    <div class="order-1 md:order-2 flex-1 w-full flex flex-col gap-6">
        
        <!-- Main Image -->
        <div class="w-full relative overflow-hidden rounded-2xl bg-white aspect-square lg:aspect-[4/3] flex items-center justify-center select-none group"
             :class="isZoomed ? 'cursor-zoom-out' : 'cursor-zoom-in'"
             x-ref="mainImageContainer"
             @click="isZoomed = !isZoomed; if(isZoomed) updatePan($event)"
             @mousemove="if(isZoomed) updatePan($event)"
             @mouseleave="isZoomed = false"
             @touchstart="touchStartX = $event.changedTouches[0].screenX"
             @touchmove="if(isZoomed) { $event.preventDefault(); updatePan($event); }"
             @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()"
             >
             
            <!-- Zoom Wrapper -->
            <div class="w-full h-full relative will-change-transform"
                 :style="isZoomed ? 'transform: scale(2.5); transform-origin: ' + zoomX + '% ' + zoomY + '%;' : 'transform: scale(1); transform-origin: center center;'"
                 :class="isZoomed ? 'transition-none' : 'transition-transform duration-300 ease-out'">
                 
                @forelse($product->images as $index => $image)
                    <img src="{{ $image->image_url }}" 
                         alt="{{ $product->name }}"
                         x-show="currentIndex === {{ $index }}"
                         x-transition.opacity.duration.300ms
                         class="absolute inset-0 h-full w-full object-contain p-0"
                         {{ $index === 0 ? 'fetchpriority=high loading=eager' : 'loading=lazy' }}
                         @if($index !== 0) x-cloak @endif
                    >
                @empty
                    <img src="{{ asset('img/placeholder.svg') }}"
                         class="absolute inset-0 h-full w-full object-contain p-0">
                @endforelse
            </div>

            <!-- Navigation Arrows (Desktop) -->
            <button type="button" x-show="images.length > 1 && !isZoomed" @click.stop="currentIndex = currentIndex > 0 ? currentIndex - 1 : images.length - 1" 
                    class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-md z-20 items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button type="button" x-show="images.length > 1 && !isZoomed" @click.stop="currentIndex = currentIndex < images.length - 1 ? currentIndex + 1 : 0" 
                    class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-md z-20 items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>

            <!-- Instruction Overlay (Appears briefly or on hover before click) -->
            <div x-show="!isZoomed" class="absolute bottom-4 lg:bottom-16 right-4 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm pointer-events-none hidden lg:block opacity-70 transition-opacity">
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
        
        <!-- Desktop/Tablet Reviews -->
        <div class="hidden md:block w-full pointer-events-auto mt-0 lg:-mt-4 relative z-20">
            @livewire('product.review-list', ['product' => $product], key('desktop-reviews-'.$product->id))
        </div>

    </div>
</div>
