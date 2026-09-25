<div class="bg-[#6b6b6b]">
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

    <header x-data="{ mobileMenuOpen: false }" @open-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen" class="sticky top-0 z-40 bg-white rounded-t-2xl border-b border-gray-200 shadow-[0_4px_12px_rgba(0,0,0,0.08)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-20 relative">
                <!-- LEFT SIDE (Hamburger on Mobile, Logo on Desktop) -->
                <div class="flex flex-1 items-center justify-start">
                    <!-- Mobile Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menü" class="md:hidden text-gray-900 focus:outline-none min-w-[44px] min-h-[44px] flex items-center justify-center -ml-2">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" d="M3 6H21M3 12H11M3 18H16"/>
                        </svg>
                    </button>
                    <a href="{{ route('home') }}" class="hidden md:block text-2xl font-black text-gray-900 tracking-tighter uppercase" wire:navigate>
                        PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                    </a>
                </div>

                <!-- CENTER (Logo on Mobile, Menu on Desktop) -->
                <div class="flex flex-shrink-0 items-center justify-center md:static absolute left-1/2 -translate-x-1/2 md:translate-x-0">
                    <a href="{{ route('home') }}" class="md:hidden text-[22px] sm:text-2xl font-black text-gray-900 tracking-tighter uppercase" wire:navigate>
                        PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                    </a>
                    <!-- Desktop Menu -->
                    <nav class="hidden md:flex space-x-6 lg:space-x-10 items-center">
                        <a href="{{ route('home') }}" class="text-[13px] font-medium text-gray-900 hover:text-gray-500 uppercase tracking-widest transition-colors" wire:navigate>Ana Sayfa</a>
                        
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
                                        @foreach($categories as $category)
                                            <a href="{{ route('category.show', ['slug' => $category->slug]) }}" class="group flex items-center px-5 py-3.5 text-[13px] text-gray-600 hover:bg-white/60 hover:text-gray-900 rounded-2xl font-bold uppercase tracking-widest transition-all duration-300" wire:navigate>
                                                <span class="flex-1 whitespace-nowrap mr-6">{{ $category->name }}</span>
                                                <div class="w-8 h-8 rounded-full bg-white/80 shadow-sm border border-white/60 flex-shrink-0 flex items-center justify-center opacity-0 -translate-x-3 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('order.tracking') }}" class="text-[13px] font-medium text-gray-900 hover:text-gray-500 uppercase tracking-widest transition-colors" wire:navigate>Sipariş Takip</a>
                        <a href="{{ route('contact') }}" class="text-[13px] font-medium text-gray-900 hover:text-gray-500 uppercase tracking-widest transition-colors" wire:navigate>İletişim</a>
                    </nav>
                </div>

                <!-- Actions (Right) -->
                <div class="flex flex-1 items-center justify-end space-x-4 md:space-x-6">
                    <button x-data @click="$dispatch('open-search')" aria-label="Arama Yap" class="hidden md:flex text-gray-900 hover:text-gray-500 transition-colors p-2 min-w-[44px] min-h-[44px] items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" aria-label="Hesabım" class="text-gray-900 hover:text-gray-500 transition-colors hidden sm:flex items-center justify-center p-2 min-w-[44px] min-h-[44px]" title="Hesabım" wire:navigate>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                    <button wire:click="$dispatch('toggle-cart')" aria-label="Sepetim" class="text-gray-900 hover:text-gray-500 transition-colors relative flex items-center justify-center p-2 min-w-[44px] min-h-[44px]">
                        <svg class="h-6 w-6" viewBox="0 0 21 20" fill="none" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1.13281 0.833547L1.54948 0.833496V0.833496C2.78264 0.833526 3.86637 1.65101 4.20515 2.83672L4.3471 3.33355M4.3471 3.33355L5.63992 7.85843C6.11531 9.5223 6.35301 10.3542 6.83827 10.9717C7.26659 11.5168 7.82919 11.9412 8.47093 12.2033C9.19799 12.5002 10.0632 12.5002 11.7937 12.5002H12.8091C13.8588 12.5002 14.3837 12.5002 14.8433 12.39C15.9407 12.127 16.8759 11.4127 17.4184 10.4232C17.6456 10.0087 17.7837 9.50235 18.0599 8.4896V8.4896C18.3964 7.2559 18.5646 6.63905 18.5321 6.13859C18.4535 4.93171 17.6578 3.89005 16.5142 3.49667C16.0399 3.33355 15.4005 3.33355 14.1218 3.33355H4.3471ZM10.2995 16.6668C10.2995 17.5873 9.55329 18.3335 8.63281 18.3335C7.71234 18.3335 6.96615 17.5873 6.96615 16.6668C6.96615 15.7464 7.71234 15.0002 8.63281 15.0002C9.55329 15.0002 10.2995 15.7464 10.2995 16.6668ZM16.9661 16.6668C16.9661 17.5873 16.22 18.3335 15.2995 18.3335C14.379 18.3335 13.6328 17.5873 13.6328 16.6668C13.6328 15.7464 14.379 15.0002 15.2995 15.0002C16.22 15.0002 16.9661 15.7464 16.9661 16.6668Z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute top-1 right-1 bg-black text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $cartCount }}</span>
                        @endif
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel (Alpine.js) -->
        <div x-show="mobileMenuOpen" 
             x-cloak
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
                <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>Ana Sayfa</a>
                
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
                        @foreach($categories as $category)
                            <a href="{{ route('category.show', ['slug' => $category->slug]) }}" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-md text-sm font-medium text-gray-600 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>- {{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('order.tracking') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>Sipariş Takip</a>
                <a href="{{ route('contact') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>İletişim</a>
                <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:text-black hover:bg-gray-50 uppercase tracking-wide" wire:navigate>Hesabım</a>
            </div>
        </div>
    </header>
</div>
