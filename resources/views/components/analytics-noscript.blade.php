@php
    $gtmId = config('services.google.gtm_container_id', env('GTM_CONTAINER_ID'));
@endphp

@if($gtmId)
{{-- Google Tag Manager (noscript) — body etiketinin hemen altına yerleştirin --}}
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
@endif
