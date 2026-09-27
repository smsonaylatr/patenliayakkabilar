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
            background: #131d2f;
            background: linear-gradient(180deg, #182234 0%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 16px 18px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 110px;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
        }
        .war-card:hover {
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
        }
        .war-card.is-active-filter {
            border-color: #ff4e00 !important;
            box-shadow: 0 0 16px rgba(255, 78, 0, 0.3) !important;
        }
        .war-card-active-pill {
            font-size: 9px;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 4px;
            background: #ff4e00;
            color: #ffffff;
            letter-spacing: 0.04em;
        }
        .war-card-top-bar {
            display: none;
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
            letter-spacing: 0.05em;
            color: #64748b;
        }
        .war-value {
            font-size: 24px;
            font-weight: 800;
            color: #f8fafc;
            line-height: 1.1;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .war-subtext {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Canlı Nabız Sinyali */
        .live-radar-dot {
            width: 7px;
            height: 7px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
        }

        /* Komuta Bilgi Çubuğu */
        .command-bar {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* İlerleme Çubuğu */
        .intent-bar-bg {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 9999px;
            height: 4px;
            overflow: hidden;
            width: 100%;
            margin-top: 4px;
        }
        .intent-bar-fill {
            height: 100%;
            border-radius: 9999px;
            background: #ff4e00;
            transition: width 0.3s ease;
        }

        /* Ziyaretçi Trafik Sinyali & Dönemlik Analiz Kartı */
        .traffic-intel-panel {
            background: #131d2f;
            background: linear-gradient(180deg, #182234 0%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 16px;
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
            padding-bottom: 12px;
        }
        .traffic-period-btn {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .traffic-period-btn.active {
            background: #ff4e00;
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            border-color: transparent;
        }
        .traffic-period-btn:not(.active) {
            background: rgba(255, 255, 255, 0.04);
            color: #94a3b8;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .traffic-period-btn:not(.active):hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
        }
        .traffic-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
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
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .traffic-intel-details-grid {
            display: grid;
            grid-template-columns: 28% 22% 50%;
            gap: 14px;
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
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* Ziyaretçi Sinyal Tablosu Hızlı Komuta Kısayolları */
        .visitor-shortcuts-panel {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .visitor-shortcuts-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .visitor-shortcuts-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .visitor-shortcuts-actions .fi-ac {
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ─── Canlı & Son Ziyaret Edenler Sekme Çubuğu Stilleri ─── */
        .av-tabs-nav-bar {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 16px;
            padding: 4px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
        }
        .av-tab-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none !important;
            user-select: none;
        }
        .av-tab-nav-btn:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.04);
        }
        .av-tab-nav-btn.is-active-tab {
            background: #1e293b;
            color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        .av-tab-nav-badge {
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
        }
        .av-tab-nav-btn.is-active-tab .av-tab-nav-badge {
            background: rgba(255, 78, 0, 0.15);
            color: #ff7849;
            border: 1px solid rgba(255, 78, 0, 0.3);
        }

        /* ─── POP-UP & MODAL GENEL DÜZEN VE HİZALAMA STİLLERİ ─── */
        /* Modal penceresi taban boyutu ve estetik çerçeve (Tam Responsive) */
        .fi-modal-window {
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.09) !important;
            max-width: min(740px, calc(100vw - 20px)) !important;
            width: 100% !important;
        }

        .fi-modal-header {
            padding: 16px 22px 14px 22px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            background: rgba(15, 23, 42, 0.96) !important;
            backdrop-filter: blur(12px) !important;
        }

        .fi-modal-heading {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: #f8fafc !important;
            letter-spacing: -0.01em !important;
            line-height: 1.3 !important;
        }

        .fi-modal-description {
            margin-top: 3px !important;
            font-size: 0.82rem !important;
            color: #94a3b8 !important;
            line-height: 1.35 !important;
        }

        /* Modal İçeriği: Rahat Kaydırma, İdeal Boşluklar */
        .fi-modal-content {
            padding: 16px 22px 20px 22px !important;
            overflow-y: auto !important;
            max-height: calc(85vh - 120px) !important;
            scrollbar-width: thin !important;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent !important;
        }

        .fi-modal-content::-webkit-scrollbar {
            width: 6px !important;
        }
        .fi-modal-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 4px !important;
        }

        /* Modal Footer / Aksiyon Butonları */
        .fi-modal-footer {
            padding: 12px 22px !important;
            border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
            background: rgba(15, 23, 42, 0.98) !important;
            backdrop-filter: blur(12px) !important;
            z-index: 10 !important;
        }

        .fi-modal-footer-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        .fi-modal-footer-actions .fi-btn,
        .fi-modal-footer .fi-btn {
            height: 36px !important;
            padding: 0 14px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
        }

        /* Modal içi form alanları ve yardım metinleri hizalaması */
        .fi-modal-content .fi-fo-field,
        .fi-modal-content .fi-fo-field-wrp {
            margin-bottom: 12px !important;
        }

        .fi-modal-content .fi-fo-field-label-col {
            margin-bottom: 5px !important;
            display: block !important;
        }

        .fi-modal-content .fi-fo-field-label,
        .fi-modal-content .fi-fo-field-label-content {
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: #e2e8f0 !important;
            line-height: 1.35 !important;
        }

        .fi-modal-content .fi-fo-field-helper-text,
        .fi-modal-content .fi-fo-field-wrp-helper-text {
            margin-top: 4px !important;
            font-size: 11px !important;
            color: #94a3b8 !important;
            line-height: 1.35 !important;
        }

        .fi-modal-content .fi-input-wrp {
            border-radius: 8px !important;
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            transition: all 0.15s ease !important;
            min-height: 38px !important;
        }

        .fi-modal-content .fi-input-wrp:focus-within {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 1px #f97316, 0 0 10px rgba(249, 115, 22, 0.25) !important;
        }

        .fi-modal-content input.fi-input,
        .fi-modal-content textarea.fi-input,
        .fi-modal-content .fi-select-input {
            font-size: 12.5px !important;
            padding: 7px 11px !important;
            color: #f8fafc !important;
        }

        /* ─── HIZLI KANAL SEÇİMİ BUTON IZGARASI (MÜKEMMEL HİZALI, ÇAKIŞMASIZ KARTLAR) ─── */
        .channel-selection-field-wrapper {
            margin-top: 4px !important;
            margin-bottom: 14px !important;
            width: 100% !important;
            display: block !important;
        }

        .channel-selection-field-wrapper .fi-fo-field-label-col {
            display: block !important;
            margin-bottom: 8px !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .channel-selection-field-wrapper .fi-fo-field-label,
        .channel-selection-field-wrapper .fi-fo-field-label-content {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #f8fafc !important;
            margin-bottom: 0 !important;
        }

        .channel-selection-grid,
        .fi-fo-radio.channel-selection-grid,
        .channel-selection-field-wrapper .fi-fo-radio {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 8px !important;
            width: 100% !important;
            margin-top: 2px !important;
            margin-bottom: 0 !important;
            padding: 0 !important;
        }

        @media (max-width: 640px) {
            .channel-selection-grid,
            .fi-fo-radio.channel-selection-grid,
            .channel-selection-field-wrapper .fi-fo-radio {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 6px !important;
            }

            .fi-modal-window {
                max-width: calc(100vw - 16px) !important;
                margin: 8px auto !important;
                border-radius: 14px !important;
            }

            .fi-modal-header {
                padding: 12px 14px !important;
            }

            .fi-modal-heading {
                font-size: 1rem !important;
            }

            .fi-modal-description {
                font-size: 0.78rem !important;
            }

            .fi-modal-content {
                padding: 12px 14px !important;
            }

            .fi-modal-footer {
                padding: 10px 14px !important;
            }

            .fi-modal-footer-actions {
                flex-direction: column-reverse !important;
                width: 100% !important;
                gap: 8px !important;
            }

            .fi-modal-footer-actions .fi-btn {
                width: 100% !important;
                justify-content: center !important;
            }
        }

        @media (max-width: 440px) {
            .channel-selection-grid,
            .fi-fo-radio.channel-selection-grid,
            .channel-selection-field-wrapper .fi-fo-radio {
                grid-template-columns: 1fr !important;
                gap: 6px !important;
            }
        }

        .channel-selection-grid .fi-fo-radio-label,
        .channel-selection-field-wrapper .fi-fo-radio-label {
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 7px 10px !important;
            min-height: 48px !important;
            border-radius: 10px !important;
            background: rgba(30, 41, 59, 0.6) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.08) !important;
            cursor: pointer !important;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1) !important;
            margin: 0 !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
            user-select: none !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }

        .channel-selection-grid .fi-fo-radio-label:hover,
        .channel-selection-field-wrapper .fi-fo-radio-label:hover {
            background: rgba(51, 65, 85, 0.75) !important;
            border-color: rgba(249, 115, 22, 0.45) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3) !important;
        }

        /* Standart radio dairesini gizle */
        .channel-selection-grid .fi-radio-input,
        .channel-selection-field-wrapper .fi-radio-input {
            position: absolute !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            pointer-events: none !important;
        }

        .channel-selection-grid .fi-fo-radio-label-text,
        .channel-selection-field-wrapper .fi-fo-radio-label-text {
            width: 100% !important;
            margin: 0 !important;
        }

        .channel-selection-grid .fi-fo-radio-label-text > p,
        .channel-selection-field-wrapper .fi-fo-radio-label-text > p {
            margin: 0 !important;
            width: 100% !important;
        }

        /* Seçili Kart Vurgusu */
        .channel-selection-grid .fi-fo-radio-label:has(input:checked),
        .channel-selection-field-wrapper .fi-fo-radio-label:has(input:checked) {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.16) 0%, rgba(234, 88, 12, 0.25) 100%) !important;
            border-color: #f97316 !important;
            box-shadow: 0 0 0 1px #f97316, 0 4px 12px rgba(249, 115, 22, 0.22) !important;
        }

        .channel-selection-grid .fi-fo-radio-label:has(input:checked)::after,
        .channel-selection-field-wrapper .fi-fo-radio-label:has(input:checked)::after {
            content: '✓' !important;
            position: absolute !important;
            top: 5px !important;
            right: 6px !important;
            width: 14px !important;
            height: 14px !important;
            background: #f97316 !important;
            color: #ffffff !important;
            font-size: 8.5px !important;
            font-weight: 800 !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35) !important;
        }

        /* Kart İçeriği Düzeni */
        .channel-card-row {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            width: 100% !important;
        }

        .channel-card-icon {
            width: 28px !important;
            height: 28px !important;
            border-radius: 7px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        .channel-card-icon svg {
            width: 15px !important;
            height: 15px !important;
        }

        .channel-card-info {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            text-align: left !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        .channel-card-title {
            font-size: 11.5px !important;
            font-weight: 700 !important;
            color: #f8fafc !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .channel-card-subtitle {
            font-size: 9.5px !important;
            font-weight: 500 !important;
            color: #94a3b8 !important;
            line-height: 1.15 !important;
            margin-top: 1px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* ─── DROPDOWN & SELECT SEÇİM LİSTESİ ÖZEL RENK TONU & VURGUSU ─── */
        /* Dropdown Paneli: Modal zemininden net ayrışan derin Slate-800 tonu, gölge ve mavi ışıma */
        .choices__list--dropdown,
        .fi-select-input-options-list,
        .fi-dropdown-panel,
        div.choices__list[role="listbox"] {
            background: #1e293b !important;
            background: linear-gradient(180deg, #1e293b 0%, #151e2e 100%) !important;
            border: 1.5px solid rgba(56, 189, 248, 0.45) !important;
            border-radius: 12px !important;
            box-shadow: 0 20px 45px -8px rgba(0, 0, 0, 0.9), 0 0 0 1px rgba(255, 255, 255, 0.14), 0 0 24px rgba(56, 189, 248, 0.15) !important;
            z-index: 999999 !important;
            max-height: 280px !important;
            overflow-y: auto !important;
            backdrop-filter: blur(16px) !important;
        }

        /* Dropdown İçi Arama Çubuğu */
        .choices__list--dropdown .choices__input,
        .choices__list--dropdown input[type="search"] {
            background: #0f172a !important;
            border: 1.5px solid rgba(255, 255, 255, 0.16) !important;
            border-radius: 8px !important;
            color: #f8fafc !important;
            font-size: 12px !important;
            padding: 8px 12px !important;
            margin: 8px 8px 6px 8px !important;
            width: calc(100% - 16px) !important;
            box-sizing: border-box !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
        }

        .choices__list--dropdown .choices__input:focus,
        .choices__list--dropdown input[type="search"]:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25), inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
            outline: none !important;
        }

        /* Grup Başlıkları (Örn: 📂 KATEGORİ SAYFALARI) */
        .choices__list--dropdown .choices__heading,
        .fi-select-input-options-list .fi-dropdown-header {
            background: rgba(255, 255, 255, 0.06) !important;
            color: #38bdf8 !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 7px 14px !important;
            border-top: 1px solid rgba(255, 255, 255, 0.07) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
            margin: 4px 0 2px 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        /* Liste Seçenekleri */
        .choices__list--dropdown .choices__item--choice,
        .fi-select-input-options-list [role="option"],
        .fi-dropdown-panel [role="option"] {
            padding: 9px 14px !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            color: #e2e8f0 !important;
            background: transparent !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
            transition: all 0.15s ease !important;
            cursor: pointer !important;
        }

        /* Seçenek Üzerine Gelindiğinde (Hover & Highlighted) */
        .choices__list--dropdown .choices__item--choice.is-highlighted,
        .choices__list--dropdown .choices__item--choice:hover,
        .fi-select-input-options-list [role="option"]:hover,
        .fi-select-input-options-list [role="option"][aria-selected="true"] {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.22) 0%, rgba(56, 189, 248, 0.06) 100%) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-left: 3px solid #38bdf8 !important;
            padding-left: 12px !important;
        }

        /* Halihazırda Seçili Olan Öğe */
        .choices__list--dropdown .choices__item--choice.is-selected {
            background: rgba(249, 115, 22, 0.18) !important;
            color: #fb923c !important;
            font-weight: 700 !important;
            border-left: 3px solid #f97316 !important;
            padding-left: 12px !important;
        }

        /* Choices Tetikleyici Kutu (Input Wrapper) */
        .choices__inner {
            background: rgba(30, 41, 59, 0.65) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 8px !important;
            color: #f8fafc !important;
            min-height: 38px !important;
            padding: 4px 10px !important;
            font-size: 12.5px !important;
            transition: all 0.15s ease !important;
        }

        .choices.is-open .choices__inner {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 1px #38bdf8, 0 0 12px rgba(56, 189, 248, 0.25) !important;
        }

        /* Dropdown Scrollbar */
        .choices__list--dropdown::-webkit-scrollbar,
        .choices__list--dropdown .choices__list::-webkit-scrollbar,
        .fi-select-input-options-list::-webkit-scrollbar {
            width: 6px !important;
        }
        .choices__list--dropdown::-webkit-scrollbar-track,
        .choices__list--dropdown .choices__list::-webkit-scrollbar-track,
        .fi-select-input-options-list::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6) !important;
        }
        .choices__list--dropdown::-webkit-scrollbar-thumb,
        .choices__list--dropdown .choices__list::-webkit-scrollbar-thumb,
        .fi-select-input-options-list::-webkit-scrollbar-thumb {
            background: #475569 !important;
            border-radius: 4px !important;
        }
        .choices__list--dropdown::-webkit-scrollbar-thumb:hover,
        .choices__list--dropdown .choices__list::-webkit-scrollbar-thumb:hover,
        .fi-select-input-options-list::-webkit-scrollbar-thumb:hover {
            background: #64748b !important;
        }
    </style>

    <div wire:poll.5s class="space-y-4">
        {{-- 1. ÜST KOKPİT KPI KARTLARI (5'li Grid) --}}
        <div class="war-room-grid">
            {{-- Kart 1: Ziyaretçi & Canlı Sinyal --}}
            <div wire:click="setCardFilter('online')" class="war-card {{ ($activeCardFilter ?? 'all') === 'online' ? 'is-active-filter' : '' }}" title="Canlı yayındaki ziyaretçileri filtrelemek için tıklayın">
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
                    <span>{{ ($activeTab ?? 'live') === 'recent' ? ($recentCount ?? ($recentLeftCount ?? 0)) : ($onlineCount ?? 0) }}</span>
                    @if(($activeTab ?? 'live') === 'recent')
                        <span style="font-size: 12px; color: #94a3b8; font-weight: 600;">Ayrılan Misafir</span>
                    @elseif(($onlineCount ?? 0) > 0)
                        <span style="font-size: 12px; color: #34d399; font-weight: 600;">Canlı Yayında</span>
                    @else
                        <span style="font-size: 12px; color: #64748b; font-weight: 600;">Dinlemede</span>
                    @endif
                </div>
                <div class="war-subtext" style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        @if(($activeTab ?? 'live') === 'recent')
                            <span>Son 24 saat içinde sitede gezinenler</span>
                        @else
                            <span>{{ ($onlineCount ?? 0) > 0 ? 'Anlık canlı sinyal verenler' : 'Canlı sinyal bekleniyor' }}</span>
                        @endif
                    </div>
                    <div style="font-size: 10px; color: #64748b; display: flex; align-items: center; gap: 6px; padding-top: 4px; border-top: 1px solid rgba(255,255,255,0.06); width: 100%;">
                        <span title="Bugünkü Tekil Ziyaretçi">Bugün: <strong style="color: #cbd5e1;">{{ number_format($todayVisitorsCount ?? ($dailyTraffic['unique_visitors'] ?? 0)) }}</strong></span>
                        <span>|</span>
                        <span title="Son 7 Günlük Tekil Ziyaretçi">7G: <strong style="color: #cbd5e1;">{{ number_format($weeklyTraffic['unique_visitors'] ?? 0) }}</strong></span>
                        <span>|</span>
                        <span title="Son 30 Günlük Tekil Ziyaretçi">30G: <strong style="color: #cbd5e1;">{{ number_format($monthlyTraffic['unique_visitors'] ?? 0) }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Kart 2: Canlı Sepetler & Potansiyel Ciro --}}
            <div wire:click="setCardFilter('cart')" class="war-card {{ ($activeCardFilter ?? 'all') === 'cart' ? 'is-active-filter' : '' }}" title="Sepetinde ürün olanları filtrelemek için tıklayın">
                <div class="war-header-row">
                    <span class="war-label">{{ ($activeTab ?? 'live') === 'recent' ? 'Terk Edilen Sepetler' : 'Bekleyen Sepetler' }}</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'cart')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 15px;">🛒</span>
                    </div>
                </div>
                <div class="war-value">
                    <span>{{ number_format($cartTotal ?? 0, 2) }} ₺</span>
                </div>
                <div class="war-subtext">
                    @if(($activeTab ?? 'live') === 'recent')
                        @if(($recentCartCount ?? 0) > 0)
                            <strong style="color: #cbd5e1;">{{ $recentCartCount }} sepette</strong> ürün terk edildi
                        @elseif(($cartCount ?? 0) > 0)
                            <strong style="color: #cbd5e1;">{{ $cartCount }} sepette</strong> ürün ödeme bekliyor
                        @else
                            <span>Sepette bekleyen ürün bulunmuyor</span>
                        @endif
                    @else
                        @if(($liveCartCount ?? 0) > 0)
                            <strong style="color: #cbd5e1;">{{ $liveCartCount }} canlı sepette</strong> ödeme bekleniyor
                        @elseif(($recentCartCount ?? 0) > 0)
                            <strong style="color: #cbd5e1;">{{ $recentCartCount }} sepette (24s)</strong> {{ number_format($recentCartTotal ?? $cartTotal, 2) }} ₺ bekliyor
                        @elseif(($cartCount ?? 0) > 0)
                            <strong style="color: #cbd5e1;">{{ $cartCount }} sepette</strong> ürün ödeme bekliyor
                        @else
                            <span>Sepette bekleyen ürün bulunmuyor</span>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Kart 3: Sıcak Satın Alma Adayları --}}
            <div wire:click="setCardFilter('high_intent')" class="war-card {{ ($activeCardFilter ?? 'all') === 'high_intent' ? 'is-active-filter' : '' }}" title="Sıcak satın alma adaylarını filtrelemek için tıklayın">
                <div class="war-header-row">
                    <span class="war-label">Sıcak Adaylar</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'high_intent')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 15px;">🔥</span>
                    </div>
                </div>
                <div class="war-value">
                    <span>{{ $highIntentCount ?? 0 }}</span>
                    <span style="font-size: 12px; color: #94a3b8; font-weight: 600;">
                        @if(($activeTab ?? 'live') === 'recent')
                            {{ ($highIntentCount ?? 0) > 0 ? '24s Adayı' : 'Müşteri' }}
                        @else
                            {{ ($liveHighIntentCount ?? 0) > 0 ? 'Canlı Aday' : (($recentHighIntentCount ?? 0) > 0 ? '24s Adayı' : 'Müşteri') }}
                        @endif
                    </span>
                </div>
                <div class="war-subtext">
                    @if(($activeTab ?? 'live') === 'recent')
                        Satın alma niyeti <strong style="color: #cbd5e1;">%60 ve üzeri</strong> (Son 24s)
                    @elseif(($liveHighIntentCount ?? 0) > 0)
                        Satın alma niyeti <strong style="color: #cbd5e1;">%60 ve üzeri</strong> (Canlı)
                    @elseif(($recentHighIntentCount ?? 0) > 0)
                        Son 24 saatte <strong style="color: #cbd5e1;">{{ $recentHighIntentCount }} sıcak aday</strong> tespit edildi
                    @else
                        Satın alma niyeti <strong style="color: #cbd5e1;">%60 ve üzeri</strong> adaylar
                    @endif
                </div>
            </div>

            {{-- Kart 4: Tereddütte Olanlar --}}
            <div wire:click="setCardFilter('hesitating')" class="war-card {{ ($activeCardFilter ?? 'all') === 'hesitating' ? 'is-active-filter' : '' }}" title="Tereddüt yaşayanları filtrelemek için tıklayın">
                <div class="war-header-row">
                    <span class="war-label">Tereddütte Olanlar</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'hesitating')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 15px;">🤔</span>
                    </div>
                </div>
                <div class="war-value">
                    <span>{{ $hesitatingCount ?? 0 }}</span>
                    <span style="font-size: 12px; color: #94a3b8; font-weight: 600;">
                        @if(($activeTab ?? 'live') === 'recent')
                            {{ ($hesitatingCount ?? 0) > 0 ? '24s Tespit' : 'Tespit Edildi' }}
                        @else
                            {{ ($liveHesitatingCount ?? 0) > 0 ? 'Canlı Müdahale' : (($recentHesitatingCount ?? 0) > 0 ? '24s Tespit' : 'Tespit Edildi') }}
                        @endif
                    </span>
                </div>
                <div class="war-subtext">
                    @if(($activeTab ?? 'live') === 'recent')
                        Beden veya kargo bariyeri yaşayan son ziyaretçiler
                    @elseif(($liveHesitatingCount ?? 0) > 0)
                        Beden veya kargo bariyeri (Hızlı indirim önerilir)
                    @elseif(($recentHesitatingCount ?? 0) > 0)
                        Son 24 saatte <strong style="color: #cbd5e1;">{{ $recentHesitatingCount }} tereddüt</strong> tespit edildi
                    @else
                        <span>Beden veya kargo bariyeri algılandı</span>
                    @endif
                </div>
            </div>

            {{-- Kart 5: Üye / Misafir Oranı --}}
            <div wire:click="setCardFilter('members')" class="war-card {{ ($activeCardFilter ?? 'all') === 'members' ? 'is-active-filter' : '' }}" title="Üye girişli kullanıcıları filtrelemek için tıklayın">
                <div class="war-header-row">
                    <span class="war-label">Kullanıcı Segmenti</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if(($activeCardFilter ?? 'all') === 'members')
                            <span class="war-card-active-pill">FİLTRE</span>
                        @endif
                        <span style="font-size: 15px;">👤</span>
                    </div>
                </div>
                <div class="war-value" style="font-size: 19px;">
                    <span>{{ $membersCount ?? 0 }} Üye</span>
                    <span style="font-size: 13px; color: #64748b;">/</span>
                    <span style="color: #94a3b8; font-size: 17px;">{{ $guestsCount ?? 0 }} Misafir</span>
                </div>
                <div class="war-subtext">
                    Giriş oranı: <strong style="color: #cbd5e1;">%{{ $loginRate ?? 0 }}</strong> {{ (($activeTab ?? 'live') === 'live' && ($onlineCount ?? 0) > 0) ? '(Canlı)' : '(24s / Genel)' }}
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

        {{-- 2. CANLI & SON ZİYARET EDENLER SEKME GEÇİŞ ÇUBUĞU --}}
        <div class="av-tabs-nav-bar">
            <a href="{{ route('filament.admin.pages.canli-ziyaretciler') }}" 
               wire:click.prevent="setActiveTab('live')" 
               class="av-tab-nav-btn {{ ($activeTab ?? 'live') === 'live' ? 'is-active-tab is-live' : '' }}">
                <span class="live-radar-dot" style="width: 8px; height: 8px;"></span>
                <span>🟢 Canlı Yayındakiler</span>
                <span class="av-tab-nav-badge {{ ($activeTab ?? 'live') === 'live' ? 'is-badge-live' : '' }}">
                    {{ $onlineCount }}
                </span>
            </a>

            <a href="{{ route('filament.admin.pages.son-ziyaret-edenler') }}" 
               wire:click.prevent="setActiveTab('recent')" 
               class="av-tab-nav-btn {{ ($activeTab ?? 'live') === 'recent' ? 'is-active-tab is-recent' : '' }}">
                <span>⏱️ Son Ziyaret Edenler (Ayrılanlar)</span>
                <span class="av-tab-nav-badge {{ ($activeTab ?? 'live') === 'recent' ? 'is-badge-recent' : '' }}">
                    {{ $recentCount ?? ($recentLeftCount ?? 0) }}
                </span>
            </a>
        </div>

        {{-- 2.1. CANLI / GEÇMİŞ İSTİHBARAT BİLGİ & DURUM ÇUBUĞU --}}
        <div class="command-bar">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-size: 18px;">{{ ($activeTab ?? 'live') === 'recent' ? '⏱️' : '📡' }}</span>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #f8fafc;">
                        {{ ($activeTab ?? 'live') === 'recent' ? 'Son Ziyaret Edenler (Siteden Ayrılan Müşteriler)' : 'Canlı Satış İstihbaratı & Müşteri Karar Radarı' }}
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">
                        {{ ($activeTab ?? 'live') === 'recent' ? 'Sitede gezinip son 24 saat içinde ayrılmış olan ziyaretçiler, ayrılma süreleri, gezdikleri sayfalar ve sepetleri listelenir.' : 'Şu an sitede aktif olarak bulunan canlı ziyaretçiler listelenir; beden seçimleri ve niyet puanları anlık izlenir.' }}
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
                @if(($activeTab ?? 'live') === 'recent')
                    <span style="font-size: 11px; color: #f59e0b; font-weight: 700; font-family: monospace;">⏱️ SON 24 SAATİN KAYITLARI</span>
                @else
                    <span class="live-radar-dot"></span>
                    <span style="font-size: 11px; color: #10b981; font-weight: 700; font-family: monospace;">CANLI RADAR AKTİF (5sn)</span>
                @endif
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

</x-filament-panels::page>
