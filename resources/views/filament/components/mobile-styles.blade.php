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

/* 5. TOPBAR (ÜST BAR) MOBİL DÜZENİ */
@media (max-width: 768px) {
    .fi-topbar {
        height: 58px !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .fi-topbar-start {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }

    .fi-topbar-start a,
    .fi-logo {
        max-width: 180px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        font-size: 1.05rem !important;
        font-weight: 800 !important;
    }

    .fi-topbar-end {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-shrink: 0 !important;
    }

    /* Mobilde buton dokunma alanı (minimum 38x38px) */
    .fi-topbar-end button,
    .fi-topbar-end a,
    .fi-topbar-start button,
    .fi-topbar-reload-btn button {
        min-width: 38px !important;
        min-height: 38px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Global arama mobilde taşma yapmaz */
    .fi-global-search-ctn {
        max-width: 130px !important;
    }
}

/* 6. SIDEBAR (MOBİL ÇEKMECE MENÜSÜ) */
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
</style>
