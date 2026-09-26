<div class="space-y-6 text-sm">
    {{-- Müşteri Özet Kartı --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-4 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-200 dark:border-gray-700">
        <div>
            <div class="text-xs text-gray-500 font-medium">Müşteri</div>
            <div class="font-bold text-gray-900 dark:text-white mt-0.5">{{ $record->display_name }}</div>
            <div class="text-xs text-gray-400 font-mono">{{ $record->ip_address }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-500 font-medium">Satın Alma Niyeti</div>
            <div class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                %{{ $record->intent_score }} - {{ strtoupper($record->intent_level) }}
            </div>
            <div class="text-xs text-gray-400">{{ $record->page_views_count }} sayfa • {{ $record->duration_formatted }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-500 font-medium">Cihaz / Tarayıcı</div>
            <div class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">
                {{ ucfirst($record->device_type) }} • {{ $record->browser }}
            </div>
            <div class="text-xs text-gray-400">{{ $record->screen_resolution ?? 'Bilinmiyor' }}</div>
        </div>
    </div>

    {{-- Davranış Teşhisi --}}
    <div class="p-4 bg-orange-50/70 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800 rounded-xl flex items-start gap-3">
        <span class="text-2xl shrink-0">💡</span>
        <div>
            <h4 class="font-bold text-orange-900 dark:text-orange-200 text-xs uppercase tracking-wider">Otomatik Davranış Teşhisi & Yorumu</h4>
            <p class="text-xs sm:text-sm text-orange-800 dark:text-orange-300 mt-1">
                {{ $record->behavior_insight ?? 'Ziyaretçi genel keşif aşamasında.' }}
            </p>
        </div>
    </div>

    {{-- Sepet Özeti (Eğer sepette ürün varsa) --}}
    @if(!empty($record->cart_summary))
        <div>
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <span>🛒 Sepet İçeriği</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                        {{ $record->cart_items_count }} Ürün
                    </span>
                </h4>
                <div class="font-bold text-emerald-600 text-sm">
                    Toplam: {{ number_format($record->cart_total, 2) }} ₺
                </div>
            </div>
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($record->cart_summary as $item)
                    <div class="p-3 flex items-center justify-between bg-white dark:bg-gray-800/40">
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $item['product_name'] ?? 'Ürün' }}</div>
                            <div class="text-xs text-gray-500">
                                @if(!empty($item['size'])) <span class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-[11px] font-mono mr-1">Beden: {{ $item['size'] }}</span> @endif
                                @if(!empty($item['color'])) <span>Renk: {{ $item['color'] }}</span> @endif
                                <span>• {{ $item['quantity'] ?? 1 }} adet</span>
                            </div>
                        </div>
                        <div class="font-bold text-gray-900 dark:text-white">
                            {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }} ₺
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Kronolojik Gezinme İzi (Journey Trail) --}}
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <span>🐾 Adım Adım Gezinme İzi (Customer Journey Trail)</span>
        </h4>

        @if(!empty($record->journey_trail) && is_array($record->journey_trail))
            <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200 dark:before:bg-gray-700">
                @foreach(array_reverse($record->journey_trail) as $index => $step)
                    <div class="relative">
                        <span class="absolute -left-6 top-1 flex h-4 w-4 items-center justify-center rounded-full {{ $index === 0 ? 'bg-orange-500 ring-4 ring-orange-100 dark:ring-orange-950' : 'bg-gray-300 dark:bg-gray-600' }}">
                            @if($index === 0)
                                <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                            @endif
                        </span>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-xs text-gray-900 dark:text-white">
                                    {{ $step['title'] ?? $step['path'] ?? 'Sayfa' }}
                                </span>
                                <span class="text-[11px] font-mono text-gray-400">
                                    {{ $step['time'] ?? '' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500 font-mono mt-0.5 truncate">
                                {{ $step['path'] ?? '' }}
                            </div>
                            @if(!empty($step['detail']))
                                <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">
                                    🎯 {{ $step['detail'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-4 text-center text-xs text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl">
                Henüz detaylı hareket izi kaydedilmedi.
            </div>
        @endif
    </div>

    {{-- Trafik ve Referrer --}}
    @if($record->referrer || $record->utm_source)
        <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl text-xs text-gray-500 space-y-1">
            @if($record->referrer)
                <div><span class="font-semibold">Geldiği Kaynak:</span> {{ $record->referrer }}</div>
            @endif
            @if($record->utm_source)
                <div><span class="font-semibold">Kampanya Kaynağı:</span> {{ $record->utm_source }} ({{ $record->utm_campaign ?? '-' }})</div>
            @endif
        </div>
    @endif
</div>
