<x-filament-panels::page>
    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">📦 Toplam Ürün</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</p>
        </div>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">✅ Stokta</p>
            <p class="text-2xl font-bold text-success-600">{{ $inStock }}</p>
        </div>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">❌ Tükenen</p>
            <p class="text-2xl font-bold text-danger-600">{{ $outOfStock }}</p>
        </div>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">⚠️ Düşük Stok</p>
            <p class="text-2xl font-bold text-warning-600">{{ $lowStock }}</p>
        </div>
    </div>

    {{-- Stok Değişim Grafiği --}}
    @livewire(\App\Livewire\Admin\StockChart::class)

    {{-- Beden Matrisi --}}
    <x-filament::section class="mb-6">
        <x-slot name="heading">
            🗂️ Beden Stok Matrisi — <span class="text-xs font-normal text-gray-400">Hücreye tıklayarak stok düzenleyebilirsiniz</span>
        </x-slot>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
                <colgroup>
                    <col style="width:200px;">
                    @foreach($sizes as $size)
                        <col style="width:44px;">
                    @endforeach
                    <col style="width:56px;">
                </colgroup>
                <thead>
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:8px 6px;text-align:left;font-size:12px;font-weight:600;color:#6b7280;">Ürün</th>
                        @foreach($sizes as $size)
                            <th style="padding:8px 2px;text-align:center;font-size:11px;font-weight:700;color:#4b5563;">{{ $size }}</th>
                        @endforeach
                        <th style="padding:8px 4px;text-align:center;font-size:11px;font-weight:700;color:#374151;background:#f9fafb;">Top.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matrix as $rowIndex => $row)
                        <tr style="border-bottom:1px solid #f3f4f6;" onmouseover="this.style.backgroundColor='#f0f9ff'" onmouseout="this.style.backgroundColor='transparent'">
                            {{-- Ürün --}}
                            <td style="padding:4px 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:11px;font-weight:500;color:#1f2937;" title="{{ $row['name'] }}">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    @if($row['image'])
                                        <img src="{{ asset('storage/' . $row['image']) }}" style="width:24px;height:24px;border-radius:4px;object-fit:cover;flex-shrink:0;" loading="lazy">
                                    @else
                                        <span style="width:24px;height:24px;border-radius:4px;background:#f3f4f6;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;">👟</span>
                                    @endif
                                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $row['name'] }}</span>
                                </div>
                            </td>

                            {{-- Beden Hücreleri --}}
                            @foreach($sizes as $size)
                                @php
                                    $sizeData = $row['sizes'][$size];
                                    $stock = $sizeData['stock'];
                                    $variantId = $sizeData['variant_id'];
                                @endphp
                                <td style="padding:2px 1px;text-align:center;vertical-align:middle;">
                                    @if($stock !== null && $variantId)
                                        <div x-data="{ editing: false, value: {{ $stock }}, original: {{ $stock }} }">
                                            <button
                                                x-show="!editing"
                                                x-cloak
                                                @click="editing = true; $nextTick(() => { let el = $refs['i{{ $variantId }}']; if(el){el.focus();el.select();} })"
                                                style="width:38px;height:26px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;border:none;display:inline-flex;align-items:center;justify-content:center;transition:transform 0.15s;
                                                    @if($stock <= 0) background:#ef4444;color:#fff;
                                                    @elseif($stock <= 3) background:#f97316;color:#fff;
                                                    @elseif($stock <= 5) background:#eab308;color:#111827;
                                                    @else background:#22c55e;color:#fff;
                                                    @endif
                                                "
                                                onmouseover="this.style.transform='scale(1.15)'"
                                                onmouseout="this.style.transform='scale(1)'"
                                            >{{ $stock }}</button>

                                            <input
                                                x-show="editing"
                                                x-cloak
                                                x-ref="i{{ $variantId }}"
                                                type="number" min="0"
                                                x-model.number="value"
                                                @keydown.enter="if(value!==original&&value>=0){$wire.updateMatrixStock({{ $variantId }},value);original=value}editing=false"
                                                @keydown.escape="value=original;editing=false"
                                                @click.outside="if(value!==original&&value>=0){$wire.updateMatrixStock({{ $variantId }},value);original=value}editing=false"
                                                style="width:38px;height:26px;border-radius:4px;font-size:11px;font-weight:700;text-align:center;border:2px solid #6366f1;outline:none;-moz-appearance:textfield;"
                                            >
                                        </div>
                                    @else
                                        <span style="display:inline-flex;width:38px;height:26px;align-items:center;justify-content:center;border-radius:4px;background:#f3f4f6;color:#d1d5db;font-size:11px;">—</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Toplam --}}
                            <td style="padding:2px 4px;text-align:center;background:#f9fafb;vertical-align:middle;">
                                <span style="display:inline-flex;align-items:center;justify-content:center;height:22px;padding:0 8px;border-radius:12px;font-size:11px;font-weight:700;
                                    @if($row['total'] <= 0) background:#fecaca;color:#b91c1c;
                                    @elseif($row['total'] <= 10) background:#fef3c7;color:#92400e;
                                    @else background:#d1fae5;color:#065f46;
                                    @endif
                                ">{{ $row['total'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Renk Açıklaması --}}
            <div style="display:flex;align-items:center;gap:16px;margin-top:12px;padding-top:12px;border-top:1px solid #f3f4f6;font-size:11px;color:#6b7280;">
                <span style="color:#9ca3af;">Renk:</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#ef4444;display:inline-block;"></span> Tükendi</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#f97316;display:inline-block;"></span> Kritik (1-3)</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#eab308;display:inline-block;"></span> Düşük (4-5)</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#22c55e;display:inline-block;"></span> Yeterli (6+)</span>
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
