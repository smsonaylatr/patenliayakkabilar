<style>
    /* ─── Görünüm Modu Değiştirici Buton Grubu ─── */
    .av-view-toggle-group {
        display: inline-flex;
        align-items: center;
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 3px;
        gap: 3px;
        flex-shrink: 0;
    }
    .av-view-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1.2;
        user-select: none;
    }
    .av-view-toggle-btn:hover {
        color: #f1f5f9;
        background: rgba(255, 255, 255, 0.05);
    }
    .av-view-toggle-btn.is-active {
        background: #1e293b;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.12);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
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
        padding: 10px 18px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        margin-bottom: 12px;
    }
    .av-card {
        background: #131d2f;
        background: linear-gradient(180deg, #182234 0%, #0f172a 100%);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 12px;
        transition: all 0.15s ease;
    }
    .av-card:hover {
        border-color: rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }
    .av-body {
        display: grid;
        grid-template-columns: 28% 26% 32% 14%;
        gap: 16px;
        padding: 14px 18px;
        align-items: center;
    }
    .av-footer {
        background: rgba(10, 15, 26, 0.95);
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        padding: 10px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .av-strategy-area {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex: 1;
        min-width: 240px;
    }
    .av-actions-area {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        flex-shrink: 0;
        justify-content: flex-end;
    }
    .av-actions-area .fi-btn {
        white-space: nowrap !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 6px !important;
        gap: 5px !important;
    }
    .av-actions-area .fi-btn-label {
        white-space: nowrap !important;
    }
    .av-actions-area .fi-btn svg,
    .av-actions-area .fi-btn .fi-btn-icon {
        width: 14px !important;
        height: 14px !important;
        flex-shrink: 0 !important;
    }
    .av-actions-area .fi-icon-btn {
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }

    /* ─── Izgara (Grid) Görünümü Stilleri ─── */
    .av-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(370px, 1fr));
        gap: 16px;
    }
    @media (max-width: 768px) {
        .av-grid-container {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }
    .av-grid-card {
        background: #131d2f;
        background: linear-gradient(180deg, #182234 0%, #0f172a 100%);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.15s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .av-grid-card:hover {
        border-color: rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }
    .av-grid-header {
        padding: 14px 16px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .av-grid-body {
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
    }
    .av-grid-section-box {
        background: rgba(15, 23, 42, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        padding: 10px 12px;
    }
    .av-grid-footer {
        background: rgba(10, 15, 26, 0.95);
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    /* ─── Grid Görünümü Birincil Aksiyon (Strateji Uygula) ─── */
    .av-grid-primary-action {
        width: 100%;
    }
    .av-grid-primary-action .fi-btn {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        padding: 6.5px 12px !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
        border-radius: 7px !important;
        background: linear-gradient(135deg, #ff5722 0%, #ff3d00 100%) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 2px 8px rgba(255, 78, 0, 0.35) !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }
    .av-grid-primary-action .fi-btn:hover {
        background: linear-gradient(135deg, #ff6e40 0%, #ff5722 100%) !important;
        box-shadow: 0 4px 14px rgba(255, 78, 0, 0.5) !important;
        transform: translateY(-1px);
    }
    .av-grid-primary-action .fi-btn-label {
        font-weight: 700 !important;
        letter-spacing: 0.02em !important;
        white-space: nowrap !important;
    }
    .av-grid-primary-action .fi-btn svg,
    .av-grid-primary-action .fi-btn .fi-btn-icon {
        width: 14px !important;
        height: 14px !important;
        flex-shrink: 0 !important;
    }

    /* ─── Grid Görünümü İkincil Aksiyonlar (Yönlendir, İncele, Sesli İleti, Menü) ─── */
    .av-grid-secondary-actions,
    .av-grid-actions-area {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        width: 100% !important;
        justify-content: space-between !important;
    }
    .av-grid-secondary-actions .fi-btn,
    .av-grid-actions-area .fi-btn {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        padding: 5px 6px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        justify-content: center !important;
        border-radius: 6px !important;
        gap: 4px !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #e2e8f0 !important;
        transition: all 0.15s ease !important;
    }
    .av-grid-secondary-actions .fi-btn:hover,
    .av-grid-actions-area .fi-btn:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
    }
    .av-grid-secondary-actions .fi-btn-label,
    .av-grid-actions-area .fi-btn-label {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .av-grid-secondary-actions .fi-btn svg,
    .av-grid-secondary-actions .fi-btn .fi-btn-icon,
    .av-grid-actions-area .fi-btn svg,
    .av-grid-actions-area .fi-btn .fi-btn-icon {
        width: 13px !important;
        height: 13px !important;
        flex-shrink: 0 !important;
    }
    .av-grid-secondary-actions .fi-action-group,
    .av-grid-secondary-actions .fi-dropdown,
    .av-grid-actions-area .fi-action-group,
    .av-grid-actions-area .fi-dropdown {
        flex: 0 0 auto !important;
    }
    .av-grid-secondary-actions .fi-icon-btn,
    .av-grid-actions-area .fi-icon-btn {
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        flex-shrink: 0 !important;
        color: #94a3b8 !important;
    }
    .av-grid-secondary-actions .fi-icon-btn:hover,
    .av-grid-actions-area .fi-icon-btn:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }

    .av-unblock-btn {
        background: #1e293b;
        color: #f1f5f9;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        padding: 5px 7px;
        font-size: 10.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        flex: 0 0 auto;
        white-space: nowrap;
        transition: all 0.15s ease;
    }
    .av-unblock-btn:hover {
        background: #334155;
        border-color: rgba(255, 255, 255, 0.25);
    }

    @media (max-width: 480px) {
        .av-grid-secondary-actions {
            flex-wrap: wrap !important;
            gap: 6px !important;
        }
        .av-grid-secondary-actions .fi-btn {
            flex: 1 1 calc(50% - 6px) !important;
        }
    }

    @media (max-width: 1024px) {
        .av-table-header {
            display: none !important;
        }
        .av-body {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
            padding: 14px !important;
        }
        .av-footer {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            padding: 12px 14px !important;
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
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse ($records as $record)
                @php
                    // 1. Ziyaretçi Verileri
                    $isOnline = $record->is_currently_online;
                    $diff = $record->last_heartbeat_at ? $record->last_heartbeat_at->diffForHumans(null, true) : 'şimdi';
                    $deviceHeroicon = match ($record->device_type) {
                        'mobile' => 'heroicon-m-device-phone-mobile',
                        'tablet' => 'heroicon-m-device-tablet',
                        default => 'heroicon-m-computer-desktop',
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
                    $intentLabel = match (true) {
                        $score >= 80 => 'Yüksek Niyet (%' . $score . ')',
                        $score >= 60 => 'Tereddütte (%' . $score . ')',
                        $score >= 40 => 'İlgili (%' . $score . ')',
                        default => 'Keşif (%' . $score . ')',
                    };
                    $intentBadgeColor = $score >= 80 ? '#34d399' : ($score >= 60 ? '#ff7849' : '#94a3b8');
                    $intentBadgeBg = $score >= 80 ? 'rgba(16, 185, 129, 0.1)' : ($score >= 60 ? 'rgba(255, 78, 0, 0.1)' : 'rgba(255, 255, 255, 0.05)');
                    $intentBadgeBorder = $score >= 80 ? 'rgba(16, 185, 129, 0.25)' : ($score >= 60 ? 'rgba(255, 78, 0, 0.25)' : 'rgba(255, 255, 255, 0.08)');

                    // 4. Strateji
                    $strategy = $record->recommended_strategy;
                    if (is_string($strategy)) {
                        $strategy = json_decode($strategy, true);
                    }

                    // 5. Kayıt Aksiyonları
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

                <div wire:key="visitor-log-{{ $record->id }}" class="av-card" style="{{ $record->is_blocked ? 'border-color: rgba(239, 68, 68, 0.35);' : '' }}">
                    {{-- ÜST KISIM: 4 Sütunlu Canlı Bilgiler --}}
                    <div class="av-body">
                        
                        {{-- 1. Ziyaretçi & Sinyal --}}
                        <div>
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                {{-- Avatar (Admin Panel Slate Uyumlu) --}}
                                <div style="width: 38px; height: 38px; min-width: 38px; border-radius: 50%; background: {{ $record->is_blocked ? 'rgba(239, 68, 68, 0.15)' : 'rgba(30, 41, 59, 0.9)' }}; border: 1px solid {{ $record->is_blocked ? 'rgba(239, 68, 68, 0.35)' : 'rgba(255, 255, 255, 0.1)' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; color: {{ $record->is_blocked ? '#f87171' : '#f1f5f9' }}; font-size: 14px; flex-shrink: 0;">
                                    @if($record->is_blocked)
                                        <x-filament::icon icon="heroicon-m-no-symbol" class="w-4 h-4 text-rose-400" />
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; flex-wrap: wrap;">
                                        <span style="font-weight: 700; color: #f8fafc; font-size: 13.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;">
                                            {{ $name }}
                                        </span>
                                        {!! $record->stars_html !!}
                                        @if ($record->is_blocked)
                                            <span style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 700;">
                                                ENGELLENDİ
                                            </span>
                                        @elseif ($isMember)
                                            <span style="background: rgba(255, 78, 0, 0.1); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.25); padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 700;">
                                                MÜŞTERİ
                                            </span>
                                        @elseif ($hasCustomName)
                                            <span style="background: rgba(255, 255, 255, 0.05); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08); padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 600;">
                                                MİSAFİR
                                            </span>
                                            <span title="Kalıcı Misafir ID: #{{ $record->guest_id }}" style="color: #64748b; font-family: monospace; font-size: 9.5px;">
                                                #{{ $record->guest_id }}
                                            </span>
                                        @endif
                                    </div>

                                    <div style="margin-bottom: 4px;">
                                        @if ($isOnline)
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.25);">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span> CANLI
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 999px; font-size: 10px; font-weight: 700; background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08);">
                                                AYRILDI ({{ $diff }})
                                            </span>
                                        @endif
                                    </div>

                                    @if($phone || $email)
                                        <div style="font-size: 11px; color: #cbd5e1; font-weight: 500; display: flex; align-items: center; gap: 6px; margin-bottom: 3px; flex-wrap: wrap;">
                                            @if($phone) <span>{{ $phone }}</span> @endif
                                            @if($phone && $email) <span style="color: #475569;">•</span> @endif
                                            @if($email) <span style="color: #94a3b8;">{{ $email }}</span> @endif
                                        </div>
                                    @endif

                                    <div style="font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                                            <x-filament::icon :icon="$deviceHeroicon" class="w-3.5 h-3.5 text-slate-400" />
                                            <span>{{ $record->browser ?? 'Tarayıcı' }}</span>
                                        </span>
                                        <span>•</span>
                                        <span style="font-family: monospace;">{{ $record->ip_address }}</span>
                                        <span>•</span>
                                        <span title="Kalıcı Misafir ID" style="font-family: monospace; font-size: 10px;">ID: #{{ $record->guest_id }}</span>
                                    </div>
                                    <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">
                                        {{ $duration }} ({{ $pageCount }}. sayfa)
                                    </div>

                                    {{-- Geldiği Kaynak & Ziyaret Sıklığı (Slate Rozetler) --}}
                                    <div style="margin-top: 6px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                        <span title="{{ $sourceInfo['detail'] }}" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 5px; font-size: 10px; font-weight: 600; background: rgba(255, 255, 255, 0.05); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.08);">
                                            <span>{{ $sourceInfo['name'] }}</span>
                                        </span>
                                        @if(!empty($record->utm_campaign))
                                            <span title="Kampanya: {{ $record->utm_campaign }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; border-radius: 5px; font-size: 9.5px; font-weight: 500; background: rgba(255, 255, 255, 0.04); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.07);">
                                                {{ $record->utm_campaign }}
                                            </span>
                                        @endif
                                        <span title="{{ $freqSignal['label'] }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; border-radius: 5px; font-size: 9.5px; font-weight: 600; background: rgba(255, 255, 255, 0.04); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.07);">
                                            <span>{{ $freqSignal['badge'] }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Bulunduğu Sayfa & Model --}}
                        <div>
                            @if ($product)
                                @php $price = $product->discount_price ?: $product->price; @endphp
                                <div style="display: flex; align-items: center; gap: 10px; background: rgba(15, 23, 42, 0.55); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 10px 12px;">
                                    @if ($productImage)
                                        <img src="{{ $productImage }}" alt="{{ $title }}" style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.1); flex-shrink: 0;" />
                                    @else
                                        <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <x-filament::icon icon="heroicon-m-photo" class="w-5 h-5 text-slate-500" />
                                        </div>
                                    @endif
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 5px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; background: rgba(255, 78, 0, 0.1); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.25);">
                                                Model
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 700; color: #f1f5f9; font-size: 12px; line-height: 1.35; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#f1f5f9'">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                            <span style="font-weight: 700; color: #f8fafc; font-size: 12px;">{{ number_format($price, 2) }} ₺</span>
                                            <span style="font-size: 9.5px; color: #94a3b8;">İnceliyor</span>
                                        </div>
                                        @if ($recentDetail)
                                            <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $recentDetail }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($isCheckout)
                                <div style="display: flex; align-items: center; gap: 10px; background: rgba(15, 23, 42, 0.55); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 10px 12px;">
                                    <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <x-filament::icon icon="heroicon-m-shopping-bag" class="w-5 h-5 text-slate-400" />
                                    </div>
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 5px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08);">
                                                Ödeme Ekranı
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 700; color: #f1f5f9; font-size: 12px; line-height: 1.35; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#f1f5f9'">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="font-size: 11px; color: #f8fafc; font-weight: 700; margin-top: 2px;">
                                            Sepet Tutarı: {{ number_format($record->cart_total, 2) }} ₺ {{ $record->cart_items_count > 0 ? "({$record->cart_items_count} ürün)" : '' }}
                                        </div>
                                        @if ($recentDetail)
                                            <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $recentDetail }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div style="display: flex; align-items: center; gap: 10px; background: rgba(15, 23, 42, 0.55); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 10px 12px;">
                                    <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
                                        <x-filament::icon icon="heroicon-m-document-text" class="w-5 h-5 text-slate-400" />
                                    </div>
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 5px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08);">
                                                {{ $pageInfo['badge'] ?? 'Sayfa' }}
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 700; color: #f1f5f9; font-size: 12px; line-height: 1.35; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#f1f5f9'">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="font-size: 10.5px; color: #64748b; font-family: monospace; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $record->current_path }}
                                        </div>
                                        @if ($recentDetail)
                                            <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $recentDetail }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- 3. Davranış Teşhisi & Niyet --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b;">
                                    Davranış &amp; Niyet
                                </span>
                                <span style="font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 4px; background: {{ $intentBadgeBg }}; color: {{ $intentBadgeColor }}; border: 1px solid {{ $intentBadgeBorder }};">
                                    {{ $intentLabel }}
                                </span>
                            </div>
                            <div style="background: rgba(255, 255, 255, 0.06); border-radius: 999px; height: 4px; width: 100%; overflow: hidden; margin-bottom: 6px;">
                                <div style="background: #ff4e00; width: {{ min(100, max(5, $score)) }}%; height: 100%; border-radius: 999px; transition: width 0.3s ease;"></div>
                            </div>
                            <div style="font-size: 11px; color: #94a3b8; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.05); padding: 6px 9px; border-radius: 6px; line-height: 1.4;">
                                {{ $insight }}
                            </div>
                        </div>

                        {{-- 4. Sepet --}}
                        <div>
                            @if ($record->cart_items_count > 0)
                                <div>
                                    <div style="font-weight: 700; color: #f8fafc; font-size: 13.5px; display: flex; align-items: center; gap: 6px;">
                                        <x-filament::icon icon="heroicon-m-shopping-cart" class="w-4 h-4 text-slate-400" />
                                        <span>{{ number_format($record->cart_total, 2) }} ₺</span>
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
                            <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0;">
                                Önerilen Strateji:
                            </span>

                            @if ($strategy)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 5px; font-size: 10.5px; font-weight: 700; background: rgba(255, 78, 0, 0.1); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.25); flex-shrink: 0;">
                                    {{ $strategy['title'] ?? 'Strateji' }}
                                </span>

                                @if (!empty($strategy['suggested_coupon']))
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 5px; font-size: 10.5px; font-family: monospace; font-weight: 700; background: rgba(255, 255, 255, 0.05); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.09); flex-shrink: 0;">
                                        {{ $strategy['suggested_coupon'] }}
                                    </span>
                                @endif

                                @if (!empty($strategy['suggested_message']))
                                    <span style="font-size: 11.5px; color: #94a3b8; font-style: italic; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 480px;" title="{{ $strategy['suggested_message'] }}">
                                        "{{ $strategy['suggested_message'] }}"
                                    </span>
                                @endif
                            @else
                                <span style="font-size: 11px; color: #64748b; font-style: italic;">
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
                                    style="background: #1e293b; color: #f1f5f9; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 6px; padding: 5px 12px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease;"
                                    title="Bu kullanıcının engelini kaldır"
                                >
                                    Engeli Kaldır
                                </button>
                            @endif

                            @foreach ($recordActions as $action)
                                {{ $action }}
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding: 40px 20px; text-align: center; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 12px;">
                    @if (($activeTab ?? 'live') === 'recent')
                        <div style="margin-bottom: 8px;">
                            <x-filament::icon icon="heroicon-o-clock" class="w-10 h-10 text-slate-500" style="margin: 0 auto;" />
                        </div>
                        <div style="font-size: 15px; font-weight: 700; color: #f8fafc;">Son Ziyaret Eden Kaydı Bulunmuyor</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 4px; max-width: 420px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Siteden ayrılan geçmiş ziyaretçilerin oturum ve sepet sinyalleri burada listelenir.
                        </div>
                    @else
                        <div style="margin-bottom: 8px;">
                            <x-filament::icon icon="heroicon-o-signal" class="w-10 h-10 text-slate-500" style="margin: 0 auto;" />
                        </div>
                        <div style="font-size: 15px; font-weight: 700; color: #f8fafc;">Şu An Sitede Canlı Ziyaretçi Yok</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 4px; max-width: 420px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Kullanıcılar siteye girdiğinde canlı sinyaller anında burada listelenir. Geçmişte ayrılan ziyaretçileri incelemek için 
                            <button type="button" wire:click="setActiveTab('recent')" style="color: #ff7849; font-weight: 700; text-decoration: underline; background: none; border: none; cursor: pointer; padding: 0;">Son Ziyaret Edenler</button> 
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
                    $deviceHeroicon = match ($record->device_type) {
                        'mobile' => 'heroicon-m-device-phone-mobile',
                        'tablet' => 'heroicon-m-device-tablet',
                        default => 'heroicon-m-computer-desktop',
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
                    $intentLabel = match (true) {
                        $score >= 80 => 'Yüksek Niyet (%' . $score . ')',
                        $score >= 60 => 'Tereddütte (%' . $score . ')',
                        $score >= 40 => 'İlgili (%' . $score . ')',
                        default => 'Keşif (%' . $score . ')',
                    };
                    $intentBadgeColor = $score >= 80 ? '#34d399' : ($score >= 60 ? '#ff7849' : '#94a3b8');
                    $intentBadgeBg = $score >= 80 ? 'rgba(16, 185, 129, 0.1)' : ($score >= 60 ? 'rgba(255, 78, 0, 0.1)' : 'rgba(255, 255, 255, 0.05)');
                    $intentBadgeBorder = $score >= 80 ? 'rgba(16, 185, 129, 0.25)' : ($score >= 60 ? 'rgba(255, 78, 0, 0.25)' : 'rgba(255, 255, 255, 0.08)');

                    // 4. Strateji
                    $strategy = $record->recommended_strategy;
                    if (is_string($strategy)) {
                        $strategy = json_decode($strategy, true);
                    }

                    // 5. Kayıt Aksiyonları
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

                <div wire:key="visitor-grid-{{ $record->id }}" class="av-grid-card" style="{{ $record->is_blocked ? 'border-color: rgba(239, 68, 68, 0.35);' : '' }}">
                    
                    {{-- 1. Kart Başlığı (Avatar, İsim, Durum, Cihaz) --}}
                    <div class="av-grid-header">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px;">
                            {{-- Avatar + İsim + Rozetler --}}
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                <div style="width: 38px; height: 38px; min-width: 38px; border-radius: 50%; background: {{ $record->is_blocked ? 'rgba(239, 68, 68, 0.15)' : 'rgba(30, 41, 59, 0.9)' }}; border: 1px solid {{ $record->is_blocked ? 'rgba(239, 68, 68, 0.35)' : 'rgba(255, 255, 255, 0.1)' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; color: {{ $record->is_blocked ? '#f87171' : '#f1f5f9' }}; font-size: 14px; flex-shrink: 0;">
                                    @if($record->is_blocked)
                                        <x-filament::icon icon="heroicon-m-no-symbol" class="w-4 h-4 text-rose-400" />
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                        <span style="font-weight: 700; color: #f8fafc; font-size: 13.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;">
                                            {{ $name }}
                                        </span>
                                        {!! $record->stars_html !!}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px; flex-wrap: wrap;">
                                        @if ($record->is_blocked)
                                            <span style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 700;">
                                                ENGELLENDİ
                                            </span>
                                        @elseif ($isMember)
                                            <span style="background: rgba(255, 78, 0, 0.1); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.25); padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 700;">
                                                MÜŞTERİ
                                            </span>
                                        @elseif ($hasCustomName)
                                            <span style="background: rgba(255, 255, 255, 0.05); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08); padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 600;">
                                                MİSAFİR
                                            </span>
                                            <span title="Kalıcı Misafir ID: #{{ $record->guest_id }}" style="color: #64748b; font-family: monospace; font-size: 9px;">
                                                #{{ $record->guest_id }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Sağ: Canlı / Ayrıldı Rozeti --}}
                            <div>
                                @if ($isOnline)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 700; background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.25);">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #10b981;"></span> CANLI
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 999px; font-size: 9.5px; font-weight: 700; background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08);">
                                        AYRILDI ({{ $diff }})
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- İletişim & Cihaz & Ziyaret Bilgisi --}}
                        @if($phone || $email)
                            <div style="font-size: 10.5px; color: #cbd5e1; font-weight: 500; display: flex; align-items: center; gap: 5px; margin-bottom: 4px; flex-wrap: wrap;">
                                @if($phone) <span>{{ $phone }}</span> @endif
                                @if($phone && $email) <span style="color: #475569;">•</span> @endif
                                @if($email) <span style="color: #94a3b8;">{{ $email }}</span> @endif
                            </div>
                        @endif

                        <div style="font-size: 10.5px; color: #64748b; display: flex; align-items: center; justify-content: space-between; gap: 6px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <x-filament::icon :icon="$deviceHeroicon" class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ $record->browser ?? 'Tarayıcı' }}</span>
                                <span>•</span>
                                <span style="font-family: monospace;">{{ $record->ip_address }}</span>
                            </div>
                            <div style="font-size: 10px;">
                                {{ $duration }} ({{ $pageCount }}. sayfa)
                            </div>
                        </div>

                        {{-- Kaynak & Frekans Rozetleri (Muted Slate) --}}
                        <div style="margin-top: 6px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                            <span title="{{ $sourceInfo['detail'] }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; border-radius: 5px; font-size: 9.5px; font-weight: 600; background: rgba(255, 255, 255, 0.05); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <span>{{ $sourceInfo['name'] }}</span>
                            </span>
                            @if(!empty($record->utm_campaign))
                                <span title="Kampanya: {{ $record->utm_campaign }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 5px; border-radius: 4px; font-size: 9px; font-weight: 500; background: rgba(255, 255, 255, 0.04); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.07);">
                                    {{ $record->utm_campaign }}
                                </span>
                            @endif
                            <span title="{{ $freqSignal['label'] }}" style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 5px; border-radius: 4px; font-size: 9px; font-weight: 600; background: rgba(255, 255, 255, 0.04); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.07);">
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
                                        <img src="{{ $productImage }}" alt="{{ $title }}" style="width: 40px; height: 40px; min-width: 40px; max-width: 40px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.1); flex-shrink: 0;" />
                                    @else
                                        <div style="width: 40px; height: 40px; min-width: 40px; max-width: 40px; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <x-filament::icon icon="heroicon-m-photo" class="w-5 h-5 text-slate-500" />
                                        </div>
                                    @endif
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 2px; padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; background: rgba(255, 78, 0, 0.1); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.25);">
                                                Model
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 700; color: #f1f5f9; font-size: 12px; line-height: 1.3; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#f1f5f9'">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                            <span style="font-weight: 700; color: #f8fafc; font-size: 11.5px;">{{ number_format($price, 2) }} ₺</span>
                                            <span style="font-size: 9.5px; color: #94a3b8;">İnceliyor</span>
                                        </div>
                                        @if ($recentDetail)
                                            <div style="font-size: 10px; color: #94a3b8; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $recentDetail }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($isCheckout)
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; min-width: 40px; max-width: 40px; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <x-filament::icon icon="heroicon-m-shopping-bag" class="w-5 h-5 text-slate-400" />
                                    </div>
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 2px; padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08);">
                                                Ödeme Ekranı
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 700; color: #f1f5f9; font-size: 12px; line-height: 1.3; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;" onmouseover="this.style.color='#ff7849'" onmouseout="this.style.color='#f1f5f9'">
                                            {{ $title }} ↗
                                        </a>
                                        <div style="font-size: 11px; color: #f8fafc; font-weight: 700; margin-top: 2px;">
                                            Sepet Tutarı: {{ number_format($record->cart_total, 2) }} ₺
                                        </div>
                                        @if ($recentDetail)
                                            <div style="font-size: 10px; color: #94a3b8; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $recentDetail }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 40px; height: 40px; min-width: 40px; max-width: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
                                        <x-filament::icon icon="heroicon-m-document-text" class="w-5 h-5 text-slate-400" />
                                    </div>
                                    <div style="overflow: hidden; flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 2px;">
                                            <span style="display: inline-flex; align-items: center; gap: 2px; padding: 1px 5px; border-radius: 4px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08);">
                                                {{ $pageInfo['badge'] ?? 'Sayfa' }}
                                            </span>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" style="font-weight: 700; color: #f1f5f9; font-size: 12px; line-height: 1.3; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none;">
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
                                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b;">
                                    Davranış &amp; Niyet
                                </span>
                                <span style="font-size: 9.5px; font-weight: 700; padding: 1px 5px; border-radius: 4px; background: {{ $intentBadgeBg }}; color: {{ $intentBadgeColor }}; border: 1px solid {{ $intentBadgeBorder }};">
                                    {{ $intentLabel }}
                                </span>
                            </div>
                            <div style="background: rgba(255, 255, 255, 0.06); border-radius: 999px; height: 4px; width: 100%; overflow: hidden; margin-bottom: 5px;">
                                <div style="background: #ff4e00; width: {{ min(100, max(5, $score)) }}%; height: 100%; border-radius: 999px; transition: width 0.3s ease;"></div>
                            </div>
                            <div style="font-size: 10.5px; color: #94a3b8; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.05); padding: 5px 8px; border-radius: 5px; line-height: 1.35;">
                                {{ $insight }}
                            </div>
                        </div>

                        {{-- Sepet Bilgisi --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 4px; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                            <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b;">Sepet:</span>
                            @if ($record->cart_items_count > 0)
                                <div style="font-weight: 700; color: #f8fafc; font-size: 12px; display: flex; align-items: center; gap: 5px;">
                                    <x-filament::icon icon="heroicon-m-shopping-cart" class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ number_format($record->cart_total, 2) }} ₺</span>
                                    <span style="font-size: 9.5px; color: #94a3b8; font-weight: 500;">({{ $record->cart_items_count }} ürün)</span>
                                </div>
                            @else
                                <span style="font-size: 10.5px; color: #64748b;">Sepet Boş</span>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Kart Altı (Strateji + Aksiyon Butonları) --}}
                    <div class="av-grid-footer">
                        {{-- Strateji Bilgisi --}}
                        <div style="display: flex; flex-direction: column; gap: 3px;">
                            <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                <span style="font-size: 9.5px; font-weight: 700; text-transform: uppercase; color: #64748b;">
                                    Öneri:
                                </span>
                                @if ($strategy)
                                    <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1.5px 6px; border-radius: 4px; font-size: 9.5px; font-weight: 700; background: rgba(255, 78, 0, 0.1); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.25);">
                                        {{ $strategy['title'] ?? 'Strateji' }}
                                    </span>
                                    @if (!empty($strategy['suggested_coupon']))
                                        <span style="display: inline-flex; align-items: center; gap: 3px; padding: 1px 5px; border-radius: 4px; font-size: 9.5px; font-family: monospace; font-weight: 700; background: rgba(255, 255, 255, 0.05); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.08);">
                                            {{ $strategy['suggested_coupon'] }}
                                        </span>
                                    @endif
                                @else
                                    <span style="font-size: 10px; color: #64748b; font-style: italic;">Henüz tetikleyici yok</span>
                                @endif
                            </div>
                            @if (!empty($strategy['suggested_message']))
                                <div style="font-size: 10px; color: #94a3b8; font-style: italic; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $strategy['suggested_message'] }}">
                                    "{{ $strategy['suggested_message'] }}"
                                </div>
                            @endif
                        </div>

                        {{-- Hızlı Butonlar: Birincil Strateji CTA + İkincil Araçlar --}}
                        @php
                            $primaryAction = null;
                            $secondaryActions = [];
                            foreach ($recordActions as $actionItem) {
                                if (method_exists($actionItem, 'getName') && $actionItem->getName() === 'apply_strategy') {
                                    $primaryAction = $actionItem;
                                } else {
                                    $secondaryActions[] = $actionItem;
                                }
                            }
                        @endphp

                        @if ($primaryAction)
                            <div class="av-grid-primary-action">
                                {{ $primaryAction }}
                            </div>
                        @endif

                        <div class="av-grid-secondary-actions">
                            @if ($record->is_blocked)
                                <button 
                                    type="button" 
                                    wire:click="unblockVisitorById({{ $record->id }})" 
                                    class="av-unblock-btn"
                                    title="Bu kullanıcının engelini kaldır"
                                >
                                    Engeli Aç
                                </button>
                            @endif

                            @foreach ($secondaryActions as $actionItem)
                                {{ $actionItem }}
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 40px 20px; text-align: center; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 12px;">
                    @if (($activeTab ?? 'live') === 'recent')
                        <div style="margin-bottom: 8px;">
                            <x-filament::icon icon="heroicon-o-clock" class="w-10 h-10 text-slate-500" style="margin: 0 auto;" />
                        </div>
                        <div style="font-size: 15px; font-weight: 700; color: #f8fafc;">Son Ziyaret Eden Kaydı Bulunmuyor</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 4px; max-width: 420px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Siteden ayrılan geçmiş ziyaretçilerin oturum ve sepet sinyalleri burada listelenir.
                        </div>
                    @else
                        <div style="margin-bottom: 8px;">
                            <x-filament::icon icon="heroicon-o-signal" class="w-10 h-10 text-slate-500" style="margin: 0 auto;" />
                        </div>
                        <div style="font-size: 15px; font-weight: 700; color: #f8fafc;">Şu An Sitede Canlı Ziyaretçi Yok</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 4px; max-width: 420px; margin-left: auto; margin-right: auto; line-height: 1.45;">
                            Kullanıcılar siteye girdiğinde canlı sinyaller anında burada listelenir. Geçmişte ayrılan ziyaretçileri incelemek için 
                            <button type="button" wire:click="setActiveTab('recent')" style="color: #ff7849; font-weight: 700; text-decoration: underline; background: none; border: none; cursor: pointer; padding: 0;">Son Ziyaret Edenler</button> 
                            sekmesine tıklayabilirsiniz.
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    @endif
</div>
