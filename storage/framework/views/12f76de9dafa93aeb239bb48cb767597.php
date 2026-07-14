<div>
    <div class="marquee-container bg-[#6b6b6b] text-white text-[10px] sm:text-xs font-semibold tracking-widest uppercase py-2.5 sm:py-3 overflow-hidden w-full relative">
        <div class="marquee-content flex whitespace-nowrap">
            <span class="mx-6 sm:mx-12">KAPIDA ÖDEME FIRSATI</span>
            <span class="mx-6 sm:mx-12">%100 İADE GARANTİSİ</span>
            <span class="mx-6 sm:mx-12">VADESİZ 3 TAKSİT</span>
            <span class="mx-6 sm:mx-12">HIZLI TESLİMAT</span>
            
            <span class="mx-6 sm:mx-12">KAPIDA ÖDEME FIRSATI</span>
            <span class="mx-6 sm:mx-12">%100 İADE GARANTİSİ</span>
            <span class="mx-6 sm:mx-12">VADESİZ 3 TAKSİT</span>
            <span class="mx-6 sm:mx-12">HIZLI TESLİMAT</span>
            
            <span class="mx-6 sm:mx-12">KAPIDA ÖDEME FIRSATI</span>
            <span class="mx-6 sm:mx-12">%100 İADE GARANTİSİ</span>
            <span class="mx-6 sm:mx-12">VADESİZ 3 TAKSİT</span>
            <span class="mx-6 sm:mx-12">HIZLI TESLİMAT</span>
            
            <span class="mx-6 sm:mx-12">KAPIDA ÖDEME FIRSATI</span>
            <span class="mx-6 sm:mx-12">%100 İADE GARANTİSİ</span>
            <span class="mx-6 sm:mx-12">VADESİZ 3 TAKSİT</span>
            <span class="mx-6 sm:mx-12">HIZLI TESLİMAT</span>
        </div>
    </div>

    <header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- LEFT SIDE (Hamburger on Mobile, Logo on Desktop) -->
                <div class="flex flex-1 items-center justify-start">
                    <!-- Mobile Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <a href="<?php echo e(route('home')); ?>" class="hidden md:block text-2xl font-black text-gray-900 tracking-tighter uppercase" wire:navigate>
                        PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                    </a>
                </div>

                <!-- CENTER (Logo on Mobile, Menu on Desktop) -->
                <div class="flex flex-shrink-0 items-center justify-center">
                    <a href="<?php echo e(route('home')); ?>" class="md:hidden text-xl font-black text-gray-900 tracking-tighter uppercase" wire:navigate>
                        PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                    </a>
                    <!-- Desktop Menu -->
                    <nav class="hidden md:flex space-x-6 lg:space-x-10 items-center">
                        <a href="<?php echo e(route('home')); ?>" class="text-[13px] font-medium text-gray-900 hover:text-gray-500 uppercase tracking-widest transition-colors" wire:navigate>Ana Sayfa</a>
                        
                        <!-- Premium Catalog Dropdown -->
                        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                            <button class="text-[13px] font-medium text-gray-900 hover:text-gray-500 uppercase tracking-widest transition-colors inline-flex items-center focus:outline-none gap-1 py-2">
                                Katalog
                                <svg :class="{'rotate-180': open}" class="h-4 w-4 transition-transform duration-300 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-300 cubic-bezier(0.4, 0, 0.2, 1)"
                                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute left-1/2 -translate-x-1/2 top-[calc(100%+2.25rem)] w-max min-w-[280px] z-50 before:content-[''] before:absolute before:-top-9 before:left-0 before:w-full before:h-9"
                                 style="display: none;">
                                
                                <div class="rounded-3xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.15)] bg-white/70 backdrop-blur-2xl border border-white/60 p-3 relative z-10">
                                    <div class="flex flex-col gap-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <a href="<?php echo e(route('products.index')); ?>?category=<?php echo e($category->slug); ?>" class="group flex items-center px-5 py-3.5 text-[13px] text-gray-600 hover:bg-white/60 hover:text-gray-900 rounded-2xl font-bold uppercase tracking-widest transition-all duration-300" wire:navigate>
                                                <span class="flex-1 whitespace-nowrap mr-6"><?php echo e($category->name); ?></span>
                                                <div class="w-8 h-8 rounded-full bg-white/80 shadow-sm border border-white/60 flex-shrink-0 flex items-center justify-center opacity-0 -translate-x-3 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                                </div>
                                            </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo e(route('order.tracking')); ?>" class="text-[13px] font-medium text-gray-900 hover:text-gray-500 uppercase tracking-widest transition-colors" wire:navigate>Sipariş Takip</a>
                        <a href="<?php echo e(route('contact')); ?>" class="text-[13px] font-medium text-gray-900 hover:text-gray-500 uppercase tracking-widest transition-colors" wire:navigate>İletişim</a>
                    </nav>
                </div>

                <!-- Actions (Right) -->
                <div class="flex flex-1 items-center justify-end space-x-4 md:space-x-6">
                    <button x-data @click="$dispatch('open-search')" class="text-gray-900 hover:text-gray-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    <a href="<?php echo e(auth()->check() ? route('account.dashboard') : route('login')); ?>" class="text-gray-900 hover:text-gray-500 transition-colors hidden sm:block" title="Hesabım" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                    <button wire:click="$dispatch('toggle-cart')" class="text-gray-900 hover:text-gray-500 transition-colors relative flex items-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cartCount > 0): ?>
                            <span class="absolute -top-1.5 -right-2 bg-black text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?php echo e($cartCount); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel (Alpine.js) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="mobileMenuOpen = false"
             class="absolute top-20 left-0 w-full bg-white border-b border-gray-100 shadow-lg md:hidden"
             style="display: none;">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="<?php echo e(route('home')); ?>" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>Ana Sayfa</a>
                
                <!-- Mobile Catalog Dropdown -->
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex justify-between items-center px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide">
                        Katalog
                        <svg :class="{'rotate-180': open}" class="h-5 w-5 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1 pb-2" style="display: none;"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('products.index')); ?>?category=<?php echo e($category->slug); ?>" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-md text-sm font-medium text-gray-600 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>- <?php echo e($category->name); ?></a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                <a href="<?php echo e(route('order.tracking')); ?>" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>Sipariş Takip</a>
                <a href="<?php echo e(route('contact')); ?>" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>İletişim</a>
                <a href="<?php echo e(auth()->check() ? route('account.dashboard') : route('login')); ?>" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>Hesabım</a>
            </div>
        </div>
    </header>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\frontend\header.blade.php ENDPATH**/ ?>