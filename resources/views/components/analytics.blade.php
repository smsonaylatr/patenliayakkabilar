@php
    $tagConfig = \App\Services\GoogleTagService::getConfig();
    $primaryTagId = $tagConfig['primary_tag_id'];
    $gaId = $tagConfig['ga4_id'];
    $googleTagId = $tagConfig['google_tag_id'];
    $gtmId = $tagConfig['gtm_id'];
    $adsId = $tagConfig['ads_id'];
    $consentModeEnabled = $tagConfig['consent_mode_enabled'];
    $enhancedConversionsEnabled = $tagConfig['enhanced_conversions_enabled'];

    $authUser = auth()->user();
    $initialUserData = [];
    if ($enhancedConversionsEnabled) {
        $uName = $authUser?->name ?: session('co_name');
        $uEmail = $authUser?->email ?: session('co_email');
        $uPhone = $authUser?->phone ?: session('co_phone');
        $uCity = session('co_city');
        $uDistrict = session('co_district');
        $uAddress = session('co_address');

        $initialUserData = \App\Services\GoogleTagService::formatUserData(
            $uName,
            $uEmail,
            $uPhone,
            $uCity,
            $uDistrict,
            $uAddress
        );
    }
@endphp

<!-- Google Tag Platform: dataLayer, Consent Mode v2 & gtag() Başlatıcı -->
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}

    @if($consentModeEnabled)
    // Google Consent Mode v2 (Varsayılan + Kayıtlı Tercih Senkronizasyonu)
    (function() {
        var defaultConsent = {
            'ad_storage': 'granted',
            'ad_user_data': 'granted',
            'ad_personalization': 'granted',
            'analytics_storage': 'granted',
            'functionality_storage': 'granted',
            'personalization_storage': 'granted',
            'security_storage': 'granted',
            'wait_for_update': 500
        };
        try {
            var savedConsent = localStorage.getItem('pa_google_consent');
            if (savedConsent) {
                var parsed = JSON.parse(savedConsent);
                if (parsed && typeof parsed === 'object') {
                    for (var k in parsed) {
                        if (Object.prototype.hasOwnProperty.call(parsed, k)) {
                            defaultConsent[k] = parsed[k];
                        }
                    }
                }
            }
        } catch (e) {}
        gtag('consent', 'default', defaultConsent);
        gtag('set', 'url_passthrough', true);
        gtag('set', 'ads_data_redaction', false);
    })();

    window.updateGoogleConsent = function(consentUpdate) {
        if (!consentUpdate || typeof consentUpdate !== 'object') return;
        gtag('consent', 'update', consentUpdate);
        try {
            localStorage.setItem('pa_google_consent', JSON.stringify(consentUpdate));
        } catch (e) {}
    };
    @endif

    // Küresel E-Ticaret ve Bölge Parametreleri
    gtag('set', {
        'currency': 'TRY',
        'country': 'TR',
        'language': 'tr'
    });

    @if($authUser)
    gtag('set', {'user_id': '{{ $authUser->id }}'});
    @endif

    @if($enhancedConversionsEnabled && !empty($initialUserData))
    gtag('set', 'user_data', @json($initialUserData, JSON_UNESCAPED_UNICODE));
    @endif
</script>

@if($primaryTagId)
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $primaryTagId }}"></script>
<script>
    gtag('js', new Date());

    @if($gaId)
    gtag('config', '{{ $gaId }}', {
        'allow_enhanced_conversions': {{ $enhancedConversionsEnabled ? 'true' : 'false' }},
        'currency': 'TRY',
        'cookie_flags': 'SameSite=None;Secure',
        'linker': { 'accept_incoming': true }
        @if($authUser), 'user_id': '{{ $authUser->id }}'@endif
    });
    @endif

    @if($googleTagId && $googleTagId !== $gaId)
    gtag('config', '{{ $googleTagId }}', {
        'allow_enhanced_conversions': {{ $enhancedConversionsEnabled ? 'true' : 'false' }}
    });
    @endif

    @if($adsId)
    gtag('config', '{{ $adsId }}', {
        'allow_enhanced_conversions': {{ $enhancedConversionsEnabled ? 'true' : 'false' }}
    });
    @endif
