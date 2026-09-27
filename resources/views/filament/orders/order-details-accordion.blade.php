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
    overflow-x: hidden !important;
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
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
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
    display: grid !important;
    grid-template-columns: 1fr;
    gap: 20px;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

@media (min-width: 880px) {
    .detail-meta {
        grid-template-columns: 240px 185px minmax(0, 1fr) !important;
        gap: 24px;
        align-items: start;
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
    width: 145px;
    vertical-align: middle;
    position: relative;
    overflow: visible;
}

.inner-table td:last-child {
    border-radius: 0 8px 8px 0;
}

.inner-thumb {
    width: 140px;
    height: 140px;
    border-radius: 12px;
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
    width: 250px;
    height: 250px;
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.65);
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
/* MOBİL RESPONSİVE STİLLERİ (max-width: 640px)             */
/* ======================================================== */
@media (max-width: 640px) {
    .order-detail-panel {
        padding: 10px 8px !important;
        gap: 14px !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 10px !important;
        box-sizing: border-box !important;
    }

    .table-responsive-container {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-x pan-y !important;
        border-radius: 8px !important;
    }

    .inner-table {
        min-width: 620px !important;
    }

    .inner-thumb {
        width: 80px !important;
        height: 80px !important;
    }

    .inner-thumb:hover {
        width: 140px !important;
        height: 140px !important;
    }

    .inner-table td:first-child {
        width: 95px !important;
    }

    .detail-meta {
        flex-direction: column !important;
        gap: 16px !important;
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

/* SKU Stilleri */
.sku-copy-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 3px;
    font-size: 0.76rem;
    font-weight: 500;
    cursor: pointer;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    color: #64748b;
    transition: opacity 0.2s ease;
}

.sku-copy-badge:hover {
    opacity: 0.85;
}

.sku-prefix {
    font-size: 0.72rem;
    font-weight: 600;
    color: #64748b;
}

.dark .sku-prefix {
    color: rgba(255, 255, 255, 0.5);
}

.sku-code {
    font-size: 0.75rem;
    font-weight: 500;
    color: #475569;
}

.dark .sku-code {
    color: rgba(255, 255, 255, 0.7);
}

.sku-icon {
    display: inline-flex;
    align-items: center;
    margin-left: 2px;
    opacity: 0.6;
}

/* Trafik Kaynağı & Pazarlama Kartı */
.traffic-card {
    display: flex;
    flex-direction: column;
    gap: 9px;
    padding: 13px 15px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

.dark .traffic-card {
    background: #141e30;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.traffic-badges-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px;
}

.traffic-source-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 11px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    line-height: 1.25;
}

.traffic-device-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 7px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.dark .traffic-device-pill {
    background: rgba(255, 255, 255, 0.06);
    color: #cbd5e1;
    border-color: rgba(255, 255, 255, 0.12);
}

.traffic-explanation {
    font-size: 0.77rem;
    line-height: 1.45;
    color: #475569;
}

.dark .traffic-explanation {
    color: #94a3b8;
}

.traffic-campaign-box {
    padding: 7px 11px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px dashed #cbd5e1;
    font-size: 0.74rem;
    line-height: 1.45;
}

.dark .traffic-campaign-box {
    background: rgba(15, 23, 42, 0.6);
    border-color: rgba(255, 255, 255, 0.12);
}

/* GCLID Chip */
.traffic-gclid-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(37, 99, 235, 0.07);
    border: 1px solid rgba(37, 99, 235, 0.2);
    border-radius: 7px;
    padding: 3px 9px;
    font-size: 0.73rem;
    max-width: 100%;
    box-sizing: border-box;
}

.dark .traffic-gclid-chip {
    background: rgba(37, 99, 235, 0.14);
    border-color: rgba(59, 130, 246, 0.28);
}

/* GELDIĞI KAYNAK LINKI (PREMIUM URL CARD) */
.traffic-url-card {
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    padding: 8px 11px;
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

.dark .traffic-url-card {
    background: rgba(11, 17, 32, 0.85);
    border-color: rgba(255, 255, 255, 0.09);
}

.traffic-url-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-width: 0;
}

.traffic-url-title {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
}

.dark .traffic-url-title {
    color: #94a3b8;
}

.traffic-url-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
}

