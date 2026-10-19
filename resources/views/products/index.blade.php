@php
    $searchTerm = trim((string) (request('search') ?? request('q') ?? ''));
    $hasSearch = !empty($searchTerm);
    $categoryName = isset($category) && $category ? $category->name : ($hasSearch ? 'Arama Sonuçları' : 'Tüm Modeller');

    $pageTitle = $hasSearch 
        ? ('"' . $searchTerm . '" Arama Sonuçları | Patenli Ayakkabılar')
        : (isset($category) && $category 
            ? ($category->meta_title ?? $categoryName . ' | Patenli Ayakkabılar')
            : 'Tüm Patenli Ayakkabı Modelleri | Patenli Ayakkabılar');

    $pageDesc = $hasSearch
        ? ('"' . $searchTerm . '" aramasına ait en uygun ışıklı ve tekerlekli patenli ayakkabı modelleri.')
        : (isset($category) && $category 
            ? ($category->meta_description ?? $categoryName . ' kategorisindeki en çok tercih edilen patenli ayakkabı modelleri. Güvenli alışveriş ve hızlı kargo.')
            : 'Tüm ışıklı ve tekerlekli patenli ayakkabı modellerimizi keşfedin. Çocuk ve genç modelleri, uygun fiyatlarla.');

    $canonicalUrl = $hasSearch
        ? url('/patenli-ayakkabilar?search=' . urlencode($searchTerm))
        : (isset($category) && $category ? url('/kategori/' . $category->slug) : url('/patenli-ayakkabilar'));
@endphp

<x-layouts.app>
    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-slot:description>{{ $pageDesc }}</x-slot:description>
    <x-slot:canonical>{{ $canonicalUrl }}</x-slot:canonical>
    @if(isset($category) && $category)
        <x-slot:schema>
            @if(app()->bound(\App\Services\SchemaService::class))
                {!! app(\App\Services\SchemaService::class)->categoryPage($category, null) !!}
            @endif
        </x-slot:schema>
    @endif

    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="mb-8">
                <x-breadcrumb :items="array_filter([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Patenli Ayakkabılar', 'url' => route('products.index')],
                    isset($category) && $category ? ['name' => $category->name] : null,
                    $hasSearch ? ['name' => 'Arama: "' . $searchTerm . '"'] : null,
                ])" />
            </div>

            <div class="text-center mb-8 sm:mb-12">
                <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    @if($hasSearch)
                        "{{ $searchTerm }}" Arama Sonuçları
                    @elseif(isset($category) && $category)
                        {{ $category->seo_h1 ?? $category->name }}
                    @else
                        Tüm Modeller
                    @endif
                </h1>
                <p class="mt-3 sm:mt-4 max-w-2xl text-base sm:text-xl text-gray-500 mx-auto px-2">
                    @if($hasSearch)
                        Aradığınız kelimeye veya kritere en uygun patenli ayakkabı modelleri listelenmektedir.
                    @elseif(isset($category) && $category)
                        {{ $category->name }} kategorisindeki en çok tercih edilen patenli ayakkabı modellerimiz.
                    @else
                        En çok tercih edilen patenli ayakkabı ve tekerlekli sneaker modellerimiz.
                    @endif
                </p>
                @if($hasSearch)
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <a href="{{ route('products.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-black bg-white shadow-sm border border-gray-200 px-3.5 py-1.5 rounded-full transition-all">
                            <span>✕ Aramayı Temizle</span>
                        </a>
                    </div>
                @endif
            </div>

            <livewire:product.product-grid 
                :category="isset($category) && $category ? $category->slug : ''" 
                :search="$searchTerm" 
            />
            
            {{-- Kategori SEO metni --}}
            @if(isset($category) && $category && $category->seo_content)
                <div class="mt-16 max-w-4xl mx-auto">
                    <div class="prose prose-gray max-w-none text-gray-600">
                        {!! $category->seo_content !!}
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>
