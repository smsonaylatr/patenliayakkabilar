{{-- Canlı Ziyaretçi İstihbarat & Dönüşüm İstemci Motoru --}}
<div id="pa-live-tracker-root" class="fixed z-[99999] pointer-events-none inset-x-0 bottom-6 flex flex-col items-center justify-end px-4 sm:px-6">
    {{-- Dinamik Bildirim & Yönlendirme Konteyneri --}}
    <div id="pa-presence-banner-container" class="pointer-events-auto transition-all duration-300 transform translate-y-8 opacity-0 hidden max-w-md w-full"></div>
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

        // URL parametreleri (UTM)
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

    // 2. Admin Komutlarını İcra Etme (Redirect, Offer, Alert, Reload)
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
            document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#0f172a;color:#f87171;font-family:sans-serif;text-align:center;padding:20px;"><div><h1 style="font-size:24px;margin-bottom:10px;">Erişim Sınırlandırıldı</h1><p style="color:#94a3b8;">' + (cmd.message || 'Bu siteye erişiminiz yönetici tarafından kısıtlanmıştır.') + '</p></div></div>';
        }
    }

    function executeRedirectCommand(cmd) {
        var targetUrl = cmd.target_url || '/';
        var countdown = parseInt(cmd.countdown, 10);
        if (isNaN(countdown) || countdown < 0) countdown = 0;

        if (countdown === 0 && !cmd.message) {
            window.location.href = targetUrl;
            return;
        }

        var container = document.getElementById('pa-presence-banner-container');
        if (!container) return;

        var secondsLeft = countdown > 0 ? countdown : 3;
        var messageText = cmd.message || 'Sizi özel sayfaya yönlendiriyoruz...';

        container.innerHTML = 
            '<div class="bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-2xl shadow-2xl p-4 sm:p-5 border border-white/20 backdrop-blur-md flex flex-col gap-3">' +
                '<div class="flex items-center justify-between gap-3">' +
                    '<div class="flex items-center gap-3">' +
                        '<div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0 animate-bounce">🚀</div>' +
                        '<div>' +
                            '<h4 class="font-bold text-sm tracking-wide uppercase text-white/90">Yönlendiriliyorsunuz</h4>' +
                            '<p class="text-xs text-white/80" id="pa-redirect-countdown-text">' + secondsLeft + ' saniye içinde yönlendirileceksiniz</p>' +
                        '</div>' +
                    '</div>' +
                    '<a href="' + targetUrl + '" class="px-3.5 py-1.5 bg-white text-orange-600 font-bold text-xs rounded-xl shadow hover:bg-orange-50 transition shrink-0">Hemen Git</a>' +
                '</div>' +
                '<p class="text-sm font-medium text-white/95 border-t border-white/10 pt-2">' + messageText + '</p>' +
                '<div class="w-full bg-black/20 rounded-full h-1.5 overflow-hidden">' +
                    '<div id="pa-redirect-progress" class="bg-white h-full transition-all duration-1000 ease-linear" style="width: 100%"></div>' +
                '</div>' +
            '</div>';

        container.classList.remove('hidden');
        setTimeout(function() {
            container.classList.remove('translate-y-8', 'opacity-0');
        }, 50);

        var totalSeconds = secondsLeft;
        var timer = setInterval(function() {
            secondsLeft--;
            var textEl = document.getElementById('pa-redirect-countdown-text');
            var progEl = document.getElementById('pa-redirect-progress');
            if (textEl) textEl.textContent = secondsLeft + ' saniye içinde yönlendirileceksiniz';
            if (progEl) progEl.style.width = Math.max(0, (secondsLeft / totalSeconds) * 100) + '%';

            if (secondsLeft <= 0) {
                clearInterval(timer);
                window.location.href = targetUrl;
            }
        }, 1000);
    }

    function executeOfferCommand(cmd) {
        var container = document.getElementById('pa-presence-banner-container');
        if (!container) return;

        var couponHtml = '';
        if (cmd.coupon_code) {
            couponHtml = 
                '<div class="flex items-center gap-2 bg-orange-50 dark:bg-orange-950/40 border border-dashed border-orange-400 rounded-xl p-2.5 my-1">' +
                    '<span class="text-xs text-gray-500 font-medium">Kupon Kodu:</span>' +
                    '<span class="font-mono font-bold text-sm text-orange-600 bg-white dark:bg-gray-800 px-2 py-0.5 rounded shadow-sm">' + cmd.coupon_code + '</span>' +
                    '<button type="button" id="pa-copy-coupon-btn" class="ml-auto text-xs bg-orange-500 hover:bg-orange-600 text-white font-semibold px-2.5 py-1 rounded-lg transition">' +
                        'Kopyala' +
                    '</button>' +
                '</div>';
        }

        var actionBtnHtml = '';
        if (cmd.action_button && cmd.action_url) {
            actionBtnHtml = 
                '<a href="' + cmd.action_url + '" class="w-full text-center py-2.5 px-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/30 transition block">' +
                    cmd.action_button +
                '</a>';
        }

        container.innerHTML = 
            '<div class="bg-white dark:bg-gray-900 border border-orange-500/30 rounded-2xl shadow-2xl p-5 flex flex-col gap-3 relative overflow-hidden">' +
                '<div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-orange-500 via-amber-400 to-orange-500"></div>' +
                '<div class="flex items-start justify-between gap-3">' +
                    '<div class="flex items-center gap-2.5">' +
                        '<div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-900/50 text-orange-600 flex items-center justify-center text-lg shrink-0">🎁</div>' +
                        '<h4 class="font-bold text-gray-900 dark:text-white text-sm">' + (cmd.title || 'Size Özel Fırsat!') + '</h4>' +
                    '</div>' +
                    '<button type="button" id="pa-close-offer-btn" class="text-gray-400 hover:text-gray-600 text-lg leading-none p-1">×</button>' +
                '</div>' +
                '<p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">' + (cmd.message || '') + '</p>' +
                couponHtml +
                actionBtnHtml +
            '</div>';

        container.classList.remove('hidden');
        setTimeout(function() {
            container.classList.remove('translate-y-8', 'opacity-0');
        }, 50);

        // Kapat Butonu
        var closeBtn = document.getElementById('pa-close-offer-btn');
        if (closeBtn) {
            closeBtn.onclick = function() {
                container.classList.add('translate-y-8', 'opacity-0');
                setTimeout(function() { container.classList.add('hidden'); }, 300);
            };
        }

        // Kupon Kopyala Butonu
        var copyBtn = document.getElementById('pa-copy-coupon-btn');
        if (copyBtn && cmd.coupon_code) {
            copyBtn.onclick = function() {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(cmd.coupon_code);
                    copyBtn.textContent = 'Kopyalandı! ✓';
                    copyBtn.classList.replace('bg-orange-500', 'bg-emerald-600');
                }
            };
        }
    }

    function executeAlertCommand(cmd) {
        if (window.dispatchEvent) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    message: (cmd.title ? cmd.title + ': ' : '') + cmd.message,
                    type: cmd.type || 'info'
                }
            }));
        } else {
            alert(cmd.message);
        }
    }

    // 3. Kullanıcı Hareketlerini Dinleme (Beden & Varyant Tıklamaları)
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

    // 4. Tab Görünürlük Yönetimi (Pil & Kaynak Optimizasyonu)
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
