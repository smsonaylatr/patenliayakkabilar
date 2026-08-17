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
    padding-right: 6px !important;
    width: 55px;
    vertical-align: middle;
    position: relative;
    overflow: visible;
}

.inner-table td:last-child {
    border-radius: 0 8px 8px 0;
}

.inner-thumb {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: #ffffff;
    overflow: hidden;
    flex-shrink: 0;
    transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), height 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-radius 0.25s ease, box-shadow 0.25s ease;
    position: relative;
    z-index: 10;
    cursor: zoom-in;
    border: 1px solid rgba(0, 0, 0, 0.12) !important;
}

.dark .inner-thumb {
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
}

.inner-thumb:hover {
    width: 140px;
    height: 140px;
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.6);
    z-index: 9999 !important;
    border-color: #38bdf8 !important;
}

.inner-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
    transition: border-radius 0.25s ease;
}

.inner-thumb:hover img {
    border-radius: 10px;
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

    .mobile-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }

    .dark .mobile-label {
        color: rgba(255, 255, 255, 0.45);
    }

@media (min-width: 641px) {
    .mobile-label {
        display: none !important;
    }
}

/* Sipariş No Tıklanabilir Stil */
.order-no-clickable {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    user-select: all !important;
    transition: opacity 0.2s ease !important;
    padding: 2px 5px !important;
    border-radius: 4px !important;
    background: transparent !important;
    border: none !important;
}

.order-no-clickable:hover {
    opacity: 0.85 !important;
    background: rgba(59, 130, 246, 0.08) !important;
}

/* SKU Kopyalanabilir Rozet Stilleri */
.sku-copy-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 600;
    cursor: pointer;
    user-select: all;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(249, 115, 22, 0.14);
    border: 1px solid rgba(249, 115, 22, 0.4);
    color: #fdba74;
    max-width: 100%;
    word-break: break-all;
}

.sku-copy-badge:hover {
    background: rgba(249, 115, 22, 0.25);
    border-color: rgba(249, 115, 22, 0.7);
    color: #ffedd5;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(249, 115, 22, 0.2);
}

.sku-prefix {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    opacity: 0.85;
    font-weight: 700;
    color: #f97316;
}

.sku-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: #ffedd5;
}

/* Aydınlık Tema Uyumlu Stiller */
:root:not(.dark) .sku-copy-badge,
html:not(.dark) .sku-copy-badge {
    background: #fff7ed;
    border-color: #fdba74;
    color: #ea580c;
}

:root:not(.dark) .sku-copy-badge:hover,
html:not(.dark) .sku-copy-badge:hover {
    background: #ffedd5;
    border-color: #f97316;
    color: #c2410c;
}

:root:not(.dark) .sku-code,
html:not(.dark) .sku-code {
    color: #9a3412;
}

.sku-icon {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    margin-left: 2px;
}

