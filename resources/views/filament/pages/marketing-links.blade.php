@php
    $metaTag = '{' . '{campaign.name}' . '}';
    $googleTag = '{' . '{campaignid}' . '}';
@endphp

<style>
/* ========================================================================== */
/* FİLAMEN V5 TEMASINA & OUTFIT YAZI TİPİNE %100 UYUMLU PAZARLAMA SAYFASI      */
/* ========================================================================== */

@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap');

.ml-wrapper {
    font-family: 'Outfit', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    display: flex;
    flex-direction: column;
    gap: 18px;
    width: 100%;
    color: #0f172a;
    box-sizing: border-box;
}

.dark .ml-wrapper {
    color: #f8fafc;
}

/* 1. Bilgi Notu (Filament Callout / Alert Stili) */
.ml-notice {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 16px;
    border-radius: 10px;
    background: #fff7ed;
    border: 1px solid #ffedd5;
    color: #9a3412;
    font-size: 0.83rem;
    line-height: 1.45;
}

.dark .ml-notice {
    background: rgba(255, 78, 0, 0.07);
    border-color: rgba(255, 78, 0, 0.2);
    color: #fdba74;
}

.ml-notice-icon {
    width: 18px;
    height: 18px;
    min-width: 18px;
    max-width: 18px;
    color: #ff4e00;
    flex-shrink: 0;
    margin-top: 2px;
}

/* 2. Filament Bölüm Kartı (Section / Card Stili) */
.ml-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.dark .ml-section {
    background: #111827;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: none;
}

.ml-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 12px;
}

.dark .ml-section-header {
    border-bottom-color: rgba(255, 255, 255, 0.06);
}

.ml-section-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ml-section-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 78, 0, 0.1);
    color: #ff4e00;
}

.dark .ml-section-icon {
    background: rgba(255, 78, 0, 0.15);
    color: #ff6b2b;
}

.ml-section-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.01em;
}

.dark .ml-section-title {
    color: #ffffff;
}

.ml-section-desc {
    font-size: 0.77rem;
    color: #64748b;
    margin: 2px 0 0 0;
}

.dark .ml-section-desc {
    color: #94a3b8;
}

/* Filament Rozetleri (Badges) */
.ml-badge {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 9999px;
    letter-spacing: 0.02em;
}

.ml-badge-primary {
    background: rgba(255, 78, 0, 0.12);
    color: #ea580c;
    border: 1px solid rgba(255, 78, 0, 0.25);
}

.dark .ml-badge-primary {
    background: rgba(255, 78, 0, 0.18);
    color: #ff7836;
    border-color: rgba(255, 78, 0, 0.35);
}

.ml-badge-gray {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.dark .ml-badge-gray {
    background: rgba(255, 255, 255, 0.06);
    color: #cbd5e1;
    border-color: rgba(255, 255, 255, 0.1);
}

/* Izgara Düzenleri */
.ml-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}

.ml-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}

.ml-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

@media (max-width: 900px) {
    .ml-grid-2, .ml-grid-3, .ml-grid-4 {
        grid-template-columns: 1fr;
    }
}

/* İç Kutu (Item Box) */
.ml-item-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 10px;
    transition: all 0.2s ease;
}

.dark .ml-item-box {
    background: rgba(255, 255, 255, 0.025);
    border-color: rgba(255, 255, 255, 0.07);
}

.ml-item-box:hover {
    border-color: #cbd5e1;
}

.dark .ml-item-box:hover {
    border-color: rgba(255, 255, 255, 0.15);
}

.ml-item-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.ml-item-title {
    font-size: 0.86rem;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}

.dark .ml-item-title {
    color: #f8fafc;
}

.ml-item-help {
    font-size: 0.75rem;
    color: #64748b;
    margin: 3px 0 0 0;
    line-height: 1.35;
}

.dark .ml-item-help {
    color: #94a3b8;
}

/* URL / Kod Gösterim Kutusu */
.ml-code-input {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 12px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.74rem;
    color: #334155;
    word-break: break-all;
    user-select: all;
    line-height: 1.4;
}

.dark .ml-code-input {
    background: rgba(0, 0, 0, 0.35);
    border-color: rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
}

/* Filament Standart Buton Stilleri */
.ml-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 35px;
    padding: 0 14px;
    border-radius: 8px;
    font-family: 'Outfit', sans-serif !important;
    font-size: 0.79rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
    text-decoration: none;
    white-space: nowrap;
}

