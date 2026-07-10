@php
    $categoryName = isset($category) && $category ? $category->name : 'Tüm Modeller';
    $pageTitle = isset($category) && $category 
        ? ($category->meta_title ?? $categoryName . ' | Patenli Ayakkabılar')
        : 'Tüm Patenli Ayakkabı Modelleri | Patenli Ayakkabılar';
    $pageDesc = isset($category) && $category 
        ? ($category->meta_description ?? $categoryName . ' kategorisindeki en çok tercih edilen patenli ayakkabı modelleri. Güvenli alışveriş ve hızlı kargo.')
        : 'Tüm ışıklı ve tekerlekli patenli ayakkabı modellerimizi keşfedin. Çocuk ve genç modelleri, uygun fiyatlarla.';
    $canonicalUrl = isset($category) && $category ? url('/kategori/' . $category->slug) : url('/patenli-ayakkabilar');
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
                ])" />
            </div>

            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    {{ isset($category) && $category ? ($category->seo_h1 ?? $category->name) : 'Tüm Modeller' }}
                </h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    {{ isset($category) && $category ? $category->name . ' kategorisindeki en çok tercih edilen patenli ayakkabı modellerimiz.' : 'En çok tercih edilen patenli ayakkabı ve tekerlekli sneaker modellerimiz.' }}
                </p>
            </div>

            <livewire:product.product-grid />
            
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
