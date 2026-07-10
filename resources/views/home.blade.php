<x-layouts.app>
    <x-slot:title>Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri ve Fiyatları</x-slot:title>
    <x-slot:description>Çocuk ve genç patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçenekler. Güvenli alışveriş, hızlı kargo ile kapınızda.</x-slot:description>
    <x-slot:canonical>{{ url('/') }}</x-slot:canonical>

    @include('livewire.home.hero-section')
    

    
    <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="pt-4 pb-16 bg-white transition-all duration-1000 ease-out">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:product.product-grid />
        </div>
    </div>
    
    <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000 ease-out">
        <livewire:product.review-list />
    </div>
    
    <livewire:frontend.newsletter-form />
</x-layouts.app>
