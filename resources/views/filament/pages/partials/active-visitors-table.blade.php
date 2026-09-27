<style>
    .av-table-header {
        display: grid;
        grid-template-columns: 28% 26% 32% 14%;
        gap: 16px;
        padding: 12px 20px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        background: #0f172a;
        background: linear-gradient(180deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }
    .av-card {
        background: #111827;
        background: linear-gradient(145deg, #182234 0%, #0d1522 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 14px;
        box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.35);
        transition: all 0.2s ease;
    }
    .av-card:hover {
        border-color: rgba(255, 255, 255, 0.18);
        box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.5);
    }
    .av-body {
        display: grid;
        grid-template-columns: 28% 26% 32% 14%;
        gap: 16px;
        padding: 18px 20px;
        align-items: center;
    }
    .av-footer {
        background: rgba(10, 15, 26, 0.96);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .av-strategy-area {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        flex: 1;
        min-width: 260px;
    }
    .av-actions-area {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex-shrink: 0;
        justify-content: flex-end;
    }
    .av-actions-area .fi-btn {
        white-space: nowrap !important;
    }
    @media (max-width: 1024px) {
        .av-table-header {
            display: none !important;
        }
        .av-body {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
            padding: 16px !important;
        }
        .av-footer {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 14px !important;
            padding: 14px 16px !important;
        }
        .av-actions-area {
            justify-content: flex-start !important;
        }
    }
</style>

<div>
    {{-- Sütun Başlıkları Çubuğu (Geniş Ekranda) --}}
    <div class="av-table-header">
        <div>ZİYARETÇİ &amp; SİNYAL</div>
        <div>BULUNDUĞU SAYFA &amp; MODEL</div>
        <div>DAVRANIŞ TEŞHİSİ &amp; NİYET</div>
        <div>SEPET</div>
    </div>

    {{-- Ziyaretçi Kayıtları Listesi --}}
    <div style="display: flex; flex-direction: column; gap: 14px;">
        @forelse ($records as $record)
            @php
                // 1. Ziyaretçi Verileri
                $isOnline = $record->is_currently_online;
                $diff = $record->last_heartbeat_at ? $record->last_heartbeat_at->diffForHumans(null, true) : 'şimdi';
                $deviceIcon = match ($record->device_type) {
                    'mobile' => '📱',
                    'tablet' => '📟',
                    default => '💻',
                };
                $name = $record->user_id && $record->user ? $record->user->name : $record->display_name;
                $isMember = ($record->user_id && $record->user) || $record->is_identified;
                $hasCustomName = !empty($record->guest_name);
                $initial = mb_substr($name, 0, 1);
                $duration = $record->duration_formatted;
                $pageCount = $record->page_views_count ?: 1;
                $phone = $record->user?->phone ?? $record->guest_phone;
                $email = $record->user?->email ?? $record->guest_email;

                // 2. Bulunduğu Sayfa & Model Verileri
                $pageInfo = $record->page_info ?? [];
                $product = $record->current_product;
                $productImage = $record->current_product_image;
                $title = $pageInfo['title'] ?? ($product ? $product->name : ($record->current_title ?: $record->current_path));
                $url = $record->current_url ?: $record->current_path;
                $isCheckout = ($pageInfo['type'] ?? '') === 'checkout' || str_contains($record->current_path, 'checkout');

                $recentDetail = null;
                if (!empty($record->journey_trail)) {
                    foreach (array_reverse($record->journey_trail) as $step) {
                        if (!empty($step['detail'])) {
                            $recentDetail = $step['detail'];
                            break;
                        }
                    }
                }

                // 3. Davranış Teşhisi Verileri
                $score = $record->intent_score ?? 15;
                $insight = $record->behavior_insight ?? 'Sitede genel keşif yapıyor.';
                $color = match (true) {
                    $score >= 80 => '#10b981',
                    $score >= 60 => '#f59e0b',
                    $score >= 40 => '#38bdf8',
                    default => '#94a3b8',
                };
                $gradient = match (true) {
                    $score >= 80 => 'linear-gradient(90deg, #10b981, #059669)',
                    $score >= 60 => 'linear-gradient(90deg, #f59e0b, #d97706)',
                    $score >= 40 => 'linear-gradient(90deg, #38bdf8, #0284c7)',
                    default => 'linear-gradient(90deg, #64748b, #475569)',
                };
                $intentLabel = match (true) {
                    $score >= 80 => '🔥 ÇOK SICAK (%' . $score . ')',
                    $score >= 60 => '⚡ TEREDDÜTTE (%' . $score . ')',
                    $score >= 40 => '👀 İLGİLİ (%' . $score . ')',
                    default => '🔍 KEŞİF (%' . $score . ')',
                };

                // 4. Strateji
                $strategy = $record->recommended_strategy;
                if (is_string($strategy)) {
                    $strategy = json_decode($strategy, true);
                }

                // 5. Kayıt Aksiyonları (Hızlı Butonlar)
                $activeTable = $table ?? (isset($this) && method_exists($this, 'getTable') ? $this->getTable() : null);
                $defaultRecordActions = $activeTable ? $activeTable->getRecordActions() : [];
                $recordActions = array_reduce(
                    $defaultRecordActions,
                    function (array $carry, $action) use ($record): array {
                        $action = $action->getClone();
                        if (! $action instanceof \Filament\Actions\BulkAction) {
                            $action->record($record);
                        }
                        if ($action->isHidden()) {
                            return $carry;
                        }
                        $carry[] = $action;
                        return $carry;
                    },
                    initial: [],
                );
            @endphp

            <div wire:key="visitor-log-{{ $record->id }}" class="av-card" style="{{ $record->is_blocked ? 'border-color: rgba(239, 68, 68, 0.45); box-shadow: 0 0 16px rgba(239, 68, 68, 0.15);' : '' }}">
                {{-- ÜST KISIM: 4 Sütunlu Canlı Bilgiler --}}
                <div class="av-body">
                    
                    {{-- 1. Ziyaretçi & Sinyal --}}
                    <div>
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            {{-- Avatar Dairesi (Kesin Genişlik/Yükseklik) --}}
                            <div style="width: 42px; height: 42px; min-width: 42px; max-width: 42px; border-radius: 50%; background: {{ $record->is_blocked ? 'linear-gradient(135deg, #ef4444, #991b1b)' : 'linear-gradient(135deg, #ff4e00, #b45309)' }}; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #ffffff; font-size: 16px; flex-shrink: 0; box-shadow: 0 0 14px {{ $record->is_blocked ? 'rgba(239, 68, 68, 0.4)' : 'rgba(255, 78, 0, 0.4)' }};">
                                {{ $record->is_blocked ? '⛔' : $initial }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; flex-wrap: wrap;">
                                    <span style="font-weight: 800; color: #f8fafc; font-size: 13.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;">
                                        {{ $name }}
                                    </span>
                                    @if ($record->is_blocked)
                                        <span style="background: rgba(239, 68, 68, 0.25); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.5); padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800;">
                                            🚫 ENGELLENDİ
                                        </span>
                                    @elseif ($isMember)
                                        <span style="background: rgba(16, 185, 129, 0.25); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4); padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800;">
                                            MÜŞTERİ
                                        </span>
                                    @elseif ($hasCustomName)
                                        <span style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800;">
                                            MİSAFİR
                                        </span>
                                    @endif
                                </div>

                                <div style="margin-bottom: 4px;">
                                    @if ($isOnline)
                                        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 800; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4);">
                                            <span class="live-radar-dot" style="width: 7px; height: 7px;"></span> CANLI
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                                            AYRILDI ({{ $diff }})
                                        </span>
                                    @endif
                                </div>

                                @if($phone || $email)
                                    <div style="font-size: 11px; color: #38bdf8; font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 3px; flex-wrap: wrap;">
                                        @if($phone) <span>📱 {{ $phone }}</span> @endif
                                        @if($phone && $email) <span style="color: #64748b;">•</span> @endif
                                        @if($email) <span style="color: #94a3b8;">✉️ {{ $email }}</span> @endif
                                    </div>
                                @endif

                                <div style="font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <span>{{ $deviceIcon }} {{ $record->browser ?? 'Tarayıcı' }}</span>
                                    <span style="color: #64748b;">•</span>
                                    <span style="font-family: monospace; color: #64748b;">{{ $record->ip_address }}</span>
                                </div>
                                <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">
                                    ⏱️ {{ $duration }} ({{ $pageCount }}. sayfa)
                                </div>

                                {{-- Geldiği Kaynak (Traffic Source) --}}
                                @php $sourceInfo = $record->source_info; @endphp
                                <div style="margin-top: 6px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                    <span title="{{ $sourceInfo['detail'] }}" style="display: inline-flex; align-items: center; gap: 4.5px; padding: 2.5px 8px; border-radius: 6px; font-size: 10.5px; font-weight: 800; letter-spacing: 0.02em; background: {{ $sourceInfo['bg_color'] }}; color: {{ $sourceInfo['color'] }}; border: 1px solid {{ $sourceInfo['border_color'] }}; box-shadow: 0 1px 4px rgba(0,0,0,0.2);">
                                        <span>{{ $sourceInfo['icon'] }}</span>
                                        <span>{{ $sourceInfo['name'] }}</span>
                                    </span>
                                    @if(!empty($record->utm_campaign))
                                        <span title="Kampanya: {{ $record->utm_campaign }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; border-radius: 5px; font-size: 9.5px; font-weight: 700; background: rgba(255, 255, 255, 0.06); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.1);">
                                            🎯 {{ $record->utm_campaign }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Bulunduğu Sayfa & Model --}}
                    <div>
                        @if ($product)
                            @php $price = $product->discount_price ?: $product->price; @endphp
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if ($productImage)
                                    <img src="{{ $productImage }}" alt="{{ $title }}" style="width: 48px; height: 48px; min-width: 48px; max-width: 48px; border-radius: 12px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.15); flex-shrink: 0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);" />
                                @else
                                    <div style="width: 48px; height: 48px; min-width: 48px; max-width: 48px; border-radius: 12px; background: rgba(255, 78, 0, 0.15); border: 1px solid rgba(255, 78, 0, 0.35); display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">👟</div>
                                @endif
                                <div style="overflow: hidden; flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                        <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; background: rgba(255, 78, 0, 0.15); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.35);">
                                            👟 {{ $pageInfo['badge'] ?? 'Model' }}
                                        </span>
                                    </div>
                                    <a href="{{ $url }}" target="_blank" style="font-weight: 800; color: #f8fafc; font-size: 12.5px; line-height: 1.35; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#f8fafc'">
                                        {{ $title }} ↗
                                    </a>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 3px;">
                                        <span style="font-weight: 800; color: #10b981; font-size: 12px;">{{ number_format($price, 2) }} ₺</span>
                                        <span style="font-size: 10px; color: #94a3b8; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1px 5px; border-radius: 4px;">İnceliyor</span>
                                    </div>
                                    @if ($recentDetail)
                                        <div style="font-size: 11px; color: #fcd34d; font-weight: 700; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            🎯 {{ $recentDetail }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif ($isCheckout)
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 48px; height: 48px; min-width: 48px; max-width: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);">
                                    🛒
                                </div>
                                <div style="overflow: hidden; flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                        <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.35);">
                                            🛒 {{ $pageInfo['badge'] ?? 'Ödeme Ekranı' }}
                                        </span>
                                    </div>
                                    <a href="{{ $url }}" target="_blank" style="font-weight: 800; color: #38bdf8; font-size: 12.5px; line-height: 1.35; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#7dd3fc'" onmouseout="this.style.color='#38bdf8'">
                                        {{ $title }} ↗
                                    </a>
                                    <div style="font-size: 11px; color: #10b981; font-weight: 700; margin-top: 3px;">
                                        Sepet Tutarı: {{ number_format($record->cart_total, 2) }} ₺ {{ $record->cart_items_count > 0 ? "({$record->cart_items_count} ürün)" : '' }}
                                    </div>
                                    @if ($recentDetail)
                                        <div style="font-size: 11px; color: #fcd34d; font-weight: 700; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            🎯 {{ $recentDetail }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            @php
                                $badgeColor = $pageInfo['color'] ?? '#94a3b8';
                                $badgeBg = $pageInfo['bg_color'] ?? 'rgba(255, 255, 255, 0.05)';
                                $badgeBorder = $pageInfo['border_color'] ?? 'rgba(255, 255, 255, 0.1)';
                                $badgeIcon = $pageInfo['icon'] ?? '📄';
                            @endphp
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 48px; height: 48px; min-width: 48px; max-width: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);">
                                    {{ $badgeIcon }}
                                </div>
                                <div style="overflow: hidden; flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                                        <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                            {{ $badgeIcon }} {{ $pageInfo['badge'] ?? 'Sayfa' }}
                                        </span>
                                    </div>
                                    <a href="{{ $url }}" target="_blank" style="font-weight: 800; color: #f8fafc; font-size: 12.5px; line-height: 1.35; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#cbd5e1'" onmouseout="this.style.color='#f8fafc'">
                                        {{ $title }} ↗
                                    </a>
                                    <div style="font-size: 10.5px; color: #64748b; font-family: monospace; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $record->current_path }}
                                    </div>
                                    @if ($recentDetail)
                                        <div style="font-size: 11px; color: #fcd34d; font-weight: 700; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            🎯 {{ $recentDetail }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 3. Davranış Teşhisi & Niyet --}}
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 10.5px; font-weight: 900; letter-spacing: 0.05em; color: {{ $color }};">
                                {{ $intentLabel }}
                            </span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.08); border-radius: 999px; height: 6px; width: 100%; overflow: hidden; margin-bottom: 6px;">
                            <div style="background: {{ $gradient }}; width: {{ min(100, max(5, $score)) }}%; height: 100%; border-radius: 999px; transition: width 0.5s ease;"></div>
                        </div>
                        <div style="font-size: 11px; color: #cbd5e1; background: rgba(0, 0, 0, 0.45); border-left: 3px solid {{ $color }}; padding: 6px 10px; border-radius: 6px; line-height: 1.4;">
                            {{ $insight }}
                        </div>
                    </div>

                    {{-- 4. Sepet --}}
                    <div>
                        @if ($record->cart_items_count > 0)
                            <div>
                                <div style="font-weight: 800; color: #10b981; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                                    🛒 {{ number_format($record->cart_total, 2) }} ₺
                                </div>
                                <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px;">
                                    {{ $record->cart_items_count }} ürün sepette
                                </div>
                            </div>
                        @else
                            <div style="font-size: 11.5px; color: #64748b; font-weight: 500;">
                                Sepet Boş
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ALT SATIR: Önerilen Strateji ve Hızlı Butonlar (Satır Olarak Altında) --}}
                <div class="av-footer visitor-log-footer">
                    
                    {{-- SOL: Önerilen Strateji Bilgisi --}}
                    <div class="av-strategy-area">
                        <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0;">
                            <span style="color: #fbbf24;">⚡</span> Önerilen Strateji:
                        </span>

                        @if ($strategy)
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; background: rgba(255, 78, 0, 0.16); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.38); flex-shrink: 0; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);">
                                ⚡ {{ $strategy['title'] ?? 'Strateji' }}
                            </span>

                            @if (!empty($strategy['suggested_coupon']))
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-family: monospace; font-weight: 700; background: rgba(16, 185, 129, 0.16); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.35); flex-shrink: 0;">
                                    🎟️ {{ $strategy['suggested_coupon'] }}
                                </span>
                            @endif

                            @if (!empty($strategy['suggested_message']))
                                <span style="font-size: 12px; color: #cbd5e1; font-style: italic; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 480px;" title="{{ $strategy['suggested_message'] }}">
                                    "{{ $strategy['suggested_message'] }}"
                                </span>
                            @endif
                        @else
                            <span style="font-size: 11.5px; color: #64748b; font-style: italic;">
                                Sitede gezinmeye devam ediyor, henüz tetikleyici oluşmadı.
                            </span>
                        @endif
                    </div>

                    {{-- SAĞ: Hızlı Butonlar (Aksiyonlar) --}}
                    <div class="av-actions-area">
                        @if ($record->is_blocked)
                            <button 
                                type="button" 
                                wire:click="unblockVisitorById({{ $record->id }})" 
                                style="background: #10b981; color: #ffffff; border: none; border-radius: 8px; padding: 6px 14px; font-size: 11.5px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); transition: all 0.2s ease;"
                                onmouseover="this.style.background='#059669'"
                                onmouseout="this.style.background='#10b981'"
                                title="Bu kullanıcının engelini kaldır"
                            >
                                <span>✅</span> Engeli Kaldır
                            </button>
                        @endif

                        @foreach ($recordActions as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div style="padding: 40px 20px; text-align: center; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px;">
                <div style="font-size: 36px; margin-bottom: 10px;">📡</div>
                <div style="font-size: 16px; font-weight: 800; color: #f8fafc;">Şu An Sitede Canlı Ziyaretçi Yok</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 6px; max-width: 450px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                    Kullanıcılar siteye girdiğinde veya sayfaları gezmeye başladığında canlı radar sinyalleri anlık olarak burada listelenecektir.
                </div>
            </div>
        @endforelse
    </div>
</div>
