<div class="space-y-6 text-sm">
    <!-- 1. Teslimat Adresi ve Müşteri Bilgileri -->
    <div class="bg-gray-50 dark:bg-gray-800/60 p-4 rounded-xl border border-gray-200 dark:border-gray-700/80">
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Teslimat Adresi & Müşteri Bilgileri
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-xs text-gray-400 block font-medium">Alıcı Adı:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_name ?: 'Belirtilmedi' }}</span>
            </div>
            
            <div>
                <span class="text-xs text-gray-400 block font-medium">Telefon:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_phone ?: 'Belirtilmedi' }}</span>
            </div>

            <div class="md:col-span-2">
                <span class="text-xs text-gray-400 block font-medium">Teslimat Adresi:</span>
                <p class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">
                    {{ $order->shipping_address ?: 'Adres girilmemiş' }}
                    @if($order->shipping_district || $order->shipping_city)
                        <br><span class="font-bold text-primary-600 dark:text-primary-400">{{ implode(' / ', array_filter([$order->shipping_district, $order->shipping_city])) }}</span>
                    @endif
                </p>
            </div>

            @if($order->customer_note)
            <div class="md:col-span-2 bg-amber-50 dark:bg-amber-950/40 p-2.5 rounded-lg border border-amber-200 dark:border-amber-800/50">
                <span class="text-xs text-amber-700 dark:text-amber-300 font-bold block">Müşteri Notu:</span>
                <p class="text-xs text-amber-900 dark:text-amber-200 italic mt-0.5">{{ $order->customer_note }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- 2. Sipariş Edilen Ürünler -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/40 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Sipariş Edilen Ürünler ({{ $order->items->count() }})
            </h4>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($order->items as $item)
                @php
                    $product = $item->product;
                    $variant = $item->variant;
                    
                    // Görsel tespiti
                    $imageUrl = null;
                    if ($product) {
                        $product->loadMissing('images');
                        $imageUrl = $product->images->first()?->image_url;
                    }
                    if (!$imageUrl) {
                        $imageUrl = asset('favicon.png');
                    }

                    // Beden ve varyant bilgisi
                    $variantText = $item->variant_info;
                    if (!$variantText && $variant) {
                        $vColor = is_array($variant->color) ? implode(', ', $variant->color) : $variant->color;
                        $vSize = $variant->size;
                        $variantText = trim(($vColor ? "Renk: {$vColor} " : '') . ($vSize ? "Beden: {$vSize}" : ''));
                    }
                @endphp
                <div class="p-3.5 flex items-center gap-4 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                    <!-- Ürün Görseli -->
                    <div class="w-14 h-14 shrink-0 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                    </div>

                    <!-- Ürün Detayları -->
                    <div class="flex-1 min-w-0">
                        <h5 class="font-bold text-gray-900 dark:text-white truncate text-sm">
                            {{ $item->product_name }}
                        </h5>
                        
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            @if($variantText)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-primary-50 dark:bg-primary-950/60 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800/50">
                                    {{ $variantText }}
                                </span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                {{ $item->quantity }} Adet × {{ number_format($item->unit_price, 2) }} ₺
                            </span>
                        </div>
                    </div>

                    <!-- Ürün Toplam Fiyatı -->
                    <div class="text-right shrink-0">
                        <span class="font-bold text-gray-900 dark:text-white text-base">
                            {{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 2) }} ₺
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-400 text-xs">
                    Bu siparişe ait ürün bulunamadı.
                </div>
            @endforelse
        </div>

        <!-- Fiyat Özeti -->
        <div class="bg-gray-50/80 dark:bg-gray-800/60 p-4 border-t border-gray-200 dark:border-gray-800 space-y-1.5 text-xs">
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Ara Toplam:</span>
                <span class="font-semibold">{{ number_format($order->subtotal ?: $order->grand_total, 2) }} ₺</span>
            </div>

            @if($order->discount_total > 0)
            <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                <span>İndirim:</span>
                <span class="font-semibold">-{{ number_format($order->discount_total, 2) }} ₺</span>
            </div>
            @endif

            @if($order->shipping_price > 0)
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Kargo Ücreti:</span>
                <span class="font-semibold">{{ number_format($order->shipping_price, 2) }} ₺</span>
            </div>
            @endif

            <div class="flex justify-between text-sm font-extrabold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                <span>Genel Toplam:</span>
                <span class="text-primary-600 dark:text-primary-400 text-base">{{ number_format($order->grand_total, 2) }} ₺</span>
            </div>
        </div>
    </div>
</div>