.copied-text {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    color: #34d399;
    font-size: 0.68rem;
    font-weight: 700;
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

                  // Renk ve Beden bilgilerini hem ilişkiden hem de ürün adından dinamik çözümleyelim
                  $vColor = null;
                  if ($variant && !empty($variant->color)) {
                      $vColor = is_array($variant->color) ? implode(', ', $variant->color) : $variant->color;
                  }

                  if (!$vColor) {
                      $colorsToMatch = ['Pudra', 'Pembe', 'Pink', 'Lila', 'Rainbow', 'Gökkuşağı', 'Blue', 'Mavi', 'Siyah', 'Black', 'Beyaz', 'White', 'Kırmızı', 'Red', 'Yeşil', 'Green', 'Mor', 'Purple', 'Turuncu', 'Orange', 'Sarı', 'Yellow', 'Fuşya', 'Gümüş', 'Altın'];
                      $checkText = ($product ? $product->name : '') . ' ' . $item->product_name . ' ' . ($variant?->sku ?: '');
                      foreach ($colorsToMatch as $c) {
                          if (stripos($checkText, $c) !== false) {
                              $vColor = $c;
                              break;
                          }
                      }
                  }

                  $vSize = $variant?->size;
                  if (!$vSize && !empty($item->variant_info) && preg_match('/(?:Beden:\s*|Numara:\s*)(\d+)/i', $item->variant_info, $m)) {
                      $vSize = $m[1];
                  }

                  if ($vColor && $vSize) {
                      $variantText = "{$vColor} / Beden: {$vSize}";
                  } elseif ($vSize) {
                      $variantText = "Beden: {$vSize}";
                  } elseif ($vColor) {
                      $variantText = $vColor;
                  } else {
                      $variantText = $item->variant_info ?: 'Standart';
                  }

                  $sku = $variant?->sku ?: ($product?->sku ?: '-');
              @endphp
              <tr>
                <td style="width:76px; text-align:center; vertical-align:middle; padding:10px 8px !important;">
                  <div class="inner-thumb">
                    <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" />
                  </div>
                </td>
                <td style="vertical-align:middle;">
                  <div class="td-bold">{{ $item->product_name }}</div>
                  @if($sku && $sku !== '-')
                  <div 
                    x-data="{ copied: false }" 
                    @click.stop="navigator.clipboard.writeText('{{ e($sku) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="sku-copy-badge"
                    title="Tıklayarak SKU'yu Kopyala"
                  >
                    <span class="sku-prefix">SKU:</span>
                    <span class="sku-code">{{ $sku }}</span>
                    <span class="sku-icon">
                      <template x-if="!copied">
                        <svg style="width:13px;height:13px;display:inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                      </template>
                      <template x-if="copied">
                        <span class="copied-text">
                          <svg style="width:13px;height:13px;display:inline-block;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                          Kopyalandı!
                        </span>
                      </template>
                    </span>
                  </div>
                  @endif
                </td>
                <td style="vertical-align: middle;">
                  <div style="display: flex; flex-direction: column; gap: 3px; line-height: 1.3;">
                    @if($vColor)
                      <div style="font-size: 0.82rem; font-weight: 700;">
                        <span style="color: #94a3b8; font-weight: 500;">Renk:</span>
                        <span style="color: #38bdf8;">{{ $vColor }}</span>
                      </div>
                    @endif
                    @if($vSize)
                      <div style="font-size: 0.82rem; font-weight: 700;">
                        <span style="color: #94a3b8; font-weight: 500;">Numara:</span>
                        <span style="color: #f8fafc;">{{ $vSize }}</span>
                      </div>
                    @endif
                    @if(!$vColor && !$vSize)
                      <span class="td-muted">{{ $item->variant_info ?: 'Standart' }}</span>
                    @endif
                  </div>
                </td>
                <td style="vertical-align: middle;">
                  <span class="td-muted">{{ $item->quantity }}</span>
                </td>
                <td style="vertical-align: middle; white-space: nowrap;">
                  <span 
                    x-data="{ copied: false }" 
                    @click.stop="navigator.clipboard.writeText('{{ e($order->order_number) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="order-no-clickable"
                    title="Tıklayarak Sipariş Numarasını Kopyala"
                  >
                    <span class="td-bold" style="color: #3b82f6;">#{{ $order->order_number }}</span>
                    <span style="display: inline-flex; align-items: center; margin-left: 4px; vertical-align: middle;">
                      <template x-if="!copied">
                        <svg style="width:13px; height:13px; display:inline-block; opacity:0.75; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                      </template>
                      <template x-if="copied">
                        <span style="color: #34d399; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 2px;">
                          <svg style="width:13px; height:13px; display:inline-block; color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                          Kopyalandı!
                        </span>
                      </template>
                    </span>
                  </span>
                </td>
                <td style="vertical-align: middle;">
                  <span class="td-muted">₺{{ number_format($item->unit_price ?: 0, 0, ',', '.') }}</span>
                </td>
                <td style="vertical-align: middle;">
                  <span class="td-bold" style="color: #10b981;">₺{{ number_format($item->total_price ?: ($item->quantity * $item->unit_price), 0, ',', '.') }}</span>
                </td>
              </tr>       </td>
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