</script>
@endif

@if($gtmId)
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
<!-- End Google Tag Manager -->
@endif

<!-- Patenli Ayakkabılar — Google Tag Platform & GA4 E-Ticaret Köprüsü -->
<script>
(function() {
    'use strict';

    var tagConfig = {
        ga4Id: @json($gaId),
        gtmId: @json($gtmId),
        adsId: @json($adsId),
        adsPurchaseLabel: @json($tagConfig['ads_purchase_label']),
        adsAddToCartLabel: @json($tagConfig['ads_add_to_cart_label']),
        adsBeginCheckoutLabel: @json($tagConfig['ads_begin_checkout_label']),
        enhancedConversions: @json($enhancedConversionsEnabled)
    };

    var ecommerceEvents = [
        'view_item_list',
        'select_item',
        'view_item',
        'add_to_cart',
        'remove_from_cart',
        'view_cart',
        'begin_checkout',
        'add_shipping_info',
        'add_payment_info',
        'purchase',
        'refund',
        'view_promotion',
        'select_promotion'
    ];

    function normalizePhoneE164(phone) {
        if (!phone) return null;
        var digits = String(phone).replace(/[^0-9]/g, '');
        if (!digits) return null;
        if (digits.length === 10 && digits.charAt(0) === '5') return '+90' + digits;
        if (digits.length === 11 && digits.indexOf('05') === 0) return '+9' + digits;
        if (digits.length === 12 && digits.indexOf('905') === 0) return '+' + digits;
        if (digits.length >= 10) return '+' + digits.replace(/^0+/, '');
        return null;
    }

    window.PaGoogleTag = {
        config: tagConfig,

        /**
         * Google Enhanced Conversions (Gelişmiş Dönüşümler) için kullanıcı verisini ayarlar.
         */
        setUserData: function(rawUser) {
            if (!tagConfig.enhancedConversions || !rawUser || typeof rawUser !== 'object') return;
            var userData = {};

            if (rawUser.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(rawUser.email).trim())) {
                userData.email = String(rawUser.email).trim().toLowerCase();
            }

            var phoneVal = rawUser.phone_number || rawUser.phone;
            if (phoneVal) {
                var e164 = normalizePhoneE164(phoneVal);
                if (e164) userData.phone_number = e164;
            }

            if (rawUser.address && typeof rawUser.address === 'object') {
                userData.address = rawUser.address;
            } else if (rawUser.name || rawUser.city || rawUser.district || rawUser.street) {
                var addr = { country: 'TR' };
                if (rawUser.name) {
                    var parts = String(rawUser.name).trim().split(/\s+/);
                    if (parts.length >= 2) {
                        addr.last_name = parts.pop();
                        addr.first_name = parts.join(' ');
                    } else if (parts.length === 1 && parts[0]) {
                        addr.first_name = parts[0];
                    }
                }
                if (rawUser.city) addr.city = String(rawUser.city).trim();
                if (rawUser.district) addr.region = String(rawUser.district).trim();
                if (rawUser.street) addr.street = String(rawUser.street).trim();
                userData.address = addr;
            }

            if (Object.keys(userData).length > 0) {
                if (typeof window.gtag === 'function') {
                    window.gtag('set', 'user_data', userData);
                }
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    event: 'user_data_updated',
                    user_data: userData
                });
            }
        },

        /**
         * Hem gtag('event', ...) hem de GTM dataLayer'a standartlara uygun gönderim yapar.
         */
        track: function(eventName, params) {
            if (!eventName) return;
            params = params || {};

            window.dataLayer = window.dataLayer || [];
            var isEcommerce = ecommerceEvents.indexOf(eventName) !== -1;

            // Google Tag Platform kuralı: Önceki ecommerce nesnesini sıfırla
            if (isEcommerce) {
                window.dataLayer.push({ ecommerce: null });
            }

            // 1. gtag.js üzerinden gönder
            if (typeof window.gtag === 'function') {
                window.gtag('event', eventName, params);
            }

            // 2. GTM dataLayer uyumluluğu için ecommerce sarmalayıcısı ile gönder (GTM yüklüyse ve gtag.js zaten aynı yapıyı kullanmıyorsa)
            if (isEcommerce) {
                window.dataLayer.push({
                    event: eventName,
                    ecommerce: params
                });
            }

            // 3. Google Ads Dönüşüm Tetikleyicileri (Eğer AW-ID ve Label tanımlıysa)
            if (tagConfig.adsId && typeof window.gtag === 'function') {
                if (eventName === 'purchase' && tagConfig.adsPurchaseLabel) {
                    window.gtag('event', 'conversion', {
                        'send_to': tagConfig.adsId + '/' + tagConfig.adsPurchaseLabel,
                        'value': params.value || 0,
                        'currency': params.currency || 'TRY',
                        'transaction_id': params.transaction_id || ''
                    });
                } else if (eventName === 'add_to_cart' && tagConfig.adsAddToCartLabel) {
                    window.gtag('event', 'conversion', {
                        'send_to': tagConfig.adsId + '/' + tagConfig.adsAddToCartLabel,
                        'value': params.value || 0,
                        'currency': params.currency || 'TRY'
                    });
                } else if (eventName === 'begin_checkout' && tagConfig.adsBeginCheckoutLabel) {
                    window.gtag('event', 'conversion', {
                        'send_to': tagConfig.adsId + '/' + tagConfig.adsBeginCheckoutLabel,
                        'value': params.value || 0,
                        'currency': params.currency || 'TRY'
                    });
                }
            }
        },

        /**
         * Ürün kartına tıklandığında select_item olayını gönderir.
         */
        selectItem: function(item, listName, listId) {
            if (!item || typeof item !== 'object') return;
            var payload = {
                item_list_name: listName || item.item_list_name || 'Ürün Listesi',
                item_list_id: listId || item.item_list_id || 'product_list',
                items: [item]
            };
            this.track('select_item', payload);
        },

        /**
         * Sipariş tamamlandığında (purchase) mükerrer gönderimi önleyerek çalışır.
         */
        trackPurchase: function(purchasePayload, userData) {
            if (!purchasePayload || !purchasePayload.transaction_id) return;
            var storageKey = 'pa_gtag_purchased_' + purchasePayload.transaction_id;

            try {
                if (sessionStorage.getItem(storageKey)) {
                    return; // Aynı oturumda sayfa yenilenirse tekrar sayma
                }
                sessionStorage.setItem(storageKey, '1');
            } catch (e) {}

            if (userData) {
                this.setUserData(userData);
            }

            this.track('purchase', purchasePayload);
        }
    };

    // Global kısayol fonksiyonu
    window.gtagTrack = function(eventName, params) {
        window.PaGoogleTag.track(eventName, params);
    };

    // Livewire 4 sunucu tarafı dispatch('gtag-event', name: '...', params: [...]) dinleyicisi
    window.addEventListener('gtag-event', function(e) {
        var detail = e.detail || {};
        // Livewire 3/4 dizi veya nesne sarma uyumluluğu
        if (Array.isArray(detail) && detail.length > 0) {
            detail = detail[0];
        }
        var eventName = detail.name || detail.event;
        var eventParams = detail.params || detail.data || {};
        if (detail.user_data) {
            window.PaGoogleTag.setUserData(detail.user_data);
        }
        if (eventName) {
            window.PaGoogleTag.track(eventName, eventParams);
        }
    });

    // Livewire SPA (wire:navigate) sayfa geçişlerinde sanal page_view gönderimi
    var initialPageLoaded = false;
    document.addEventListener('livewire:navigated', function() {
        if (!initialPageLoaded) {
            initialPageLoaded = true;
            return;
        }
        if (typeof window.gtag === 'function' && tagConfig.ga4Id) {
            window.gtag('event', 'page_view', {
                page_title: document.title,
                page_location: window.location.href,
                page_path: window.location.pathname + window.location.search
            });
        }
    });
})();
</script>
