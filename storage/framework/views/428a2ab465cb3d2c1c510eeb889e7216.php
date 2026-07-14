<div 
    x-data="{ open: false }" 
    @open-search.window="open = true; setTimeout(() => $refs.searchInput.focus(), 100)"
    @keydown.escape.window="open = false"
    class="relative z-[10000]" 
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    style="display: none;"
    x-show="open"
>
    <!-- Backdrop -->
    <div 
        x-show="open" 
        x-transition:enter="ease-out duration-300" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="ease-in duration-200" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
        @click="open = false"
    ></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto pt-16 sm:pt-24">
        <div class="flex min-h-full items-start justify-center p-4 text-center sm:p-0">
            <div 
                x-show="open" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl"
                @click.away="open = false"
            >
                <div class="p-2 sm:p-4">
                    <!-- Search Input -->
                    <div class="relative flex items-center">
                        <svg class="pointer-events-none absolute left-4 h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            x-ref="searchInput"
                            type="text" 
                            class="h-14 w-full rounded-xl border-0 bg-gray-50 pl-12 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-black sm:text-lg" 
                            placeholder="Ürün, kategori veya kelime arayın..."
                        >
                        
                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="search" class="absolute right-4">
                            <div class="h-5 w-5 animate-spin rounded-full border-2 border-gray-300 border-t-black"></div>
                        </div>
                        
                        <!-- Close Button (Mobile) -->
                        <button @click="open = false" class="md:hidden absolute right-4 text-gray-400 hover:text-gray-900" wire:loading.remove wire:target="search">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Search Results -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strlen($search) >= 2): ?>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->count() > 0): ?>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-2">Ürünler (<?php echo e($results->count()); ?>)</h3>
                                <ul class="space-y-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <li>
                                            <a href="<?php echo e(route('products.show', $product->slug)); ?>" wire:navigate class="flex items-center gap-4 rounded-xl p-2 hover:bg-gray-50 transition-colors group">
                                                <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                                    <img src="<?php echo e($product->images->first() ? $product->images->first()->image_url : 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80'); ?>" alt="<?php echo e($product->name); ?>" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate"><?php echo e($product->name); ?></h4>
                                                    <div class="mt-1 flex items-center gap-2">
                                                        <?php
                                                            $displayPrice = $product->price;
                                                            $displayDiscount = $product->discount_price;
                                                            if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                                                                $displayPrice = $product->discount_price;
                                                                $displayDiscount = $product->price;
                                                            }
                                                        ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayDiscount): ?>
                                                            <span class="text-sm font-bold text-brand-orange"><?php echo e(number_format($displayDiscount, 2)); ?> ₺</span>
                                                            <span class="text-xs text-gray-400 line-through"><?php echo e(number_format($displayPrice, 2)); ?> ₺</span>
                                                        <?php else: ?>
                                                            <span class="text-sm font-bold text-gray-900"><?php echo e(number_format($displayPrice, 2)); ?> ₺</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="text-gray-400 group-hover:text-black transition-colors">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                            </a>
                                        </li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <div class="px-4 py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">"<strong><?php echo e($search); ?></strong>" ile ilgili sonuç bulunamadı.</p>
                                    <p class="text-xs text-gray-400 mt-1">Lütfen farklı kelimelerle tekrar deneyin.</p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Suggestions Area (When Empty) -->
                        <div class="mt-6 px-2 text-left">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Popüler Aramalar</h3>
                            <div class="flex flex-wrap gap-2 mb-8">
                                <button wire:click="$set('search', 'Işıklı')" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm rounded-xl font-medium transition-colors border border-gray-100">Işıklı</button>
                                <button wire:click="$set('search', 'Tekerlekli')" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm rounded-xl font-medium transition-colors border border-gray-100">Tekerlekli</button>
                                <button wire:click="$set('search', 'Kız Çocuk')" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm rounded-xl font-medium transition-colors border border-gray-100">Kız Çocuk</button>
                                <button wire:click="$set('search', 'Erkek Çocuk')" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm rounded-xl font-medium transition-colors border border-gray-100">Erkek Çocuk</button>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\frontend\search-modal.blade.php ENDPATH**/ ?>