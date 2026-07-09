<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-8 sm:gap-x-8 sm:gap-y-12" style="perspective: 1200px;">
    @forelse($products as $product)
        <div 
            x-data="{
                rotateX: 0,
                rotateY: 0,
                glareX: 50,
                glareY: 50,
                opacity: 0,
                showSizeModal: false,
                handleMove(e) {
                    // Only apply 3D tilt on non-touch devices (width > 768px as a simple heuristic)
                    if (window.innerWidth < 768) return;
                    
                    const rect = this.$el.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    this.rotateY = ((x - centerX) / centerX) * 10; 
                    this.rotateX = -((y - centerY) / centerY) * 10;
                    
                    this.glareX = (x / rect.width) * 100;
                    this.glareY = (y / rect.height) * 100;
                    this.opacity = 1;
                },
                handleLeave() {
                    this.rotateX = 0;
                    this.rotateY = 0;
                    this.opacity = 0;
                }
            }"
            @mousemove="handleMove"
            @mouseleave="handleLeave"
            class="group relative flex flex-col transition-all duration-300 ease-out will-change-transform"
            :style="`transform: rotateX(${rotateX}deg) rotateY(${rotateY}deg);`"
        >
            <div class="relative w-full aspect-square bg-transparent rounded-2xl shadow-sm transition-all duration-500 group-hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.15)]" style="perspective: 1000px;">
                
                <!-- ================= CARD FACE ================= -->
                <div class="absolute inset-0 w-full h-full bg-gray-50 rounded-2xl overflow-hidden">
                    
                    <a href="{{ route('products.show', $product->slug) }}" wire:navigate class="block w-full h-full active:scale-95 transition-transform duration-200 origin-center">
                        @if($product->images->isNotEmpty())
                            @if($product->images->count() > 1)
                                <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-center object-cover transition-all duration-500 ease-in-out group-hover:opacity-0 group-hover:scale-95">
                                <img src="{{ $product->images->skip(1)->first()->image_url }}" alt="{{ $product->name }} Alternate" class="absolute inset-0 w-full h-full object-center object-cover opacity-0 scale-110 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500 ease-in-out">
                            @else
                                <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-center object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                            @endif
                        @else
                            <img src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="{{ $product->name }}" class="w-full h-full object-center object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                        @endif
                        
                        <!-- Dark overlay on hover for better button contrast -->
                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-0"></div>
                    </a>
                    
                    @if($product->discount_price)
                        <div class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-white/90 backdrop-blur-sm text-red-600 text-[9px] sm:text-[10px] font-bold px-2 py-1 sm:px-3 sm:py-1.5 rounded-full uppercase tracking-widest z-10 shadow-sm border border-white/50">İndirim</div>
                    @endif
                    
                    <!-- Premium Quick Add Button -->
                    <div class="absolute inset-x-0 bottom-3 sm:bottom-6 flex justify-center z-20 opacity-0 translate-y-6 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 cubic-bezier(0.4, 0, 0.2, 1)">
                        @if($product->variants->count() > 0)
                            <button @click.prevent="showSizeModal = true" class="bg-white/90 sm:bg-white/80 backdrop-blur-md text-black w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center shadow-md sm:shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:bg-black hover:text-white hover:scale-110 hover:shadow-[0_8px_30px_rgb(0,0,0,0.2)] transition-all duration-300 border border-white/50" aria-label="Hızlı Ekle" title="Hızlı Ekle">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </button>
                        @else
                            <button wire:click="addToCart({{ $product->id }})" class="bg-white/90 sm:bg-white/80 backdrop-blur-md text-black w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center shadow-md sm:shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:bg-black hover:text-white hover:scale-110 hover:shadow-[0_8px_30px_rgb(0,0,0,0.2)] transition-all duration-300 border border-white/50" aria-label="Hızlı Ekle" title="Hızlı Ekle">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- ================= SIZE SELECTION MODAL ================= -->
                @if($product->variants->count() > 0)
                <template x-teleport="body">
                    <div x-show="showSizeModal" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0"
                         style="display: none;">
                        
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showSizeModal = false"></div>
                        
                        <!-- Modal Content -->
                        <div x-show="showSizeModal"
                             x-transition:enter="transition ease-out duration-300 delay-100"
                             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                             class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden z-10 flex flex-col border border-gray-100">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                                <h4 class="text-gray-900 font-extrabold text-lg flex items-center gap-2">
                                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    Beden Seçin
                                </h4>
                                <button @click.prevent="showSizeModal = false" class="text-gray-400 hover:text-black transition-colors bg-white rounded-full p-1.5 hover:bg-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            
                            <!-- Body -->
                            <div class="p-6">
                                <div class="grid grid-cols-4 gap-3">
                                    @foreach($product->variants as $variant)
                                        <button wire:click="addToCart({{ $product->id }}, {{ $variant->id }})" 
                                                @click="showSizeModal = false" 
                                                class="bg-white border border-gray-200 text-gray-800 font-bold text-sm py-3 rounded-xl hover:border-black hover:bg-black hover:text-white transition-all transform hover:scale-105 active:scale-95 shadow-sm">
                                            {{ $variant->size }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                @endif
            </div>
            
            <div class="mt-4 flex flex-col items-center justify-center text-center px-2 w-full overflow-hidden">
                <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors duration-300 w-full truncate">
                    <a href="{{ route('products.show', $product->slug) }}" wire:navigate title="{{ $product->name }}">
                        {{ $product->name }}
                    </a>
                </h3>
                <div class="mt-2 flex items-center justify-center gap-3">
                    @php
                        $displayPrice = $product->price;
                        $displayDiscount = $product->discount_price;
                        if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                            $displayPrice = $product->discount_price;
                            $displayDiscount = $product->price;
                        }
                    @endphp
                    @if($displayDiscount)
                        <span class="text-sm font-bold text-red-600">{{ number_format($displayDiscount, 2) }} ₺</span>
                        <span class="text-xs text-gray-400 line-through decoration-gray-300">{{ number_format($displayPrice, 2) }} ₺</span>
                    @else
                        <span class="text-sm font-semibold text-gray-700">{{ number_format($displayPrice, 2) }} ₺</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <p class="text-lg font-medium text-gray-900">Henüz ürün eklenmemiş.</p>
            <p class="text-gray-500 mt-1">Çok yakında yeni modellerimizle buradayız.</p>
        </div>
    @endforelse
</div>
