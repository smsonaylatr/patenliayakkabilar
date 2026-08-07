@php
    $order = $getRecord();
@endphp

@if($order)
<style>
/* Siparişler tablosunda akordiyon açılsa bile sağdaki ikonların en üstte sabit kalması */
.fi-ta-record-actions,
.fi-ta-actions,
td.fi-ta-actions-cell {
    align-self: flex-start !important;
    vertical-align: top !important;
    padding-top: 14px !important;
}

.order-detail-panel {
    padding: 20px 24px;
    background: rgba(10, 12, 35, 0.65);
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-align: left;
    color: #fff;
    font-family: inherit;
    width: 100% !important;
    max-width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    box-sizing: border-box !important;
}
.detail-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.detail-title {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255, 255, 255, 0.35);
}
.detail-addr {
    display: flex;
    flex-direction: column;
    gap: 3px;
    font-size: 0.82rem;
}
.detail-meta {
    display: flex;
    gap: 60px;
}
.inner-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 3px;
    font-size: 0.82rem;
}
.inner-table th {
    padding: 6px 12px;
    text-align: left;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(255, 255, 255, 0.25);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.inner-table td {
    padding: 11px 12px !important;
    border-bottom: none !important;
    vertical-align: middle;
    background: rgba(255, 255, 255, 0.02);
}
.inner-table td:first-child {
    border-radius: 12px 0 0 12px;
    padding-right: 16px !important;
    width: 100px;
    vertical-align: middle;
}
.inner-table td:last-child {
    border-radius: 0 12px 12px 0;
}
.inner-thumb {
    width: 90px;
    height: 90px;
    border-radius: 14px;
    background: #c2f542;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
    position: relative;
    padding: 6px;
    box-sizing: border-box;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    transition: all 0.2s ease;
}
.inner-thumb img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 8px;
    transition: transform 0.2s ease;
}
.inner-thumb:hover {
    transform: scale(1.04);
    box-shadow: 0 8px 20px rgba(194, 245, 66, 0.45);
}
.td-bold {
    font-weight: 700;
    color: #ffffff;
}
.td-muted {
    color: rgba(255, 255, 255, 0.45);
    font-size: 0.8rem;
}
</style>

<div class="order-detail-panel">
  <!-- Products -->
  <div class="detail-section">
    <div class="detail-title">Sipariş Ürünleri</div>
    <table class="inner-table">
      <thead>
        <tr>
          <th style="width:44px"></th>
          <th>Ürün</th>
          <th>Renk / Numara</th>
          <th>Adet</th>
          <th>Sipariş No</th>
          <th>Toplam</th>
        </tr>
      </thead>
      <tbody>
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
            <tr>
              <td style="width:44px">
                <div class="inner-thumb">
                  <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" />
                </div>
              </td>
              <td class="td-bold">{{ $item->product_name }}</td>
              <td class="td-muted">{{ $variantText }}</td>
              <td class="td-muted">{{ $item->quantity }}</td>
              <td class="td-bold" style="color: #60a5fa;">#{{ $order->order_number }}</td>
              <td class="td-bold">₺{{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="td-muted" style="text-align:center; padding: 15px !important;">
                    Sipariş ürünü bulunamadı.
                </td>
            </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Detail Meta (Kargo Adresi & Ödeme Bilgisi) -->
  <div class="detail-meta">
    <div class="detail-section" style="flex:1">
      <div class="detail-title">Kargo Adresi</div>
      <div class="detail-addr">
        <div class="td-bold">{{ $order->customer_name ?: 'Misafir Müşteri' }}</div>
        <div class="td-muted">{{ $order->shipping_address ?: 'Adres girilmemiş' }}</div>
        @if($order->shipping_district || $order->shipping_city)
            <div class="td-muted">{{ implode(', ', array_filter([$order->shipping_district, $order->shipping_city])) }}</div>
        @endif
        @if($order->customer_phone)
            <div class="td-muted" style="margin-top:2px;">{{ $order->customer_phone }}</div>
        @endif
        @if($order->customer_note)
            <div class="td-muted" style="color: #fbbf24; margin-top:2px;">Not: {{ $order->customer_note }}</div>
        @endif
      </div>
    </div>

    <div class="detail-section" style="flex:1">
      <div class="detail-title">Ödeme Bilgisi</div>
      <div class="detail-addr">
        <div class="td-bold">
            @switch($order->payment_method)
                @case('credit_card') Kredi Kartı @break
                @case('wire_transfer') Havale / EFT @break
                @case('cash_on_delivery') Kapıda Ödeme @break
                @default {{ $order->payment_method ?: 'Kredi Kartı' }}
            @endswitch
        </div>
        @if($order->payment_method === 'credit_card' || !$order->payment_method)
            <div class="td-muted" style="font-family: monospace;">**** **** **** 4521</div>
        @endif
        <div class="td-muted" style="margin-top:2px;">
            Sipariş Tarihi: {{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}
        </div>
        <div class="td-bold" style="margin-top:4px; font-size:0.9rem;">
            Toplam Tutar: ₺{{ number_format($order->grand_total, 0, ',', '.') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endif