.traffic-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 3.5px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.71rem;
    font-weight: 700;
    cursor: pointer;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    transition: all 0.15s ease;
    text-decoration: none;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.dark .traffic-action-btn {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.14);
    color: #f1f5f9;
    box-shadow: none;
}

.traffic-action-btn:hover {
    background: rgba(59, 130, 246, 0.12);
    border-color: rgba(59, 130, 246, 0.4);
    color: #3b82f6;
}

.dark .traffic-action-btn:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.5);
    color: #60a5fa;
}

.traffic-action-btn.is-copied {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
    color: #10b981 !important;
}

.dark .traffic-action-btn.is-copied {
    color: #34d399 !important;
}

.traffic-action-btn.open-link {
    color: #64748b;
}

.dark .traffic-action-btn.open-link {
    color: #cbd5e1;
}

.traffic-url-box {
    display: flex;
    align-items: center;
    gap: 6px;
    width: 100%;
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 9px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.dark .traffic-url-box {
    background: #070b14;
    border-color: rgba(255, 255, 255, 0.1);
}

.traffic-url-box:hover {
    border-color: #38bdf8;
    box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.15);
}

.traffic-url-text {
    flex: 1;
    min-width: 0 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-break: break-all;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.72rem;
    color: #2563eb;
    text-decoration: none;
    line-height: 1.3;
}

.dark .traffic-url-text {
    color: #38bdf8;
}

/* Alt Ağ & Referrer Satırı */
.traffic-network-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    font-size: 0.72rem;
    color: #64748b;
    border-top: 1px solid rgba(226, 232, 240, 0.7);
    padding-top: 7px;
    margin-top: 2px;
    min-width: 0;
}

.dark .traffic-network-row {
    color: #94a3b8;
    border-top-color: rgba(255, 255, 255, 0.08);
}

/* Platform Bazlı Rozet Renkleri (Açık ve Koyu Mod Tam Uyumlu) */
.traffic-pill-direct {
    background: #f8fafc;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.dark .traffic-pill-direct {
    background: rgba(148, 163, 184, 0.18);
    color: #f1f5f9;
    border-color: rgba(148, 163, 184, 0.35);
}

.traffic-pill-instagram {
    background: #fdf2f8;
    color: #be185d;
    border: 1px solid #fbcfe8;
}
.dark .traffic-pill-instagram {
    background: rgba(236, 72, 153, 0.2);
    color: #f472b6;
    border-color: rgba(244, 114, 182, 0.4);
}

