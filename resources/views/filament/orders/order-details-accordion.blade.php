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
    border-radius: 8px 0 0 8px;
    padding-right: 12px !important;
    width: 66px;
    vertical-align: middle;
    transition: width 0.25s ease;
}
.inner-table td:last-child {
    border-radius: 0 8px 8px 0;
}
.inner-thumb {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.06);
    flex-shrink: 0;
    overflow: hidden;
    position: relative;
    cursor: zoom-in;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
.inner-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.inner-thumb:hover {
    width: 150px;
    height: 150px;
    border-radius: 16px;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.8), 0 0 0 2px rgba(255, 255, 255, 0.3);
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
          <th>Birim Fiyat</th>
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
              <td class="td-muted">₺{{ number_format($item->unit_price ?: 0, 0, ',', '.') }}</td>
              <td class="td-bold">₺{{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="td-muted" style="text-align:center; padding: 15px !important;">
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
                @case('credit_card') <span style="color: #a78bfa; font-weight: 600;">Kredi Kartı</span> @break
                @case('wire_transfer') <span style="color: #2dd4bf; font-weight: 600;">Havale / EFT</span> @break
                @case('cash_on_delivery') <span style="color: #fb923c; font-weight: 600;">Kapıda Ödeme</span> @break
                @default <span style="color: #a78bfa; font-weight: 600;">{{ $order->payment_method ?: 'Kredi Kartı' }}</span>
            @endswitch
        </div>
        @if($order->payment_method === 'credit_card' || !$order->payment_method)
            <div class="td-muted" style="font-family: monospace;">**** **** **** 4521</div>
        @endif
        <div class="td-muted" style="margin-top:2px;">
            @php
                $trMonths = [1=>'Ağu', 1=>'Oca', 2=>'Şub', 3=>'Mar', 4=>'Nis', 5=>'May', 6=>'Haz', 7=>'Tem', 8=>'Ağu', 9=>'Eyl', 10=>'Ekm', 11=>'Kas', 12=>'Ara'];
                $dtStr = '-';
                if ($order->created_at) {
                    $mName = $trMonths[(int)$order->created_at->format('n')] ?? '';
                    $dtStr = $order->created_at->format('d ') . $mName . $order->created_at->format(' Y H:i:s');
                }
            @endphp
            Sipariş Tarihi: {{ $dtStr }}
        </div>
        <div class="td-bold" style="margin-top:4px; font-size:0.9rem;">
            Toplam Tutar: ₺{{ number_format($order->grand_total, 0, ',', '.') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endif
