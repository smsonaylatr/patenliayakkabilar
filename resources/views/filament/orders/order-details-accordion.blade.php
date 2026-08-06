@php
    $order = $getRecord();
@endphp

@if($order)
<div class="p-4 my-2 text-sm bg-gray-50/80 dark:bg-gray-900/80 rounded-2xl border border-gray-200/80 dark:border-gray-800 space-y-5">
    
    <!-- 1. ÜST BİLGİ KARTLARI (2 Kolonlu Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- Teslimat Adresi Kartı -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-2.5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700/70">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                    @svg('heroicon-o-map-pin', 'text-primary-500', ['style' => 'width: 1.1rem; height: 1.1rem; flex-shrink: 0; display: inline-block;'])
                    <span>Teslimat & İletişim Bilgileri</span>
                </h4>
                @if($order->shipping_city)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400">
                        {{ implode(' / ', array_filter([$order->shipping_district, $order->shipping_city])) }}
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <span class="text-gray-400 block font-medium">Alıcı Adı Soyadı:</span>
                    <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $order->customer_name ?: 'Misafir' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Telefon:</span>
                    <a href="tel:{{ $order->customer_phone }}" class="font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        {{ $order->customer_phone ?: '-' }}
                    </a>
                </div>
            </div>

            <div class="text-xs pt-1">
                <span class="text-gray-400 block font-medium mb-0.5">Açık Adres:</span>
                <p class="font-medium text-gray-800 dark:text-gray-200 leading-relaxed bg-gray-50 dark:bg-gray-900/50 p-2 rounded-lg border border-gray-100 dark:border-gray-800">
                    {{ $order->shipping_address ?: 'Adres girilmemiş' }}
                </p>
            </div>

            @if($order->customer_note)
            <div class="bg-amber-50/80 dark:bg-amber-950/50 p-2.5 rounded-lg border border-amber-200/80 dark:border-amber-800/60 text-xs">
                <span class="text-amber-800 dark:text-amber-300 font-bold block mb-0.5">Müşteri Notu:</span>
                <p class="text-amber-900 dark:text-amber-200 italic">{{ $order->customer_note }}</p>
            </div>
            @endif
        </div>

        <!-- Ödeme & Kargo Özeti Kartı -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-2.5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700/70">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                    @svg('heroicon-o-credit-card', 'text-primary-500', ['style' => 'width: 1.1rem; height: 1.1rem; flex-shrink: 0; display: inline-block;'])
                    <span>Ödeme & Kargo Özeti</span>
                </h4>
                <span class="text-xs font-mono font-bold text-gray-400">#{{ $order->order_number }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-gray-400 block font-medium">Ödeme Yöntemi:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        @switch($order->payment_method)
                            @case('credit_card') Kredi Kartı @break
                            @case('wire_transfer') Havale / EFT @break
                            @case('cash_on_delivery') Kapıda Ödeme @break
                            @default {{ $order->payment_method ?: '-' }}
                        @endswitch
                    </span>
                </div>

                <div>
                    <span class="text-gray-400 block font-medium">Ödeme Durumu:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold 
                        @if($order->payment_status === 'paid') bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300
                        @elseif($order->payment_status === 'pending') bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300
                        @else bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 @endif">
                        @switch($order->payment_status)
                            @case('paid') Ödendi @break
                            @case('pending') Beklemede @break
                            @case('refunded') İade Edildi @break
                            @case('failed') Başarısız @break
                            @default {{ $order->payment_status }}
                        @endswitch
                    </span>
                </div>

                <div>
                    <span class="text-gray-400 block font-medium">Kargo Şirketi:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->cargo_company ?: '-') }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block font-medium">Takip Kodu:</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $order->cargo_tracking_code ?: '-' }}</span>
                </div>
            </div>

            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60 flex justify-between items-center text-xs">
                <span class="text-gray-400 font-medium">E-Fatura Durumu:</span>
                @if($order->is_invoiced)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                        Fatura Kesildi
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                        Kesilmedi
                    </span>
                @endif
            </div>
        </div>

    </div>

    <!-- 2. SİPARİŞ EDİLEN ÜRÜNLER TABLOSU -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs overflow-hidden">
        
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/80 border-b border-gray-200/80 dark:border-gray-700 flex justify-between items-center">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 flex items-center gap-2">
                @svg('heroicon-o-shopping-bag', 'text-primary-500', ['style' => 'width: 1.1rem; height: 1.1rem; flex-shrink: 0; display: inline-block;'])
                <span>Sipariş Edilen Ürünler Tablosu ({{ $order->items->count() }})</span>
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-gray-700/80">
                <thead class="bg-gray-100/70 dark:bg-gray-900/60 text-gray-500 dark:text-gray-400 uppercase font-bold text-[11px] tracking-wider">
                    <tr>
                        <th scope="col" class="py-3 px-4 w-16 text-center">Görsel</th>
                        <th scope="col" class="py-3 px-4">Ürün Adı</th>
                        <th scope="col" class="py-3 px-4 text-center">Beden / Varyant</th>
                        <th scope="col" class="py-3 px-4 text-center">Adet</th>
                        <th scope="col" class="py-3 px-4 text-right">Birim Fiyat</th>
                        <th scope="col" class="py-3 px-4 text-right">Toplam Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 bg-white dark:bg-gray-800">
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
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/40 transition-colors">
                            <!-- Görsel -->
                            <td class="py-3 px-4 text-center">
                                <div class="w-12 h-12 mx-auto rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 shrink-0 shadow-2xs" style="width: 48px; height: 48px;">
                                    <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" style="width: 48px; height: 48px; object-fit: cover;">
                                </div>
                            </td>

                            <!-- Ürün Adı -->
                            <td class="py-3 px-4">
                                <span class="font-bold text-gray-900 dark:text-white text-sm block leading-tight">
                                    {{ $item->product_name }}
                                </span>
                                @if($product && $product->sku)
                                    <span class="text-[10px] font-mono text-gray-400 block mt-0.5">SKU: {{ $product->sku }}</span>
                                @endif
                            </td>

                            <!-- Beden / Varyant -->
                            <td class="py-3 px-4 text-center">
                                @if($variantText)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-primary-50 dark:bg-primary-950/70 text-primary-700 dark:text-primary-300 border border-primary-200/80 dark:border-primary-800/60">
                                        {{ $variantText }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs font-medium">-</span>
                                @endif
                            </td>

                            <!-- Adet -->
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 font-extrabold text-gray-900 dark:text-white text-xs">
                                    {{ $item->quantity }}
                                </span>
                            </td>

                            <!-- Birim Fiyat -->
                            <td class="py-3 px-4 text-right font-medium text-gray-600 dark:text-gray-300">
                                {{ number_format($item->unit_price, 2) }} ₺
                            </td>

                            <!-- Toplam Tutar -->
                            <td class="py-3 px-4 text-right">
                                <span class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 2) }} ₺
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400 text-xs">
                                Bu siparişe ait ürün bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- TABLO ALTI FİYAT HESAPLAMA FOOTER BAR -->
        <div class="bg-gray-50 dark:bg-gray-900/90 p-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-end sm:items-center gap-2">
            <div class="text-xs text-gray-400 font-medium">
                Sipariş Tarihi: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : '-' }}</span>
            </div>

            <div class="w-full sm:w-auto space-y-1 text-xs text-right">
                <div class="flex justify-between sm:justify-end gap-6 text-gray-500 dark:text-gray-400">
                    <span>Ara Toplam:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($order->subtotal ?: $order->grand_total, 2) }} ₺</span>
                </div>

                @if($order->discount_total > 0)
                <div class="flex justify-between sm:justify-end gap-6 text-emerald-600 dark:text-emerald-400">
                    <span>İndirim:</span>
                    <span class="font-semibold">-{{ number_format($order->discount_total, 2) }} ₺</span>
                </div>
                @endif

                @if($order->shipping_price > 0)
                <div class="flex justify-between sm:justify-end gap-6 text-gray-500 dark:text-gray-400">
                    <span>Kargo Ücreti:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($order->shipping_price, 2) }} ₺</span>
                </div>
                @endif

                <div class="flex justify-between sm:justify-end gap-6 text-sm font-extrabold text-gray-900 dark:text-white pt-1.5 border-t border-gray-200 dark:border-gray-700">
                    <span>Genel Ödenecek Toplam:</span>
                    <span class="text-primary-600 dark:text-primary-400 text-base font-black">{{ number_format($order->grand_total, 2) }} ₺</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endif
