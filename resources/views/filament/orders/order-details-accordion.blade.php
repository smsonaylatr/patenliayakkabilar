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
    padding: 16px 20px;
    background-color: rgba(248, 250, 252, 0.95);
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-align: left;
    color: #0f172a;
    font-family: inherit;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    transition: all 0.2s ease;
}

.dark .order-detail-panel {
    background-color: rgba(15, 23, 42, 0.75);
    border-color: rgba(255, 255, 255, 0.08);
    color: #f8fafc;
}

.detail-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}

.detail-title {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #64748b;
}

.dark .detail-title {
    color: rgba(255, 255, 255, 0.45);
}

.detail-addr {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 0.84rem;
}

.detail-meta {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

@media (min-width: 768px) {
    .detail-meta {
        flex-direction: row;
        gap: 40px;
    }
}

.table-responsive-container {
    width: 100%;
    border-radius: 10px;
}

.inner-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 4px;
    font-size: 0.84rem;
}

.inner-table th {
    padding: 8px 12px;
    text-align: left;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.dark .inner-table th {
    color: rgba(255, 255, 255, 0.35);
    border-bottom-color: rgba(255, 255, 255, 0.08);
}

.inner-table td {
    padding: 10px 12px !important;
    border-bottom: none !important;
    vertical-align: middle;
    background: rgba(0, 0, 0, 0.03);
}

.dark .inner-table td {
    background: rgba(255, 255, 255, 0.03);
}

.inner-table td:first-child {
    border-radius: 8px 0 0 8px;
    padding-right: 12px !important;
    width: 116px;
    vertical-align: middle;
}

.inner-table td:last-child {
    border-radius: 0 8px 8px 0;
}

.inner-thumb {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    background: rgba(0, 0, 0, 0.05);
    flex-shrink: 0;
    overflow: hidden;
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.12) !important;
}

.dark .inner-thumb {
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
}

.inner-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.td-bold {
    font-weight: 700;
    color: #0f172a;
}

.dark .td-bold {
    color: #ffffff;
}

.td-muted {
    color: #64748b;
    font-size: 0.82rem;
}

.dark .td-muted {
    color: rgba(255, 255, 255, 0.55);
}

