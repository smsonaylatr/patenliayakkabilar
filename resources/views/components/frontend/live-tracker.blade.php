{{-- Canlı Ziyaretçi İstihbarat & Satış Dönüşüm Motoru (PatenliAyakkabılar® Ultra-Premium Native Tasarım) --}}
<style>
    /* ==========================================================
       PATENLİ AYAKKABILAR — CANLI DÖNÜŞÜM & POPUP MOTORU STİLLERİ
       Vite/Tailwind purge bağımsız, %100 garantili bağımsız tasarım
       ========================================================== */
    #pa-live-tracker-root {
        position: static !important;
    }

    #pa-modal-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 2147483647 !important;
        display: none;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        background: rgba(15, 23, 42, 0.72) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        pointer-events: auto !important;
        box-sizing: border-box !important;
    }

    #pa-modal-overlay.pa-show {
        display: flex !important;
        opacity: 1 !important;
    }

    #pa-modal-container {
        position: relative !important;
        width: 100% !important;
        max-width: 440px !important;
        background: #ffffff !important;
        border-radius: 28px !important;
        padding: 34px 28px 28px 28px !important;
        box-shadow: 0 32px 80px -15px rgba(15, 23, 42, 0.45), 0 0 0 1px rgba(15, 23, 42, 0.05) !important;
        text-align: center !important;
        font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        transform: scale(0.92) translateY(16px) !important;
        opacity: 0 !important;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    #pa-modal-overlay.pa-show #pa-modal-container {
        transform: scale(1) translateY(0) !important;
        opacity: 1 !important;
    }

    /* Üst İnce Siyah Çizgi */
    .pa-top-accent {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 5px !important;
        background: #0f172a !important;
    }

    /* Kapat Butonu */
    .pa-close-btn {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        z-index: 10 !important;
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        background: #f1f5f9 !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    }
    .pa-close-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        transform: scale(1.08) !important;
    }

    /* Marka Rozeti (Pill) */
    .pa-brand-pill {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        padding: 6px 15px !important;
        border-radius: 9999px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        color: #0f172a !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        letter-spacing: 0.08em !important;
        text-transform: uppercase !important;
        margin-bottom: 18px !important;
    }

    /* Animasyonlu Nabız Noktası */
    .pa-pulse-dot {
        width: 7px !important;
        height: 7px !important;
        border-radius: 50% !important;
        background: #0f172a !important;
        box-shadow: 0 0 0 0 rgba(15, 23, 42, 0.7) !important;
        animation: pa-pulse 1.8s infinite !important;
        flex-shrink: 0 !important;
    }
    @keyframes pa-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(15, 23, 42, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(15, 23, 42, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(15, 23, 42, 0); }
    }

    /* İkon Rozetleri */
    .pa-hero-icon-offer {
        width: 76px !important;
        height: 76px !important;
        border-radius: 26px !important;
        background: #0f172a !important;
        color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto 16px !important;
        box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.35) !important;
        border: 4px solid #f8fafc !important;
    }

    .pa-hero-icon-redirect {
        width: 76px !important;
        height: 76px !important;
        border-radius: 26px !important;
        background: #0f172a !important;
        color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto 16px !important;
        box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.35) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
    }

    /* Başlık ve Açıklamalar */
    .pa-modal-title {
        font-size: 23px !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        letter-spacing: -0.02em !important;
        margin: 0 0 8px 0 !important;
        font-family: 'Outfit', sans-serif !important;
    }

    .pa-modal-desc {
        font-size: 14px !important;
        color: #64748b !important;
        line-height: 1.6 !important;
        margin: 0 0 20px 0 !important;
        font-weight: 500 !important;
        padding: 0 8px !important;
        font-family: 'Outfit', sans-serif !important;
    }

    /* Kupon Bileti (Voucher Ticket) */
    .pa-coupon-box {
        background: linear-gradient(135deg, #fffaf5 0%, #fff7ed 100%) !important;
        border: 2px dashed #f97316 !important;
        border-radius: 18px !important;
        padding: 14px 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-bottom: 20px !important;
        text-align: left !important;
        position: relative !important;
        box-sizing: border-box !important;
    }

    .pa-coupon-label {
        font-size: 10px !important;
        font-weight: 800 !important;
        color: #ea580c !important;
        letter-spacing: 0.1em !important;
        text-transform: uppercase !important;
        margin-bottom: 2px !important;
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    .pa-coupon-code {
        font-family: 'SF Mono', Monaco, Consolas, monospace !important;
        font-size: 21px !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        letter-spacing: 0.12em !important;
        user-select: all !important;
    }

    .pa-copy-btn {
        background: #0f172a !important;
        color: #ffffff !important;
        padding: 10px 18px !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        cursor: pointer !important;
        border: none !important;
        transition: all 0.2s ease !important;
        flex-shrink: 0 !important;
    }
    .pa-copy-btn:hover {
        background: #000000 !important;
        transform: translateY(-1px) !important;
    }
    .pa-copy-btn.pa-copied {
        background: #16a34a !important;
    }

    /* Aksiyon Butonları */
    .pa-btn-primary {
        width: 100% !important;
        padding: 15px 24px !important;
        background: #0f172a !important;
        color: #ffffff !important;
        border-radius: 9999px !important;
        font-weight: 800 !important;
        font-size: 15px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.35) !important;
        cursor: pointer !important;
        border: none !important;
        transition: all 0.2s ease !important;
        box-sizing: border-box !important;
    }
    .pa-btn-primary:hover {
        background: #000000 !important;
        color: #ffffff !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 14px 28px -4px rgba(0, 0, 0, 0.5) !important;
    }

    .pa-btn-group {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        width: 100% !important;
        margin-top: 10px !important;
    }
    .pa-btn-group .pa-btn-primary {
        flex: 1 1 auto !important;
        width: auto !important;
    }
    .pa-btn-group .pa-btn-secondary {
        flex: 0 0 auto !important;
    }

    .pa-btn-secondary {
        background: #f1f5f9 !important;
        color: #475569 !important;
        padding: 15px 22px !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box !important;
    }
    .pa-btn-secondary:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }

    /* Geri Sayım Rozeti & İlerleme Çubuğu */
    .pa-countdown-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        padding: 8px 18px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 9999px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-bottom: 14px !important;
    }

    .pa-progress-track {
        width: 100% !important;
        height: 8px !important;
        background: #f1f5f9 !important;
        border-radius: 9999px !important;
        overflow: hidden !important;
        margin-bottom: 22px !important;
        padding: 2px !important;
        border: 1px solid #e2e8f0 !important;
        box-sizing: border-box !important;
    }

    .pa-progress-fill {
        height: 100% !important;
        border-radius: 9999px !important;
        background: #0f172a !important;
        transition: width 1s linear !important;
        box-shadow: 0 0 8px rgba(15, 23, 42, 0.25) !important;
    }

    /* Güven Satırı (Trust Footer) */
    .pa-trust-row {
        margin-top: 20px !important;
        padding-top: 16px !important;
        border-top: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 14px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #64748b !important;
    }

    .pa-trust-item {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
    }

    /* Bildirim Toast'ları */
    #pa-toast-container {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        z-index: 2147483647 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
        max-width: 380px !important;
        width: calc(100% - 40px) !important;
        pointer-events: none !important;
    }

    .pa-toast-card {
        pointer-events: auto !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-radius: 20px !important;
        padding: 16px !important;
        box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
        border-left: 4px solid #FF7A1A !important;
        display: flex !important;
        align-items: flex-start !important;
        gap: 12px !important;
        transform: translateY(-10px) !important;
        opacity: 0 !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        font-family: 'Outfit', sans-serif !important;
    }
    .pa-toast-card.pa-show {
        transform: translateY(0) !important;
        opacity: 1 !important;
    }
