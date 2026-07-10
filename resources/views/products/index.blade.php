<x-layouts.app>
    <x-slot:title>Tüm Patenli Ayakkabı Modelleri | Patenli Ayakkabılar</x-slot>
    <x-slot:description>Tüm ışıklı ve tekerlekli sneaker modellerimizi keşfedin.</x-slot>

    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    {{ isset($category) && $category ? $category->name : 'Tüm Modeller' }}
                </h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    {{ isset($category) && $category ? $category->name . ' kategorisindeki en çok tercih edilen patenli ayakkabı modellerimiz.' : 'En çok tercih edilen patenli ayakkabı ve tekerlekli sneaker modellerimiz.' }}
                </p>
            </div>

            <livewire:product.product-grid />
            
        </div>
    </div>
</x-layouts.app>
