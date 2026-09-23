<div>
    <!-- Mobile Catalog — Full Screen Bottom Sheet -->
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
             class="fixed inset-0 bg-black/50 z-[10000]"
             style="display: none;"></div>

        <!-- Full Screen Panel — aşağıdan yukarı kayan perde -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-250"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed inset-0 z-[10001] bg-white flex flex-col md:hidden"
             style="display: none;">

            <!-- Header Bar -->
            <div class="flex items-center justify-between px-5 h-16 border-b border-gray-100 flex-shrink-0">
                <h2 class="text-base font-bold text-gray-900 uppercase tracking-widest">Katalog</h2>
                <button @click="open = false" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Categories List -->
            <div class="flex-1 overflow-y-auto overscroll-contain">
                <div class="px-5 py-4 space-y-1">
                    @foreach($categories as $category)
                        <a href="{{ route('category.show', ['slug' => $category->slug]) }}"
                           @click="open = false"
                           wire:navigate
                           class="flex items-center justify-between px-4 py-5 border-b border-gray-50 active:bg-gray-50 transition-colors">
                            <span class="text-[15px] font-semibold text-gray-900 uppercase tracking-wide">{{ $category->name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>

                <!-- Tüm Ürünler -->
                <div class="px-5 pb-8 pt-4">
                    <a href="{{ route('products.index') }}"
                       @click="open = false"
                       wire:navigate
                       class="flex items-center justify-center gap-2 w-full py-4 rounded-xl bg-black text-white font-semibold text-sm uppercase tracking-widest active:bg-gray-800 transition-colors">
                        Tüm Ürünleri Gör
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
