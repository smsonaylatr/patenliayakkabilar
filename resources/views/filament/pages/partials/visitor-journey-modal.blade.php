<div style="font-family: inherit; color: #f8fafc; display: flex; flex-direction: column; gap: 20px; padding-bottom: 24px;">
    {{-- Müşteri Özet Kartı --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; padding: 16px; background: #1e293b; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 14px;">
        <div>
            <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Ziyaretçi Kimliği</div>
            <div style="font-size: 15px; font-weight: 800; color: #ffffff; margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <span>{{ $record->display_name }}</span>
                {!! $record->stars_html !!}
                <span title="Kalıcı Misafir ID" style="font-family: monospace; color: #38bdf8; font-size: 10px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); padding: 1px 6px; border-radius: 4px;">ID: #{{ $record->guest_id }}</span>
            </div>
            @php
                $phone = $record->user?->phone ?? $record->guest_phone;
                $email = $record->user?->email ?? $record->guest_email;
            @endphp
            @if($phone || $email)
                <div style="font-size: 11px; color: #38bdf8; font-weight: 700; margin-top: 3px;">
                    {{ $phone ? '📱 ' . $phone : '' }} {{ $phone && $email ? '• ' : '' }} {{ $email ? '✉️ ' . $email : '' }}
                </div>
            @endif
            @php $modalSource = $record->source_info; @endphp
            <div style="margin-top: 5px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <span title="{{ $modalSource['detail'] }}" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 800; background: {{ $modalSource['bg_color'] }}; color: {{ $modalSource['color'] }}; border: 1px solid {{ $modalSource['border_color'] }};">
                    {{ $modalSource['icon'] }} {{ $modalSource['name'] }}
                </span>
                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 800; background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35);">
                    {{ $record->stars_string }} {{ $record->visit_count ?: 1 }}. Gelişi
                </span>
            </div>
            <div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 4px;">{{ $record->ip_address }}</div>
        </div>
        <div>
            <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Satın Alma Niyeti</div>
            <div style="font-size: 15px; font-weight: 800; color: #10b981; margin-top: 2px;">
                %{{ $record->intent_score }} - {{ strtoupper($record->intent_level) }}
            </div>
            <div style="font-size: 11px; color: #94a3b8;">{{ $record->page_views_count }} sayfa • {{ $record->duration_formatted }}</div>
        </div>
        <div>
            <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Cihaz / Tarayıcı</div>
            <div style="font-size: 13px; font-weight: 700; color: #e2e8f0; margin-top: 2px;">
                {{ ucfirst($record->device_type) }} • {{ $record->browser }}
            </div>
            <div style="font-size: 11px; color: #64748b;">{{ $record->screen_resolution ?? 'Bilinmiyor' }}</div>
        </div>
    </div>

    {{-- Otomatik Davranış Teşhisi --}}
    <div style="padding: 16px; background: rgba(255, 78, 0, 0.1); border: 1px solid rgba(255, 78, 0, 0.3); border-radius: 14px; display: flex; align-items: flex-start; gap: 12px;">
        <span style="font-size: 24px; line-height: 1;">💡</span>
        <div>
            <div style="font-size: 11px; font-weight: 800; color: #ff7849; text-transform: uppercase; letter-spacing: 0.05em;">
                Yapay Zeka & Kural Motoru Teşhisi
            </div>
            <div style="font-size: 13px; color: #ffedd5; margin-top: 4px; line-height: 1.45; font-weight: 500;">
                {{ $record->behavior_insight ?? 'Ziyaretçi genel keşif aşamasında.' }}
            </div>
        </div>
    </div>

    {{-- Sepet İçeriği --}}
    @if(!empty($record->cart_summary))
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #e2e8f0; display: flex; align-items: center; gap: 6px;">
                    <span>🛒 Sepet İçeriği</span>
                    <span style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4); padding: 1px 6px; border-radius: 999px; font-size: 10px; font-weight: 800;">
                        {{ $record->cart_items_count }} Ürün
                    </span>
                </div>
                <div style="font-size: 14px; font-weight: 800; color: #10b981;">
                    Toplam: {{ number_format($record->cart_total, 2) }} ₺
                </div>
            </div>
            <div style="background: #1e293b; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; overflow: hidden;">
                @foreach($record->cart_summary as $item)
                    @php
                        $itemImage = $item['product_image'] ?? null;
                        if (!$itemImage && $record->cart) {
                            $matched = $record->cart->items->first(function ($ci) use ($item) {
                                return ($ci->product?->name === ($item['product_name'] ?? ''))
                                    || ($ci->product_id === ($item['product_id'] ?? null));
                            });
                            $itemImage = $matched?->product?->images?->first()?->image_url ?? $matched?->product?->images?->first()?->raw_image_url;
                        }
                        if (!$itemImage && !empty($item['product_name'])) {
                            $prod = \App\Models\Product::where('name', $item['product_name'])->with('images')->first();
                            $itemImage = $prod?->images?->first()?->image_url ?? $prod?->images?->first()?->raw_image_url;
                        }
                        $productUrl = $item['product_url'] ?? null;
                    @endphp
                    <div style="padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                        <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                            @if ($itemImage)
                                <img src="{{ $itemImage }}" alt="{{ $item['product_name'] ?? '' }}" style="width: 46px; height: 46px; min-width: 46px; max-width: 46px; border-radius: 10px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35); flex-shrink: 0;" />
                            @else
                                <div style="width: 46px; height: 46px; min-width: 46px; max-width: 46px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                    👟
                                </div>
                            @endif
                            <div style="min-width: 0;">
                                <div style="font-weight: 700; color: #ffffff; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    @if($productUrl)
                                        <a href="{{ $productUrl }}" target="_blank" style="color: #ffffff; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#ffffff'">
                                            {{ $item['product_name'] ?? 'Patenli Ayakkabı' }} ↗
                                        </a>
                                    @else
                                        {{ $item['product_name'] ?? 'Patenli Ayakkabı' }}
                                    @endif
                                </div>
                                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                    @if(!empty($item['size']))
                                        @php
                                            $sizeVal = is_array($item['size']) ? implode(', ', $item['size']) : $item['size'];
                                        @endphp
                                        <span style="background: rgba(255, 255, 255, 0.1); padding: 1px 6px; border-radius: 4px; font-family: monospace; color: #f8fafc; margin-right: 4px;">
                                            Beden: {{ $sizeVal }}
                                        </span>
                                    @endif
                                    @if(!empty($item['color']))
                                        @php
                                            $colorVal = is_array($item['color']) ? implode(', ', $item['color']) : $item['color'];
                                        @endphp
                                        <span>Renk: {{ $colorVal }} • </span>
                                    @endif
                                    <span>{{ $item['quantity'] ?? 1 }} Adet</span>
                                </div>
                            </div>
                        </div>
                        <div style="font-size: 13px; font-weight: 800; color: #10b981; flex-shrink: 0;">
                            {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }} ₺
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Kronolojik Gezinme İzi (Customer Journey Trail) --}}
    <div>
        <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #e2e8f0; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
            <span>🐾 Adım Adım Gezinme İzi (Timeline)</span>
        </div>

        @if(!empty($record->journey_trail) && is_array($record->journey_trail))
            <div style="position: relative; padding-left: 20px; display: flex; flex-direction: column; gap: 12px; border-left: 2px solid rgba(255, 255, 255, 0.1); margin-left: 8px;">
                @foreach(array_reverse($record->journey_trail) as $index => $step)
                    @php
                        $stepPath = $step['path'] ?? '/';
                        $stepRawTitle = $step['title'] ?? null;
                        $stepInfo = \App\Models\ActiveVisitor::resolvePageInfo($stepPath, $stepRawTitle);
                        $isCurrent = ($index === 0);
                        $badgeText = $step['badge'] ?? $stepInfo['badge'];
                        $icon = $step['icon'] ?? $stepInfo['icon'];
                        $badgeColor = $step['color'] ?? $stepInfo['color'];
                        $badgeBg = $stepInfo['bg_color'];
                        $badgeBorder = $stepInfo['border_color'];
                        
                        $cleanTitle = \App\Models\ActiveVisitor::cleanTitle($stepRawTitle);
                        $displayTitle = (!empty($cleanTitle) && strcasecmp($cleanTitle, 'Patenli Ayakkabılar') !== 0)
                            ? $cleanTitle
                            : $stepInfo['title'];

                        $stepProduct = $stepInfo['product'] ?? null;
                        $stepImage = $step['image'] ?? $stepInfo['image'] ?? null;
                        if (!$stepImage && $stepProduct) {
                            $stepImage = $stepProduct->images->first()?->image_url ?? $stepProduct->images->first()?->raw_image_url;
                        }
                        if (!$stepImage && ($stepInfo['is_product'] ?? false)) {
                            // Slug veya başlık ile son çare ürün bulma
                            $parts = explode('/', trim(explode('?', $stepPath)[0], '/'));
                            $slug = $parts[1] ?? null;
                            if ($slug) {
                                $foundProduct = \App\Models\Product::where('slug', $slug)
                                    ->orWhere('slug', urldecode($slug))
                                    ->with('images')
                                    ->first();
                                if ($foundProduct) {
                                    $stepProduct = $foundProduct;
                                    $stepImage = $foundProduct->images->first()?->image_url ?? $foundProduct->images->first()?->raw_image_url;
                                }
                            }
                            if (!$stepImage && !empty($displayTitle)) {
                                $foundProduct = \App\Models\Product::where('name', $displayTitle)
                                    ->orWhere('name', 'like', '%' . $displayTitle . '%')
                                    ->with('images')
                                    ->first();
                                if ($foundProduct) {
                                    $stepProduct = $foundProduct;
                                    $stepImage = $foundProduct->images->first()?->image_url ?? $foundProduct->images->first()?->raw_image_url;
                                }
                            }
                        }
                        $stepPrice = $stepInfo['price'] ?? ($stepProduct ? ($stepProduct->discount_price ?: $stepProduct->price) : null);
                        $isProduct = $stepInfo['is_product'] ?? (bool) $stepProduct;
                    @endphp
                    <div style="position: relative;">
                        <span style="position: absolute; left: -27px; top: 16px; width: 12px; height: 12px; border-radius: 50%; background: {{ $isCurrent ? '#ff4e00' : '#475569' }}; box-shadow: 0 0 10px {{ $isCurrent ? '#ff4e00' : 'transparent' }};"></span>
                        <div style="background: #1e293b; padding: 14px 16px; border-radius: 14px; border: 1px solid {{ $isCurrent ? 'rgba(255, 78, 0, 0.4)' : 'rgba(255, 255, 255, 0.08)' }}; {{ $isCurrent ? 'box-shadow: 0 4px 16px rgba(255, 78, 0, 0.1);' : '' }}">
                            
                            {{-- Üst Başlık Satırı: Badge ve Saat --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; letter-spacing: 0.03em; text-transform: uppercase; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                        {{ $icon }} {{ $badgeText }}
                                    </span>
                                    @if($isCurrent)
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: 800; background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4);">
                                            ● ŞU AN BURADA
                                        </span>
                                    @endif
                                </div>
                                <span style="font-size: 11px; font-family: monospace; color: #94a3b8; font-weight: 600;">
                                    {{ $step['time'] ?? '' }}
                                </span>
                            </div>

                            {{-- İçerik Satırı: Görsel + Bilgiler --}}
                            <div style="display: flex; align-items: flex-start; gap: 14px;">
                                {{-- Görsel / İkon Alanı --}}
                                @if ($stepImage)
                                    <a href="{{ $stepPath }}" target="_blank" style="display: block; flex-shrink: 0; position: relative;">
                                        <img src="{{ $stepImage }}" alt="{{ $displayTitle }}" style="width: 58px; height: 58px; min-width: 58px; max-width: 58px; border-radius: 12px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.16); box-shadow: 0 4px 14px rgba(0, 0, 0, 0.45); transition: transform 0.15s ease;" onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'" />
                                    </a>
                                @elseif ($isProduct)
                                    <div style="width: 58px; height: 58px; min-width: 58px; max-width: 58px; border-radius: 12px; background: rgba(255, 78, 0, 0.15); border: 1px solid rgba(255, 78, 0, 0.35); display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);">
                                        👟
                                    </div>
                                @else
                                    <div style="width: 50px; height: 50px; min-width: 50px; max-width: 50px; border-radius: 12px; background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);">
                                        {{ $icon }}
                                    </div>
                                @endif

                                {{-- Metin ve Detaylar --}}
                                <div style="flex: 1; min-width: 0;">
                                    <div style="margin-bottom: 3px;">
                                        <a href="{{ $stepPath }}" target="_blank" style="font-weight: 800; font-size: 13.5px; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; line-height: 1.35; transition: color 0.15s;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#ffffff'">
                                            <span>{{ $displayTitle }}</span>
                                            <span style="font-size: 11px; color: #94a3b8;">↗</span>
                                        </a>
                                    </div>

                                    @if ($stepPrice)
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                            <span style="font-weight: 800; color: #10b981; font-size: 12.5px;">
                                                {{ number_format($stepPrice, 2) }} ₺
                                            </span>
                                            <span style="font-size: 10px; color: #94a3b8; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1px 6px; border-radius: 4px;">
                                                {{ $isCurrent ? 'Şu An İnceliyor' : 'İncelendi' }}
                                            </span>
                                        </div>
                                    @endif

                                    <div style="font-size: 10.5px; color: #64748b; font-family: monospace; word-break: break-all; margin-bottom: 2px;">
                                        {{ $stepPath }}
                                    </div>

                                    @if(!empty($step['interactions']) && is_array($step['interactions']))
                                        <div style="margin-top: 8px; padding: 7px 10px; background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 8px; display: flex; flex-direction: column; gap: 4px;">
                                            <div style="font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 2px; display: flex; align-items: center; justify-content: space-between;">
                                                <span>⚡ mikro hareketler & tıklamalar</span>
                                                <span style="font-size: 9px; font-family: monospace; color: #94a3b8; background: rgba(255,255,255,0.06); padding: 1px 5px; border-radius: 4px;">{{ count($step['interactions']) }} işlem</span>
                                            </div>
                                            @foreach($step['interactions'] as $micro)
                                                <div style="font-size: 11px; color: #94a3b8; display: flex; align-items: baseline; justify-content: space-between; gap: 8px; line-height: 1.4; border-top: 1px dashed rgba(255, 255, 255, 0.05); padding-top: 3px;">
                                                    <span style="color: #cbd5e1; text-transform: lowercase;">
                                                        {{ $micro['icon'] ?? '•' }} {{ mb_strtolower($micro['text'] ?? '', 'UTF-8') }}
                                                    </span>
                                                    @if(!empty($micro['time']))
                                                        <span style="font-size: 9.5px; font-family: monospace; color: #64748b; flex-shrink: 0;">
                                                            {{ $micro['time'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif(!empty($step['detail']))
                                        <div style="margin-top: 6px; display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: lowercase; background: rgba(245, 158, 11, 0.12); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.25);">
                                            🎯 {{ mb_strtolower($step['detail'], 'UTF-8') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="padding: 16px; text-align: center; font-size: 12px; color: #64748b; background: #1e293b; border-radius: 12px;">
                Henüz hareket izi kaydedilmedi.
            </div>
        @endif
    </div>

    {{-- Trafik Kaynağı ve Kampanya Bilgileri --}}
    @php $modalBottomSource = $record->source_info; @endphp
    <div style="padding: 14px 16px; background: #1e293b; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; font-size: 11px; color: #94a3b8; display: flex; flex-direction: column; gap: 8px;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #cbd5e1; letter-spacing: 0.04em;">🌐 Trafik Kaynağı</span>
            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: {{ $modalBottomSource['bg_color'] }}; color: {{ $modalBottomSource['color'] }}; border: 1px solid {{ $modalBottomSource['border_color'] }};">
                <span>{{ $modalBottomSource['icon'] }}</span>
                <span>{{ $modalBottomSource['name'] }}</span>
            </span>
        </div>
        @if($record->referrer)
            <div><strong style="color: #cbd5e1;">Yönlendiren URL:</strong> <span style="font-family: monospace; color: #94a3b8; word-break: break-all;">{{ $record->referrer }}</span></div>
        @endif
        @if($record->utm_source)
            <div><strong style="color: #cbd5e1;">UTM Kampanyası:</strong> <span style="font-family: monospace; color: #38bdf8;">{{ $record->utm_source }} {{ $record->utm_campaign ? '• ' . $record->utm_campaign : '' }}</span></div>
        @endif
        @if(!$record->referrer && !$record->utm_source)
            <div style="color: #64748b;">Doğrudan ziyaret (Doğrudan adres çubuğuna yazarak veya yer imlerinden siteye girdi).</div>
        @endif
    </div>
</div>