</style>

<div id="pa-live-tracker-root">
    {{-- 1. Tam Ekran Modal / Popup Backdrop & Container --}}
    <div id="pa-modal-overlay" role="dialog" aria-modal="true">
        <div id="pa-modal-container">
            {{-- Dinamik Modal İçeriği --}}
        </div>
    </div>

    {{-- 2. Floating Toast / Alert Konteyneri --}}
    <div id="pa-toast-container">
        {{-- Dinamik Toast Bildirimleri --}}
    </div>
</div>

<script>
(function() {
    'use strict';

    // 1. Kalıcı Ziyaretçi Belirteci (Persistent Visitor Token)
    var storageKey = 'pa_visitor_token';
    var visitorToken = localStorage.getItem(storageKey);
    if (!visitorToken) {
        visitorToken = 'pa_vt_' + Date.now().toString(36) + '_' + Math.random().toString(36).substring(2, 10);
        try {
            localStorage.setItem(storageKey, visitorToken);
        } catch(e) {}
    }

    var heartbeatInterval = null;
    var normalDelay = 7000; // 7 saniye
    var backgroundDelay = 30000; // Sekme arka plandayken 30 saniye
    var currentDelay = normalDelay;
    var isSending = false;
    var activeRedirectTimer = null;

    // Ziyaretçi Kimlik Durumu (Canlı Form ve Müşteri Tanıma)
    var lastKnownIdentity = {
        guest_name: null,
        guest_email: null,
        guest_phone: null
    };
    var identityTimer = null;

    function sendIdentityNow() {
        if (!lastKnownIdentity.guest_name && !lastKnownIdentity.guest_email && !lastKnownIdentity.guest_phone) {
            return;
        }

        var payload = {
            visitor_token: visitorToken,
            guest_name: lastKnownIdentity.guest_name,
            guest_email: lastKnownIdentity.guest_email,
            guest_phone: lastKnownIdentity.guest_phone,
            url: window.location.href,
            path: window.location.pathname
        };

        fetch('/api/presence/identify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(function(res) { return res.json(); })
        .catch(function() {});
    }

    function triggerIdentityUpdate(delayMs) {
        if (identityTimer) clearTimeout(identityTimer);
        identityTimer = setTimeout(sendIdentityNow, delayMs || 250);
    }

    window.paIdentifyVisitor = function(name, email, phone) {
        var changed = false;
        if (typeof name !== 'undefined' && name !== null) {
            var trimmedName = name.trim();
            if (trimmedName !== lastKnownIdentity.guest_name) {
                lastKnownIdentity.guest_name = trimmedName;
                changed = true;
            }
        }
        if (typeof email !== 'undefined' && email !== null) {
            var trimmedEmail = email.trim();
            if (trimmedEmail !== lastKnownIdentity.guest_email) {
                lastKnownIdentity.guest_email = trimmedEmail;
                changed = true;
            }
        }
        if (typeof phone !== 'undefined' && phone !== null) {
            var trimmedPhone = phone.trim();
            if (trimmedPhone !== lastKnownIdentity.guest_phone) {
                lastKnownIdentity.guest_phone = trimmedPhone;
                changed = true;
            }
        }
        if (changed) {
            triggerIdentityUpdate(300);
        }
    };

    // Güvenli HTML escape
    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Modal Yönetimi (Sitenin tepesinde z-index: 2147483647 ile açılır)
    function openModal(contentHtml) {
        var overlay = document.getElementById('pa-modal-overlay');
        var container = document.getElementById('pa-modal-container');
        if (!overlay || !container) return;

        container.innerHTML = contentHtml;
        overlay.classList.add('pa-show');

        // Sayfa kaydırmasını kilitle
        document.body.style.overflow = 'hidden';

        // Mobil cihazda tatmin edici hafif titreşim
        if (navigator.vibrate) {
            try { navigator.vibrate([35, 45, 35]); } catch(e) {}
        }
    }

    function closeModal() {
        var overlay = document.getElementById('pa-modal-overlay');
        var container = document.getElementById('pa-modal-container');
        if (!overlay || !container) return;

        // Varsa çalışan yönlendirme sayacını durdur
        if (activeRedirectTimer) {
            clearInterval(activeRedirectTimer);
            activeRedirectTimer = null;
        }

        overlay.classList.remove('pa-show');

        // Sayfa kaydırmasını geri aç
        document.body.style.overflow = '';

        setTimeout(function() {
            container.innerHTML = '';
        }, 300);
    }

    // ESC ve Backdrop Tıklama Dinleyicileri
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    var overlayEl = document.getElementById('pa-modal-overlay');
    if (overlayEl) {
        overlayEl.addEventListener('click', function(e) {
            if (e.target === overlayEl) closeModal();
        });
    }

    // 2. Kalp Atışı (Heartbeat) Gönderimi
    function sendHeartbeat(actionType, actionDetail) {
        if (isSending) return;
        isSending = true;

        var payload = {
            visitor_token: visitorToken,
            url: window.location.href,
            path: window.location.pathname,
            title: document.title || 'Patenli Ayakkabılar',
            referrer: document.referrer || '',
            screen: window.innerWidth + 'x' + window.innerHeight,
            action: actionType || 'heartbeat',
            action_detail: actionDetail || null
        };

        try {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('utm_source')) payload.utm_source = urlParams.get('utm_source');
            if (urlParams.has('utm_campaign')) payload.utm_campaign = urlParams.get('utm_campaign');
        } catch(e) {}

        fetch('/api/presence/heartbeat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            isSending = false;
            if (data && data.command) {
                handleAdminCommand(data.command);
            }
        })
        .catch(function(err) {
            isSending = false;
        });
    }

    // 3. Admin Komutlarını İcra Etme (Redirect, Offer, Alert, Reload, Kick, Blocked)
    function handleAdminCommand(cmd) {
        if (!cmd || !cmd.action) return;

        if (cmd.action === 'redirect') {
            executeRedirectCommand(cmd);
        } else if (cmd.action === 'offer') {
            executeOfferCommand(cmd);
        } else if (cmd.action === 'alert') {
            executeAlertCommand(cmd);
        } else if (cmd.action === 'voice') {
            executeVoiceCommand(cmd);
        } else if (cmd.action === 'reload') {
            window.location.reload();
        } else if (cmd.action === 'kick') {
            window.location.href = cmd.target_url || '/';
        } else if (cmd.action === 'blocked') {
            document.body.innerHTML = 
                '<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#0f172a;color:#ffffff;font-family:sans-serif;text-align:center;padding:24px;">' +
                    '<div style="max-width:440px;background:#1e293b;padding:36px;border-radius:24px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.1);">' +
                        '<div style="width:64px;height:64px;background:rgba(239,68,68,0.2);color:#ef4444;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">⛔</div>' +
                        '<h1 style="font-size:22px;font-weight:800;margin-bottom:10px;color:#ffffff;">Erişim Kısıtlandı</h1>' +
                        '<p style="color:#94a3b8;font-size:14px;line-height:1.6;">' + escapeHtml(cmd.message || 'Bu siteye erişiminiz yönetici tarafından kısıtlanmıştır.') + '</p>' +
                    '</div>' +
                '</div>';
        }
    }

    // 4. Birebir Site Tasarımında Yönlendirme Modalı (Redirect Modal)
    function executeRedirectCommand(cmd) {
        var targetUrl = cmd.target_url || '/';
        var countdown = parseInt(cmd.countdown, 10);
        if (isNaN(countdown) || countdown < 0) countdown = 0;

        if (countdown === 0 && !cmd.message) {
            window.location.href = targetUrl;
            return;
        }

        var secondsLeft = countdown > 0 ? countdown : 3;
        var totalSeconds = secondsLeft;
        var messageText = cmd.message || 'Sizin için hazırlanan özel fırsat sayfasına aktarılıyorsunuz...';

        var modalHtml = 
            '<div class="pa-top-accent"></div>' +
            
            '<button type="button" id="pa-redirect-close-btn" class="pa-close-btn" aria-label="Kapat">' +
                '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>' +
            '</button>' +

            '<div class="pa-brand-pill">' +
                '<span class="pa-pulse-dot"></span>' +
                '<span>PATENLİAYAKKABILAR® YÖNLENDİRME</span>' +
            '</div>' +

            '<div class="pa-hero-icon-redirect">' +
                '<svg width="34" height="34" style="transform: rotate(-45deg); filter: drop-shadow(0 0 6px rgba(255, 122, 26, 0.4));" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>' +
                '</svg>' +
            '</div>' +

            '<h3 class="pa-modal-title">Özel Fırsat Sayfasına Geçiş Yapılıyor</h3>' +
            '<p class="pa-modal-desc">' + escapeHtml(messageText) + '</p>' +

            '<div class="pa-countdown-badge">' +
                '<svg width="15" height="15" style="color: #FF7A1A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                '<span id="pa-redirect-countdown-text">' + secondsLeft + ' saniye içinde yönlendirileceksiniz</span>' +
            '</div>' +

            '<div class="pa-progress-track">' +
                '<div id="pa-redirect-progress" class="pa-progress-fill" style="width: 100%;"></div>' +
            '</div>' +

            '<div class="pa-btn-group">' +
                '<a href="' + escapeHtml(targetUrl) + '" id="pa-redirect-go-btn" class="pa-btn-primary">' +
                    '<span>Hemen Geçiş Yap</span>' +
                    '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>' +
                '</a>' +
                '<button type="button" id="pa-redirect-cancel-btn" class="pa-btn-secondary">' +
                    'Sitede Kal' +
                '</button>' +
            '</div>';

        openModal(modalHtml);

        // Kapat / Sitede Kal butonları
        var closeBtn = document.getElementById('pa-redirect-close-btn');
        var cancelBtn = document.getElementById('pa-redirect-cancel-btn');
        if (closeBtn) closeBtn.onclick = closeModal;
        if (cancelBtn) cancelBtn.onclick = closeModal;

        // Geri Sayım Döngüsü
        if (activeRedirectTimer) clearInterval(activeRedirectTimer);
        activeRedirectTimer = setInterval(function() {
            secondsLeft--;
            var textEl = document.getElementById('pa-redirect-countdown-text');
            var progEl = document.getElementById('pa-redirect-progress');
            
            if (secondsLeft > 0) {
                if (textEl) textEl.textContent = secondsLeft + ' saniye içinde yönlendirileceksiniz';
                if (progEl) progEl.style.width = Math.max(0, (secondsLeft / totalSeconds) * 100) + '%';
            } else {
                if (textEl) textEl.textContent = 'Yönlendiriliyorsunuz...';
                if (progEl) progEl.style.width = '0%';
                clearInterval(activeRedirectTimer);
                activeRedirectTimer = null;
                window.location.href = targetUrl;
            }
        }, 1000);
    }

    // 5. Birebir Site Tasarımında Fırsat / Kupon Pop-up'ı (Offer Modal)
    function executeOfferCommand(cmd) {
        var couponHtml = '';
        if (cmd.coupon_code) {
            couponHtml = 
                '<div class="pa-coupon-box">' +
                    '<div style="min-width: 0; flex: 1;">' +
                        '<div class="pa-coupon-label">' +
                            '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>' +
                            '<span>İndirim Kuponunuz</span>' +
                        '</div>' +
                        '<div class="pa-coupon-code">' + escapeHtml(cmd.coupon_code) + '</div>' +
                    '</div>' +
                    '<button type="button" id="pa-copy-coupon-btn" class="pa-copy-btn">' +
                        '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>' +
                        '<span id="pa-copy-btn-text">Kopyala</span>' +
                    '</button>' +
                '</div>';
        }

        var actionBtnHtml = '';
        if (cmd.action_button && cmd.action_url) {
            actionBtnHtml = 
                '<a href="' + escapeHtml(cmd.action_url) + '" class="pa-btn-primary">' +
                    '<span>' + escapeHtml(cmd.action_button) + '</span>' +
                    '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>' +
                '</a>';
        } else if (cmd.coupon_code) {
            actionBtnHtml = 
                '<button type="button" id="pa-use-coupon-btn" class="pa-btn-primary">' +
                    '<span>Kuponu Kopyala & Alışverişe Devam Et</span>' +
                    '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>' +
                '</button>';
        }

        var modalHtml = 
            '<div class="pa-top-accent"></div>' +
            
            '<button type="button" id="pa-modal-close-btn" class="pa-close-btn" aria-label="Kapat">' +
                '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>' +
            '</button>' +

            '<div class="pa-brand-pill">' +
                '<svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"/></svg>' +
                '<span>PATENLİAYAKKABILAR® ÖZEL FIRSAT</span>' +
            '</div>' +

            '<div class="pa-hero-icon-offer">' +
                '<svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H4.5a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>' +
                '</svg>' +
            '</div>' +

            '<h3 class="pa-modal-title">' + escapeHtml(cmd.title || 'Size Özel Fırsat!') + '</h3>' +
            '<p class="pa-modal-desc">' + escapeHtml(cmd.message || '') + '</p>' +

            couponHtml +
            actionBtnHtml +

            '<div class="pa-trust-row">' +
                '<div class="pa-trust-item">' +
                    '<svg width="15" height="15" style="color: #16a34a;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>' +
                    '<span>Aynı Gün Kargo</span>' +
                '</div>' +
                '<span style="color: #cbd5e1;">•</span>' +
                '<div class="pa-trust-item">' +
                    '<svg width="15" height="15" style="color: #16a34a;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>' +
                    '<span>Güvenli Ödeme</span>' +
                '</div>' +
                '<span style="color: #cbd5e1;">•</span>' +
                '<div class="pa-trust-item">' +
                    '<svg width="15" height="15" style="color: #16a34a;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>' +
                    '<span>14 Gün Değişim</span>' +
                '</div>' +
            '</div>';

        openModal(modalHtml);

        // Kapat Butonu
        var closeBtn = document.getElementById('pa-modal-close-btn');
        if (closeBtn) closeBtn.onclick = closeModal;

        // Kupon Kopyalama Mantığı
        function copyCouponToClipboard() {
            if (!cmd.coupon_code) return;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(cmd.coupon_code).then(function() {
                    var copyBtn = document.getElementById('pa-copy-coupon-btn');
                    var btnText = document.getElementById('pa-copy-btn-text');
                    if (copyBtn && btnText) {
                        btnText.textContent = 'Kopyalandı! ✓';
                        copyBtn.classList.add('pa-copied');
                    }
                });
            }
        }

        var copyBtn = document.getElementById('pa-copy-coupon-btn');
        if (copyBtn) copyBtn.onclick = copyCouponToClipboard;

        var useBtn = document.getElementById('pa-use-coupon-btn');
        if (useBtn) {
            useBtn.onclick = function() {
                copyCouponToClipboard();
                setTimeout(closeModal, 600);
            };
        }
    }

    // 6. Zarif Toast Bildirimi (executeAlertCommand)
    function executeAlertCommand(cmd) {
        var toastContainer = document.getElementById('pa-toast-container');
        if (!toastContainer) return;

        var toastId = 'pa-toast-' + Date.now();
        var toastEl = document.createElement('div');
        toastEl.id = toastId;
        toastEl.className = 'pa-toast-card';

        var iconSvg = '<svg width="20" height="20" style="color: #FF7A1A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        if (cmd.type === 'success') {
            iconSvg = '<svg width="20" height="20" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }

        toastEl.innerHTML = 
            '<div style="width: 36px; height: 36px; border-radius: 12px; background: #fff7ed; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">' +
                iconSvg +
            '</div>' +
            '<div style="flex: 1; min-width: 0; padding-top: 2px;">' +
                '<h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">' + escapeHtml(cmd.title || 'Bilgilendirme') + '</h4>' +
                '<p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.5;">' + escapeHtml(cmd.message || '') + '</p>' +
            '</div>' +
            '<button type="button" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 2px;" aria-label="Kapat">' +
                '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
            '</button>';

        toastContainer.appendChild(toastEl);

        // Animasyonla aç
        requestAnimationFrame(function() {
            toastEl.classList.add('pa-show');
        });

        // Kapatma
        var removeToast = function() {
            toastEl.classList.remove('pa-show');
            setTimeout(function() {
                if (toastEl.parentNode) toastEl.parentNode.removeChild(toastEl);
            }, 300);
        };

        toastEl.querySelector('button').onclick = removeToast;
        setTimeout(removeToast, 6000);
    }

    // 6.5. Melodili Mağaza Anons Zili (Web Audio API)
    function playChimeSound(callback) {
        try {
            var AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (!AudioContextClass) {
                if (callback) callback();
                return;
            }
            var ctx = new AudioContextClass();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            var t = ctx.currentTime;
            // 1. Ton: 587.33 Hz (D5)
            var osc1 = ctx.createOscillator();
            var gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, t);
            gain1.gain.setValueAtTime(0.2, t);
            gain1.gain.exponentialRampToValueAtTime(0.001, t + 0.45);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(t);
            osc1.stop(t + 0.45);

            // 2. Ton: 880 Hz (A5 - Mağaza Anons Çanı)
            var osc2 = ctx.createOscillator();
            var gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880, t + 0.16);
            gain2.gain.setValueAtTime(0.25, t + 0.16);
            gain2.gain.exponentialRampToValueAtTime(0.001, t + 0.85);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(t + 0.16);
            osc2.stop(t + 0.85);

            if (callback) {
                setTimeout(callback, 850);
            }
        } catch (e) {
            if (callback) callback();
        }
    }

    // Tarayıcı Yerleşik Türkçe Konuşma Sentezi (TTS Fallback)
    function speakTurkishText(text) {
        if (!('speechSynthesis' in window) || !text) return;
        try {
            window.speechSynthesis.cancel();
            var clean = text.replace(/<[^>]*>?/gm, '');
            var utter = new SpeechSynthesisUtterance(clean);
            utter.lang = 'tr-TR';
            utter.rate = 0.95;
            utter.pitch = 1.05;

            var voices = window.speechSynthesis.getVoices();
            for (var i = 0; i < voices.length; i++) {
                if (voices[i].lang && voices[i].lang.toLowerCase().indexOf('tr') !== -1) {
                    utter.voice = voices[i];
                    break;
                }
            }

            window.speechSynthesis.speak(utter);
        } catch(e) {}
    }

    // Sesli İleti & Anons Yönetimi (ElevenLabs AI Audio + TTS Fallback + Görsel Kart)
    function executeVoiceCommand(cmd) {
        var toastContainer = document.getElementById('pa-toast-container');
        if (!toastContainer) return;

        var soundType = cmd.sound_type || 'chime_and_speech';
        var messageText = cmd.message || '';
        var titleText = cmd.title || '🎙️ Canlı Mağaza Anonsu';
        var audioUrl = cmd.audio_url || null;

        var currentAudio = null;

        var playAudioSequence = function() {
            // Eğer ElevenLabs MP3 dosyası hazırsa doğrudan stüdyo kalitesindeki sesi çal
            if (audioUrl) {
                try {
                    if (currentAudio) {
                        currentAudio.pause();
                        currentAudio.currentTime = 0;
                    }
                    currentAudio = new Audio(audioUrl);
                    currentAudio.volume = 1.0;

                    if (soundType === 'chime_and_speech') {
                        playChimeSound(function() {
                            currentAudio.play().catch(function() {
                                // Tarayıcı otomatik oynatmayı engellerse Web Speech fallback dene
                                speakTurkishText(messageText);
                            });
                        });
                    } else if (soundType === 'speech_only') {
                        currentAudio.play().catch(function() {
                            speakTurkishText(messageText);
                        });
                    } else if (soundType === 'chime_only') {
                        playChimeSound();
                    }
                    return;
                } catch(e) {
                    // Fallback to TTS
                }
            }

            // Fallback: Web Speech API TTS
            if (soundType === 'chime_and_speech') {
                playChimeSound(function() {
                    speakTurkishText(messageText);
                });
            } else if (soundType === 'speech_only') {
                speakTurkishText(messageText);
            } else if (soundType === 'chime_only') {
                playChimeSound();
            }
        };

        // Otomatik ses çalmayı başlat
        playAudioSequence();

        var toastId = 'pa-voice-' + Date.now();
        var card = document.createElement('div');
        card.id = toastId;
        card.className = 'pa-toast-card';
        card.style.cssText = 'background: #0f172a !important; color: #ffffff !important; border-left: 4px solid #f97316 !important; border: 1px solid rgba(249, 115, 22, 0.4) !important; flex-direction: column !important; gap: 12px !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;';

        var couponHtml = '';
        if (cmd.coupon_code) {
            couponHtml = 
                '<div style="background: rgba(255,255,255,0.06); border: 1px dashed #f97316; border-radius: 12px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">' +
                    '<div>' +
                        '<div style="font-size: 9px; font-weight: 800; color: #fb923c; text-transform: uppercase; letter-spacing: 0.08em;">İNDİRİM KUPONUNUZ</div>' +
                        '<div style="font-family: monospace; font-size: 14px; font-weight: 800; color: #ffffff;">' + escapeHtml(cmd.coupon_code) + '</div>' +
                    '</div>' +
                    '<button type="button" class="pa-copy-voice-coupon" style="background: #f97316; color: #ffffff; border: none; border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer;">Kopyala</button>' +
                '</div>';
        }

        var btnHtml = '';
        if (cmd.action_button && cmd.action_url) {
            btnHtml = 
                '<a href="' + escapeHtml(cmd.action_url) + '" style="background: linear-gradient(135deg, #FF7A1A 0%, #ea580c 100%); color: #ffffff; text-decoration: none; padding: 10px 16px; border-radius: 999px; font-size: 12px; font-weight: 800; text-align: center; display: block; box-shadow: 0 4px 12px rgba(255,122,26,0.35);">' +
                    escapeHtml(cmd.action_button) + ' ↗' +
                '</a>';
        }

        card.innerHTML = 
            '<div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; width: 100%;">' +
                '<div style="display: flex; align-items: center; gap: 10px;">' +
                    '<div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(249, 115, 22, 0.2); border: 1px solid rgba(249, 115, 22, 0.4); display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fb923c; flex-shrink: 0;">' +
                        '🎙️' +
                    '</div>' +
                    '<div>' +
                        '<div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #fb923c; display: flex; align-items: center; gap: 5px;">' +
                            '<span class="pa-pulse-dot" style="width: 6px; height: 6px;"></span>' +
                            'CANLI SESLİ ANONS' +
                        '</div>' +
                        '<h4 style="font-size: 13.5px; font-weight: 900; color: #ffffff; margin: 2px 0 0 0; line-height: 1.25;">' + escapeHtml(titleText) + '</h4>' +
                    '</div>' +
                '</div>' +
                '<button type="button" class="pa-close-voice-toast" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 2px;" aria-label="Kapat">' +
                    '<svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
                '</button>' +
            '</div>' +

            '<p style="font-size: 12.5px; color: #cbd5e1; line-height: 1.55; margin: 0; font-weight: 500;">' + escapeHtml(messageText) + '</p>' +

            couponHtml +
            btnHtml +

            '<div style="display: flex; align-items: center; justify-content: space-between; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 11px; color: #94a3b8; width: 100%;">' +
                '<button type="button" class="pa-replay-voice-btn" style="background: none; border: none; color: #fb923c; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; padding: 0;">' +
                    '<span>🔊</span> Tekrar Dinle' +
                '</button>' +
                '<span style="font-size: 10px; color: #64748b; font-family: monospace;">Patenli Ayakkabılar Live</span>' +
            '</div>';

        toastContainer.appendChild(card);

        requestAnimationFrame(function() {
            card.classList.add('pa-show');
        });

        // Kupon kopyalama
        var copyBtn = card.querySelector('.pa-copy-voice-coupon');
        if (copyBtn && cmd.coupon_code) {
            copyBtn.onclick = function() {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(cmd.coupon_code).then(function() {
                        copyBtn.textContent = 'Kopyalandı! ✓';
                        copyBtn.style.background = '#16a34a';
                    });
                }
            };
        }

        // Tekrar Dinle Butonu
        var replayBtn = card.querySelector('.pa-replay-voice-btn');
        if (replayBtn) {
            replayBtn.onclick = function() {
                playAudioSequence();
            };
        }

        // Kapatma Butonu
        var closeToast = function() {
            card.classList.remove('pa-show');
            setTimeout(function() {
                if (card.parentNode) card.parentNode.removeChild(card);
            }, 300);
        };

        var closeBtn = card.querySelector('.pa-close-voice-toast');
        if (closeBtn) closeBtn.onclick = closeToast;

        // 14 saniye sonra otomatik kapanış
        setTimeout(closeToast, 14000);
    }

    // 7. Kullanıcı Hareketlerini Dinleme (Beden & Varyant Tıklamaları)
    function setupInteractions() {
        document.addEventListener('click', function(e) {
            var target = e.target;
            if (!target) return;

            // Beden butonları tespiti
            var sizeBtn = target.closest('[data-size], .size-button, button[value]');
            if (sizeBtn) {
                var sizeVal = sizeBtn.getAttribute('data-size') || sizeBtn.getAttribute('value') || sizeBtn.textContent.trim();
                if (sizeVal && sizeVal.length <= 6) {
                    sendHeartbeat('size_click', 'Beden seçildi: ' + sizeVal);
                }
            }
        });
    }

    // 8. Canlı Form Dinleyicisi (Checkout İsim, E-posta, Telefon Canlı Takip)
    function setupCheckoutListeners() {
        document.addEventListener('input', function(e) {
            var target = e.target;
            if (!target || !target.tagName || target.tagName.toLowerCase() !== 'input') return;

            var val = target.value;
            var nameAttr = (target.getAttribute('name') || '').toLowerCase();
            var autocomplete = (target.getAttribute('autocomplete') || '').toLowerCase();
            var wireModel = (target.getAttribute('wire:model') || target.getAttribute('wire:model.live') || target.getAttribute('wire:model.blur') || '').toLowerCase();
            var placeholder = (target.getAttribute('placeholder') || '').toLowerCase();

            // Ad Soyad alanı (canlı isim yazarken anında güncelle)
            if (autocomplete === 'name' || wireModel.indexOf('name') !== -1 || nameAttr.indexOf('name') !== -1 || placeholder.indexOf('adınız') !== -1) {
                if (val && val.length >= 2) {
                    window.paIdentifyVisitor(val, null, null);
                }
            }
            // E-posta alanı
            else if (target.type === 'email' || autocomplete === 'email' || wireModel.indexOf('email') !== -1 || nameAttr.indexOf('email') !== -1 || placeholder.indexOf('@') !== -1) {
                if (val && val.length >= 4) {
                    window.paIdentifyVisitor(null, val, null);
                }
            }
            // Telefon alanı
            else if (target.type === 'tel' || autocomplete === 'tel' || wireModel.indexOf('phone') !== -1 || nameAttr.indexOf('phone') !== -1 || placeholder.indexOf('5xx') !== -1 || placeholder.indexOf('telefon') !== -1) {
                if (val && val.length >= 6) {
                    window.paIdentifyVisitor(null, null, val);
                }
            }
        }, { passive: true });

        // Input dışına çıkıldığında (blur)
        document.addEventListener('focusout', function(e) {
            var target = e.target;
            if (!target || !target.tagName || target.tagName.toLowerCase() !== 'input') return;
            var val = target.value;
            var nameAttr = (target.getAttribute('name') || '').toLowerCase();
            var autocomplete = (target.getAttribute('autocomplete') || '').toLowerCase();
            var wireModel = (target.getAttribute('wire:model') || target.getAttribute('wire:model.live') || target.getAttribute('wire:model.blur') || '').toLowerCase();

            if (autocomplete === 'name' || wireModel.indexOf('name') !== -1 || nameAttr.indexOf('name') !== -1) {
                if (val) window.paIdentifyVisitor(val, null, null);
            } else if (target.type === 'email' || autocomplete === 'email' || wireModel.indexOf('email') !== -1) {
                if (val) window.paIdentifyVisitor(null, val, null);
            } else if (target.type === 'tel' || autocomplete === 'tel' || wireModel.indexOf('phone') !== -1) {
                if (val) window.paIdentifyVisitor(null, null, val);
            }
        }, { passive: true });
    }

    // 9. Tab Görünürlük Yönetimi (Pil & Kaynak Optimizasyonu)
    function handleVisibility() {
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                currentDelay = backgroundDelay;
            } else {
                currentDelay = normalDelay;
                sendHeartbeat('view', 'Sekmeye geri dönüldü');
            }
            restartLoop();
        });
    }

    function restartLoop() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        heartbeatInterval = setInterval(function() {
            sendHeartbeat('heartbeat');
        }, currentDelay);
    }

    // Başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            sendHeartbeat('view');
            setupInteractions();
            setupCheckoutListeners();
            handleVisibility();
            restartLoop();
        });
    } else {
        sendHeartbeat('view');
        setupInteractions();
        setupCheckoutListeners();
        handleVisibility();
        restartLoop();
    }
})();
</script>
