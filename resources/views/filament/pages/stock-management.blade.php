<x-filament-panels::page>
    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-500/10">
                    <x-heroicon-o-cube class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Toplam Ürün</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-50 dark:bg-success-500/10">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Stokta</p>
                    <p class="text-2xl font-bold text-success-600">{{ $inStock }}</p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-danger-50 dark:bg-danger-500/10">
                    <x-heroicon-o-x-circle class="h-5 w-5 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tükenen</p>
                    <p class="text-2xl font-bold text-danger-600">{{ $outOfStock }}</p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-50 dark:bg-warning-500/10">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Düşük Stok</p>
                    <p class="text-2xl font-bold text-warning-600">{{ $lowStock }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stok Değişim Grafiği --}}
    @livewire(\App\Livewire\Admin\StockChart::class)

    {{-- Beden Matrisi --}}
    <x-filament::section class="mb-6">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-table-cells class="h-5 w-5 text-primary-500" />
                <span>Beden Stok Matrisi</span>
                <span class="text-xs font-normal text-gray-400 ml-2">Hücreye tıklayarak stok düzenleyebilirsiniz</span>
            </div>
        </x-slot>

        <div class="overflow-x-auto" x-data="stockMatrix()">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr>
                        <th class="sticky left-0 z-10 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-left font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 min-w-[240px]">
                            Ürün
                        </th>
                        @foreach($sizes as $size)
                            <th class="px-1.5 py-2.5 text-center font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 min-w-[48px]">
                                {{ $size }}
                            </th>
                        @endforeach
                        <th class="px-3 py-2.5 text-center font-bold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 min-w-[60px]">
                            Toplam
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matrix as $rowIndex => $row)
                        <tr class="group border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            {{-- Ürün Adı --}}
                            <td class="sticky left-0 z-10 bg-white dark:bg-gray-900 group-hover:bg-gray-50/50 dark:group-hover:bg-gray-800/50 px-3 py-2 transition-colors">
                                <div class="flex items-center gap-2.5">
                                    @if($row['image'])
                                        <img src="{{ asset('storage/' . $row['image']) }}" alt="" class="h-8 w-8 rounded-md object-cover ring-1 ring-gray-200 dark:ring-gray-700 flex-shrink-0">
                                    @else
                                        <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-photo class="h-4 w-4 text-gray-400" />
                                        </div>
                                    @endif
                                    <span class="font-medium text-gray-800 dark:text-gray-200 text-xs leading-tight line-clamp-2">{{ $row['name'] }}</span>
                                </div>
                            </td>

                            {{-- Beden Hücreleri --}}
                            @foreach($sizes as $size)
                                @php
                                    $sizeData = $row['sizes'][$size];
                                    $stock = $sizeData['stock'];
                                    $variantId = $sizeData['variant_id'];
                                @endphp
                                <td class="px-0.5 py-1 text-center">
                                    @if($stock !== null)
                                        <div
                                            x-data="{ editing: false, value: {{ $stock }}, original: {{ $stock }} }"
                                            class="relative"
                                        >
                                            {{-- Gösterim Modu --}}
                                            <button
                                                x-show="!editing"
                                                @click="editing = true; $nextTick(() => $refs.input_{{ $rowIndex }}_{{ $size }}.select())"
                                                class="w-full h-8 rounded-md text-xs font-bold cursor-pointer transition-all duration-150 hover:scale-110 hover:shadow-md
                                                    @if($stock <= 0) bg-red-500 text-white hover:bg-red-600
                                                    @elseif($stock <= 3) bg-orange-400 text-white hover:bg-orange-500
                                                    @elseif($stock <= 5) bg-amber-400 text-gray-900 hover:bg-amber-500
                                                    @else bg-emerald-500 text-white hover:bg-emerald-600
                                                    @endif
                                                "
                                            >
                                                {{ $stock }}
                                            </button>

                                            {{-- Düzenleme Modu --}}
                                            <input
                                                x-show="editing"
                                                x-ref="input_{{ $rowIndex }}_{{ $size }}"
                                                type="number"
                                                min="0"
                                                x-model.number="value"
                                                @keydown.enter="
                                                    if (value !== original && value >= 0) {
                                                        $wire.updateMatrixStock({{ $variantId }}, value);
                                                        original = value;
                                                    }
                                                    editing = false;
                                                "
                                                @keydown.escape="value = original; editing = false"
                                                @click.outside="
                                                    if (value !== original && value >= 0) {
                                                        $wire.updateMatrixStock({{ $variantId }}, value);
                                                        original = value;
                                                    }
                                                    editing = false;
                                                "
                                                class="w-full h-8 rounded-md text-xs font-bold text-center border-2 border-primary-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                            >
                                        </div>
                                    @else
                                        <div class="w-full h-8 rounded-md bg-gray-100 dark:bg-gray-800/50 flex items-center justify-center">
                                            <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Toplam --}}
                            <td class="px-3 py-1 text-center bg-gray-50 dark:bg-gray-800/50">
                                <span class="inline-flex items-center justify-center h-7 px-2.5 rounded-full text-xs font-bold
                                    @if($row['total'] <= 0) bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400
                                    @elseif($row['total'] <= 10) bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400
                                    @else bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400
                                    @endif
                                ">
                                    {{ $row['total'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Renk Açıklaması --}}
            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <span class="text-xs text-gray-400">Renk Kodu:</span>
                <div class="flex items-center gap-1"><div class="w-3 h-3 rounded bg-red-500"></div><span class="text-xs text-gray-500">Tükendi</span></div>
                <div class="flex items-center gap-1"><div class="w-3 h-3 rounded bg-orange-400"></div><span class="text-xs text-gray-500">Kritik (1-3)</span></div>
                <div class="flex items-center gap-1"><div class="w-3 h-3 rounded bg-amber-400"></div><span class="text-xs text-gray-500">Düşük (4-5)</span></div>
                <div class="flex items-center gap-1"><div class="w-3 h-3 rounded bg-emerald-500"></div><span class="text-xs text-gray-500">Yeterli (6+)</span></div>
            </div>
        </div>
    </x-filament::section>

    {{-- Varyant Tablosu --}}
    {{ $this->table }}

    {{-- Stok Hareket Geçmişi --}}
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <x-heroicon-o-clock class="h-5 w-5 text-gray-400" />
            Stok Hareket Geçmişi
        </h3>
        @livewire(\App\Livewire\Admin\StockMovementHistory::class)
    </div>

    <script>
        function stockMatrix() {
            return {};
        }
    </script>
</x-filament-panels::page>
