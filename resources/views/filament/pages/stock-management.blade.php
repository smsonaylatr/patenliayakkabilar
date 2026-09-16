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

    @livewire(\App\Livewire\Admin\StockChart::class)

    <x-filament::section class="mb-6">
        <x-slot name="heading">Beden Matrisi Görünümü</x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="p-2 border dark:border-gray-700">Ürün Adı</th>
                        @foreach($sizes as $size)
                            <th class="p-2 border dark:border-gray-700 text-center">{{ $size }}</th>
                        @endforeach
                        <th class="p-2 border dark:border-gray-700 text-center">Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matrix as $row)
                        <tr>
                            <td class="p-2 border dark:border-gray-700 font-medium">{{ $row['name'] }}</td>
                            @foreach($sizes as $size)
                                @php
                                    $stock = $row['sizes'][$size];
                                    $style = '';
                                    if ($stock !== null) {
                                        if ($stock === 0) $style = 'background-color: #ef4444; color: white;';
                                        elseif ($stock <= 3) $style = 'background-color: #f97316; color: white;';
                                        elseif ($stock <= 5) $style = 'background-color: #eab308; color: white;';
                                        else $style = 'background-color: #22c55e; color: white;';
                                    }
                                @endphp
                                <td class="p-2 border dark:border-gray-700 text-center" style="{{ $style }}">
                                    {{ $stock !== null ? $stock : '-' }}
                                </td>
                            @endforeach
                            <td class="p-2 border dark:border-gray-700 text-center font-bold">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{ $this->table }}

    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Stok Hareket Geçmişi</h3>
        @livewire(\App\Livewire\Admin\StockMovementHistory::class)
    </div>
</x-filament-panels::page>
