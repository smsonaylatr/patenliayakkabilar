@php
    $order = $getRecord();
@endphp

@if($order)
<div class="p-4 space-y-4 text-sm bg-gray-50/70 dark:bg-gray-900/60 rounded-xl border border-gray-200/80 dark:border-gray-800 my-2">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 1. Teslimat Adresi ve Müşteri Bilgileri -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-1.5 pb-2 border-b border-gray-100 dark:border-gray-700">
                @svg('heroicon-o-map-pin', 'text-primary-500', ['style' => 'width: 1.1rem; height: 1.1rem; flex-shrink: 0; display: inline-block;'])
                <span>Teslimat Adresi</span>
            </h4>

            <div>
                <span class="text-xs text-gray-400 block font-medium">Alıcı Adı:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_name ?: 'Belirtilmedi' }}</span>
            </div>

            <div>
                <span class="text-xs text-gray-400 block font-medium">Telefon:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_phone ?: 'Belirtilmedi' }}</span>
            </div>

            <div>
                <span class="text-xs text-gray-400 block font-medium">Adres:</span>
                <p class="font-medium text-gray-800 dark:text-gray-200 text-xs mt-0.5 leading-relaxed">
                    {{ $order->shipping_address ?: 'Adres girilmemiş' }}
                    @if($order->shipping_district || $order->shipping_city)
                        <br><strong class="text-primary-600 dark:text-primary-400">{{ implode(' / ', array_filter([$order->shipping_district, $order->shipping_city])) }}</strong>
                    @endif
                </p>
            </div>

            @if($order->customer_note)
            <div class="bg-amber-50 dark:bg-amber-950/40 p-2 rounded border border-amber-200 dark:border-amber-800/50 text-xs">
                <span class="text-amber-700 dark:text-amber-300 font-bold block">Not:</span>
                <p class="text-amber-900 dark:text-amber-200 italic mt-0.5">{{ $order->customer_note }}</p>
            </div>
            @endif
        </div>

        <!-- 2. Sipariş Edilen Ürünler Listesi -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300 flex items-center gap-1.5">
                        @svg('heroicon-o-shopping-bag', 'text-primary-500', ['style' => 'width: 1.1rem; height: 1.1rem; flex-shrink: 0; display: inline-block;'])
                        <span>Sipariş Edilen Ürünler ({{ $order->items->count() }})</span>
                    </h4>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700/60 max-h-72 overflow-y-auto">
                    @forelse($order->items as $item)
                        @php
                            $product = $item->product;
                            $variant = $item->variant;
                            
                            $imageUrl = null;
                            if ($product) {
                                $product->loadMissing('images');
                                $imageUrl = $product->images->first()?->image_url;
                            }
                            if (!$imageUrl) {
                                $imageUrl = asset('favicon.png');
                            }

                            $variantText = $item->variant_info;
                            if (!$variantText && $variant) {
                                $vColor = is_array($variant->color) ? implode(', ', $variant->color) : $variant->color;
                                $vSize = $variant->size;
                                $variantText = trim(($vColor ? "Renk: {$vColor} " : '') . ($vSize ? "Beden: {$vSize}" : ''));
                            }
                        @endphp
                        <div class="p-3 flex items-center gap-3 hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                            <div class="shrink-0 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px;">
                                <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" style="width: 48px; height: 48px; object-fit: cover;">
                            </div>

                            <div class="flex-1 min-w-0">
                                <h5 class="font-bold text-gray-900 dark:text-white truncate text-xs">
                                    {{ $item->product_name }}
                                </h5>
                                
                                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                    @if($variantText)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-primary-50 dark:bg-primary-950/60 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800/50">
                                            {{ $variantText }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $item->quantity }} Adet × {{ number_format($item->unit_price, 2) }} ₺
                                    </span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="font-bold text-gray-900 dark:text-white text-xs">
                                    {{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 2) }} ₺
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-gray-400 text-xs">
                            Bu siparişe ait ürün bulunamadı.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Fiyat Alt Bilgisi -->
            <div class="bg-gray-50 dark:bg-gray-700/40 p-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-xs">
                <span class="text-gray-500 dark:text-gray-400 font-medium">Genel Toplam:</span>
                <span class="text-sm font-extrabold text-primary-600 dark:text-primary-400">{{ number_format($order->grand_total, 2) }} ₺</span>
            </div>
        </div>
    </div>
</div>
@endif
