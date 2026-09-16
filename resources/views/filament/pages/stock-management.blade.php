<x-filament-panels::page>
    {{-- Dashboard Üst Bölüm: İstatistik + Grafikler --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">

        {{-- SOL: Stok Durumu Özet + Donut --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">📊 Stok Durumu</h3>
            <div style="display:flex;align-items:center;gap:20px;">
                {{-- Donut Chart --}}
                <div style="position:relative;width:130px;height:130px;flex-shrink:0;" wire:ignore>
                    <canvas id="donutChart" width="130" height="130"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                        <div style="font-size:22px;font-weight:800;color:#fff;">{{ $totalStock }}</div>
                        <div style="font-size:10px;color:#9ca3af;">Toplam Adet</div>
                    </div>
                </div>
                {{-- İstatistikler --}}
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Stokta</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;">{{ $inStock }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#eab308;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Düşük Stok</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;">{{ $lowStock }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Tükenen</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;">{{ $outOfStock }}</span>
                    </div>
                    <div style="border-top:1px solid rgba(255,255,255,0.1);padding-top:10px;margin-top:4px;display:flex;align-items:center;gap:8px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#6366f1;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Toplam Ürün</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;">{{ $totalProducts }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SAĞ: Son 14 Gün Hareketleri --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">📈 Son 14 Gün Stok Hareketleri</h3>
            <div style="position:relative;width:100%;height:140px;" wire:ignore>
                <canvas id="movementChart" style="width:100%;height:140px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Beden Bazlı Stok Dağılımı (Horizontal Bar) --}}
    <div style="background:#111827;border-radius:12px;padding:16px 20px;margin-bottom:24px;border:1px solid rgba(255,255,255,0.08);">
        <h3 style="font-size:13px;font-weight:600;color:#9ca3af;margin-bottom:10px;">👟 Beden Bazlı Stok Dağılımı</h3>
        <div style="display:flex;align-items:flex-end;gap:4px;height:60px;">
            @php $maxSize = max(array_values($sizeDistribution) ?: [1]); @endphp
            @foreach($sizeDistribution as $sizeNum => $sizeTotal)
                @php
                    $pct = $maxSize > 0 ? ($sizeTotal / $maxSize * 100) : 0;
                    if ($sizeTotal <= 0) $barColor = '#ef4444';
                    elseif ($sizeTotal <= 10) $barColor = '#f97316';
                    elseif ($sizeTotal <= 20) $barColor = '#eab308';
                    else $barColor = '#22c55e';
                @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;">
                    <span style="font-size:9px;color:#fff;font-weight:600;">{{ $sizeTotal }}</span>
                    <div style="width:100%;height:{{ max($pct * 0.4, 2) }}px;background:{{ $barColor }};border-radius:3px 3px 0 0;transition:height 0.3s;"></div>
                    <span style="font-size:9px;color:#6b7280;">{{ $sizeNum }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        function init() {
            // Donut
            var dc = document.getElementById('donutChart');
            if (dc) {
                new Chart(dc.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Stokta', 'Düşük', 'Tükenen'],
                        datasets: [{
                            data: [{{ $inStock - $lowStock }}, {{ $lowStock }}, {{ $outOfStock }}],
                            backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                            borderWidth: 0,
                            borderRadius: 3,
                        }]
                    },
                    options: {
                        responsive: false,
                        cutout: '70%',
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // Bar
            var mc = document.getElementById('movementChart');
            if (mc) {
                new Chart(mc.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            { label: 'Giriş', data: @json($chartIn), backgroundColor: 'rgba(34,197,94,0.7)', borderRadius: 3 },
                            { label: 'Çıkış', data: @json($chartOut), backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 3 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { color: '#6b7280', stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.05)' } },
                            x: { ticks: { color: '#6b7280', font: { size: 10 } }, grid: { display: false } }
                        },
                        plugins: {
                            legend: { position: 'top', labels: { color: '#9ca3af', boxWidth: 12, font: { size: 11 } } }
                        }
                    }
                });
            }
        }
        if (typeof Chart !== 'undefined') init();
        else document.addEventListener('DOMContentLoaded', function(){ setTimeout(init, 300); });
    })();
    </script>

    {{-- Beden Matrisi --}}
    <x-filament::section class="mb-6">
        <x-slot name="heading">
            🗂️ Beden Stok Matrisi — <span class="text-xs font-normal text-gray-400">Hücreye tıklayarak stok düzenleyebilirsiniz</span>
        </x-slot>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
                <colgroup>
                    <col style="width:260px;">
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
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                            {{-- Ürün --}}
                            <td style="padding:4px 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:11px;font-weight:500;" title="{{ $row['name'] }}">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($row['image'])
                                        <img src="{{ asset('storage/' . $row['image']) }}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0;" loading="lazy">
                                    @else
                                        <span style="width:40px;height:40px;border-radius:6px;background:#374151;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;">👟</span>
                                    @endif
                                    <span class="text-gray-900 dark:text-white" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $row['name'] }}</span>
                                </div>
                            </td>

                            {{-- Beden Hücreleri --}}
                            @foreach($sizes as $size)
                                @php
                                    $sizeData = $row['sizes'][$size];
                                    $stock = $sizeData['stock'];
                                    $variantId = $sizeData['variant_id'];
                                    
                                    if ($stock !== null) {
                                        if ($stock <= 0) { $bg = '#ef4444'; $fg = '#fff'; }
                                        elseif ($stock <= 3) { $bg = '#f97316'; $fg = '#fff'; }
                                        elseif ($stock <= 5) { $bg = '#eab308'; $fg = '#111'; }
                                        else { $bg = '#22c55e'; $fg = '#fff'; }
                                    } else {
                                        $bg = '#374151'; $fg = '#6b7280';
                                    }
                                @endphp
                                <td style="padding:2px 1px;text-align:center;vertical-align:middle;">
                                    @if($stock !== null && $variantId)
                                        <div x-data="{ editing: false, value: {{ $stock }}, original: {{ $stock }} }">
                                            <button
                                                x-show="!editing"
                                                x-cloak
                                                @click="editing = true; $nextTick(() => { let el = $refs['i{{ $variantId }}']; if(el){el.focus();el.select();} })"
                                                style="width:38px;height:26px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;border:none;display:inline-flex;align-items:center;justify-content:center;transition:transform 0.15s;background:{{ $bg }};color:{{ $fg }};"
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
                                        <span style="display:inline-flex;width:38px;height:26px;align-items:center;justify-content:center;border-radius:4px;background:#374151;color:#6b7280;font-size:11px;">—</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Toplam --}}
                            @php
                                if ($row['total'] <= 0) { $tbg = '#fecaca'; $tfg = '#b91c1c'; }
                                elseif ($row['total'] <= 10) { $tbg = '#fef3c7'; $tfg = '#92400e'; }
                                else { $tbg = '#d1fae5'; $tfg = '#065f46'; }
                            @endphp
                            <td style="padding:2px 4px;text-align:center;vertical-align:middle;">
                                <span style="display:inline-flex;align-items:center;justify-content:center;height:22px;padding:0 8px;border-radius:12px;font-size:11px;font-weight:700;background:{{ $tbg }};color:{{ $tfg }};">{{ $row['total'] }}</span>
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
