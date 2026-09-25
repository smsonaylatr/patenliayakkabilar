<div>
    <!-- Mobile Catalog — Bottom Sheet from Navbar -->
    <div x-data="{
            open: false,
            dragY: 0,
            dragging: false,
            touchStartY: 0,
            startDrag(e) {
                this.touchStartY = e.touches[0].clientY;
                this.dragging = true;
                this.dragY = 0;
            },
            onDrag(e) {
                if (!this.dragging) return;
                const diff = e.touches[0].clientY - this.touchStartY;
                this.dragY = Math.max(0, diff);
            },
            endDrag() {
                if (!this.dragging) return;
                this.dragging = false;
                if (this.dragY > 90) {
                    this.open = false;
                }
                this.dragY = 0;
            },
            closeDrawer() {
                this.open = false;
                this.dragY = 0;
                this.dragging = false;
            }
        }"
         @open-mobile-catalog.window="open = !open"
         @keydown.escape.window="if(open) closeDrawer()"
         x-cloak>

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeDrawer()"
             class="fixed inset-0 bg-black/40"
             style="display: none; z-index: 9998;"></div>

        <!-- Panel — navbarın üstünden yukarı doğru kayıyor (%70 açılış) -->
        <div x-show="open"
             x-transition:enter="transition-transform ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition-transform ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed left-0 right-0 bottom-0 flex flex-col md:hidden rounded-t-2xl shadow-[0_-4px_24px_rgba(0,0,0,0.12)] bg-white overflow-hidden"
             style="display: none; z-index: 9998; top: 30%;"
             :style="dragging ? 'transform: translateY(' + dragY + 'px) translateZ(0); transition: none;' : (dragY > 0 ? 'transform: translateY(0); transition: transform 0.2s ease-out;' : '')"
             @touchend="endDrag()" 
             @touchcancel="endDrag()">

            <!-- Drag Pill -->
            <div class="flex justify-center pt-3 pb-1 shrink-0 cursor-grab active:cursor-grabbing"
                 @touchstart="startDrag($event)" @touchmove.prevent="onDrag($event)"
                 style="touch-action: none;">
                <div class="w-10 h-1 rounded-full bg-black/[0.08]"></div>
            </div>

            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-3 pb-4 flex-shrink-0"
                 @touchstart="startDrag($event)" @touchmove.prevent="onDrag($event)">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em] mb-1">Patenli Ayakkabılar</p>
                    <h2 class="text-2xl text-gray-900 font-light tracking-wide" style="font-family: Georgia, 'Times New Roman', serif;">Kategoriler</h2>
                </div>
                <a href="{{ route('products.index') }}" @click="open = false" wire:navigate class="text-[11px] text-gray-500 uppercase tracking-widest hover:text-black transition-colors">
                    Tüm Ürünler →
                </a>
            </div>

            <!-- Divider -->
            <div class="mx-6 border-t border-gray-200 flex-shrink-0"></div>

            <!-- Categories List -->
            <div class="flex-1 overflow-y-auto overscroll-contain min-h-0">
                <div class="px-6">
                    @foreach($categories as $category)
                        <a href="{{ route('category.show', ['slug' => $category->slug]) }}"
                           @click="open = false"
                           wire:navigate
                           class="flex items-center justify-between py-5 border-b border-gray-100 active:bg-gray-50 transition-colors group">
                            <span class="text-lg text-gray-900 font-normal tracking-wide" style="font-family: Georgia, 'Times New Roman', serif;">{{ $category->name }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-medium">{{ $category->products_count }} Ürün</span>
                                <svg class="w-4 h-4 text-gray-300 group-active:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex-shrink-0 px-6 pt-4 border-t border-gray-200" style="padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px));">
                <button @click="closeDrawer()" class="w-full py-3 text-center text-sm text-gray-500 uppercase tracking-[0.2em] hover:text-black transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>
