<x-layouts.app>
    <x-slot:title>Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri ve Fiyatları</x-slot:title>
    <x-slot:description>Çocuk ve genç patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçenekler. Güvenli alışveriş, hızlı kargo ile kapınızda.</x-slot:description>
    <x-slot:canonical>{{ url('/') }}</x-slot:canonical>

    @include('livewire.home.hero-section')
    

    @php
        $featuredCategories = \App\Models\Category::where('is_featured', 1)
                                ->where('status', 1)
                                ->orderBy('sort_order')
                                ->get();
    @endphp

    @if($featuredCategories->count() > 0)
        <div class="bg-white pt-10 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-y-20">
                @foreach($featuredCategories as $category)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000 ease-out">
                        <div class="relative mb-6">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-3xl md:text-[2.5rem] font-black text-gray-900 tracking-tight leading-none">{{ $category->name }}</h2>
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" wire:navigate class="group inline-flex items-center gap-2 text-sm sm:text-[15px] font-medium text-gray-800 hover:text-black transition-colors shrink-0">
                                    Daha Fazla 
                                    <span class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-gray-200 text-gray-600 group-hover:bg-gray-300 transition-colors">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 ml-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Kategoriye Ait Öne Çıkan Ürünler -->
                        <livewire:product.product-grid :category="$category->slug" isFeaturedOnly="true" :key="'featured-cat-'.$category->id" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <livewire:frontend.newsletter-form />
</x-layouts.app>
