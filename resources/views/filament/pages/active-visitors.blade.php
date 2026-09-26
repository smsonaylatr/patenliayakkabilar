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
        }
        .war-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.18);
            box-shadow: 0 14px 28px -4px rgba(0, 0, 0, 0.5);
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
    </style>

    <div wire:poll.5s class="space-y-4">
        {{-- 1. ÜST KOKPİT KPI KARTLARI (5'li Grid) --}}
        <div class="war-room-grid">
            {{-- Kart 1: Canlı Sitede Olanlar --}}
            <div class="war-card">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
                <div class="war-header-row">
                    <span class="war-label">Şu An Sitede</span>
                    <span class="live-radar-dot"></span>
                </div>
                <div class="war-value">
                    <span>{{ $onlineCount }}</span>
                    <span style="font-size: 13px; color: #10b981; font-weight: 700;">Canlı Yayında</span>
                </div>
                <div class="war-subtext">
                    <span style="color: #10b981;">●</span> Son 45 saniyede sinyal verenler
                </div>
            </div>

            {{-- Kart 2: Canlı Sepetler & Potansiyel Ciro --}}
            <div class="war-card">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #06b6d4, #0284c7);"></div>
                <div class="war-header-row">
                    <span class="war-label">Bekleyen Sepetler</span>
                    <span style="font-size: 16px;">🛒</span>
                </div>
                <div class="war-value" style="color: #38bdf8;">
                    <span>{{ number_format($cartTotal, 2) }} ₺</span>
                </div>
                <div class="war-subtext">
                    <strong style="color: #f1f5f9;">{{ $cartCount }} sepette</strong> ürün ödeme bekliyor
                </div>
            </div>

            {{-- Kart 3: Sıcak Satın Alma Adayları --}}
            <div class="war-card">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #ff4e00, #ea580c);"></div>
                <div class="war-header-row">
                    <span class="war-label">Sıcak Adaylar</span>
                    <span style="font-size: 16px;">🔥</span>
                </div>
                <div class="war-value" style="color: #ffedd5;">
                    <span>{{ $highIntentCount }}</span>
                    <span style="font-size: 12px; color: #fb923c; font-weight: 700;">Müşteri</span>
                </div>
                <div class="war-subtext">
                    Satın alma niyeti <strong style="color: #fb923c;">%60 ve üzeri</strong>
                </div>
            </div>

            {{-- Kart 4: Tereddütte Olanlar --}}
            <div class="war-card">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
                <div class="war-header-row">
                    <span class="war-label">Tereddütte Olanlar</span>
                    <span style="font-size: 16px;">🤔</span>
                </div>
                <div class="war-value" style="color: #fde68a;">
                    <span>{{ $hesitatingCount }}</span>
                    <span style="font-size: 12px; color: #f59e0b; font-weight: 700;">Müdahale Bekliyor</span>
                </div>
                <div class="war-subtext">
                    Beden veya kargo bariyeri algılandı
                </div>
            </div>

            {{-- Kart 5: Üye / Misafir Oranı --}}
            <div class="war-card">
                <div class="war-card-top-bar" style="background: linear-gradient(90deg, #8b5cf6, #6366f1);"></div>
                <div class="war-header-row">
                    <span class="war-label">Kullanıcı Segmenti</span>
                    <span style="font-size: 16px;">👤</span>
                </div>
                <div class="war-value" style="font-size: 20px;">
                    <span style="color: #c4b5fd;">{{ $membersCount }} Üye</span>
                    <span style="font-size: 13px; color: #64748b;">/</span>
                    <span style="color: #94a3b8; font-size: 18px;">{{ $guestsCount }} Misafir</span>
                </div>
                <div class="war-subtext">
                    Giriş oranı: <strong style="color: #a78bfa;">%{{ $onlineCount > 0 ? round(($membersCount / $onlineCount) * 100) : 0 }}</strong>
                </div>
            </div>
        </div>

        {{-- 2. CANLI İSTİHBARAT BİLGİ & DURUM ÇUBUĞU --}}
        <div class="command-bar">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">📡</span>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #f8fafc;">
                        Canlı Satış İstihbaratı & Müşteri Karar Radarı
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">
                        Ziyaretçinin beden seçimleri, sepette kalış süresi ve sayfa gezinme izi analiz edilir; tereddüt anında tek tıkla kupon veya yönlendirme fırlatabilirsiniz.
                    </div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="live-radar-dot"></span>
                <span style="font-size: 11px; color: #10b981; font-weight: 700; font-family: monospace;">CANLI RADAR AKTİF (5sn)</span>
            </div>
        </div>

        {{-- 3. CANLI İSTİHBARAT TABLOSU --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