.traffic-pill-google-ads {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
.dark .traffic-pill-google-ads {
    background: rgba(59, 130, 246, 0.2);
    color: #93c5fd;
    border-color: rgba(147, 197, 253, 0.4);
}

.traffic-pill-google-organic {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.dark .traffic-pill-google-organic {
    background: rgba(34, 197, 94, 0.2);
    color: #86efac;
    border-color: rgba(134, 239, 172, 0.4);
}

.traffic-pill-whatsapp {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.dark .traffic-pill-whatsapp {
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
    border-color: rgba(110, 231, 183, 0.4);
}

.traffic-pill-meta {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
}
.dark .traffic-pill-meta {
    background: rgba(99, 102, 241, 0.2);
    color: #a5b4fc;
    border-color: rgba(165, 180, 252, 0.4);
}

.traffic-pill-tiktok {
    background: #f8fafc;
    color: #0f172a;
    border: 1px solid #cbd5e1;
}
.dark .traffic-pill-tiktok {
    background: rgba(255, 255, 255, 0.12);
    color: #f8fafc;
    border-color: rgba(255, 255, 255, 0.25);
}

.traffic-pill-admin {
    background: #faf5ff;
    color: #7e22ce;
    border: 1px solid #e9d5ff;
}
.dark .traffic-pill-admin {
    background: rgba(168, 85, 247, 0.2);
    color: #d8b4fe;
    border-color: rgba(216, 180, 254, 0.4);
}

.traffic-pill-sms {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.dark .traffic-pill-sms {
    background: rgba(245, 158, 11, 0.2);
    color: #fcd34d;
    border-color: rgba(252, 211, 77, 0.4);
}

.traffic-pill-email {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #ffedd5;
}
.dark .traffic-pill-email {
    background: rgba(234, 88, 12, 0.2);
    color: #fdba74;
    border-color: rgba(253, 186, 116, 0.4);
}

.traffic-pill-referral {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.dark .traffic-pill-referral {
    background: rgba(148, 163, 184, 0.15);
    color: #cbd5e1;
    border-color: rgba(148, 163, 184, 0.25);
}
</style>

<div 
  x-data="{ copyToast: false, toastMsg: '' }"
  @copy-toast.window="toastMsg = $event.detail; copyToast = true; setTimeout(() => copyToast = false, 2000)"
  class="order-detail-panel"
>
  <!-- Floating Toast Notification -->
  <div 
    x-show="copyToast" 
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
    style="position: fixed; bottom: 24px; right: 24px; z-index: 999999; background: #10b981; color: #ffffff; padding: 10px 18px; border-radius: 10px; font-size: 0.82rem; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); pointer-events: none;"
    x-cloak
  >
    <svg style="width:16px; height:16px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    <span x-text="toastMsg"></span>
  </div>

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
                <td style="width:145px; text-align:center; vertical-align:middle; padding:10px 8px !important;">
                  <div class="inner-thumb">
                    <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" />
                  </div>
                </td>
                <td style="vertical-align:middle;">
                  <div class="td-bold">{{ $item->product_name }}</div>
                  @if($sku && $sku !== '-')
                  <div 
                    x-data="{ copied: false }" 
                    @click.stop="navigator.clipboard.writeText('{{ e($sku) }}'); copied = true; setTimeout(() => copied = false, 1500); $dispatch('copy-toast', 'SKU Kopyalandı!')"
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
                        <svg style="width:13px;height:13px;display:inline-block;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
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
                        <span class="td-bold">{{ $vSize }}</span>
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
                    @click.stop="navigator.clipboard.writeText('{{ e($order->order_number) }}'); copied = true; setTimeout(() => copied = false, 1500); $dispatch('copy-toast', 'Sipariş No Kopyalandı!')"
                    class="order-no-clickable"
                    title="Tıklayarak Sipariş Numarasını Kopyala"
                  >
                    <span class="td-bold" style="color: #3b82f6;">#{{ $order->order_number }}</span>
                    <span style="display: inline-flex; align-items: center; margin-left: 4px; vertical-align: middle;">
                      <template x-if="!copied">
                        <svg style="width:13px; height:13px; display:inline-block; opacity:0.75; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                      </template>
                      <template x-if="copied">
                        <svg style="width:13px; height:13px; display:inline-block; color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
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
            @php
                $rawPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                if (str_starts_with($rawPhone, '0')) {
                    $rawPhone = '90' . substr($rawPhone, 1);
                } elseif (!str_starts_with($rawPhone, '90')) {
                    $rawPhone = '90' . $rawPhone;
                }
                $waMessage = "Merhabalar, " . ($order->customer_name ?: 'Değerli Müşterimiz') . " patenliayakkabilar.com üzerinden oluşturduğunuz " . $order->order_number . " numaralı siparişiniz hakkında destek sağlamak için ulaşıyorum sizlere.";
            @endphp
            <div class="td-muted" style="margin-top:2px; display: flex; align-items: center; gap: 6px;">
              {{ $order->customer_phone }}
              <a 
                href="whatsapp://send?phone={{ $rawPhone }}&text={{ urlencode($waMessage) }}" 
                target="_blank" 
                title="WhatsApp Masaüstü ile mesaj at"
                style="display: inline-flex; align-items: center; justify-content: center; color: #25D366; text-decoration: none; transition: all 0.15s ease; flex-shrink: 0;"
                onmouseover="this.style.transform='scale(1.2)'; this.style.filter='brightness(1.15)';"
                onmouseout="this.style.transform='scale(1)'; this.style.filter='none';"
              >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              </a>
            </div>
        @endif
        @if($order->customer_note)
            <div class="td-muted" style="color: #f59e0b; margin-top:2px;">Not: {{ $order->customer_note }}</div>
        @endif
        @if($order->invoice_type === 'corporate')
            <div style="margin-top: 6px; padding: 6px 8px; background: #fef3c7; border-radius: 6px; border: 1px solid #fde68a;">
              <div style="font-size: 0.65rem; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">🏢 Kurumsal Fatura</div>
              <div class="td-bold" style="font-size: 0.8rem;">{{ $order->company_name }}</div>
              <div class="td-muted" style="font-size: 0.75rem;">VD: {{ $order->tax_office }} — VN: {{ $order->tax_number }}</div>
            </div>
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

    <!-- Sipariş Kaynağı & Pazarlama -->
    <div class="detail-section">
      <div class="detail-title">Sipariş Kaynağı & Cihaz</div>
      @php
          $src = $order->traffic_source ?: 'Doğrudan';
          $srcLower = strtolower($src);
          $device = $order->device_type ?: 'Bilinmiyor';

          // Platform rozet sınıfı, başlık, ikon ve ilk bakışta anlaşılır açıklama
          $pillClass = 'traffic-pill-direct';
          $sourceTitle = 'Doğrudan Giriş';
          $sourceIcon = '⚡';
          $sourceDesc = 'Müşteri site adresini (patenliayakkabilar.com) doğrudan tarayıcısına yazarak veya kayıtlı sekmesinden gelip sipariş verdi.';

          if (str_contains($srcLower, 'google ads')) {
              $pillClass = 'traffic-pill-google-ads';
              $sourceTitle = 'Google Ads Reklamı';
              $sourceIcon = '🎯';
              $sourceDesc = 'Müşteri Google sponsorlu arama veya alışveriş reklamına tıklayarak geldi.';
          } elseif (str_contains($srcLower, 'google')) {
              $pillClass = 'traffic-pill-google-organic';
              $sourceTitle = 'Google Doğal Arama';
              $sourceIcon = '🔍';
              $sourceDesc = 'Müşteri Google arama motorunda arama yaparak doğal (SEO) sonuçlarından ulaştı.';
          } elseif (str_contains($srcLower, 'instagram')) {
              $pillClass = 'traffic-pill-instagram';
              $isPaid = ($order->utm_medium === 'cpc' || str_contains($srcLower, 'ads'));
              $sourceTitle = $isPaid ? 'Instagram Reklamı' : 'Instagram';
              $sourceIcon = '📸';
              $sourceDesc = $isPaid 
                  ? 'Müşteri Instagram sponsorlu reklam kampanyasına (Meta Ads) tıklayarak geldi.' 
                  : 'Müşteri Instagram profil linki (biyografi) veya hikaye üzerinden mağazaya ulaştı.';
          } elseif (str_contains($srcLower, 'facebook') || str_contains($srcLower, 'meta')) {
              $pillClass = 'traffic-pill-meta';
              $isPaid = ($order->utm_medium === 'cpc' || str_contains($srcLower, 'ads'));
              $sourceTitle = $isPaid ? 'Facebook Reklamı' : 'Facebook';
              $sourceIcon = '👥';
              $sourceDesc = $isPaid 
                  ? 'Müşteri Facebook sponsorlu reklam kampanyasına tıklayarak geldi.' 
                  : 'Müşteri Facebook sayfası veya gönderi bağlantısı üzerinden ulaştı.';
          } elseif (str_contains($srcLower, 'tiktok')) {
              $pillClass = 'traffic-pill-tiktok';
              $isPaid = ($order->utm_medium === 'cpc' || str_contains($srcLower, 'ads'));
              $sourceTitle = $isPaid ? 'TikTok Reklamı' : 'TikTok';
              $sourceIcon = '🎵';
              $sourceDesc = $isPaid 
                  ? 'Müşteri TikTok sponsorlu reklam kampanyasına tıklayarak geldi.' 
                  : 'Müşteri TikTok profil bağlantısı veya video linkinden yönlendirildi.';
          } elseif (str_contains($srcLower, 'whatsapp')) {
              $pillClass = 'traffic-pill-whatsapp';
              $sourceTitle = 'WhatsApp';
              $sourceIcon = '💬';
              $sourceDesc = 'Müşteri WhatsApp sohbetinde paylaşılan ürün/sipariş linki üzerinden geldi.';
          } elseif (str_contains($srcLower, 'admin')) {
              $pillClass = 'traffic-pill-admin';
              $sourceTitle = 'Admin Paneli';
              $sourceIcon = '⚙️';
              $sourceDesc = 'Bu sipariş yönetici tarafından admin panelinden manuel oluşturuldu.';
          } elseif (str_contains($srcLower, 'sms')) {
              $pillClass = 'traffic-pill-sms';
              $sourceTitle = 'SMS Kampanyası';
              $sourceIcon = '📱';
              $sourceDesc = 'Müşteri gönderilen SMS mesajındaki kampanya bağlantısına tıklayarak geldi.';
          } elseif (str_contains($srcLower, 'e-posta') || str_contains($srcLower, 'newsletter')) {
              $pillClass = 'traffic-pill-email';
              $sourceTitle = 'E-Posta Bülteni';
              $sourceIcon = '✉️';
              $sourceDesc = 'Müşteri e-posta bültenindeki bağlantıya tıklayarak ulaştı.';
          } elseif (str_contains($srcLower, 'yönlendirme')) {
              $pillClass = 'traffic-pill-referral';
              $sourceTitle = $src;
              $sourceIcon = '🔗';
              $sourceDesc = 'Harici bir web sitesi yönlendirmesi üzerinden mağazaya geldi.';
          }

          $deviceLabel = match($device) {
              'Mobil' => 'Mobil Telefon',
              'Tablet' => 'Tablet Cihaz',
              'Masaüstü' => 'Masaüstü Bilgisayar',
              default => $device
          };
          $deviceIcon = match($device) {
              'Mobil' => '📱',
              'Tablet' => '📟',
              'Masaüstü' => '💻',
              default => '🌐'
          };
      @endphp

      <div class="traffic-card">
        <!-- Rozetler Satırı: Platform Kaynağı ve Cihaz Türü -->
        <div class="traffic-badges-row">
          <div class="traffic-source-pill {{ $pillClass }}">
            <span>{{ $sourceIcon }}</span>
            <span>{{ $sourceTitle }}</span>
          </div>

          <div class="traffic-device-pill">
            <span>{{ $deviceIcon }}</span>
            <span>{{ $deviceLabel }}</span>
          </div>
        </div>

        <!-- İlk Bakışta Anlaşılır Açıklama -->
        <div class="traffic-explanation">
          {{ $sourceDesc }}
        </div>

        <!-- Kampanya & UTM Detayları (Varsa) -->
        @if($order->utm_campaign || $order->utm_source || $order->utm_medium || $order->utm_term)
          <div class="traffic-campaign-box">
            @if($order->utm_campaign)
              <div>
                <strong style="color: #64748b;">🎯 Kampanya:</strong>
                <span class="td-bold" style="color: #3b82f6;">{{ $order->utm_campaign }}</span>
              </div>
            @endif
            @if($order->utm_medium)
              <div style="margin-top: 2px;">
                <strong style="color: #64748b;">📊 Mecra (Medium):</strong>
                <span>{{ $order->utm_medium }}</span>
              </div>
            @endif
            @if($order->utm_source && $order->utm_source !== $order->traffic_source)
              <div style="margin-top: 2px;">
                <strong style="color: #64748b;">📍 UTM Kaynak:</strong>
                <span>{{ $order->utm_source }}</span>
              </div>
            @endif
            @if($order->utm_term)
              <div style="margin-top: 2px;">
                <strong style="color: #64748b;">🔑 Anahtar Kelime:</strong>
                <span>{{ $order->utm_term }}</span>
              </div>
            @endif
          </div>
        @endif

        <!-- GCLID (Google Ads Tıklama Kimliği) -->
        @if($order->gclid)
          <div class="traffic-gclid-chip" x-data="{ gclidCopied: false }">
            <span style="font-weight: 800; color: #2563eb; display: inline-flex; align-items: center; gap: 4px;">
              <span>🔑</span> GCLID:
            </span>
            <span 
              style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.73rem; color: #475569;" 
              title="{{ $order->gclid }}"
            >
              {{ \Illuminate\Support\Str::limit($order->gclid, 26, '...') }}
            </span>
            <button 
              type="button" 
              @click.stop="navigator.clipboard.writeText('{{ e($order->gclid) }}'); gclidCopied = true; setTimeout(() => gclidCopied = false, 1600); $dispatch('copy-toast', 'GCLID Kopyalandı!')"
              style="background: transparent; border: none; cursor: pointer; padding: 2px 4px; display: inline-flex; align-items: center; color: #3b82f6; border-radius: 4px;"
              title="GCLID'yi Kopyala"
            >
              <template x-if="!gclidCopied">
                <svg style="width:12px; height:12px; opacity:0.8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              </template>
              <template x-if="gclidCopied">
                <svg style="width:12px; height:12px; color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </template>
            </button>
          </div>
        @endif

        <!-- Geldiği Kaynak / Giriş Linki -->
        @php
            $sourceUrl = $order->source_url;
        @endphp
        <div class="traffic-url-card" x-data="{ linkCopied: false }">
          <div class="traffic-url-head">
            <span class="traffic-url-title">
              <svg style="width:13px; height:13px; color:#38bdf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
              <span>GELDİĞİ KAYNAK LİNKİ:</span>
            </span>
            <div class="traffic-url-actions">
              <button 
                type="button"
                @click.stop="navigator.clipboard.writeText('{{ e($sourceUrl) }}'); linkCopied = true; setTimeout(() => linkCopied = false, 1800); $dispatch('copy-toast', 'Kaynak Linki Kopyalandı!')"
                class="traffic-action-btn"
                :class="{ 'is-copied': linkCopied }"
                title="Kaynak Linkini Panoya Kopyala"
              >
                <template x-if="!linkCopied">
                  <span style="display:inline-flex; align-items:center; gap:3px;">
                    <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Kopyala</span>
                  </span>
                </template>
                <template x-if="linkCopied">
                  <span style="display:inline-flex; align-items:center; gap:3px;">
                    <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Kopyalandı!</span>
                  </span>
                </template>
              </button>
              <a 
                href="{{ $sourceUrl }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="traffic-action-btn open-link"
                title="Linki Yeni Sekmede Aç"
              >
                <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Aç</span>
              </a>
            </div>
          </div>
          <div 
            class="traffic-url-box"
            @click.stop="navigator.clipboard.writeText('{{ e($sourceUrl) }}'); linkCopied = true; setTimeout(() => linkCopied = false, 1800); $dispatch('copy-toast', 'Kaynak Linki Kopyalandı!')"
            title="Tıklayarak Linki Kopyala: {{ $sourceUrl }}"
          >
            <span class="traffic-url-text">{{ $sourceUrl }}</span>
          </div>
        </div>

        <!-- Ağ & Referrer (IP ve Yönlendiren) -->
        <div class="traffic-network-row">
          @if($order->ip_address)
            <span 
              x-data="{ ipCopied: false }"
              @click.stop="navigator.clipboard.writeText('{{ e($order->ip_address) }}'); ipCopied = true; setTimeout(() => ipCopied = false, 1500); $dispatch('copy-toast', 'IP Kopyalandı!')"
              title="Tıklayarak IP'yi Kopyala"
              style="cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
            >
              🌐 IP: <strong>{{ $order->ip_address }}</strong>
            </span>
          @endif
          @if($order->referrer)
            @php
                $refHost = parse_url($order->referrer, PHP_URL_HOST) ?: $order->referrer;
            @endphp
            <span title="Önceki Sayfa (Referrer)">
              🔗 Yönlendiren: 
              <a href="{{ $order->referrer }}" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline;">
                <strong>{{ \Illuminate\Support\Str::limit($refHost, 28) }}</strong>
              </a>
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endif