.ml-btn-primary {
    background-color: #ff4e00;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(255, 78, 0, 0.2);
}

.ml-btn-primary:hover {
    background-color: #e04500;
    color: #ffffff;
}

.ml-btn-primary:active {
    transform: translateY(1px);
}

.ml-btn-secondary {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.ml-btn-secondary:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

.dark .ml-btn-secondary {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.12);
    color: #f1f5f9;
    box-shadow: none;
}

.dark .ml-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

.dark .ml-btn-secondary:active {
    transform: translateY(1px);
}

.ml-btn-full {
    width: 100%;
}

/* Form Elemanları (Filament Standart Input & Select) */
.ml-form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.ml-form-label {
    font-size: 0.74rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
}

.dark .ml-form-label {
    color: #cbd5e1;
}

.ml-form-control {
    width: 100%;
    height: 38px;
    padding: 0 12px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #0f172a;
    font-family: 'Outfit', sans-serif !important;
    font-size: 0.84rem;
    outline: none;
    transition: all 0.15s ease;
    box-sizing: border-box;
}

.dark .ml-form-control {
    background: rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.12);
    color: #ffffff;
}

.ml-form-control:focus {
    border-color: #ff4e00;
    box-shadow: 0 0 0 2px rgba(255, 78, 0, 0.25);
}

.ml-form-hint {
    font-size: 0.72rem;
    color: #64748b;
    margin: 2px 0 0 0;
}

.dark .ml-form-hint {
    color: #94a3b8;
}

/* Canlı Üretilen Sonuç Kutusu */
.ml-live-result-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.dark .ml-live-result-box {
    background: rgba(0, 0, 0, 0.25);
    border-color: rgba(255, 255, 255, 0.08);
}

.ml-live-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

