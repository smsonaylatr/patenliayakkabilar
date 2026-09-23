<div>
    <!-- Mobile Catalog Overlay -->
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
             class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[9998]"
             style="display: none;"></div>

        <!-- Catalog Panel (bottom slide-up) -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed inset-x-0 bottom-0 z-[9998] bg-white rounded-t-3xl shadow-2xl max-h-[80vh] overflow-hidden flex flex-col md:hidden"
             style="display: none; padding-bottom: calc(76px + env(safe-area-inset-bottom));">

            <!-- Drag Handle -->
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wider">Katalog</h3>
                <button @click="open = false" class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Categories List -->
            <div class="flex-1 overflow-y-auto overscroll-contain px-4 py-3">
                <div class="space-y-1">
                    @foreach($categories as $category)
                        <a href="{{ route('category.show', ['slug' => $category->slug]) }}"
                           @click="open = false"
                           wire:navigate
                           class="flex items-center justify-between px-4 py-4 rounded-2xl hover:bg-gray-50 active:bg-gray-100 transition-colors group">
                            <span class="text-[15px] font-semibold text-gray-800 uppercase tracking-wide">{{ $category->name }}</span>
                            <div class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Tüm Ürünler Link -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('products.index') }}"
                       @click="open = false"
                       wire:navigate
                       class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl bg-black text-white font-semibold text-sm uppercase tracking-widest hover:bg-gray-900 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 21 20" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1.7666 4.8665C1.7666 3.7464 1.7666 3.18635 1.98459 2.75852C2.17634 2.3822 2.4823 2.07624 2.85862 1.88449C3.28644 1.6665 3.8465 1.6665 4.9666 1.6665H5.23327C6.35337 1.6665 6.91343 1.6665 7.34125 1.88449C7.71757 2.07624 8.02353 2.3822 8.21528 2.75852C8.43327 3.18635 8.43327 3.7464 8.43327 4.8665V5.13317C8.43327 6.25328 8.43327 6.81333 8.21528 7.24115C8.02353 7.61748 7.71757 7.92344 7.34125 8.11518C6.91343 8.33317 6.35337 8.33317 5.23327 8.33317H4.9666C3.8465 8.33317 3.28644 8.33317 2.85862 8.11518C2.4823 7.92344 2.17634 7.61748 1.98459 7.24115C1.7666 6.81333 1.7666 6.25328 1.7666 5.13317V4.8665Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.7666 4.8665C11.7666 3.7464 11.7666 3.18635 11.9846 2.75852C12.1763 2.3822 12.4823 2.07624 12.8586 1.88449C13.2864 1.6665 13.8465 1.6665 14.9666 1.6665H15.2333C16.3534 1.6665 16.9134 1.6665 17.3413 1.88449C17.7176 2.07624 18.0235 2.3822 18.2153 2.75852C18.4333 3.18635 18.4333 3.7464 18.4333 4.8665V5.13317C18.4333 6.25328 18.4333 6.81333 18.2153 7.24115C18.0235 7.61748 17.7176 7.92344 17.3413 8.11518C16.9134 8.33317 16.3534 8.33317 15.2333 8.33317H14.9666C13.8465 8.33317 13.2864 8.33317 12.8586 8.11518C12.4823 7.92344 12.1763 7.61748 11.9846 7.24115C11.7666 6.81333 11.7666 6.25328 11.7666 5.13317V4.8665Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1.7666 14.8665C1.7666 13.7464 1.7666 13.1863 1.98459 12.7585C2.17634 12.3822 2.4823 12.0762 2.85862 11.8845C3.28644 11.6665 3.8465 11.6665 4.9666 11.6665H5.23327C6.35337 11.6665 6.91343 11.6665 7.34125 11.8845C7.71757 12.0762 8.02353 12.3822 8.21528 12.7585C8.43327 13.1863 8.43327 13.7464 8.43327 14.8665V15.1332C8.43327 16.2533 8.43327 16.8133 8.21528 17.2412C8.02353 17.6175 7.71757 17.9234 7.34125 18.1152C6.91343 18.3332 6.35337 18.3332 5.23327 18.3332H4.9666C3.8465 18.3332 3.28644 18.3332 2.85862 18.1152C2.4823 17.9234 2.17634 17.6175 1.98459 17.2412C1.7666 16.8133 1.7666 16.2533 1.7666 15.1332V14.8665Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.7666 14.8665C11.7666 13.7464 11.7666 13.1863 11.9846 12.7585C12.1763 12.3822 12.4823 12.0762 12.8586 11.8845C13.2864 11.6665 13.8465 11.6665 14.9666 11.6665H15.2333C16.3534 11.6665 16.9134 11.6665 17.3413 11.8845C17.7176 12.0762 18.0235 12.3822 18.2153 12.7585C18.4333 13.1863 18.4333 13.7464 18.4333 14.8665V15.1332C18.4333 16.2533 18.4333 16.8133 18.2153 17.2412C18.0235 17.6175 17.7176 17.9234 17.3413 18.1152C16.9134 18.3332 16.3534 18.3332 15.2333 18.3332H14.9666C13.8465 18.3332 13.2864 18.3332 12.8586 18.1152C12.4823 17.9234 12.1763 17.6175 11.9846 17.2412C11.7666 16.8133 11.7666 16.2533 11.7666 15.1332V14.8665Z"/>
                        </svg>
                        Tüm Ürünleri Gör
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
