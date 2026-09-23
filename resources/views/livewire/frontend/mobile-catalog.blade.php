<div>
    <!-- Mobile Catalog — Full Screen Bottom Sheet (Larcivert Style) -->
    <div x-data="{ open: false }"
         @open-mobile-catalog.window="open = !open"
         x-cloak>

        <!-- Full Screen Panel — aşağıdan yukarı kayan perde -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-[400ms]"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed inset-0 z-[10001] bg-black flex flex-col md:hidden"
             style="display: none;">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4 flex-shrink-0">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase tracking-[0.3em] mb-1">Patenli Ayakkabılar</p>
                    <h2 class="text-2xl text-white font-light tracking-wide" style="font-family: Georgia, 'Times New Roman', serif;">Kategoriler</h2>
                </div>
                <a href="{{ route('products.index') }}" @click="open = false" wire:navigate class="text-[11px] text-gray-400 uppercase tracking-widest hover:text-white transition-colors">
                    Tüm Ürünler →
                </a>
            </div>

            <!-- Divider -->
            <div class="mx-6 border-t border-gray-800"></div>

            <!-- Categories List -->
            <div class="flex-1 overflow-y-auto overscroll-contain">
                <div class="px-6">
                    @foreach($categories as $category)
                        <a href="{{ route('category.show', ['slug' => $category->slug]) }}"
                           @click="open = false"
                           wire:navigate
                           class="flex items-center justify-between py-5 border-b border-gray-800/60 active:opacity-60 transition-opacity group">
                            <span class="text-xl text-white font-light tracking-wide" style="font-family: Georgia, 'Times New Roman', serif;">{{ $category->name }}</span>
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $category->products_count }} Ürün</span>
                                <span class="text-gray-500 text-lg leading-none group-hover:text-white transition-colors">+</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex-shrink-0 px-6 py-6 border-t border-gray-800">
                <button @click="open = false" class="w-full py-4 text-center text-sm text-gray-400 uppercase tracking-[0.2em] hover:text-white transition-colors">
                    ✕ Kapat
                </button>
            </div>
        </div>
    </div>
</div>
