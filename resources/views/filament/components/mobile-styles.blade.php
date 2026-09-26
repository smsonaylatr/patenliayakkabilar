<style>
/* ==========================================================================
   PATENLİ AYAKKABILAR — FILAMENT V5 ADMIN FULL MOBİL RESPONSIVE ENGINE
   ========================================================================== */

/* 1. GENEL MOBİL & TOUCH ENTEGRASYONU */
html, body {
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

@media (max-width: 768px) {
    /* Alt navigasyon barı için içerik alt boşluğu */
    body,
    .fi-main,
    .fi-main-ctn,
    main.fi-main {
        padding-bottom: 84px !important;
    }

    /* Bildirim balonlarının alt navigasyon barı üstünde durması */
    .fi-no.fi-vertical-align-bottom {
        bottom: 84px !important;
    }

    /* Kopyalama ve özel toast'lar için alt boşluk */
    div[x-show="copyToast"] {
        bottom: 84px !important;
        right: 16px !important;
    }

    /* Mobilde takılı kalan tippy tooltip balonlarını gizleme */
    .tippy-box,
    [data-tippy-root],
    .fi-tooltip {
        display: none !important;
    }
}

/* 2. MASAÜSTÜ TABLO DÜZENİ (Mevcut kararlılığı koruma) */
@media (min-width: 769px) {
    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-content-ctn,
    .fi-ta-table-container {
        overflow-x: hidden !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .fi-ta-table {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        table-layout: auto !important;
    }
}

/* 3. TABLO GENEL HÜCRE & AKSİYON DÜZENİ */
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
    padding: 3px !important;
}

.fi-ta-actions svg {
    width: 16px !important;
    height: 16px !important;
}

/* Aksiyon Butonlarını Sola Yanaştırma */
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

/* 4. MOBİL TABLOLAR (Siparişler, Ürünler, vb.) */
@media (max-width: 768px) {
    /* Dokunmatik yatay kaydırma konteyneri */
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

    /* Tablo genişliği: squish olmasını önler, akıcı dokunmatik kaydırma sağlar */
    .fi-ta-table {
        min-width: 580px !important;
        width: 100% !important;
        zoom: 1 !important; /* iOS koordinat kaymalarını önleme */
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

    .fi-ta-actions-ctn,
    .fi-ta-header-toolbar .fi-ta-actions {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        justify-content: flex-start !important;
        width: 100% !important;
    }

    /* Mobilde sayfalama butonu optimizasyonu */
    .fi-ta-pagination {
        padding: 10px 12px !important;
        flex-direction: column !important;
        gap: 8px !important;
    }

    .fi-pagination-nav {
        width: 100% !important;
        justify-content: space-between !important;
    }
}

/* 5. SIDEBAR (MOBİL ÇEKMECE MENÜSÜ) */
@media (max-width: 768px) {
    .fi-sidebar-close-overlay {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
    }

    aside.fi-sidebar {
        width: min(84vw, 320px) !important;
        box-shadow: 4px 0 25px rgba(0, 0, 0, 0.25) !important;
    }

    /* Menü linkleri dokunma alanı */
    .fi-sidebar-item-btn {
        min-height: 44px !important;
        font-size: 0.88rem !important;
        padding-top: 8px !important;
        padding-bottom: 8px !important;
        border-radius: 10px !important;
    }
}

/* 7. MOBİL FORMLAR & GİRİŞ ALANLARI */
@media (max-width: 768px) {
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

    /* Form grid kolonlarını tek kolona sığdırma */
    .fi-fo-field-wrp {
        grid-column: span 12 / span 12 !important;
        width: 100% !important;
    }

    /* Form bölümlerinde mobil iç dolgu */
    .fi-section {
        padding: 14px 14px !important;
        border-radius: 14px !important;
        margin-bottom: 14px !important;
    }

    .fi-section-header {
        padding-bottom: 8px !important;
    }

    /* Form Tab'ları: Mobilde satır kırmaz, parmakla yatay kaydırılır */
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

    /* RichEditor toolbar yatay taşmasını önleme */
    .fi-fo-rich-editor-toolbar {
        flex-wrap: wrap !important;
        gap: 4px !important;
    }

    /* FileUpload mobil düzeni */
    .fi-fo-file-upload {
        width: 100% !important;
    }

    /* Form aksiyon butonları */
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
}

/* 8. MODALLAR (DETAY & POPUP PENCERELERİ) */
@media (max-width: 768px) {
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
}

/* 9. STATS OVERVIEW & DASHBOARD WIDGETLARI */
@media (max-width: 640px) {
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

@media (max-width: 420px) {
    .fi-wi-stats-overview {
        grid-template-columns: 1fr !important;
    }
}

/* 10. INFOLIST MOBİL UYUMLULUĞU */
@media (max-width: 768px) {
    .fi-in-entry-wrp {
        grid-column: span 12 / span 12 !important;
    }
}

/* 11. MOBİL ALT NAVİGASYON BARI (BOTTOM BAR) */
.fi-mobile-bottom-bar {
    display: none !important;
}

@media (max-width: 768px) {
    .fi-mobile-bottom-bar {
        display: block !important;
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 9995 !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08) !important;
        padding-bottom: max(env(safe-area-inset-bottom, 0px), 8px) !important;
        pointer-events: auto !important;
    }

    .dark .fi-mobile-bottom-bar {
        background: rgba(17, 24, 39, 0.95) !important;
        border-top-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.4) !important;
    }

    .fi-mobile-nav-container {
        display: flex !important;
        align-items: center !important;
        justify-content: space-around !important;
        height: 56px !important;
        max-width: 480px !important;
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
