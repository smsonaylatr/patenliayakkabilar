<div class="py-12 bg-gray-50 border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Haftanın Favorileri</h2>
            <a href="{{ route('products.index') }}" wire:navigate class="text-sm font-semibold text-brand-orange hover:text-orange-500 flex items-center gap-1 transition-colors">
                Tümünü Gör
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        
        <!-- CSS Snap Scroll Carousel -->
        <div class="relative group">
            <div id="best-seller-carousel" class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbars gap-6 pb-8 pt-4 px-2 -mx-2">
                @foreach($products as $product)
                    <div class="snap-start shrink-0 w-[280px] sm:w-[300px] md:w-[320px] transition-transform duration-300 hover:-translate-y-2 relative group/card">
                        <a href="{{ route('products.show', $product->slug) }}" wire:navigate class="block bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] hover:shadow-[0_10px_40px_rgba(0,0,0,0.1)] transition-shadow duration-300 overflow-hidden h-full flex flex-col border border-gray-100/50">
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 z-10 flex flex-col gap-2 pointer-events-none">
                                @if($product->stock <= 5 && $product->stock > 0)
                                    <span class="bg-red-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                        Son {{ $product->stock }} Ürün
                                    </span>
                                @endif
                                @php
                                    $displayPrice = $product->price;
                                    $displayDiscount = $product->discount_price;
                                    if ($displayDiscount && $displayPrice && $displayDiscount > $displayPrice) {
                                        $displayPrice = $product->discount_price;
                                        $displayDiscount = $product->price;
                                    }
                                @endphp
                                @if($displayDiscount)
                                    @php
                                        $discountPercent = round((($displayPrice - $displayDiscount) / $displayPrice) * 100);
                                    @endphp
                                    <span class="bg-gray-900 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                        %{{ $discountPercent }} İndirim
                                    </span>
                                @endif
                            </div>

                            <!-- Size Range Badge -->
                            @php
                                $sizes = $product->variants->pluck('size')->filter()->sort()->values();
                            @endphp
                            @if($sizes->isNotEmpty())
                                <div class="absolute top-4 right-4 z-10 bg-white/70 backdrop-blur-md text-gray-500 text-[11px] font-medium tracking-wide px-2 py-1 rounded-lg shadow-sm border border-white/40 pointer-events-none transition-all duration-300 group-hover/card:opacity-0">
                                    @if($sizes->first() == $sizes->last())
                                        {{ $sizes->first() }}
                                    @else
                                        {{ $sizes->first() }}-{{ $sizes->last() }}
                                    @endif
                                </div>
                            @endif

                            <!-- Image -->
                            <div class="aspect-[4/5] bg-gray-100 relative overflow-hidden">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ $product->images->first()->image_url }}" 
                                         alt="{{ $product->name }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover object-center group-hover/card:scale-105 transition-transform duration-700 ease-out" />
                                    
                                    @if($product->best_seller)
                                        <?php
                                        $badgeSetting = \Illuminate\Support\Facades\Cache::remember('best_seller_badge_setting', 3600, function () {
                                            return \App\Models\Setting::where('key', 'best_seller_badge')->value('value');
                                        });
                                        $badgeUrl = $badgeSetting ? '/storage/' . $badgeSetting : '/img/en-cok-satan.svg?v=big';
                                        ?>
                                        <div class="absolute top-0 -left-1.5 sm:top-0 sm:-left-2 z-40 w-[65px] h-[65px] sm:w-[85px] sm:h-[85px] drop-shadow-lg transition-transform duration-500 ease-in-out group-hover:scale-105">
                                            <img src="{{ $badgeUrl }}" alt="En Çok Satan" class="w-full h-full object-contain">
                                        </div>
                                    @endif



                                    @if($product->images->count() > 1)
                                        <img src="{{ $product->images->skip(1)->first()->image_url }}" 
                                             alt="{{ $product->name }} Alternate"
                                             loading="lazy"
                                             class="absolute inset-0 w-full h-full object-cover object-center opacity-0 group-hover/card:opacity-100 transition-opacity duration-500 ease-in-out" />
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                
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
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-gray-500 mb-4 line-clamp-1 flex-grow font-medium">{{ $product->category->name ?? 'Patenli Ayakkabı' }}</p>
                                
                                <div class="flex items-center justify-between mt-auto">
                                    <div class="flex flex-col">
                                        @if($displayDiscount)
                                            <span class="text-xs text-gray-400 line-through font-medium">{{ number_format($displayPrice, 2) }} ₺</span>
                                            <span class="text-lg font-black text-red-600 leading-none mt-0.5">{{ number_format($displayDiscount, 2) }} ₺</span>
                                        @else
                                            <span class="text-lg font-black text-gray-900 leading-none">{{ number_format($displayPrice, 2) }} ₺</span>
                                        @endif
                                    </div>
                                    <button class="w-10 h-10 rounded-full bg-gray-50 hover:bg-black hover:text-white text-gray-600 flex items-center justify-center transition-colors md:hidden border border-gray-100">
                                        <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
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
