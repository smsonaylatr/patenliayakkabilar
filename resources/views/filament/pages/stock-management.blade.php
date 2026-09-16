<x-filament-panels::page>
    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-500/10 text-xl">📦</div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Toplam Ürün</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-50 dark:bg-success-500/10 text-xl">✅</div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Stokta</p>
                    <p class="text-2xl font-bold text-success-600">{{ $inStock }}</p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-danger-50 dark:bg-danger-500/10 text-xl">❌</div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tükenen</p>
                    <p class="text-2xl font-bold text-danger-600">{{ $outOfStock }}</p>
                </div>
            </div>
        </div>

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-50 dark:bg-warning-500/10 text-xl">⚠️</div>
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
            🗂️ Beden Stok Matrisi — <span class="text-xs font-normal text-gray-400">Hücreye tıklayarak stok düzenleyebilirsiniz</span>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr>
                        <th class="sticky left-0 z-10 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-left font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-700 min-w-[220px]">
                            Ürün
                        </th>
                        @foreach($sizes as $size)
                            <th class="px-1 py-2.5 text-center font-semibold text-gray-600 dark:text-gray-400 border-b-2 border-gray-200 dark:border-gray-700 w-[46px]">
                                {{ $size }}
                            </th>
                        @endforeach
                        <th class="px-3 py-2.5 text-center font-bold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 w-[60px]">
                            Toplam
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matrix as $rowIndex => $row)
                        <tr class="group border-b border-gray-100 dark:border-gray-800 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                            {{-- Ürün Adı + Görsel --}}
                            <td class="sticky left-0 z-10 bg-white dark:bg-gray-900 group-hover:bg-blue-50/30 dark:group-hover:bg-blue-900/10 px-3 py-1.5 transition-colors">
                                <div class="flex items-center gap-2">
                                    @if($row['image'])
                                        <img src="{{ asset('storage/' . $row['image']) }}" 
                                             alt="" 
                                             class="h-8 w-8 rounded object-cover ring-1 ring-gray-200 dark:ring-gray-700 flex-shrink-0"
                                             style="max-width:32px;max-height:32px;"
                                             loading="lazy">
                                    @else
                                        <div class="h-8 w-8 rounded bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-xs">👟</div>
                                    @endif
                                    <span class="font-medium text-gray-800 dark:text-gray-200 text-xs leading-tight truncate max-w-[160px]" title="{{ $row['name'] }}">{{ $row['name'] }}</span>
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
                                    @if($stock !== null && $variantId)
                                        <div x-data="{ editing: false, value: {{ $stock }}, original: {{ $stock }} }">
                                            {{-- Gösterim --}}
                                            <button
                                                x-show="!editing"
                                                x-cloak
                                                @click="editing = true; $nextTick(() => { let el = $refs['i{{ $variantId }}']; if(el) el.select(); })"
                                                class="w-10 h-7 rounded text-xs font-bold cursor-pointer transition-all hover:scale-110 hover:shadow-md inline-flex items-center justify-center
                                                    @if($stock <= 0) bg-red-500 text-white
                                                    @elseif($stock <= 3) bg-orange-400 text-white
                                                    @elseif($stock <= 5) bg-amber-400 text-gray-900
                                                    @else bg-emerald-500 text-white
                                                    @endif
                                                "
                                            >{{ $stock }}</button>

                                            {{-- Düzenleme --}}
                                            <input
                                                x-show="editing"
                                                x-cloak
                                                x-ref="i{{ $variantId }}"
                                                type="number" min="0"
                                                x-model.number="value"
                                                @keydown.enter="if(value!==original&&value>=0){$wire.updateMatrixStock({{ $variantId }},value);original=value}editing=false"
                                                @keydown.escape="value=original;editing=false"
                                                @click.outside="if(value!==original&&value>=0){$wire.updateMatrixStock({{ $variantId }},value);original=value}editing=false"
                                                class="w-10 h-7 rounded text-xs font-bold text-center border-2 border-primary-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                style="max-width:40px;"
                                            >
                                        </div>
                                    @else
                                        <span class="inline-flex w-10 h-7 items-center justify-center rounded bg-gray-100 dark:bg-gray-800/50 text-gray-300 dark:text-gray-600 text-xs">—</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Toplam --}}
                            <td class="px-2 py-1 text-center bg-gray-50/50 dark:bg-gray-800/30">
                                <span class="inline-flex items-center justify-center h-6 px-2 rounded-full text-xs font-bold
                                    @if($row['total'] <= 0) bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400
                                    @elseif($row['total'] <= 10) bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400
                                    @else bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400
                                    @endif
                                ">{{ $row['total'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Renk Açıklaması --}}
            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500">
                <span class="text-gray-400">Renk:</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-500 inline-block"></span> Tükendi</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-400 inline-block"></span> Kritik (1-3)</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-amber-400 inline-block"></span> Düşük (4-5)</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Yeterli (6+)</span>
            </div>
        </div>
    </x-filament::section>

    {{-- Varyant Tablosu --}}
    {{ $this->table }}

    {{-- Stok Hareket Geçmişi --}}
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Stok Hareket Geçmişi</h3>
        @livewire(\App\Livewire\Admin\StockMovementHistory::class)
    </div>
</x-filament-panels::page>
