<div>
    <!-- Mobile Catalog — Bottom Sheet from Navbar -->
    <div x-data="{ open: false }"
         @open-mobile-catalog.window="open = !open"
         x-cloak>

        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"
             class="fixed inset-0 bg-black/30"
             style="display: none; z-index: 9998;"></div>

        <!-- Panel — navbarın üstünden yukarı doğru kayıyor -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-[400ms]"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed left-0 right-0 bottom-0 flex flex-col md:hidden rounded-t-2xl shadow-2xl"
             style="display: none; z-index: 9998; top: 50%; background-color: #fff;">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4 flex-shrink-0">
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
                <button @click="open = false" class="w-full py-3 text-center text-sm text-gray-500 uppercase tracking-[0.2em] hover:text-black transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>
