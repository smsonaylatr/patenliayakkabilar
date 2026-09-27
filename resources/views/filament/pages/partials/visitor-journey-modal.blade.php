<div style="font-family: inherit; color: #f8fafc; display: flex; flex-direction: column; gap: 20px;">
    {{-- Müşteri Özet Kartı --}}
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; padding: 16px; background: #1e293b; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 14px;">
        <div>
            <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Ziyaretçi Kimliği</div>
            <div style="font-size: 15px; font-weight: 800; color: #ffffff; margin-top: 2px;">{{ $record->display_name }}</div>
            <div style="font-size: 11px; color: #64748b; font-family: monospace;">{{ $record->ip_address }}</div>
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
                    <div style="padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                        <div>
                            <div style="font-weight: 700; color: #ffffff; font-size: 13px;">{{ $item['product_name'] ?? 'Patenli Ayakkabı' }}</div>
                            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                @if(!empty($item['size']))
                                    <span style="background: rgba(255, 255, 255, 0.1); padding: 1px 6px; border-radius: 4px; font-family: monospace; color: #f8fafc; margin-right: 4px;">
                                        Beden: {{ $item['size'] }}
                                    </span>
                                @endif
                                @if(!empty($item['color']))
                                    <span>Renk: {{ $item['color'] }} • </span>
                                @endif
                                <span>{{ $item['quantity'] ?? 1 }} Adet</span>
                            </div>
                        </div>
                        <div style="font-size: 13px; font-weight: 800; color: #38bdf8;">
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
                    @endphp
                    <div style="position: relative;">
                        <span style="position: absolute; left: -27px; top: 12px; width: 12px; height: 12px; border-radius: 50%; background: {{ $isCurrent ? '#ff4e00' : '#475569' }}; box-shadow: 0 0 10px {{ $isCurrent ? '#ff4e00' : 'transparent' }};"></span>
                        <div style="background: #1e293b; padding: 12px 16px; border-radius: 14px; border: 1px solid {{ $isCurrent ? 'rgba(255, 78, 0, 0.4)' : 'rgba(255, 255, 255, 0.08)' }}; {{ $isCurrent ? 'box-shadow: 0 4px 16px rgba(255, 78, 0, 0.08);' : '' }}">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 6px;">
                                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; letter-spacing: 0.03em; text-transform: uppercase; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                        {{ $icon }} {{ $badgeText }}
                                    </span>
                                    @if($isCurrent)
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 999px; font-size: 9px; font-weight: 800; background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4);">
                                            ● ŞU AN BURADA
                                        </span>
                                    @endif
                                </div>
                                <span style="font-size: 11px; font-family: monospace; color: #94a3b8; font-weight: 600;">
                                    {{ $step['time'] ?? '' }}
                                </span>
                            </div>

                            <div style="margin-bottom: 4px;">
                                <a href="{{ $stepPath }}" target="_blank" style="font-weight: 800; font-size: 13.5px; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: color 0.15s;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#ffffff'">
                                    <span>{{ $displayTitle }}</span>
                                    <span style="font-size: 11px; color: #94a3b8;">↗</span>
                                </a>
                            </div>

                            <div style="font-size: 11px; color: #64748b; font-family: monospace; word-break: break-all;">
                                {{ $stepPath }}
                            </div>

                            @if(!empty($step['detail']))
                                <div style="margin-top: 8px; display: inline-flex; align-items: center; gap: 4px; padding: 2px 9px; border-radius: 6px; font-size: 11px; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35);">
                                    🎯 {{ $step['detail'] }}
                                </div>
                            @endif
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

    {{-- Kaynak ve UTM Bilgileri --}}
    @if($record->referrer || $record->utm_source)
        <div style="padding: 12px 16px; background: #1e293b; border-radius: 12px; font-size: 11px; color: #94a3b8; display: flex; flex-direction: column; gap: 4px;">
            @if($record->referrer)
                <div><strong style="color: #cbd5e1;">Geldiği Kaynak:</strong> {{ $record->referrer }}</div>
            @endif
            @if($record->utm_source)
                <div><strong style="color: #cbd5e1;">Kampanya Kaynağı:</strong> {{ $record->utm_source }} ({{ $record->utm_campaign ?? '-' }})</div>
            @endif
        </div>
    @endif
</div>
