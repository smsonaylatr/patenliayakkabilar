<div class="py-12 bg-gray-50 border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Haftanın Favorileri</h2>
            <a href="<?php echo e(route('products.index')); ?>" wire:navigate class="text-sm font-semibold text-brand-orange hover:text-orange-500 flex items-center gap-1 transition-colors">
                Tümünü Gör
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        
        <!-- CSS Snap Scroll Carousel -->
        <div class="relative group">
            <div id="best-seller-carousel" class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbars gap-6 pb-8 pt-4 px-2 -mx-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="snap-start shrink-0 w-[280px] sm:w-[300px] md:w-[320px] transition-transform duration-300 hover:-translate-y-2 relative group/card">
                        <a href="<?php echo e(route('products.show', $product->slug)); ?>" wire:navigate class="block bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] hover:shadow-[0_10px_40px_rgba(0,0,0,0.1)] transition-shadow duration-300 overflow-hidden h-full flex flex-col border border-gray-100/50">
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 z-10 flex flex-col gap-2 pointer-events-none">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->stock <= 5 && $product->stock > 0): ?>
                                    <span class="bg-red-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                        Son <?php echo e($product->stock); ?> Ürün
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php
                                    $displayPrice = $product->price;
                                    $displayDiscount = $product->discount_price;
                                    if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                                        $displayPrice = $product->discount_price;
                                        $displayDiscount = $product->price;
                                    }
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayDiscount): ?>
                                    <?php
                                        $discountPercent = round((($displayPrice - $displayDiscount) / $displayPrice) * 100);
                                    ?>
                                    <span class="bg-gray-900 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                        %<?php echo e($discountPercent); ?> İndirim
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <!-- Image -->
                            <div class="aspect-[4/5] bg-gray-100 relative overflow-hidden">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->images->isNotEmpty()): ?>
                                    <img src="<?php echo e($product->images->first()->image_url); ?>" 
                                         alt="<?php echo e($product->name); ?>"
                                         loading="lazy"
                                         class="w-full h-full object-cover object-center group-hover/card:scale-105 transition-transform duration-700 ease-out" />
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->images->count() > 1): ?>
                                        <img src="<?php echo e($product->images->skip(1)->first()->image_url); ?>" 
                                             alt="<?php echo e($product->name); ?> Alternate"
                                             loading="lazy"
                                             class="absolute inset-0 w-full h-full object-cover object-center opacity-0 group-hover/card:opacity-100 transition-opacity duration-500 ease-in-out" />
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <!-- Hover Quick Add Button (Desktop only) -->
                                <div class="absolute bottom-4 left-0 right-0 px-4 opacity-0 translate-y-4 group-hover/card:opacity-100 group-hover/card:translate-y-0 transition-all duration-300 hidden md:block">
                                    <button class="w-full bg-white/90 backdrop-blur-sm text-black font-semibold py-3 px-4 rounded-xl shadow-lg border border-white hover:bg-black hover:text-white hover:border-black transition-colors flex justify-center items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                        Hemen İncele
                                    </button>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 flex flex-col flex-grow bg-white relative z-10">
                                <h3 class="text-sm font-bold text-gray-900 group-hover/card:text-brand-orange transition-colors line-clamp-1 mb-1">
                                    <?php echo e($product->name); ?>

                                </h3>
                                <p class="text-xs text-gray-500 mb-4 line-clamp-1 flex-grow font-medium"><?php echo e($product->category->name ?? 'Patenli Ayakkabı'); ?></p>
                                
                                <div class="flex items-center justify-between mt-auto">
                                    <div class="flex flex-col">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayDiscount): ?>
                                            <span class="text-xs text-gray-400 line-through font-medium"><?php echo e(number_format($displayPrice, 2)); ?> ₺</span>
                                            <span class="text-lg font-black text-red-600 leading-none mt-0.5"><?php echo e(number_format($displayDiscount, 2)); ?> ₺</span>
                                        <?php else: ?>
                                            <span class="text-lg font-black text-gray-900 leading-none"><?php echo e(number_format($displayPrice, 2)); ?> ₺</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <button class="w-10 h-10 rounded-full bg-gray-50 hover:bg-black hover:text-white text-gray-600 flex items-center justify-center transition-colors md:hidden border border-gray-100">
                                        <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            
            <!-- Controls (Visible on hover for desktop) -->
            <button onclick="document.getElementById('best-seller-carousel').scrollBy({left: -300, behavior: 'smooth'})" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 backdrop-blur text-black rounded-full shadow-[0_4px_20px_rgba(0,0,0,0.1)] items-center justify-center border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex hover:bg-black hover:text-white cursor-pointer z-20 hover:scale-110 duration-200">
                <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button onclick="document.getElementById('best-seller-carousel').scrollBy({left: 300, behavior: 'smooth'})" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 backdrop-blur text-black rounded-full shadow-[0_4px_20px_rgba(0,0,0,0.1)] items-center justify-center border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex hover:bg-black hover:text-white cursor-pointer z-20 hover:scale-110 duration-200">
                <svg class="w-6 h-6 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <style>
        .hide-scrollbars {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .hide-scrollbars::-webkit-scrollbar {
            display: none;
        }
    </style>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\product\best-seller-carousel.blade.php ENDPATH**/ ?>