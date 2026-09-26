{{-- Canlı Ziyaretçi İstihbarat & Dönüşüm İstemci Motoru (PatenliAyakkabılar® Native Tasarım) --}}
<div id="pa-live-tracker-root" class="relative z-[999999]">
    {{-- 1. Tam Ekran Modal / Popup Backdrop & Container --}}
    <div id="pa-modal-overlay" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300 opacity-0 pointer-events-none hidden" aria-modal="true" role="dialog">
        <div id="pa-modal-container" class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 text-center font-sans transform scale-95 transition-all duration-300 overflow-hidden">
            {{-- Dinamik Modal İçeriği --}}
        </div>
    </div>

    {{-- 2. Floating Toast / Alert Konteyneri --}}
    <div id="pa-toast-container" class="fixed top-5 right-5 z-[999999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0">
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

    // Güvenli HTML escape
    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Modal Yönetimi
    function openModal(contentHtml) {
        var overlay = document.getElementById('pa-modal-overlay');
        var container = document.getElementById('pa-modal-container');
        if (!overlay || !container) return;

        container.innerHTML = contentHtml;
        overlay.classList.remove('hidden');

        // Body kaydırmasını kilitle
        document.body.style.overflow = 'hidden';

        // Animasyonla aç
        requestAnimationFrame(function() {
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        });

        // Mobil cihazda hafif titreşim (dokunsal geri bildirim)
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

        overlay.classList.remove('opacity-100', 'pointer-events-auto');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        container.classList.remove('scale-100');
        container.classList.add('scale-95');

        // Body kaydırmasını geri aç
        document.body.style.overflow = '';

        setTimeout(function() {
            overlay.classList.add('hidden');
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

    // 4. Birebir Site Tasarımında Yönlendirme Modalı
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
        var messageText = cmd.message || 'Sizin için hazırlanan özel sayfaya aktarılıyorsunuz...';

        var modalHtml = 
            '<div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#FF7A1A] via-amber-400 to-[#FF7A1A]"></div>' +
            
            '<button type="button" id="pa-redirect-close-btn" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shadow-xs" aria-label="Kapat">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>' +
            '</button>' +

            '<div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-orange-50 text-[#FF7A1A] border border-orange-200/60 mb-4">' +
                '<span class="w-2 h-2 rounded-full bg-[#FF7A1A] animate-ping"></span>' +
                '<span>SAYFA YÖNLENDİRMESİ</span>' +
            '</div>' +

            '<div class="w-16 h-16 rounded-2xl bg-slate-900 text-white flex items-center justify-center mx-auto mb-3.5 shadow-xl ring-4 ring-slate-100">' +
                '<svg class="w-8 h-8 text-[#FF7A1A] transform -rotate-45" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>' +
                '</svg>' +
            '</div>' +

            '<h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-snug">Özel Fırsat Sayfasına Geçiş Yapılıyor</h3>' +
            '<p class="text-sm text-slate-600 font-normal leading-relaxed mt-2 px-2">' + escapeHtml(messageText) + '</p>' +

            '<div class="inline-flex items-center gap-2 mt-4 px-4 py-1.5 bg-slate-100 rounded-full text-xs font-bold text-slate-700">' +
                '<svg class="w-3.5 h-3.5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                '<span id="pa-redirect-countdown-text">' + secondsLeft + ' saniye içinde yönlendirileceksiniz</span>' +
            '</div>' +

            '<div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden p-0.5">' +
                '<div id="pa-redirect-progress" class="bg-gradient-to-r from-[#FF7A1A] to-amber-500 h-full rounded-full transition-all duration-1000 ease-linear shadow-xs" style="width: 100%;"></div>' +
            '</div>' +

            '<div class="flex items-center gap-2.5 mt-6">' +
                '<a href="' + escapeHtml(targetUrl) + '" id="pa-redirect-go-btn" class="flex-1 py-3.5 px-5 bg-[#FF7A1A] hover:bg-[#e56a10] text-white font-extrabold text-sm rounded-full shadow-lg shadow-orange-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 cursor-pointer">' +
                    '<span>Hemen Geçiş Yap</span>' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>' +
                '</a>' +
                '<button type="button" id="pa-redirect-cancel-btn" class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-full transition cursor-pointer">' +
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
            if (textEl) textEl.textContent = secondsLeft + ' saniye içinde yönlendirileceksiniz';
            if (progEl) progEl.style.width = Math.max(0, (secondsLeft / totalSeconds) * 100) + '%';

            if (secondsLeft <= 0) {
                clearInterval(activeRedirectTimer);
                activeRedirectTimer = null;
                window.location.href = targetUrl;
            }
        }, 1000);
    }

    // 5. Birebir Site Tasarımında Fırsat / Kupon Pop-up'ı
    function executeOfferCommand(cmd) {
        var couponHtml = '';
        if (cmd.coupon_code) {
            couponHtml = 
                '<div class="mt-5 p-4 rounded-2xl bg-gradient-to-r from-orange-50 via-amber-50/50 to-orange-50 border-2 border-dashed border-[#FF7A1A]/40 flex items-center justify-between gap-3 text-left shadow-xs relative">' +
                    '<div class="min-w-0 flex-1">' +
                        '<div class="text-[10px] font-bold uppercase tracking-wider text-orange-600/90 flex items-center gap-1">' +
                            '<svg class="w-3 h-3 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>' +
                            '<span>İndirim Kuponunuz</span>' +
                        '</div>' +
                        '<div class="font-mono text-xl font-black tracking-widest text-[#FF7A1A] mt-0.5 select-all">' + escapeHtml(cmd.coupon_code) + '</div>' +
                    '</div>' +
                    '<button type="button" id="pa-copy-coupon-btn" class="shrink-0 px-4 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow flex items-center gap-1.5 cursor-pointer active:scale-95">' +
                        '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>' +
                        '<span id="pa-copy-btn-text">Kopyala</span>' +
                    '</button>' +
                '</div>';
        }

        var actionBtnHtml = '';
        if (cmd.action_button && cmd.action_url) {
            actionBtnHtml = 
                '<a href="' + escapeHtml(cmd.action_url) + '" class="w-full mt-5 py-3.5 px-6 bg-[#FF7A1A] hover:bg-[#e56a10] text-white font-extrabold text-sm sm:text-base rounded-full shadow-lg shadow-orange-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 cursor-pointer">' +
                    '<span>' + escapeHtml(cmd.action_button) + '</span>' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>' +
                '</a>';
        } else if (cmd.coupon_code) {
            actionBtnHtml = 
                '<button type="button" id="pa-use-coupon-btn" class="w-full mt-5 py-3.5 px-6 bg-[#FF7A1A] hover:bg-[#e56a10] text-white font-extrabold text-sm sm:text-base rounded-full shadow-lg shadow-orange-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 cursor-pointer">' +
                    '<span>Kuponu Kopyala & Alışverişe Devam Et</span>' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>' +
                '</button>';
        }

        var modalHtml = 
            '<div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#FF7A1A] via-amber-400 to-[#FF7A1A]"></div>' +
            
            '<button type="button" id="pa-modal-close-btn" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shadow-xs" aria-label="Kapat">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>' +
            '</button>' +

            '<div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-orange-50 text-[#FF7A1A] border border-orange-200/60 mb-4">' +
                '<svg class="w-3.5 h-3.5 text-[#FF7A1A]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"/></svg>' +
                '<span>PATENLİAYAKKABILAR® ÖZEL FIRSAT</span>' +
            '</div>' +

            '<div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#FF7A1A] to-amber-500 text-white flex items-center justify-center mx-auto mb-3.5 shadow-lg shadow-orange-500/25 ring-4 ring-orange-50">' +
                '<svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H4.5a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>' +
                '</svg>' +
            '</div>' +

            '<h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-snug">' + escapeHtml(cmd.title || 'Size Özel Fırsat!') + '</h3>' +
            '<p class="text-sm text-slate-600 font-normal leading-relaxed mt-2.5 px-2">' + escapeHtml(cmd.message || '') + '</p>' +

            couponHtml +
            actionBtnHtml +

            '<div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-center gap-3 sm:gap-4 text-[11px] text-slate-500 font-medium">' +
                '<span class="flex items-center gap-1.5">' +
                    '<svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>' +
                    'Ücretsiz Kargo' +
                '</span>' +
                '<span class="text-slate-300">•</span>' +
                '<span class="flex items-center gap-1.5">' +
                    '<svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>' +
                    'Güvenli Ödeme' +
                '</span>' +
                '<span class="text-slate-300">•</span>' +
                '<span class="flex items-center gap-1.5">' +
                    '<svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>' +
                    'Hızlı Teslimat' +
                '</span>' +
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
                        copyBtn.classList.remove('bg-slate-900', 'hover:bg-black');
                        copyBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
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
        toastEl.className = 'pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-slate-200/80 flex items-start gap-3.5 transform translate-y-4 opacity-0 transition-all duration-300 font-sans';

        var iconSvg = '<svg class="w-5 h-5 text-[#FF7A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        if (cmd.type === 'success') {
            iconSvg = '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }

        toastEl.innerHTML = 
            '<div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">' +
                iconSvg +
            '</div>' +
            '<div class="flex-1 min-w-0 pt-0.5">' +
                '<h4 class="font-bold text-sm text-slate-900 leading-snug">' + escapeHtml(cmd.title || 'Bilgilendirme') + '</h4>' +
                '<p class="text-xs text-slate-600 mt-1 leading-relaxed">' + escapeHtml(cmd.message || '') + '</p>' +
            '</div>' +
            '<button type="button" class="text-slate-400 hover:text-slate-600 p-1 shrink-0 cursor-pointer">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
            '</button>';

        toastContainer.appendChild(toastEl);

        // Animasyonla aç
        requestAnimationFrame(function() {
            toastEl.classList.remove('translate-y-4', 'opacity-0');
            toastEl.classList.add('translate-y-0', 'opacity-100');
        });

        // Kapatma
        var removeToast = function() {
            toastEl.classList.remove('translate-y-0', 'opacity-100');
            toastEl.classList.add('translate-y-4', 'opacity-0');
            setTimeout(function() {
                if (toastEl.parentNode) toastEl.parentNode.removeChild(toastEl);
            }, 300);
        };

        toastEl.querySelector('button').onclick = removeToast;
        setTimeout(removeToast, 6000);
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

    // 8. Tab Görünürlük Yönetimi (Pil & Kaynak Optimizasyonu)
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
            handleVisibility();
            restartLoop();
        });
    } else {
        sendHeartbeat('view');
        setupInteractions();
        handleVisibility();
        restartLoop();
    }
})();
</script>
