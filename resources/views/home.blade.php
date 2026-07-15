<x-layouts.app>
    <x-slot:title>Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri ve Fiyatları</x-slot:title>
    <x-slot:description>Çocuk ve genç patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçenekler. Güvenli alışveriş, hızlı kargo ile kapınızda.</x-slot:description>
    <x-slot:canonical>{{ url('/') }}</x-slot:canonical>

    @include('livewire.home.hero-section')
    

    @php
        $featuredCategories = \App\Models\Category::where('is_featured', true)
                                ->where('status', true)
                                ->orderBy('sort_order')
                                ->get();
    @endphp

    @if($featuredCategories->count() > 0)
        <div class="bg-white pt-10 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">
                @foreach($featuredCategories as $category)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000 ease-out">
                        <div class="relative mb-8 border-b border-gray-100 pb-2">
                            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                                <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ $category->name }}</h2>
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" wire:navigate class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-gray-500 hover:text-black transition-colors uppercase tracking-wider self-start sm:self-auto mb-1">
                                    Daha Fazla 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Kategoriye Ait Öne Çıkan Ürünler -->
                        <livewire:product.product-grid :category="$category->slug" isFeaturedOnly="true" :key="'featured-cat-'.$category->id" />
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="pt-10 pb-16 bg-white transition-all duration-1000 ease-out">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative mb-10 overflow-hidden" style="border-radius: 4px;">
                    <div class="absolute inset-0 bg-yellow-300 transform -skew-x-12 scale-110 origin-left"></div>
                    <div class="relative z-10 flex items-center justify-between px-8 py-4">
                        <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Haftanın Favorileri</h2>
                        <a href="{{ route('products.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-bold text-gray-900 hover:text-black transition-colors uppercase tracking-wider">
                            Tümünü Gör 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
                <livewire:product.product-grid isFeaturedOnly="true" />
            </div>
        </div>
    @endif

    <livewire:frontend.newsletter-form />
</x-layouts.app>
