<style>
/* ==========================================================================
   PATENLİ AYAKKABILAR — FILAMENT V5 COMPREHENSIVE RESPONSIVE ENGINE
   (Masaüstü, Tablet & Mobil Telefon Tam Uyumluluk)
   ========================================================================== */

/* 1. GENEL RESET & DOKUNMATİK ENTEGRASYONU */
html, body {
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
    max-width: 100vw;
}

/* ==========================================================================
   2. MASAÜSTÜ & BÜYÜK EKRANLAR (min-width: 1024px)
   ========================================================================== */
@media (min-width: 1024px) {
    /* Masaüstü Tablo Konteyneri */
    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-content-ctn,
    .fi-ta-table-container,
    .fi-ta-table-ctn {
        overflow-x: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        scrollbar-width: thin !important;
    }

    .fi-ta-table {
        width: 100% !important;
        min-width: 100% !important;
        table-layout: auto !important;
    }

    /* Masaüstü Başlıklar & Hücreler */
    .fi-ta-header-cell {
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.04em !important;
        text-transform: uppercase !important;
        padding: 12px 10px !important;
        white-space: nowrap !important;
    }

    .fi-ta-cell {
        font-size: 0.85rem !important;
        padding: 10px 10px !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .fi-ta-cell .fi-badge {
        font-size: 0.75rem !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
    }

    .fi-ta-row {
        transition: background-color 0.15s ease !important;
    }

    .fi-ta-row:hover {
        background-color: rgba(241, 245, 249, 0.7) !important;
    }

    .dark .fi-ta-row:hover {
        background-color: rgba(30, 41, 59, 0.5) !important;
    }

    /* Masaüstü Aksiyon Butonları */
    td.fi-ta-actions-cell,
    .fi-ta-actions-cell {
        width: auto !important;
        white-space: nowrap !important;
        padding: 8px 12px !important;
    }

    .fi-ta-actions {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        justify-content: flex-end !important;
        width: auto !important;
    }

    .fi-ta-actions button,
    .fi-ta-actions a,
    .fi-ta-actions .fi-icon-btn {
        padding: 5px !important;
        border-radius: 8px !important;
        transition: transform 0.15s ease, background-color 0.15s ease !important;
    }

    .fi-ta-actions .fi-icon-btn:hover {
        transform: scale(1.1) !important;
    }

    .fi-ta-actions svg {
        width: 18px !important;
        height: 18px !important;
    }

    /* Masaüstü Üst Araç Çubuğu */
    .fi-ta-header-toolbar {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        padding: 12px 16px !important;
    }

    .fi-ta-search-field {
        min-width: 280px !important;
        max-width: 420px !important;
    }

    .fi-ta-search-field input {
        font-size: 14px !important;
        height: 38px !important;
        border-radius: 8px !important;
    }

    /* Masaüstü Dashboard & Stats Overview Grid */
    .fi-wi-stats-overview {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 16px !important;
    }

    .fi-wi-stats-overview-stat {
        padding: 18px 20px !important;
        border-radius: 14px !important;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
    }

    .dark .fi-wi-stats-overview-stat:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35) !important;
    }

    /* Masaüstü Modallar & Kartlar */
    .fi-section {
        border-radius: 14px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        margin-bottom: 20px !important;
    }

    .fi-modal-window {
        border-radius: 16px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        max-height: 90vh !important;
    }

    .fi-modal-content,
    .fi-modal-body {
        max-height: calc(85vh - 130px) !important;
        scrollbar-width: thin !important;
    }

    /* Masaüstü Kaydırma Çubuğu */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.35);
        border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(100, 116, 139, 0.6);
    }
    .dark ::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.22);
    }
    .dark ::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.45);
    }
}

/* ==========================================================================
   3. TABLET MODU (min-width: 769px and max-width: 1023px)
   (iPad, iPad Air, Galaxy Tab vb.)
   ========================================================================== */
