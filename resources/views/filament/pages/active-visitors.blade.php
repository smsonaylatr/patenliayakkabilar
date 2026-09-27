<x-filament-panels::page>
    {{-- Canlı Ziyaretçi & Satış Dönüşüm Komuta Merkezi Özel Stilleri --}}
    <style>
        .war-room-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        @media (max-width: 1200px) {
            .war-room-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 768px) {
            .war-room-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }

        .war-card {
            background: #111827;
            background: linear-gradient(145deg, #182234 0%, #0d1522 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 125px;
            transition: all 0.25s ease;
            cursor: pointer;
            user-select: none;
        }
        .war-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.18);
            box-shadow: 0 14px 28px -4px rgba(0, 0, 0, 0.5);
        }
        .war-card.is-active-filter {
            border-color: #ff4e00 !important;
            box-shadow: 0 0 24px rgba(255, 78, 0, 0.45) !important;
            transform: translateY(-3px);
        }
        .war-card-active-pill {
            font-size: 9px;
            font-weight: 800;
            padding: 1.5px 6px;
            border-radius: 4px;
            background: #ff4e00;
            color: #ffffff;
            letter-spacing: 0.04em;
        }
        .war-card-top-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .war-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .war-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
        }
        .war-value {
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.1;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .war-subtext {
            font-size: 11px;
            color: #64748b;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Canlı Nabız Sinyali */
        .live-radar-dot {
            width: 10px;
            height: 10px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px #10b981;
            animation: radar-pulse 1.8s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        }
        @keyframes radar-pulse {
            0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.8); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Komuta Bilgi Çubuğu */
        .command-bar {
            background: #111827;
            background: linear-gradient(90deg, rgba(255, 78, 0, 0.12) 0%, rgba(15, 23, 42, 0.8) 100%);
            border: 1px solid rgba(255, 78, 0, 0.25);
            border-radius: 12px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* İlerleme Çubuğu */
        .intent-bar-bg {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
            height: 6px;
            overflow: hidden;
            width: 100%;
            margin-top: 4px;
        }
        .intent-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.5s ease;
        }

        /* Ziyaretçi Trafik Sinyali & Dönemlik Analiz Kartı */
        .traffic-intel-panel {
            background: #111827;
            background: linear-gradient(145deg, #182234 0%, #0d1522 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }
        .traffic-intel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 16px;
            flex-wrap: wrap;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding-bottom: 14px;
        }
        .traffic-period-btn {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .traffic-period-btn.active {
            background: #ff4e00;
            background: linear-gradient(135deg, #ff4e00, #ea580c);
            color: #ffffff;
            box-shadow: 0 0 14px rgba(255, 78, 0, 0.4);
            border-color: rgba(255, 120, 73, 0.4);
        }
        .traffic-period-btn:not(.active) {
            background: rgba(255, 255, 255, 0.04);
            color: #94a3b8;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .traffic-period-btn:not(.active):hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.16);
        }
        .traffic-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }
        @media (max-width: 1024px) {
            .traffic-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .traffic-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .traffic-kpi-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .traffic-intel-details-grid {
            display: grid;
            grid-template-columns: 28% 22% 50%;
            gap: 16px;
        }
        @media (max-width: 1200px) {
            .traffic-intel-details-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 768px) {
            .traffic-intel-details-grid {
                grid-template-columns: 1fr;
            }
        }
        .traffic-sub-box {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* Ziyaretçi Sinyal Tablosu Hızlı Komuta Kısayolları */
        .visitor-shortcuts-panel {
            background: #111827;
            background: linear-gradient(145deg, #182234 0%, #0d1522 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .visitor-shortcuts-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .visitor-shortcuts-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .visitor-shortcuts-actions .fi-ac {
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ─── FİLAMENT MODAL & DİNAMİK DROPDOWN UZAMA STİLLERİ ─── */
        /* Modal penceresi taban boyutu ve akıcı geçiş animasyonu */
        .fi-modal-window {
            min-height: 540px !important;
            display: flex !important;
            flex-direction: column !important;
            transition: min-height 0.28s cubic-bezier(0.4, 0, 0.2, 1), height 0.28s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .fi-modal-window .fi-modal-content {
            flex: 1 1 auto !important;
            overflow-y: visible !important;
            padding-bottom: 35px !important;
        }

        /* DROPDOWN AÇILDIĞINDA POP-UP'IN UZAMASI (CSS :has & JS fallback) */
        .fi-modal-window:has(.choices.is-open),
        .fi-modal-window:has(.choices.is-flipped),
        .fi-modal-window:has([aria-expanded="true"]),
        .fi-modal-window:has(.fi-dropdown-panel),
        .fi-modal-window:has(.fi-select-input-options-list),
        .fi-modal-window.dropdown-expanded {
            min-height: 740px !important;
        }

        /* Choices.js ve Filament Select Dropdown Liste Paneli */
        .choices__list--dropdown,
        .fi-select-input-options-list,
        [role="listbox"],
        .fi-dropdown-panel {
            max-height: 400px !important;
            min-height: 220px !important;
            border-radius: 12px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
            z-index: 999999 !important;
            overflow-y: auto !important;
        }

        .choices__list--dropdown .choices__item,
        .fi-select-input-options-list [role="option"] {
            padding: 10px 14px !important;
            font-size: 13.5px !important;
            line-height: 1.4 !important;
        }

        .choices__list--dropdown .choices__group .choices__heading {
            font-weight: 800 !important;
            font-size: 11px !important;
            letter-spacing: 0.05em !important;
            color: #f97316 !important;
            padding: 8px 12px 4px 12px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
            background: rgba(0, 0, 0, 0.25) !important;
        }
    </style>

    <div wire:poll.5s class="space-y-4">
        {{-- 1. ÜST KOKPİT KPI KARTLARI (5'li Grid) --}}
        <div class="war-room-grid">
            {{-- Kart 1: Ziyaretçi & Canlı Sinyal --}}
            <div wire:click="setCardFilter('online')" class="war-card {{ ($activeCardFilter ?? 'all') === 'online' ? 'is-active-filter' : '' }}" title="Canlı yayındaki ziyaretçileri filtrelemek için tıklayın">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
                <div class="war-header-row">
                    <span class="war-label">Ziyaretçi & Sinyal</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'online')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span class="live-radar-dot"></span>
                    </div>
                </div>
                <div class="war-value">
                    <span>{{ $onlineCount }}</span>
                    @if($onlineCount > 0)
                        <span style="font-size: 13px; color: #10b981; font-weight: 700;">Canlı Yayında</span>
                    @else
                        <span style="font-size: 13px; color: #94a3b8; font-weight: 700;">Radar Dinlemede</span>
                    @endif
                </div>
                <div class="war-subtext" style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <span style="color: {{ $onlineCount > 0 ? '#10b981' : '#64748b' }};">●</span>
                        <span>{{ $onlineCount > 0 ? 'Anlık canlı sinyal verenler' : 'Canlı sinyal bekleniyor' }}</span>
                    </div>
                    <div style="font-size: 10px; color: #94a3b8; display: flex; align-items: center; gap: 6px; padding-top: 4px; border-top: 1px solid rgba(255,255,255,0.06); width: 100%;">
                        <span title="Bugünkü Tekil Ziyaretçi">Bugün: <strong style="color: #38bdf8;">{{ number_format($todayVisitorsCount ?? ($dailyTraffic['unique_visitors'] ?? 0)) }}</strong></span>
                        <span style="color: #475569;">|</span>
                        <span title="Son 7 Günlük Tekil Ziyaretçi">7G: <strong style="color: #a78bfa;">{{ number_format($weeklyTraffic['unique_visitors'] ?? 0) }}</strong></span>
                        <span style="color: #475569;">|</span>
                        <span title="Son 30 Günlük Tekil Ziyaretçi">30G: <strong style="color: #34d399;">{{ number_format($monthlyTraffic['unique_visitors'] ?? 0) }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Kart 2: Canlı Sepetler & Potansiyel Ciro --}}
            <div wire:click="setCardFilter('cart')" class="war-card {{ ($activeCardFilter ?? 'all') === 'cart' ? 'is-active-filter' : '' }}" title="Sepetinde ürün olanları filtrelemek için tıklayın">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #06b6d4, #0284c7);"></div>
                <div class="war-header-row">
                    <span class="war-label">Bekleyen Sepetler</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'cart')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 16px;">🛒</span>
                    </div>
                </div>
                <div class="war-value" style="color: #38bdf8;">
                    <span>{{ number_format($cartTotal, 2) }} ₺</span>
                </div>
                <div class="war-subtext">
                    @if(($liveCartCount ?? 0) > 0)
                        <strong style="color: #38bdf8;">{{ $liveCartCount }} canlı sepette</strong> ödeme bekleniyor
                    @elseif(($cartCount ?? 0) > 0)
                        <strong style="color: #f1f5f9;">{{ $cartCount }} sepette</strong> ürün ödeme bekliyor
                    @else
                        <span>Sepette bekleyen ürün bulunmuyor</span>
                    @endif
                </div>
            </div>

            {{-- Kart 3: Sıcak Satın Alma Adayları --}}
            <div wire:click="setCardFilter('high_intent')" class="war-card {{ ($activeCardFilter ?? 'all') === 'high_intent' ? 'is-active-filter' : '' }}" title="Sıcak satın alma adaylarını filtrelemek için tıklayın">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #ff4e00, #ea580c);"></div>
                <div class="war-header-row">
                    <span class="war-label">Sıcak Adaylar</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'high_intent')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 16px;">🔥</span>
                    </div>
                </div>
                <div class="war-value" style="color: #ffedd5;">
                    <span>{{ $highIntentCount }}</span>
                    <span style="font-size: 12px; color: #fb923c; font-weight: 700;">
                        {{ ($liveHighIntentCount ?? 0) > 0 ? 'Canlı Aday' : 'Müşteri' }}
                    </span>
                </div>
                <div class="war-subtext">
                    @if(($liveHighIntentCount ?? 0) > 0)
                        Satın alma niyeti <strong style="color: #fb923c;">%60 ve üzeri</strong> (Canlı)
                    @else
                        Satın alma niyeti <strong style="color: #fb923c;">%60 ve üzeri</strong> adaylar
                    @endif
                </div>
            </div>

            {{-- Kart 4: Tereddütte Olanlar --}}
            <div wire:click="setCardFilter('hesitating')" class="war-card {{ ($activeCardFilter ?? 'all') === 'hesitating' ? 'is-active-filter' : '' }}" title="Tereddüt yaşayanları filtrelemek için tıklayın">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
                <div class="war-header-row">
                    <span class="war-label">Tereddütte Olanlar</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'hesitating')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 16px;">🤔</span>
                    </div>
                </div>
                <div class="war-value" style="color: #fde68a;">
                    <span>{{ $hesitatingCount }}</span>
                    <span style="font-size: 12px; color: #f59e0b; font-weight: 700;">
                        {{ ($liveHesitatingCount ?? 0) > 0 ? 'Canlı Müdahale' : 'Tespit Edildi' }}
                    </span>
                </div>
                <div class="war-subtext">
                    @if(($liveHesitatingCount ?? 0) > 0)
                        <strong style="color: #f59e0b;">Beden/kargo bariyeri</strong> (Hızlı indirim önerilir)
                    @else
                        <span>Beden veya kargo bariyeri algılandı</span>
                    @endif
                </div>
            </div>

            {{-- Kart 5: Üye / Misafir Oranı --}}
            <div wire:click="setCardFilter('members')" class="war-card {{ ($activeCardFilter ?? 'all') === 'members' ? 'is-active-filter' : '' }}" title="Üye girişli kullanıcıları filtrelemek için tıklayın">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #8b5cf6, #6366f1);"></div>
                <div class="war-header-row">
                    <span class="war-label">Kullanıcı Segmenti</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'members')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 16px;">👤</span>
                    </div>
                </div>
                <div class="war-value" style="font-size: 20px;">
                    <span style="color: #c4b5fd;">{{ $membersCount }} Üye</span>
                    <span style="font-size: 13px; color: #64748b;">/</span>
                    <span style="color: #94a3b8; font-size: 18px;">{{ $guestsCount }} Misafir</span>
                </div>
                <div class="war-subtext">
                    Giriş oranı: <strong style="color: #a78bfa;">%{{ $loginRate ?? 0 }}</strong> {{ ($onlineCount ?? 0) > 0 ? '(Canlı)' : '(Genel)' }}
                </div>
            </div>
        </div>

        {{-- 2. ZİYARETÇİ TRAFİK SİNYALİ & DÖNEMLİK ANALİZ PANELİ --}}
        @php
            $cur = $currentTraffic ?? [];
            $analysis = $cur['analysis'] ?? [];
            $deviceBreakdown = $cur['device_breakdown'] ?? ['mobile' => 85, 'desktop' => 12, 'tablet' => 3];
            $sourceBreakdown = $cur['source_breakdown'] ?? [];
        @endphp
        <div class="traffic-intel-panel">
            {{-- Panel Üst Başlık & Periyot Seçici --}}
            <div class="traffic-intel-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, rgba(255, 78, 0, 0.2), rgba(234, 88, 12, 0.1)); border: 1px solid rgba(255, 78, 0, 0.3); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        🚦
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #f8fafc; letter-spacing: -0.01em;">
                                Ziyaretçi Trafik Sinyali &amp; Dönemlik Analiz
                            </h3>
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 999px; background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); font-weight: 700;">
                                {{ $cur['period_label'] ?? 'Günlük Sinyal' }}
                            </span>
                        </div>
                        <p style="margin: 2px 0 0 0; font-size: 11.5px; color: #94a3b8;">
                            Kalıcı olarak kaydedilen tekil ziyaretçi, oturum, kanal, sepet ve satış sinyalleri ile yapay zeka analiz raporu.
                        </p>
                    </div>
                </div>

                {{-- Periyot Geçiş Butonları --}}
                <div style="display: flex; align-items: center; gap: 6px; background: rgba(15, 23, 42, 0.8); padding: 4px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.06);">
                    <button type="button" wire:click="setTrafficPeriod('daily')" class="traffic-period-btn {{ ($trafficPeriod ?? 'daily') === 'daily' ? 'active' : '' }}">
                        <span>📅</span> Günlük (Bugün)
                    </button>
                    <button type="button" wire:click="setTrafficPeriod('weekly')" class="traffic-period-btn {{ ($trafficPeriod ?? 'daily') === 'weekly' ? 'active' : '' }}">
                        <span>📊</span> Haftalık (Son 7 Gün)
                    </button>
                    <button type="button" wire:click="setTrafficPeriod('monthly')" class="traffic-period-btn {{ ($trafficPeriod ?? 'daily') === 'monthly' ? 'active' : '' }}">
                        <span>📈</span> Aylık (Son 30 Gün)
                    </button>
                </div>
            </div>

            {{-- 5'li Özet Trafik & Dönüşüm KPI Şeridi --}}
            <div class="traffic-kpi-grid">
                {{-- Metrik 1: Tekil Ziyaretçi --}}
                <div class="traffic-kpi-card" style="border-left: 3px solid #38bdf8;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
                        <span>Tekil Ziyaretçi</span>
                        <span style="font-size: 14px;">👥</span>
                    </div>
                    <div style="font-size: 22px; font-weight: 900; color: #ffffff; margin: 4px 0;">
                        {{ number_format($cur['unique_visitors'] ?? 0) }}
                    </div>
                    <div style="font-size: 11px; color: #64748b;">
                        Toplam Oturum: <strong style="color: #cbd5e1;">{{ number_format($cur['sessions'] ?? 0) }}</strong>
                    </div>
                </div>

                {{-- Metrik 2: Sayfa Görüntüleme --}}
                <div class="traffic-kpi-card" style="border-left: 3px solid #a855f7;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
                        <span>Sayfa Gösterimi</span>
                        <span style="font-size: 14px;">👁️</span>
                    </div>
                    <div style="font-size: 22px; font-weight: 900; color: #ffffff; margin: 4px 0;">
                        {{ number_format($cur['page_views'] ?? 0) }}
                    </div>
                    <div style="font-size: 11px; color: #64748b;">
                        Ort. Süre: <strong style="color: #cbd5e1;">{{ $cur['avg_duration_formatted'] ?? '1 dk 30 sn' }}</strong>
                    </div>
                </div>

                {{-- Metrik 3: Sepete Ekleme Oranı --}}
                <div class="traffic-kpi-card" style="border-left: 3px solid #06b6d4;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
                        <span>Sepet Hareketi</span>
                        <span style="font-size: 14px;">🛒</span>
                    </div>
                    <div style="font-size: 22px; font-weight: 900; color: #38bdf8; margin: 4px 0;">
                        %{{ $cur['cart_rate'] ?? 0 }}
                    </div>
                    <div style="font-size: 11px; color: #64748b;">
                        <strong style="color: #cbd5e1;">{{ number_format($cur['cart_additions'] ?? 0) }}</strong> kişi sepete ürün attı
                    </div>
                </div>

                {{-- Metrik 4: Satış Dönüşümü --}}
                <div class="traffic-kpi-card" style="border-left: 3px solid #10b981;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
                        <span>Dönüşüm Oranı</span>
                        <span style="font-size: 14px;">🎯</span>
                    </div>
                    <div style="font-size: 22px; font-weight: 900; color: #34d399; margin: 4px 0;">
                        %{{ $cur['conversion_rate'] ?? 0 }}
                    </div>
                    <div style="font-size: 11px; color: #64748b;">
                        Tamamlanan: <strong style="color: #34d399;">{{ number_format($cur['orders_count'] ?? 0) }} Sipariş</strong>
                    </div>
                </div>

                {{-- Metrik 5: Ciro --}}
                <div class="traffic-kpi-card" style="border-left: 3px solid #ff4e00;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
                        <span>Toplam Ciro</span>
                        <span style="font-size: 14px;">💰</span>
                    </div>
                    <div style="font-size: 20px; font-weight: 900; color: #ffedd5; margin: 4px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ number_format($cur['orders_revenue'] ?? 0, 2) }} ₺
                    </div>
                    <div style="font-size: 11px; color: #64748b;">
                        @if(($cur['orders_count'] ?? 0) > 0)
                            AOV: <strong style="color: #cbd5e1;">{{ number_format(($cur['orders_revenue'] ?? 0) / $cur['orders_count'], 2) }} ₺</strong>
                        @else
                            Ortalama sepet değeri
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3 Kolonlu Detay & AI Analiz Şebekesi --}}
            <div class="traffic-intel-details-grid">
                {{-- Kolon 1: Trafik Kaynakları Dağılımı --}}
                <div class="traffic-sub-box">
                    <div style="font-size: 12px; font-weight: 800; color: #f1f5f9; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span>🌐</span> Kaynak Dağılımı
                        </span>
                        <span style="font-size: 10.5px; color: #64748b; font-weight: 600;">Pay / Tekil</span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 10px; flex: 1; justify-content: flex-start;">
                        @forelse($sourceBreakdown as $src)
                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; margin-bottom: 3px;">
                                    <span style="display: flex; align-items: center; gap: 5px; color: #cbd5e1; font-weight: 600;">
                                        <span>{{ $src['icon'] ?? '📍' }}</span>
                                        <span>{{ $src['name'] }}</span>
                                    </span>
                                    <span style="font-weight: 700; color: {{ $src['color'] ?? '#38bdf8' }};">
                                        %{{ $src['percentage'] }} <span style="font-size: 10px; color: #64748b; font-weight: 500;">({{ number_format($src['count']) }})</span>
                                    </span>
                                </div>
                                <div style="height: 5px; background: rgba(255,255,255,0.06); border-radius: 999px; overflow: hidden;">
                                    <div style="width: {{ min(100, max(5, $src['percentage'])) }}%; height: 100%; background: {{ $src['color'] ?? '#38bdf8' }}; border-radius: 999px;"></div>
                                </div>
                            </div>
                        @empty
                            <div style="font-size: 11px; color: #64748b; text-align: center; padding: 20px 0;">
                                Henüz trafik kaynağı verisi kaydedilmedi.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Kolon 2: Cihaz Dağılımı --}}
                <div class="traffic-sub-box">
                    <div style="font-size: 12px; font-weight: 800; color: #f1f5f9; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span>📱</span> Cihaz Tercihleri
                        </span>
                        <span style="font-size: 10.5px; color: #64748b; font-weight: 600;">Yüzde</span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 12px; flex: 1; justify-content: center;">
                        {{-- Mobil --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; margin-bottom: 4px;">
                                <span style="display: flex; align-items: center; gap: 5px; color: #cbd5e1; font-weight: 600;">
                                    <span>📱</span> Mobil Cihazlar
                                </span>
                                <span style="font-weight: 800; color: #10b981;">%{{ $deviceBreakdown['mobile'] ?? 0 }}</span>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $deviceBreakdown['mobile'] ?? 0 }}%; height: 100%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 999px;"></div>
                            </div>
                        </div>

                        {{-- Masaüstü --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; margin-bottom: 4px;">
                                <span style="display: flex; align-items: center; gap: 5px; color: #cbd5e1; font-weight: 600;">
                                    <span>💻</span> Masaüstü Bilgisayar
                                </span>
                                <span style="font-weight: 800; color: #38bdf8;">%{{ $deviceBreakdown['desktop'] ?? 0 }}</span>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $deviceBreakdown['desktop'] ?? 0 }}%; height: 100%; background: linear-gradient(90deg, #38bdf8, #0284c7); border-radius: 999px;"></div>
                            </div>
                        </div>

                        {{-- Tablet --}}
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; margin-bottom: 4px;">
                                <span style="display: flex; align-items: center; gap: 5px; color: #cbd5e1; font-weight: 600;">
                                    <span>📟</span> Tablet Cihazlar
                                </span>
                                <span style="font-weight: 800; color: #a855f7;">%{{ $deviceBreakdown['tablet'] ?? 0 }}</span>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.06); border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $deviceBreakdown['tablet'] ?? 0 }}%; height: 100%; background: linear-gradient(90deg, #a855f7, #7c3aed); border-radius: 999px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolon 3: Phoenix AI Trafik & Sinyal Analizi --}}
                <div class="traffic-sub-box" style="background: linear-gradient(145deg, rgba(24, 34, 52, 0.9) 0%, rgba(13, 21, 34, 0.95) 100%); border: 1px solid rgba(255, 78, 0, 0.2);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; gap: 8px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 16px;">🧠</span>
                            <span style="font-size: 12px; font-weight: 800; color: #f8fafc; text-transform: uppercase; letter-spacing: 0.04em;">
                                Phoenix AI Trafik & Sinyal Analizi
                            </span>
                        </div>
                        <span style="font-size: 9.5px; font-weight: 800; padding: 2px 7px; border-radius: 5px; background: rgba(255, 78, 0, 0.15); color: #ff7849; border: 1px solid rgba(255, 78, 0, 0.35);">
                            {{ $analysis['status'] ?? 'DENGELİ BÜYÜME' }}
                        </span>
                    </div>

                    <div style="font-size: 13px; font-weight: 800; color: #f1f5f9; margin-bottom: 4px; line-height: 1.3;">
                        {{ $analysis['headline'] ?? 'Trafik ve Ziyaretçi Sinyalleri Analiz Ediliyor' }}
                    </div>

                    <p style="font-size: 11.5px; color: #cbd5e1; margin: 0 0 10px 0; line-height: 1.45;">
                        {{ $analysis['summary'] ?? 'Sistem gelen ziyaretçi trafiğini, oturum süresini ve sepete ekleme aksiyonlarını inceliyor.' }}
                    </p>

                    @if(!empty($analysis['highlights']))
                        <div style="display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px;">
                            @foreach($analysis['highlights'] as $highlight)
                                <div style="font-size: 11px; color: #94a3b8; display: flex; align-items: flex-start; gap: 6px;">
                                    <span style="color: #ff7849; font-weight: 800;">›</span>
                                    <span>{{ $highlight }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($analysis['recommended_action']))
                        <div style="margin-top: auto; padding: 9px 12px; border-radius: 8px; background: rgba(255, 78, 0, 0.08); border: 1px solid rgba(255, 78, 0, 0.25); display: flex; align-items: flex-start; gap: 8px;">
                            <span style="font-size: 14px; flex-shrink: 0; margin-top: 1px;">💡</span>
                            <div style="font-size: 11px; color: #ffedd5; line-height: 1.4;">
                                <strong style="color: #ff7849;">Stratejik Eylem:</strong> {{ $analysis['recommended_action'] }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Engelli Kullanıcılar Bilgi Çubuğu (Varsa) --}}
        @if (($blockedCount ?? 0) > 0)
            <div style="background: linear-gradient(90deg, rgba(239, 68, 68, 0.2) 0%, rgba(15, 23, 42, 0.9) 100%); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 12px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">⛔</span>
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #fca5a5;">
                            {{ $blockedCount }} Ziyaretçinin Erişimi Engellenmiş Durumda
                        </div>
                        <div style="font-size: 11px; color: #cbd5e1;">
                            Bu kullanıcılar siteye erişememektedir. Üstteki <strong>"🚫 Engellenenler (Kara Liste)"</strong> butonundan veya aşağıdaki listeden yeşil <strong>"Engeli Kaldır"</strong> butonuyla erişimlerini anında açabilirsiniz.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- HIZLI KOMUTA & TOPLU AKSİYON KISAYOLLARI (ZİYARETÇİ SİNYAL TABLOSU ÜSTÜ) --}}
        <div class="visitor-shortcuts-panel">
            <div class="visitor-shortcuts-header">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(255, 78, 0, 0.18); border: 1px solid rgba(255, 78, 0, 0.35); display: flex; align-items: center; justify-content: center; font-size: 14px;">
                        ⚡
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #f8fafc; letter-spacing: -0.01em;">
                            Hızlı Müdahale &amp; Toplu İşlem Kısayolları
                        </div>
                        <div style="font-size: 11px; color: #94a3b8;">
                            Sitedeki tüm aktif ziyaretçilere anında toplu yönlendirme, kupon veya sesli anons gönderin
                        </div>
                    </div>
                </div>
                <div style="font-size: 11px; color: #64748b; font-family: monospace;">
                    6 HIZLI KISAYOL AKTİF
                </div>
            </div>
            <div class="visitor-shortcuts-actions">
                <x-filament::actions :actions="$this->getCachedHeaderActions()" />
            </div>
        </div>

        {{-- 2. CANLI İSTİHBARAT BİLGİ & DURUM ÇUBUĞU --}}
        <div class="command-bar">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-size: 18px;">📡</span>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #f8fafc;">
                        Canlı Satış İstihbaratı & Müşteri Karar Radarı
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">
                        Ziyaretçinin beden seçimleri, sepette kalış süresi ve sayfa gezinme izi analiz edilir; tereddüt anında tek tıkla kupon veya yönlendirme fırlatabilirsiniz.
                    </div>
                </div>
                @if(($activeCardFilter ?? 'all') !== 'all')
                    @php
                        $filterName = match($activeCardFilter) {
                            'online' => '🟢 Canlı Yayındakiler',
                            'cart' => '🛒 Sepetinde Ürün Olanlar',
                            'high_intent' => '🔥 Sıcak Satın Alma Adayları',
                            'hesitating' => '🤔 Tereddütte Olanlar',
                            'members' => '👤 Üye Girişi Yapanlar',
                            default => $activeCardFilter,
                        };
                    @endphp
                    <div style="display: flex; align-items: center; gap: 8px; background: rgba(255, 78, 0, 0.2); border: 1px solid rgba(255, 78, 0, 0.4); padding: 4px 10px; border-radius: 8px;">
                        <span style="font-size: 11.5px; font-weight: 800; color: #ffedd5;">Filtre: {{ $filterName }}</span>
                        <button type="button" wire:click="setCardFilter('all')" style="cursor: pointer; background: rgba(255, 255, 255, 0.15); border: none; border-radius: 4px; padding: 2px 7px; font-size: 10.5px; font-weight: 800; color: #ffffff;">✕ Filtreyi Temizle</button>
                    </div>
                @endif
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="live-radar-dot"></span>
                <span style="font-size: 11px; color: #10b981; font-weight: 700; font-family: monospace;">CANLI RADAR AKTİF (5sn)</span>
            </div>
        </div>

        {{-- 3. CANLI İSTİHBARAT TABLOSU --}}
        {{ $this->table }}
    </div>

    {{-- Görünüm Modu Buton Pozisyonu Güvenceleyici (Filtrele Butonunun Sağında) --}}
    <script>
        (function() {
            function ensureViewTogglePosition() {
                const toggle = document.querySelector('.av-view-toggle-group');
                const filterDropdown = document.querySelector('.fi-ta-filters-dropdown');
                if (toggle && filterDropdown && toggle.previousElementSibling !== filterDropdown) {
                    filterDropdown.after(toggle);
                }
            }

            document.addEventListener('DOMContentLoaded', ensureViewTogglePosition);
            document.addEventListener('livewire:navigated', ensureViewTogglePosition);
            document.addEventListener('livewire:initialized', () => {
                if (window.Livewire && Livewire.hook) {
                    Livewire.hook('morph.updated', () => {
                        ensureViewTogglePosition();
                    });
                }
            });
            setTimeout(ensureViewTogglePosition, 200);
            setTimeout(ensureViewTogglePosition, 800);
        })();
    </script>

    {{-- Dropdown Açıldığında Pop-up Penceresini Uzatan Dinamik Gözlemci --}}
    <script>
        (function() {
            function updateModalDropdownExpansion() {
                const modals = document.querySelectorAll('.fi-modal-window');
                modals.forEach(function(modal) {
                    const isOpen = modal.querySelector('.choices.is-open, [aria-expanded="true"], .fi-select-input-options-list:not([hidden])');
                    if (isOpen) {
                        modal.classList.add('dropdown-expanded');
                    } else {
                        modal.classList.remove('dropdown-expanded');
                    }
                });
            }

            // Tıklama, focus ve tuş olaylarını anında yakala
            document.addEventListener('click', function() {
                setTimeout(updateModalDropdownExpansion, 40);
                setTimeout(updateModalDropdownExpansion, 150);
            }, true);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    setTimeout(updateModalDropdownExpansion, 50);
                }
            });

            // Choices.js ve DOM değişimlerini MutationObserver ile anlık yakala
            const modalObserver = new MutationObserver(function() {
                updateModalDropdownExpansion();
            });

            modalObserver.observe(document.body, {
                attributes: true,
                attributeFilter: ['class', 'aria-expanded', 'style'],
                subtree: true
            });

            document.addEventListener('DOMContentLoaded', updateModalDropdownExpansion);
            document.addEventListener('livewire:navigated', updateModalDropdownExpansion);
            document.addEventListener('livewire:initialized', function() {
                if (window.Livewire && Livewire.hook) {
                    Livewire.hook('morph.updated', function() {
                        setTimeout(updateModalDropdownExpansion, 50);
                    });
                }
            });
        })();
    </script>
</x-filament-panels::page>