@media (max-width: 640px) {
    .ml-live-row {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<x-filament-panels::page>
    <div 
        x-data="{
            copyToast: false,
            toastMsg: '',
            baseUrl: '{{ $baseUrl }}',
            selectedTarget: 'home',
            customPath: '',
            selectedProductSlug: '{{ $products->first()?->slug ?? '' }}',
            selectedChannel: 'ig_story',
            campaignName: '',
            get generatedUrl() {
                let targetUrl = this.baseUrl;
                if (this.selectedTarget === 'product' && this.selectedProductSlug) {
                    targetUrl += '/urun/' + this.selectedProductSlug;
                } else if (this.selectedTarget === 'custom' && this.customPath) {
                    let path = this.customPath.trim();
                    if (!path.startsWith('http')) {
                        if (!path.startsWith('/')) path = '/' + path;
                        targetUrl += path;
                    } else {
                        targetUrl = path;
                    }
                }

                let params = [];
                let cleanCamp = this.campaignName.trim().replace(/\s+/g, '_').toLowerCase();

                switch(this.selectedChannel) {
                    case 'ig_bio':
                        params.push('utm_source=instagram', 'utm_medium=bio');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'ig_story':
                        params.push('utm_source=instagram', 'utm_medium=story');
                        params.push('utm_campaign=' + (cleanCamp || 'story_paylasim'));
                        break;
                    case 'ig_reels':
                        params.push('utm_source=instagram', 'utm_medium=reels');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'meta_ads':
                        params.push('utm_source=instagram', 'utm_medium=cpc');
                        params.push('utm_campaign=' + (cleanCamp || '{{ $metaTag }}'));
                        break;
                    case 'tiktok_bio':
                        params.push('utm_source=tiktok', 'utm_medium=bio');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'tiktok_video':
                        params.push('utm_source=tiktok', 'utm_medium=video');
                        params.push('utm_campaign=' + (cleanCamp || 'kesfet'));
                        break;
                    case 'google_ads':
                        params.push('utm_source=google', 'utm_medium=cpc');
                        params.push('utm_campaign=' + (cleanCamp || '{{ $googleTag }}'));
                        break;
                    case 'whatsapp':
                        params.push('utm_source=whatsapp', 'utm_medium=chat');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'sms':
                        params.push('utm_source=sms', 'utm_medium=bulk_sms');
                        params.push('utm_campaign=' + (cleanCamp || 'firsat'));
                        break;
                    case 'influencer':
                        params.push('utm_source=influencer', 'utm_medium=story');
                        params.push('utm_campaign=' + (cleanCamp || 'isbirligi'));
                        break;
                }

                if (params.length > 0) {
                    let separator = targetUrl.includes('?') ? '&' : '?';
                    return targetUrl + separator + params.join('&');
                }
                return targetUrl;
            },
            copyText(text, label) {
                navigator.clipboard.writeText(text);
                this.toastMsg = (label || 'Link') + ' panoya kopyalandı!';
                this.copyToast = true;
                setTimeout(() => this.copyToast = false, 2500);
            }
        }"
        class="ml-wrapper"
    >
        <!-- Floating Toast Bildirimi -->
        <div 
            x-show="copyToast" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            style="position: fixed; bottom: 28px; right: 28px; z-index: 999999; background: #10b981; color: #ffffff; padding: 10px 20px; border-radius: 10px; font-family: 'Outfit', sans-serif; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); pointer-events: none;"
            x-cloak
        >
            <svg style="width:18px; height:18px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span x-text="toastMsg"></span>
        </div>

        <!-- 1. BİLGİ BANNERI (NASIL ÇALIŞIR?) -->
        <div class="ml-notice">
            <svg class="ml-notice-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong>Bu Takip Linkleri Nasıl Çalışır?</strong><br>
                Aşağıdaki hazır bağlantıları Instagram biyografinizde, hikayelerinizde, Reels videolarınızda veya sponsorlu reklamlarınızda kullandığınızda; tıklayan ziyaretçiler sipariş verdiğinde sistemimiz kaynağı otomatik olarak algılar ve <strong>Siparişler sayfasında</strong> <em>(📸 Instagram, 📱 Mobil, Kampanya Adı)</em> şeklinde anında rozetle gösterir.
            </div>
        </div>

        <!-- 2. INSTAGRAM HAZIR KOPYALAMA KARTLARI -->
        <div class="ml-section">
            <div class="ml-section-header">
                <div class="ml-section-title-group">
                    <div class="ml-section-icon">
                        <svg style="width:20px; height:20px;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.13-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </div>
                    <div>
                        <h3 class="ml-section-title">Instagram İçin Hazır Bağlantılar</h3>
                        <p class="ml-section-desc">Instagram hesabınızda doğrudan kullanabileceğiniz hazır takip linkleri</p>
                    </div>
                </div>
                <span class="ml-badge ml-badge-primary">En Çok Kullanılan</span>
            </div>

            <div class="ml-grid-2">
                <!-- 1. Bio Linki -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>📌</span> Instagram Profil Biyografisi (Bio)
                            </span>
                            <span class="ml-badge ml-badge-gray">Profil</span>
                        </div>
                        <p class="ml-item-help">Instagram profilinizde <strong>Profili Düzenle > Bağlantılar > Web Sitesi</strong> alanına yapıştırın.</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            {{ $baseUrl }}/?utm_source=instagram&amp;utm_medium=bio
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('{{ $baseUrl }}/?utm_source=instagram&utm_medium=bio', 'Instagram Bio Linki')"
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Bio Linkini Kopyala</span>
                    </button>
                </div>

                <!-- 2. Story Linki -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>✨</span> Instagram Hikaye (Story) Linki
                            </span>
                            <span class="ml-badge ml-badge-gray">Hikaye</span>
                        </div>
                        <p class="ml-item-help">Hikaye paylaşırken çıkartmalardan <strong>🔗 Bağlantı</strong> etiketini seçip yapıştırın.</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            {{ $baseUrl }}/?utm_source=instagram&amp;utm_medium=story&amp;utm_campaign=story_paylasim
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('{{ $baseUrl }}/?utm_source=instagram&utm_medium=story&utm_campaign=story_paylasim', 'Instagram Story Linki')"
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Story Linkini Kopyala</span>
                    </button>
                </div>

                <!-- 3. Reels Linki -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>🎬</span> Instagram Reels Paylaşımı
                            </span>
                            <span class="ml-badge ml-badge-gray">Reels</span>
                        </div>
                        <p class="ml-item-help">Reels açıklamalarında veya müşteriye özel mesajlarda (DM) doğrudan gönderin.</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            {{ $baseUrl }}/?utm_source=instagram&amp;utm_medium=reels
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('{{ $baseUrl }}/?utm_source=instagram&utm_medium=reels', 'Instagram Reels Linki')"
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Reels Linkini Kopyala</span>
                    </button>
                </div>

                <!-- 4. Meta / Instagram Reklam Parametresi -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>🎯</span> Meta (Instagram & Facebook) Reklam Parametresi
                            </span>
                            <span class="ml-badge ml-badge-primary">Meta Ads</span>
                        </div>
                        <p class="ml-item-help">Meta Ads Manager'da reklam oluştururken <strong>URL Parametreleri</strong> kutusuna yapıştırın:</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            utm_source=instagram&amp;utm_medium=cpc&amp;utm_campaign={{ $metaTag }}
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('utm_source=instagram&utm_medium=cpc&utm_campaign=' + '{{ $metaTag }}', 'Meta Reklam Parametresi')"
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Reklam Parametresini Kopyala</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. ÖZEL ÜRÜN & KAMPANYA LINK OLUŞTURUCU (İNTERAKTİF) -->
        <div class="ml-section">
            <div class="ml-section-header">
                <div class="ml-section-title-group">
                    <div class="ml-section-icon">
                        <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <div>
                        <h3 class="ml-section-title">Özel Ürün & Kampanya Link Oluşturucu</h3>
                        <p class="ml-section-desc">İstediğiniz ürünü ve paylaşım kanalını seçin, takip linkiniz anında oluşturulsun</p>
                    </div>
                </div>
                <span class="ml-badge ml-badge-gray">Canlı UTM</span>
            </div>

            <div class="ml-grid-3">
                <!-- 1. Hedef Sayfa / Ürün -->
                <div class="ml-form-group">
                    <label class="ml-form-label">1. Hedef Sayfa</label>
                    <select x-model="selectedTarget" class="ml-form-control">
                        <option value="home">Ana Sayfa (/)</option>
                        <option value="product">Belirli Bir Ürün Sayfası</option>
                        <option value="custom">Özel Link / Yol Yaz</option>
                    </select>

                    <!-- Ürün Seçimi -->
                    <template x-if="selectedTarget === 'product'">
                        <div style="margin-top: 6px;">
                            <label class="ml-form-hint" style="font-weight: 600; display: block; margin-bottom: 3px;">Ürünü Seçin:</label>
                            <select x-model="selectedProductSlug" class="ml-form-control">
                                @foreach($products as $product)
                                    <option value="{{ $product->slug }}">{{ $product->name }} (₺{{ number_format($product->price, 0) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </template>

                    <!-- Özel Link -->
                    <template x-if="selectedTarget === 'custom'">
                        <div style="margin-top: 6px;">
                            <label class="ml-form-hint" style="font-weight: 600; display: block; margin-bottom: 3px;">Özel URL / Yol:</label>
                            <input 
                                type="text" 
                                x-model="customPath" 
                                placeholder="/kategori/patenler veya tam link" 
                                class="ml-form-control"
                            />
                        </div>
                    </template>
                </div>

                <!-- 2. Paylaşım Kanalı / Mecra -->
                <div class="ml-form-group">
                    <label class="ml-form-label">2. Paylaşım Kanalı</label>
                    <select x-model="selectedChannel" class="ml-form-control">
                        <optgroup label="Instagram">
                            <option value="ig_story">📸 Instagram Hikaye (Story)</option>
                            <option value="ig_bio">📌 Instagram Profil Biyografisi (Bio)</option>
                            <option value="ig_reels">🎬 Instagram Reels</option>
                            <option value="meta_ads">🎯 Meta / Instagram Ücretli Reklam</option>
                        </optgroup>
                        <optgroup label="Diğer Sosyal Medya">
                            <option value="tiktok_bio">🎵 TikTok Profil Linki</option>
                            <option value="tiktok_video">🎵 TikTok Video / Reklam</option>
                            <option value="google_ads">🔍 Google Ads Reklamı</option>
                        </optgroup>
                        <optgroup label="Doğrudan İletişim">
                            <option value="whatsapp">💬 WhatsApp Mesaj / Destek</option>
                            <option value="sms">✉️ Toplu SMS Kampanyası</option>
                            <option value="influencer">🤝 Influencer / İş Birliği</option>
                        </optgroup>
                    </select>
                    <p class="ml-form-hint">Kanal seçildiğinde kaynak parametresi otomatik eklenir.</p>
                </div>

                <!-- 3. Kampanya Adı (Opsiyonel) -->
                <div class="ml-form-group">
                    <label class="ml-form-label">3. Kampanya Adı (Opsiyonel)</label>
                    <input 
                        type="text" 
                        x-model="campaignName" 
                        placeholder="Örn: bahar_indirimi, okuladonus, fenomen1" 
                        class="ml-form-control"
                    />
                    <p class="ml-form-hint">Hangi kampanyanızdan satış geldiğini raporlarda görmek için ad verin.</p>
                </div>
            </div>

            <!-- Canlı Üretilen Link Gösterimi -->
            <div class="ml-live-result-box">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.76rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b;">
                        Üretilen Takip Bağlantısı
                    </span>
                    <span class="ml-badge ml-badge-primary">Kullanıma Hazır</span>
                </div>

                <div class="ml-live-row">
                    <div 
                        x-text="generatedUrl" 
                        class="ml-code-input"
                        style="flex: 1; font-size: 0.82rem; padding: 10px 14px;"
                    ></div>
                    <button 
                        type="button" 
                        @click="copyText(generatedUrl, 'Özel Takip Linki')" 
                        class="ml-btn ml-btn-primary"
                        style="padding: 0 20px; height: 38px;"
                    >
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Linki Kopyala</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. DİĞER KANALLAR (TIKTOK, WHATSAPP, SMS, GOOGLE) -->
        <div class="ml-section">
            <div class="ml-section-header">
                <div class="ml-section-title-group">
                    <div class="ml-section-icon">
                        <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h3 class="ml-section-title">Diğer Pazarlama & İletişim Kanalları</h3>
                        <p class="ml-section-desc">TikTok, WhatsApp, SMS ve Google Ads için hazır takip bağlantıları</p>
                    </div>
                </div>
            </div>

            <div class="ml-grid-4">
                <!-- TikTok -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>🎵</span> TikTok Profil
                            </span>
                            <span class="ml-badge ml-badge-gray">TikTok</span>
                        </div>
                        <p class="ml-item-help">TikTok profil açıklamasındaki bağlantı için kullanılır.</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            {{ $baseUrl }}/?utm_source=tiktok&amp;utm_medium=bio
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('{{ $baseUrl }}/?utm_source=tiktok&utm_medium=bio', 'TikTok Linki')" 
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <span>Kopyala</span>
                    </button>
                </div>

                <!-- WhatsApp -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>💬</span> WhatsApp
                            </span>
                            <span class="ml-badge ml-badge-gray">Destek</span>
                        </div>
                        <p class="ml-item-help">Müşteriye WhatsApp mesajında gönderilen linkler.</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            {{ $baseUrl }}/?utm_source=whatsapp&amp;utm_medium=chat
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('{{ $baseUrl }}/?utm_source=whatsapp&utm_medium=chat', 'WhatsApp Linki')" 
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <span>Kopyala</span>
                    </button>
                </div>

                <!-- Toplu SMS -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>✉️</span> Toplu SMS
                            </span>
                            <span class="ml-badge ml-badge-gray">SMS</span>
                        </div>
                        <p class="ml-item-help">Toplu SMS iletilerinde gönderilen kampanya linki.</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            {{ $baseUrl }}/?utm_source=sms&amp;utm_medium=bulk_sms&amp;utm_campaign=firsat
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('{{ $baseUrl }}/?utm_source=sms&utm_medium=bulk_sms&utm_campaign=firsat', 'SMS Linki')" 
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <span>Kopyala</span>
                    </button>
                </div>

                <!-- Google Ads -->
                <div class="ml-item-box">
                    <div>
                        <div class="ml-item-head">
                            <span class="ml-item-title">
                                <span>🔍</span> Google Ads
                            </span>
                            <span class="ml-badge ml-badge-gray">Google</span>
                        </div>
                        <p class="ml-item-help">Google Ads kampanya URL son eki (Final URL suffix).</p>
                        <div style="margin-top: 8px;" class="ml-code-input">
                            utm_source=google&amp;utm_medium=cpc&amp;utm_campaign={{ $googleTag }}
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('utm_source=google&utm_medium=cpc&utm_campaign=' + '{{ $googleTag }}', 'Google Ads Parametresi')" 
                        class="ml-btn ml-btn-secondary ml-btn-full"
                    >
                        <span>Kopyala</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
