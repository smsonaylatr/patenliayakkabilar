<style>
    /* ─── Görünüm Modu Değiştirici Buton Grubu ─── */
    .av-view-toggle-group {
        display: inline-flex;
        align-items: center;
        background: #0f172a;
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 9px;
        padding: 2.5px;
        gap: 3px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }
    .av-view-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 5.5px;
        padding: 4px 11px;
        border-radius: 7px;
        font-size: 11.5px;
        font-weight: 700;
        color: #94a3b8;
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        line-height: 1.2;
        user-select: none;
    }
    .av-view-toggle-btn:hover {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.06);
    }
    .av-view-toggle-btn.is-active {
        background: #ff4e00;
        background: linear-gradient(135deg, #ff4e00 0%, #ea580c 100%);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(255, 78, 0, 0.4);
        border-color: rgba(255, 120, 73, 0.4);
    }
    .av-view-toggle-btn svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }
    @media (max-width: 640px) {
        .av-view-toggle-label {
            display: none;
        }
        .av-view-toggle-btn {
            padding: 5px 8px;
        }
    }

    /* ─── Liste Görünümü Stilleri ─── */
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

    /* ─── Izgara (Grid) Görünümü Stilleri ─── */
    .av-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(370px, 1fr));
        gap: 18px;
    }
    @media (max-width: 768px) {
        .av-grid-container {
            grid-template-columns: 1fr;
            gap: 14px;
        }
    }
    .av-grid-card {
        background: #111827;
        background: linear-gradient(145deg, #182234 0%, #0d1522 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.35);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .av-grid-card:hover {
        border-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 14px 32px -4px rgba(0, 0, 0, 0.55);
        transform: translateY(-2px);
    }
    .av-grid-header {
        padding: 16px 18px 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(255, 255, 255, 0.015);
    }
    .av-grid-body {
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        flex: 1;
    }
    .av-grid-section-box {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 11px 13px;
    }
    .av-grid-footer {
        background: rgba(10, 15, 26, 0.96);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding: 11px 13px;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }
    .av-grid-actions-area {
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
        flex-wrap: nowrap !important;
        width: 100% !important;
        justify-content: space-between !important;
    }
    .av-grid-actions-area .fi-btn {
        flex: 1 1 0 !important;
        min-width: 0 !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
        padding-top: 6px !important;
        padding-bottom: 6px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
        justify-content: center !important;
        border-radius: 8px !important;
        gap: 2px !important;
    }
    .av-grid-actions-area .fi-btn .fi-btn-label {
        font-size: 10.5px !important;
        letter-spacing: -0.02em !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.2 !important;
    }
    /* Grid kartlarında buton metinlerindeki emojiler yeterli olduğundan SVG ikonları gizleyip butonların tek satıra tam sığmasını sağlıyoruz */
    .av-grid-actions-area .fi-btn svg,
    .av-grid-actions-area .fi-btn .fi-btn-icon {
        display: none !important;
    }
    .av-grid-actions-area .fi-action-group,
    .av-grid-actions-area .fi-dropdown {
        flex: 0 0 auto !important;
    }
    .av-grid-actions-area .fi-icon-btn {
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        padding: 4px !important;
        border-radius: 7px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .av-grid-actions-area .fi-icon-btn svg {
        display: block !important;
        width: 15px !important;
        height: 15px !important;
    }
    @media (max-width: 1400px) {
        .av-grid-actions-area {
            gap: 3px !important;
        }
        .av-grid-actions-area .fi-btn {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .av-grid-actions-area .fi-btn .fi-btn-label {
            font-size: 10px !important;
        }
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

@php
    $viewMode = $viewMode ?? (request()->cookie('av_view_mode', session('av_view_mode', 'list')));
    if (! in_array($viewMode, ['list', 'grid'])) {
        $viewMode = 'list';
    }
@endphp

<div>
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- 1. LİSTE GÖRÜNÜMÜ MODELİ                                                  --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    @if ($viewMode === 'list')
        {{-- Sütun Başlıkları Çubuğu (Geniş Ekranda) --}}
        <div class="av-table-header">
            <div>ZİYARETÇİ &amp; SİNYAL</div>
            <div>{{ ($activeTab ?? 'live') === 'recent' ? 'SON GEZDİĞİ SAYFA & MODEL' : 'BULUNDUĞU SAYFA & MODEL' }}</div>
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
                                <div style="width: 42px; height: 42px; min-width: 42px; max-width: 42px; border-radius: 50%; background: {{ $record->is_blocked ? 'linear-gradient(135deg, #ef4444, #991b1b)' : 'linear-gradient(135deg, #ff4e00, #b45309)' }}; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #ffffff; font-size: 16px; flex-shrink: 0; box-shadow: 0 0 14px {{ $record->is_blocked ? 'rgba(239, 68, 68, 0.4)' : 'rgba(255, 78, 0, 0.4)' }};">
                                    {{ $record->is_blocked ? '⛔' : $initial }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; flex-wrap: wrap;">
                                        <span style="font-weight: 800; color: #f8fafc; font-size: 13.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;">
                                            {{ $name }}
                                        </span>
                                        {!! $record->stars_html !!}
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
                                            <span title="Kalıcı Misafir ID: #{{ $record->guest_id }}" style="background: rgba(255, 255, 255, 0.08); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.15); padding: 1px 5px; border-radius: 4px; font-size: 9.5px; font-family: monospace; font-weight: 700;">
                                                #{{ $record->guest_id }}
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
                                        <span style="color: #64748b;">•</span>
                                        <span title="Kalıcı Misafir ID" style="font-family: monospace; color: #38bdf8; font-size: 10px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); padding: 0.5px 5px; border-radius: 4px;">ID: #{{ $record->guest_id }}</span>
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

                                    {{-- Ziyaret Sıklığı & Sinyal Geçmişi --}}
                                    @php $freqSignal = app(\App\Services\TrafficAnalyticsService::class)->getVisitorFrequencySignal($record); @endphp
                                    <div style="margin-top: 5px;">
                                        <span title="{{ $freqSignal['label'] }}" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 6px; font-size: 9.5px; font-weight: 800; background: {{ $freqSignal['bg'] }}; color: {{ $freqSignal['color'] }}; border: 1px solid {{ $freqSignal['border'] }};">
                                            <span>{{ $freqSignal['icon'] }}</span>
                                            <span>{{ $freqSignal['badge'] }}</span>
                                            <span style="opacity: 0.85; font-weight: 600;">({{ $freqSignal['label'] }})</span>
                                        </span>
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

                    {{-- ALT SATIR: Önerilen Strateji ve Hızlı Butonlar --}}
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
                    @if (($activeTab ?? 'live') === 'recent')
                        <div style="font-size: 36px; margin-bottom: 10px;">⏱️</div>
                        <div style="font-size: 16px; font-weight: 800; color: #f8fafc;">Son Ziyaret Eden Kaydı Bulunmuyor</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 6px; max-width: 450px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Siteden ayrılan geçmiş ziyaretçilerin oturum ve sepet sinyalleri burada listelenir.
                        </div>
                    @else
                        <div style="font-size: 36px; margin-bottom: 10px;">📡</div>
                        <div style="font-size: 16px; font-weight: 800; color: #f8fafc;">Şu An Sitede Canlı Ziyaretçi Yok</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 6px; max-width: 450px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Kullanıcılar siteye girdiğinde canlı sinyaller anında burada listelenir. Geçmişte ayrılan ziyaretçileri incelemek için 
                            <button type="button" wire:click="setActiveTab('recent')" style="color: #38bdf8; font-weight: 800; text-decoration: underline; background: none; border: none; cursor: pointer; padding: 0;">Son Ziyaret Edenler</button> 
                            sekmesine tıklayabilirsiniz.
                        </div>
                    @endif
                </div>
            @endforelse
        </div>

    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    {{-- 2. IZGARA (GRID) GÖRÜNÜMÜ MODELİ                                          --}}
    {{-- ═════════════════════════════════════════════════════════════════════════ --}}
    @else
        <div class="av-grid-container">
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
                    $sourceInfo = $record->source_info;
                    $freqSignal = app(\App\Services\TrafficAnalyticsService::class)->getVisitorFrequencySignal($record);
                @endphp

                <div wire:key="visitor-grid-{{ $record->id }}" class="av-grid-card" style="{{ $record->is_blocked ? 'border-color: rgba(239, 68, 68, 0.45); box-shadow: 0 0 16px rgba(239, 68, 68, 0.2);' : '' }}">
                    
                    {{-- 1. Kart Başlığı (Avatar, İsim, Durum, Cihaz) --}}
                    <div class="av-grid-header">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px;">
                            {{-- Avatar + İsim + Rozetler --}}
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                <div style="width: 40px; height: 40px; min-width: 40px; border-radius: 50%; background: {{ $record->is_blocked ? 'linear-gradient(135deg, #ef4444, #991b1b)' : 'linear-gradient(135deg, #ff4e00, #b45309)' }}; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #ffffff; font-size: 15px; flex-shrink: 0; box-shadow: 0 0 12px {{ $record->is_blocked ? 'rgba(239, 68, 68, 0.4)' : 'rgba(255, 78, 0, 0.35)' }};">
                                    {{ $record->is_blocked ? '⛔' : $initial }}
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                        <span style="font-weight: 800; color: #f8fafc; font-size: 13.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;">
                                            {{ $name }}
                                        </span>
                                        {!! $record->stars_html !!}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px; flex-wrap: wrap;">
                                        @if ($record->is_blocked)
                                            <span style="background: rgba(239, 68, 68, 0.25); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.5); padding: 0.5px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 800;">
                                                🚫 ENGELLENDİ
                                            </span>
                                        @elseif ($isMember)
                                            <span style="background: rgba(16, 185, 129, 0.25); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.5px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 800;">
                                                MÜŞTERİ
                                            </span>
                                        @elseif ($hasCustomName)
                                            <span style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); padding: 0.5px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 800;">
                                                MİSAFİR
                                            </span>
                                            <span title="Kalıcı Misafir ID: #{{ $record->guest_id }}" style="background: rgba(255, 255, 255, 0.08); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.15); padding: 0.5px 4px; border-radius: 3px; font-size: 9px; font-family: monospace; font-weight: 700;">
                                                #{{ $record->guest_id }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Sağ: Canlı / Ayrıldı Rozeti --}}
                            <div>
                                @if ($isOnline)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 800; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.4);">
                                        <span class="live-radar-dot" style="width: 6px; height: 6px;"></span> CANLI
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                                        AYRILDI ({{ $diff }})
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- İletişim & Cihaz & Ziyaret Bilgisi --}}
                        @if($phone || $email)
                            <div style="font-size: 10.5px; color: #38bdf8; font-weight: 700; display: flex; align-items: center; gap: 5px; margin-bottom: 4px; flex-wrap: wrap;">
                                @if($phone) <span>📱 {{ $phone }}</span> @endif
                                @if($phone && $email) <span style="color: #64748b;">•</span> @endif
                                @if($email) <span style="color: #94a3b8;">✉️ {{ $email }}</span> @endif
                            </div>
                        @endif

                        <div style="font-size: 10.5px; color: #94a3b8; display: flex; align-items: center; justify-content: space-between; gap: 6px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <span>{{ $deviceIcon }} {{ $record->browser ?? 'Tarayıcı' }}</span>
                                <span style="color: #64748b;">•</span>
                                <span style="font-family: monospace; color: #64748b;">{{ $record->ip_address }}</span>
                            </div>
                            <div style="color: #64748b; font-size: 10px;">
                                ⏱️ {{ $duration }} ({{ $pageCount }}. sayfa)
                            </div>
                        </div>

                        {{-- Kaynak & Frekans Rozetleri --}}
                        <div style="margin-top: 6px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                            <span title="{{ $sourceInfo['detail'] }}" style="display: inline-flex; align-items: center; gap: 3.5px; padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 800; background: {{ $sourceInfo['bg_color'] }}; color: {{ $sourceInfo['color'] }}; border: 1px solid {{ $sourceInfo['border_color'] }};">
                                <span>{{ $sourceInfo['icon'] }}</span>
                                <span>{{ $sourceInfo['name'] }}</span>
                            </span>
                            @if(!empty($record->utm_campaign))
                                <span title="Kampanya: {{ $record->utm_campaign }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; border-radius: 5px; font-size: 9px; font-weight: 700; background: rgba(255, 255, 255, 0.06); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.1);">
                                    🎯 {{ $record->utm_campaign }}
                                </span>
                            @endif
                            <span title="{{ $freqSignal['label'] }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; border-radius: 6px; font-size: 9px; font-weight: 800; background: {{ $freqSignal['bg'] }}; color: {{ $freqSignal['color'] }}; border: 1px solid {{ $freqSignal['border'] }};">
                                <span>{{ $freqSignal['icon'] }}</span>
                                <span>{{ $freqSignal['badge'] }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- 2. Kart Gövdesi (Bulunduğu Sayfa, Niyet, Sepet) --}}
                    <div class="av-grid-body">
                        
                        {{-- Bulunduğu Sayfa & Model Kutusu --}}
                        <div class="av-grid-section-box">
                            @if ($product)
                                @php $price = $product->discount_price ?: $product->price; @endphp
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if ($productImage)
                                        <img src="{{ $productImage }}" alt="{{ $title }}" style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 10px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.15); flex-shrink: 0; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);" />
                                    @else
                                        <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 10px; background: rgba(255, 78, 0, 0.15); border: 1px solid rgba(255, 78, 0, 0.35); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">👟</div>
                                    @endif
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 2px; padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 800; text-transform: uppercase; background: rgba(255, 78, 0, 0.15); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.35);">
                                                👟 {{ $pageInfo['badge'] ?? 'Model' }}
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 800; color: #f8fafc; font-size: 12px; line-height: 1.3; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#f8fafc'">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                            <span style="font-weight: 800; color: #10b981; font-size: 11.5px;">{{ number_format($price, 2) }} ₺</span>
                                            <span style="font-size: 9.5px; color: #94a3b8; background: rgba(255, 255, 255, 0.06); padding: 0.5px 4px; border-radius: 3px;">İnceliyor</span>
                                        </div>
                                        @if ($recentDetail)
                                            <div style="font-size: 10px; color: #fcd34d; font-weight: 700; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                🎯 {{ $recentDetail }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($isCheckout)
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                        🛒
                                    </div>
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 2px; padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 800; text-transform: uppercase; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.35);">
                                                🛒 {{ $pageInfo['badge'] ?? 'Ödeme Ekranı' }}
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 800; color: #38bdf8; font-size: 12px; line-height: 1.3; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#7dd3fc'" onmouseout="this.style.color='#38bdf8'">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="font-size: 11px; color: #10b981; font-weight: 700; margin-top: 2px;">
                                            Sepet Tutarı: {{ number_format($record->cart_total, 2) }} ₺
                                        </div>
                                        @if ($recentDetail)
                                            <div style="font-size: 10px; color: #fcd34d; font-weight: 700; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }};">
                                        {{ $badgeIcon }}
                                    </div>
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 2px; padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 800; text-transform: uppercase; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                                {{ $badgeIcon }} {{ $pageInfo['badge'] ?? 'Sayfa' }}
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 800; color: #f8fafc; font-size: 12px; line-height: 1.3; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="font-size: 10px; color: #64748b; font-family: monospace; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $record->current_path }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Davranış Teşhisi & Niyet Çubuğu --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 3px;">
                                <span style="font-size: 10px; font-weight: 900; letter-spacing: 0.04em; color: {{ $color }};">
                                    {{ $intentLabel }}
                                </span>
                            </div>
                            <div style="background: rgba(255, 255, 255, 0.08); border-radius: 999px; height: 5px; width: 100%; overflow: hidden; margin-bottom: 6px;">
                                <div style="background: {{ $gradient }}; width: {{ min(100, max(5, $score)) }}%; height: 100%; border-radius: 999px; transition: width 0.5s ease;"></div>
                            </div>
                            <div style="font-size: 10.5px; color: #cbd5e1; background: rgba(0, 0, 0, 0.45); border-left: 3px solid {{ $color }}; padding: 5px 8px; border-radius: 4px; line-height: 1.35;">
                                {{ $insight }}
                            </div>
                        </div>

                        {{-- Sepet Bilgisi --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 4px; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8;">Sepet Durumu:</span>
                            @if ($record->cart_items_count > 0)
                                <div style="font-weight: 800; color: #10b981; font-size: 12.5px; display: flex; align-items: center; gap: 4px;">
                                    🛒 {{ number_format($record->cart_total, 2) }} ₺ <span style="font-size: 9.5px; color: #94a3b8; font-weight: 600;">({{ $record->cart_items_count }} ürün)</span>
                                </div>
                            @else
                                <span style="font-size: 10.5px; color: #64748b;">Sepet Boş</span>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Kart Altı (Strateji + Aksiyon Butonları) --}}
                    <div class="av-grid-footer">
                        {{-- Strateji Bilgisi --}}
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                <span style="font-size: 9.5px; font-weight: 800; text-transform: uppercase; color: #94a3b8;">
                                    <span style="color: #fbbf24;">⚡</span> Öneri:
                                </span>
                                @if ($strategy)
                                    <span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 800; background: rgba(255, 78, 0, 0.16); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.38);">
                                        {{ $strategy['title'] ?? 'Strateji' }}
                                    </span>
                                    @if (!empty($strategy['suggested_coupon']))
                                        <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1.5px 6px; border-radius: 5px; font-size: 9.5px; font-family: monospace; font-weight: 700; background: rgba(16, 185, 129, 0.16); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.35);">
                                            🎟️ {{ $strategy['suggested_coupon'] }}
                                        </span>
                                    @endif
                                @else
                                    <span style="font-size: 10.5px; color: #64748b; font-style: italic;">Henüz tetikleyici yok</span>
                                @endif
                            </div>
                            @if (!empty($strategy['suggested_message']))
                                <div style="font-size: 10.5px; color: #94a3b8; font-style: italic; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $strategy['suggested_message'] }}">
                                    "{{ $strategy['suggested_message'] }}"
                                </div>
                            @endif
                        </div>

                        {{-- Hızlı Butonlar --}}
                        <div class="av-grid-actions-area">
                            @if ($record->is_blocked)
                                <button 
                                    type="button" 
                                    wire:click="unblockVisitorById({{ $record->id }})" 
                                    style="background: #10b981; color: #ffffff; border: none; border-radius: 7px; padding: 5px 8px; font-size: 10.5px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 3px; flex: 0 0 auto; white-space: nowrap;"
                                    title="Bu kullanıcının engelini kaldır"
                                >
                                    <span>✅</span> Engeli Aç
                                </button>
                            @endif

                            @foreach ($recordActions as $action)
                                {{ $action }}
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 40px 20px; text-align: center; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px;">
                    @if (($activeTab ?? 'live') === 'recent')
                        <div style="font-size: 36px; margin-bottom: 10px;">⏱️</div>
                        <div style="font-size: 16px; font-weight: 800; color: #f8fafc;">Son Ziyaret Eden Kaydı Bulunmuyor</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 6px; max-width: 450px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Siteden ayrılan geçmiş ziyaretçilerin oturum ve sepet sinyalleri burada listelenir.
                        </div>
                    @else
                        <div style="font-size: 36px; margin-bottom: 10px;">📡</div>
                        <div style="font-size: 16px; font-weight: 800; color: #f8fafc;">Şu An Sitede Canlı Ziyaretçi Yok</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 6px; max-width: 450px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Kullanıcılar siteye girdiğinde canlı sinyaller anında burada listelenir. Geçmişte ayrılan ziyaretçileri incelemek için 
                            <button type="button" wire:click="setActiveTab('recent')" style="color: #38bdf8; font-weight: 800; text-decoration: underline; background: none; border: none; cursor: pointer; padding: 0;">Son Ziyaret Edenler</button> 
                            sekmesine tıklayabilirsiniz.
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    @endif
</div>
