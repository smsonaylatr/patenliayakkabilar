@php
    $order = $getRecord();
@endphp

@if($order)
<div class="p-6 my-2 text-xs bg-[#0b0e14] dark:bg-[#0b0e14] border-t border-b border-gray-800/80 text-gray-200 space-y-6">
    
    <!-- SİPARİŞ ÜRÜNLERİ -->
    <div>
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-3 flex items-center gap-2">
            <span>SİPARİŞ ÜRÜNLERİ</span>
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] tracking-wider border-b border-gray-800/80">
                        <th scope="col" class="py-2.5 px-3 font-bold">ÜRÜN</th>
                        <th scope="col" class="py-2.5 px-3 font-bold text-center">RENK / NUMARA</th>
                        <th scope="col" class="py-2.5 px-3 font-bold text-center">ADET</th>
                        <th scope="col" class="py-2.5 px-3 font-bold text-right">BİRİM FİYAT</th>
                        <th scope="col" class="py-2.5 px-3 font-bold text-right">TOPLAM</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/40">
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

                            // Renk / Numara biçimlendirmesi (örneğin: Siyah / 43 veya Lila / 33)
                            $variantText = $item->variant_info;
                            if (!$variantText && $variant) {
                                $vColor = is_array($variant->color) ? implode(', ', $variant->color) : $variant->color;
                                $vSize = $variant->size;
                                if ($vColor && $vSize) {
                                    $variantText = "{$vColor} / {$vSize}";
                                } elseif ($vSize) {
                                    $variantText = "Beden: {$vSize}";
                                } elseif ($vColor) {
                                    $variantText = $vColor;
                                }
                            }
                            if (!$variantText) {
                                $variantText = 'Standart';
                            }
                        @endphp
                        <tr class="hover:bg-gray-800/20 transition-colors">
                            <!-- Ürün Görseli ve Adı -->
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-900 border border-gray-800 shrink-0" style="width: 40px; height: 40px;">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-white text-xs leading-snug">
                                            {{ $item->product_name }}
                                        </span>
                                        @if($product && $product->sku)
                                            <span class="text-[10px] font-mono text-gray-500">SKU: {{ $product->sku }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Renk / Numara -->
                            <td class="py-3 px-3 text-center text-gray-300 font-medium">
                                {{ $variantText }}
                            </td>

                            <!-- Adet -->
                            <td class="py-3 px-3 text-center text-gray-300 font-medium">
                                {{ $item->quantity }}
                            </td>

                            <!-- Birim Fiyat -->
                            <td class="py-3 px-3 text-right font-medium text-gray-300">
                                ₺{{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>

                            <!-- Toplam -->
                            <td class="py-3 px-3 text-right font-bold text-white">
                                ₺{{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500 text-xs">
                                Sipariş ürünü bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- KARGO ADRESİ & ÖDEME BİLGİSİ (Referans Tasarım Düzeni) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
        
        <!-- KARGO ADRESİ -->
        <div class="space-y-1 text-xs">
            <h5 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">KARGO ADRESİ</h5>
            <div class="font-bold text-white text-sm">{{ $order->customer_name ?: 'Misafir Müşteri' }}</div>
            <div class="text-gray-300 leading-relaxed">{{ $order->shipping_address ?: 'Adres girilmemiş' }}</div>
            @if($order->shipping_district || $order->shipping_city)
                <div class="text-gray-300 font-semibold">
                    {{ implode(', ', array_filter([$order->shipping_district, $order->shipping_city])) }}
                </div>
            @endif
            @if($order->customer_phone)
                <div class="text-gray-400 pt-1 font-mono">{{ $order->customer_phone }}</div>
            @endif
            @if($order->customer_note)
                <div class="text-amber-400/90 italic pt-1 text-[11px]">
                    Not: {{ $order->customer_note }}
                </div>
            @endif
        </div>

        <!-- ÖDEME BİLGİSİ -->
        <div class="space-y-1 text-xs">
            <h5 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">ÖDEME BİLGİSİ</h5>
            <div class="font-bold text-white text-sm">
                @switch($order->payment_method)
                    @case('credit_card') Kredi Kartı @break
                    @case('wire_transfer') Havale / EFT @break
                    @case('cash_on_delivery') Kapıda Ödeme @break
                    @default {{ $order->payment_method ?: 'Kredi Kartı' }}
                @endswitch
            </div>
            
            @if($order->payment_method === 'credit_card' || !$order->payment_method)
                <div class="text-gray-400 font-mono text-[11px]">**** **** **** 4521</div>
            @endif

            <div class="text-gray-400 pt-1">
                Sipariş Tarihi: {{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}
            </div>

            <div class="pt-2 text-xs">
                <span class="text-gray-400">Toplam Tutar: </span>
                <span class="font-bold text-white">₺{{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

</div>
@endif