@media (min-width: 769px) and (max-width: 1023px) {
    /* Tablet Tablo Düzeni: Akıcı dokunmatik yatay kaydırma */
    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-content-ctn,
    .fi-ta-table-container,
    .fi-ta-table-ctn {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-x pan-y !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 12px !important;
        scrollbar-width: thin !important;
    }

    .fi-ta-table {
        min-width: 680px !important;
        width: 100% !important;
        table-layout: auto !important;
    }

    /* Tablet Tablo Hücreleri: Dengeli ve ferah */
    .fi-ta-header-cell {
        font-size: 0.74rem !important;
        font-weight: 700 !important;
        padding: 10px 8px !important;
        white-space: nowrap !important;
    }

    .fi-ta-cell {
        font-size: 0.82rem !important;
        padding: 9px 8px !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .fi-ta-cell .fi-badge {
        font-size: 0.72rem !important;
        padding: 2px 7px !important;
    }

    /* Tablet Aksiyon Butonları */
    td.fi-ta-actions-cell,
    .fi-ta-actions-cell {
        width: auto !important;
        white-space: nowrap !important;
        padding: 8px 10px !important;
    }

    .fi-ta-actions {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        justify-content: flex-end !important;
    }

    .fi-ta-actions button,
    .fi-ta-actions a,
    .fi-ta-actions .fi-icon-btn {
        padding: 5px !important;
        border-radius: 8px !important;
    }

    .fi-ta-actions svg {
        width: 17px !important;
        height: 17px !important;
    }

    /* Tablet Üst Araç Çubuğu */
    .fi-ta-header-toolbar {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
        padding: 10px 14px !important;
    }

    .fi-ta-search-field {
        min-width: 240px !important;
        max-width: 340px !important;
    }

    /* Tablet Dashboard Grid (2 Kolon) */
    .fi-wi-stats-overview {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 14px !important;
    }

    .fi-wi-stats-overview-stat {
        padding: 16px 18px !important;
        border-radius: 12px !important;
    }

    /* Tablet Modalları */
    .fi-modal-window {
        width: min(92vw, 760px) !important;
        max-width: min(92vw, 760px) !important;
        border-radius: 16px !important;
        max-height: 90vh !important;
    }

    .fi-modal-content,
    .fi-modal-body {
        max-height: calc(88vh - 130px) !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    /* Tablet Sidebar Çekmecesi */
    aside.fi-sidebar,
    .fi-sidebar {
        width: min(88vw, 480px) !important;
        max-width: 480px !important;
        height: 100dvh !important;
    }
}

/* ==========================================================================
   4. MOBİL TELEFON MODU (max-width: 768px)
   ========================================================================== */
@media (max-width: 768px) {
    /* Mobilde takılı kalan tippy tooltip balonlarını gizleme */
    .tippy-box,
    [data-tippy-root],
    .fi-tooltip {
        display: none !important;
    }

    /* Kopyalama ve özel toast'lar için alt boşluk */
    div[x-show="copyToast"] {
        bottom: 84px !important;
        right: 16px !important;
    }

    /* Mobil Tablolar: Dokunmatik yatay kaydırma */
    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-content-ctn,
    .fi-ta-table-container,
    .fi-ta-table-ctn {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-x pan-y !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 12px !important;
        position: relative;
        scrollbar-width: thin !important;
    }

    /* Tablo içeriğinin ezilmesini önleyen minimum genişlik */
    .fi-ta-table {
        min-width: 580px !important;
        width: 100% !important;
        zoom: 1 !important;
    }

    .fi-ta-header-cell {
        font-size: 0.72rem !important;
        padding: 8px 6px !important;
        white-space: nowrap !important;
    }

    .fi-ta-cell {
        font-size: 0.8rem !important;
        padding: 8px 6px !important;
        white-space: nowrap !important;
    }

    .fi-ta-cell .fi-badge {
        font-size: 0.7rem !important;
        padding: 2px 6px !important;
    }

    .fi-ta-actions button,
    .fi-ta-actions a,
    .fi-ta-actions .fi-icon-btn {
        padding: 4px !important;
    }

    .fi-ta-actions svg {
        width: 16px !important;
        height: 16px !important;
    }

    td.fi-ta-actions-cell,
    .fi-ta-actions-cell {
        width: 1px !important;
        white-space: nowrap !important;
        padding-left: 6px !important;
        padding-right: 6px !important;
    }

    .fi-ta-actions {
        display: flex !important;
        justify-content: flex-start !important;
        align-items: center !important;
        gap: 4px !important;
        width: auto !important;
    }

    /* Tablo Üst Araç Çubuğu (Arama & Filtreler) */
    .fi-ta-header-toolbar {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
        width: 100% !important;
    }

    .fi-ta-search-field {
        width: 100% !important;
        max-width: 100% !important;
    }

    .fi-ta-search-field input {
        font-size: 15px !important;
        height: 40px !important;
        border-radius: 10px !important;
    }

    .fi-ta-header-toolbar .fi-ta-actions {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        justify-content: flex-start !important;
        width: 100% !important;
    }

    /* Mobil Sayfalama */
    .fi-ta-pagination {
        padding: 10px 12px !important;
        flex-direction: column !important;
        gap: 8px !important;
    }

    .fi-pagination-nav {
        width: 100% !important;
        justify-content: space-between !important;
    }

    /* Mobil Sidebar Çekmecesi: Tam Ekrana Sığdırma Motoru */
    .fi-sidebar-close-overlay {
        background: rgba(0, 0, 0, 0.65) !important;
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
    }

    aside.fi-sidebar,
    .fi-sidebar {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        inset-inline-start: 0 !important;
        inset-inline-end: 0 !important;
        box-shadow: none !important;
        background: #ffffff !important;
        display: flex !important;
        flex-direction: column !important;
        z-index: 9998 !important;
    }

    .dark aside.fi-sidebar,
    .dark .fi-sidebar {
        background: #0f172a !important;
    }

    .fi-sidebar-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 14px 20px !important;
        height: 64px !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        flex-shrink: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .dark .fi-sidebar-header {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-sidebar-header-logo-ctn {
        display: flex !important;
        align-items: center !important;
    }

    .fi-sidebar-nav {
        flex-grow: 1 !important;
        height: calc(100dvh - 64px) !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        padding: 16px 18px 110px 18px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .fi-sidebar-nav-groups {
        display: flex !important;
        flex-direction: column !important;
        gap: 14px !important;
        width: 100% !important;
        max-width: 600px !important;
        margin: 0 auto !important;
    }

    .fi-sidebar-group {
        width: 100% !important;
    }

    .fi-sidebar-group-label {
        font-size: 0.78rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        color: #94a3b8 !important;
        padding: 6px 12px !important;
        margin-bottom: 4px !important;
    }

    .fi-sidebar-group-items {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
        width: 100% !important;
    }

    .fi-sidebar-item {
        width: 100% !important;
    }

    .fi-sidebar-item-btn {
        width: 100% !important;
        min-height: 48px !important;
        padding: 10px 14px !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        transition: background-color 0.15s ease, transform 0.1s ease !important;
    }

    .fi-sidebar-item-btn:active {
        transform: scale(0.98) !important;
    }

    .fi-sidebar-item-label {
        font-size: 0.95rem !important;
        flex-grow: 1 !important;
        white-space: normal !important;
        line-height: 1.3 !important;
    }

    .fi-sidebar-item-badge {
        margin-left: auto !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        padding: 2px 8px !important;
        border-radius: 9999px !important;
    }

    /* Mobil Formlar */
    .fi-fo-field-wrp {
        grid-column: span 12 / span 12 !important;
        width: 100% !important;
    }

    .fi-section {
        padding: 14px 14px !important;
        border-radius: 14px !important;
        margin-bottom: 14px !important;
    }

    .fi-section-header {
        padding-bottom: 8px !important;
    }

    /* Form & Tab Bar: Mobilde parmakla yatay kaydırma */
    .fi-tabs {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        gap: 6px !important;
        padding-bottom: 6px !important;
        scrollbar-width: none !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
    }

    .dark .fi-tabs {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-tabs::-webkit-scrollbar {
        display: none !important;
    }

    .fi-tabs-item {
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        font-size: 0.82rem !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
    }

    /* RichEditor toolbar */
    .fi-fo-rich-editor-toolbar {
        flex-wrap: wrap !important;
        gap: 4px !important;
    }

    .fi-fo-file-upload {
        width: 100% !important;
    }

    .fi-form-actions {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
        width: 100% !important;
    }

    .fi-form-actions button,
    .fi-form-actions a {
        width: 100% !important;
        justify-content: center !important;
        min-height: 44px !important;
    }

    /* Mobil Modallar (Tam Ekran Kart) */
    .fi-modal,
    [role="dialog"],
    .fi-modal-window-container,
    .fi-modal-window-ctn {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .fi-modal-window {
        width: calc(100vw - 16px) !important;
        max-width: calc(100vw - 16px) !important;
        min-width: 0 !important;
        margin: 10px auto !important;
        border-radius: 16px !important;
        max-height: 92dvh !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        zoom: 1 !important;
    }

    .fi-modal-header {
        padding: 12px 14px !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
        flex-shrink: 0 !important;
    }

    .dark .fi-modal-header {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-modal-content,
    .fi-modal-body {
        padding: 14px 14px !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        flex-grow: 1 !important;
        max-height: calc(92dvh - 120px) !important;
    }

    .fi-modal-footer {
        padding: 10px 14px !important;
        border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
        flex-shrink: 0 !important;
        gap: 8px !important;
    }

    .dark .fi-modal-footer {
        border-top-color: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-in-entry-wrp {
        grid-column: span 12 / span 12 !important;
    }
}

/* ==========================================================================
   5. MOBİL BAŞLIK & TOOLBAR TEMİZLİĞİ (max-width: 640px)
   ========================================================================== */
@media (max-width: 640px) {
    /* Mobilde ekrandan çok yer çalan gereksiz breadcrumbs'ı gizle */
    .fi-breadcrumbs {
        display: none !important;
    }

    /* Sayfa Başlığı ve Butonları */
    .fi-header {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
        margin-bottom: 12px !important;
    }

    .fi-header-heading {
        font-size: 1.25rem !important;
        line-height: 1.25 !important;
        font-weight: 800 !important;
    }

    .fi-header-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        width: 100% !important;
    }

    .fi-header-actions button,
    .fi-header-actions a {
        flex: 1 1 auto !important;
        min-height: 40px !important;
        justify-content: center !important;
        font-size: 0.85rem !important;
    }

    /* Topbar yüksekliği */
    .fi-topbar {
        height: 52px !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    /* Dashboard Stats Kartları: 2 kolonlu */
    .fi-wi-stats-overview {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px !important;
    }

    .fi-wi-stats-overview-stat {
        padding: 12px 14px !important;
        border-radius: 12px !important;
    }

    .fi-wi-stats-overview-stat-value {
        font-size: 1.3rem !important;
        line-height: 1.25 !important;
    }

    .fi-wi-stats-overview-stat-label {
        font-size: 0.72rem !important;
    }
}

/* ==========================================================================
   6. KÜÇÜK TELEFON MODU (max-width: 420px)
   (iPhone SE, Küçük Android Cihazlar)
   ========================================================================== */
@media (max-width: 420px) {
    .fi-wi-stats-overview {
        grid-template-columns: 1fr !important;
    }

    .fi-mobile-nav-container {
        height: 52px !important;
        padding: 0 2px !important;
    }

    .fi-mobile-nav-label {
        font-size: 9px !important;
    }

    .fi-mobile-nav-icon {
        width: 20px !important;
        height: 20px !important;
    }
}

/* ==========================================================================
   7. DOKUNMATİK & ORTAK MOBİL-TABLET KURALLARI (max-width: 1023px)
   ========================================================================== */
.fi-mobile-bottom-bar,
.fi-sidebar-mobile-close-btn {
    display: none !important;
}

@media (max-width: 1023px) {
    .fi-sidebar-mobile-close-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        border-radius: 10px !important;
        border: none !important;
        background: rgba(0, 0, 0, 0.05) !important;
        color: #475569 !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        margin-left: auto !important;
    }

    .dark .fi-sidebar-mobile-close-btn {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #cbd5e1 !important;
    }

    .fi-sidebar-mobile-close-btn:active {
        transform: scale(0.92) !important;
    }
    /* Alt navigasyon barı için içerik alt boşluğu */
    body,
    .fi-main,
    .fi-main-ctn,
    main.fi-main {
        padding-bottom: 84px !important;
    }

    /* Bildirim balonlarının alt bar üstünde durması */
    .fi-no.fi-vertical-align-bottom {
        bottom: 84px !important;
    }

    /* iOS Safari otomatik zoom'u engelleme: yazı tipi en az 16px */
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="tel"],
    input[type="password"],
    input[type="search"],
    input[type="url"],
    select,
    textarea,
    .fi-input,
    .fi-select-input {
        font-size: 16px !important;
    }

    /* Alt Navigasyon Barı (Bottom Bar) */
    .fi-mobile-bottom-bar {
        display: block !important;
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 9995 !important;
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08) !important;
        padding-bottom: max(env(safe-area-inset-bottom, 0px), 8px) !important;
        pointer-events: auto !important;
    }

    .dark .fi-mobile-bottom-bar {
        background: rgba(17, 24, 39, 0.96) !important;
        border-top-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.4) !important;
    }

    .fi-mobile-nav-container {
        display: flex !important;
        align-items: center !important;
        justify-content: space-around !important;
        height: 56px !important;
        max-width: 520px !important;
        margin: 0 auto !important;
        padding: 0 4px !important;
        box-sizing: border-box !important;
        user-select: none !important;
        -webkit-user-select: none !important;
    }

    .fi-mobile-nav-item {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 1 1 0 !important;
        height: 100% !important;
        padding: 4px 0 !important;
        text-decoration: none !important;
        cursor: pointer !important;
        touch-action: manipulation !important;
        -webkit-tap-highlight-color: transparent !important;
        color: #64748b !important;
        background: transparent !important;
        border: none !important;
        position: relative !important;
        transition: transform 0.15s ease, color 0.15s ease !important;
        outline: none !important;
    }

    .dark .fi-mobile-nav-item {
        color: #94a3b8 !important;
    }

    .fi-mobile-nav-item:active {
        transform: scale(0.9) !important;
    }

    .fi-mobile-nav-item.is-active {
        color: #ff4e00 !important;
    }

    .dark .fi-mobile-nav-item.is-active {
        color: #ff4e00 !important;
    }

    .fi-mobile-nav-icon-wrapper {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fi-mobile-nav-icon {
        width: 22px !important;
        height: 22px !important;
        transition: transform 0.15s ease !important;
    }

    .fi-mobile-nav-item.is-active .fi-mobile-nav-icon {
        transform: scale(1.08) !important;
    }

    .fi-mobile-nav-label {
        font-size: 10px !important;
        font-weight: 600 !important;
        letter-spacing: -0.01em !important;
        line-height: 1.1 !important;
        margin-top: 3px !important;
        white-space: nowrap !important;
    }

    .fi-mobile-nav-item.is-active .fi-mobile-nav-label {
        font-weight: 800 !important;
    }

    .fi-mobile-nav-badge {
        position: absolute !important;
        top: -6px !important;
        right: -10px !important;
        min-width: 17px !important;
        height: 17px !important;
        padding: 0 4px !important;
        background: #ff4e00 !important;
        color: #ffffff !important;
        font-size: 9px !important;
        font-weight: 900 !important;
        border-radius: 9999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 2px 5px rgba(255, 78, 0, 0.4) !important;
        animation: fi-pulse 2s infinite ease-in-out !important;
    }

    .fi-mobile-nav-indicator {
        position: absolute !important;
        bottom: 2px !important;
        width: 16px !important;
        height: 2.5px !important;
        background: #ff4e00 !important;
        border-radius: 9999px !important;
    }
}

@keyframes fi-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
</style>
