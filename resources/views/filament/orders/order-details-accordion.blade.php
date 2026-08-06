@php
    $order = $getRecord();
@endphp

@if($order)
<div class="p-6 bg-[#0b0e17] dark:bg-[#0b0e17] text-gray-200 rounded-xl space-y-6 text-xs font-sans w-full border-t border-b border-gray-800/80">
    
    <!-- SİPARİŞ ÜRÜNLERİ -->
    <div class="space-y-3">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
            SİPARİŞ ÜRÜNLERİ
        </h4>

        <!-- Ürünler Tablo Kapsayıcısı -->
        <div class="space-y-2">
            <!-- TABLO BAŞLIKLARI (CSS Grid) -->
            <div class="grid grid-cols-12 gap-4 px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-800/80">
                <div class="col-span-5">ÜRÜN</div>
                <div class="col-span-3 text-center">RENK / NUMARA</div>
                <div class="col-span-1 text-center">ADET</div>
                <div class="col-span-1 text-right">BİRİM FİYAT</div>
                <div class="col-span-2 text-right">TOPLAM</div>
            </div>

            <!-- ÜRÜN SATIRLARI -->
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
                <div class="grid grid-cols-12 gap-4 px-4 py-3 items-center bg-[#131722] dark:bg-[#131722] rounded-lg border border-gray-800/60 hover:border-gray-700/80 transition-colors">
                    <!-- ÜRÜN (Resim + Ad) -->
                    <div class="col-span-5 flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-md overflow-hidden bg-gray-900 border border-gray-800 shrink-0" style="width: 40px; height: 40px; min-width: 40px;">
                            <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover" style="width: 40px; height: 40px; object-fit: cover;">
                        </div>
                        <span class="font-bold text-white text-xs truncate">
                            {{ $item->product_name }}
                        </span>
                    </div>

                    <!-- RENK / NUMARA -->
                    <div class="col-span-3 text-center text-gray-300 font-medium truncate">
                        {{ $variantText }}
                    </div>

                    <!-- ADET -->
                    <div class="col-span-1 text-center text-gray-300 font-medium">
                        {{ $item->quantity }}
                    </div>

                    <!-- BİRİM FİYAT -->
                    <div class="col-span-1 text-right text-gray-300 font-medium">
                        ₺{{ number_format($item->unit_price, 0, ',', '.') }}
                    </div>

                    <!-- TOPLAM -->
                    <div class="col-span-2 text-right font-bold text-white text-sm">
                        ₺{{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 text-xs">
                    Sipariş ürünü bulunamadı.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ALT BİLGİ KARTLARI (KARGO ADRESİ & ÖDEME BİLGİSİ) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-800/80">
        <!-- KARGO ADRESİ -->
        <div class="space-y-1.5 text-xs">
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
        <div class="space-y-1.5 text-xs">
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
                <span class="font-bold text-white text-sm">₺{{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

</div>
@endif
