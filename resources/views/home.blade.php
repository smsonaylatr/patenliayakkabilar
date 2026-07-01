<x-layouts.app>
    @include('livewire.home.hero-section')
    
    <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000 ease-out">
        <livewire:product.best-seller-carousel />
    </div>
    
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
