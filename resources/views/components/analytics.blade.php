@php
    $gaId = config('services.google.analytics_id', env('GOOGLE_ANALYTICS_ID'));
    $gtmId = config('services.google.gtm_container_id', env('GTM_CONTAINER_ID'));
@endphp

{{-- Google Analytics 4 --}}
@if($gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $gaId }}', {
        'anonymize_ip': true,
        'cookie_flags': 'SameSite=None;Secure'
    });
</script>
@endif

{{-- Google Tag Manager (Head) --}}
@if($gtmId)
<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');
</script>
@endif

{{--
    GTM Noscript:
    Aşağıdaki kodu <body> etiketinin hemen altına ekleyin:

    @if($gtmId)
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    @endif
--}}
