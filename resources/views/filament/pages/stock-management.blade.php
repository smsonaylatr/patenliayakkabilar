<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-filament::section>
            <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Ürün</span>
                <span class="text-3xl font-bold">{{ $totalProducts }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Stokta Olan</span>
                <span class="text-3xl font-bold text-success-600">{{ $inStock }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tükenen</span>
                <span class="text-3xl font-bold text-danger-600">{{ $outOfStock }}</span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Düşük Stok</span>
                <span class="text-3xl font-bold text-warning-600">{{ $lowStock }}</span>
            </div>
        </x-filament::section>
    </div>

    {{ $this->table }}

    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Stok Hareket Geçmişi</h3>
        @livewire(\App\Livewire\Admin\StockMovementHistory::class)
    </div>
</x-filament-panels::page>