/* ======================================================== */
/* MOBİL DİKEY TABLO / KART DÜZENİ (max-width: 640px)       */
/* ======================================================== */
@media (max-width: 640px) {
    .order-detail-panel {
        padding: 8px 6px !important;
        gap: 12px !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 10px !important;
        box-sizing: border-box !important;
    }

    .inner-table,
    .inner-table thead,
    .inner-table tbody,
    .inner-table th,
    .inner-table td,
    .inner-table tr {
        display: block !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .inner-table thead {
        display: none !important;
    }

    .inner-table tr {
        margin-bottom: 10px !important;
        background: rgba(0, 0, 0, 0.04) !important;
        border-radius: 10px !important;
        padding: 12px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
    }

    .dark .inner-table tr {
        background: rgba(255, 255, 255, 0.04) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    .inner-thumb {
        width: 70px !important;
        height: 70px !important;
    }

    .inner-table td {
        background: transparent !important;
        padding: 2px 0 !important;
        border-radius: 0 !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        font-size: 0.82rem !important;
    }

    .inner-table td.hidden,
    .inner-table td.sm\:table-cell {
        display: none !important;
    }

    .inner-table td:first-child {
        width: 100% !important;
        justify-content: flex-start !important;
        gap: 12px !important;
        margin-bottom: 4px !important;
    }

    .inner-table td.mobile-item-header {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding-bottom: 6px !important;
        border-bottom: 1px dashed rgba(0, 0, 0, 0.08) !important;
    }

    .dark .inner-table td.mobile-item-header {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }

    .mobile-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }

    .dark .mobile-label {
        color: rgba(255, 255, 255, 0.45);
    }
}

@media (min-width: 641px) {
    .mobile-item-title,
    .mobile-label {
        display: none !important;
    }
}
</style>

<div class="order-detail-panel">
  <!-- Products -->
  <div class="detail-section">
    <div class="detail-title">Sipariş Ürünleri</div>
    <div class="table-responsive-container">
      <table class="inner-table">
        <thead>
          <tr>
            <th style="width:116px"></th>
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
                <td class="mobile-item-header">
                  <div class="inner-thumb">
                    <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" />
                  </div>
                  <div class="mobile-item-title">
                    <div class="td-bold">{{ $item->product_name }}</div>
                    <div class="td-muted" style="font-size:0.78rem;">#{{ $order->order_number }}</div>
                  </div>
                </td>
                <td class="hidden sm:table-cell td-bold">{{ $item->product_name }}</td>
                <td>
                  <span class="mobile-label sm:hidden">Varyant:</span>
                  <span class="td-muted">{{ $variantText }}</span>
                </td>
                <td>
                  <span class="mobile-label sm:hidden">Adet:</span>
                  <span class="td-muted">{{ $item->quantity }}</span>
                </td>
                <td class="hidden sm:table-cell td-bold" style="color: #3b82f6;">#{{ $order->order_number }}</td>
                <td>
                  <span class="mobile-label sm:hidden">Birim Fiyat:</span>
                  <span class="td-muted">₺{{ number_format($item->unit_price ?: 0, 0, ',', '.') }}</span>
                </td>
                <td>
                  <span class="mobile-label sm:hidden">Toplam:</span>
                  <span class="td-bold" style="color: #10b981;">₺{{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 0, ',', '.') }}</span>
                </td>
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
            <div class="td-muted" style="color: #f59e0b; margin-top:2px;">Not: {{ $order->customer_note }}</div>
        @endif
      </div>
    </div>

    <div class="detail-section" style="flex:1" x-data="{ showPayment: false }">
      <button 
        type="button" 
        @click="showPayment = !showPayment" 
        class="detail-title flex items-center justify-between w-full cursor-pointer hover:opacity-80 transition-opacity select-none"
        style="background: transparent; border: none; padding: 0; margin: 0; text-align: left; width: 100%;"
      >
        <span class="flex items-center gap-2">
          <span>Ödeme Bilgisi</span>
          <span style="font-size: 0.7rem; font-weight: normal; text-transform: none; opacity: 0.7;" x-text="showPayment ? '(Gizle)' : '(Göster)'"></span>
        </span>
        <svg 
          class="w-4 h-4 transition-transform duration-200" 
          :class="{ 'rotate-180': showPayment }" 
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
          style="width: 16px; height: 16px;"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </button>

      <div 
        x-show="showPayment" 
        x-collapse 
        x-cloak 
        class="detail-addr mt-2"
        style="margin-top: 8px;"
      >
        <div class="td-bold">
            @switch($order->payment_method)
                @case('credit_card') <span style="color: #8b5cf6; font-weight: 600;">Kredi Kartı</span> @break
                @case('wire_transfer') <span style="color: #0d9488; font-weight: 600;">Havale / EFT</span> @break
                @case('cash_on_delivery') <span style="color: #ea580c; font-weight: 600;">Kapıda Ödeme</span> @break
                @default <span style="color: #8b5cf6; font-weight: 600;">{{ $order->payment_method ?: 'Kredi Kartı' }}</span>
            @endswitch
        </div>
        @if($order->payment_method === 'credit_card' || !$order->payment_method)
            <div class="td-muted" style="font-family: monospace;">**** **** **** 4521</div>
        @endif
        <div class="td-muted" style="margin-top:2px;">
            @php
                $trMonths = [1=>'Oca', 2=>'Şub', 3=>'Mar', 4=>'Nis', 5=>'May', 6=>'Haz', 7=>'Tem', 8=>'Ağu', 9=>'Eyl', 10=>'Eki', 11=>'Kas', 12=>'Ara'];
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
