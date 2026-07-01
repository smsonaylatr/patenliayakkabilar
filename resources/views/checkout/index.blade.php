<x-layouts.app>
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-brand-dark mb-10">Güvenli Ödeme</h1>
            
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
                <div class="lg:col-span-7">
                    <livewire:checkout.checkout-form />
                </div>
                <div class="mt-10 lg:mt-0 lg:col-span-5">
                    <livewire:checkout.checkout-summary />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
