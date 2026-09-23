<!DOCTYPE html>
<html lang="tr" dir="ltr">
    <head>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-583KKT3Q');</script>
        <!-- End Google Tag Manager -->

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZMY22BQK1N"></script>
        
        <!-- Google AdSense -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1770880036279606"
     crossorigin="anonymous"></script>
     
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-ZMY22BQK1N');
        </script>
        

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1">
        <!-- Tarayıcı ve Ana Ekran (Home Screen) İkonları -->
        <link rel="icon" type="image/png" href="/favicon.png?v={{ time() }}">
        <link rel="apple-touch-icon" href="/favicon.png?v={{ time() }}">
        <link rel="icon" type="image/png" sizes="192x192" href="/favicon.png?v={{ time() }}">
        <link rel="icon" type="image/png" sizes="512x512" href="/favicon.png?v={{ time() }}">

        <!-- PWA (Web Uygulaması) Ayarları -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0f172a">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="PatenliAyakkabılar®">
        <title>{{ $title ?? 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri' }}</title>
        <meta name="description" content="{{ $description ?? 'Çocuklar için en güvenli ve eğlenceli patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçeneklerle ücretsiz kargo fırsatı.' }}">
        <meta name="robots" content="{{ $robots ?? 'index, follow' }}">
        <meta name="google" content="notranslate">
        <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
        <link rel="mcp" href="/mcp.json">

        {{-- Arama Motoru Doğrulama --}}
        @php
            $verifications = \App\Models\Setting::whereIn('key', ['seo_google_verification', 'seo_yandex_verification', 'seo_bing_verification'])->pluck('value', 'key');
        @endphp
        @if($verifications->get('seo_google_verification'))
            <meta name="google-site-verification" content="{{ $verifications->get('seo_google_verification') }}">
        @endif
        @if($verifications->get('seo_yandex_verification'))
            <meta name="yandex-verification" content="{{ $verifications->get('seo_yandex_verification') }}">
        @endif
        @if($verifications->get('seo_bing_verification'))
            <meta name="msvalidate.01" content="{{ $verifications->get('seo_bing_verification') }}">
        @endif

        <!-- Locale -->
        <meta property="og:locale" content="tr_TR">
        <meta property="og:site_name" content="Patenli Ayakkabılar">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ $ogType ?? 'website' }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $title ?? 'Patenli Ayakkabılar' }}">
        <meta property="og:description" content="{{ $description ?? 'Işıklı ve tekerlekli ayakkabı modelleri' }}">
        <meta property="og:image" content="{{ $ogImage ?? asset('whatsapp-cover.png?v=' . time()) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">

        <!-- Open Graph / WhatsApp (Kare Önizleme) -->
        <meta property="og:image" content="{{ $ogImageSquare ?? asset('og-square.png?v=' . time()) }}">
        <meta property="og:image:width" content="800">
        <meta property="og:image:height" content="800">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ $title ?? 'Patenli Ayakkabılar' }}">
        <meta name="twitter:description" content="{{ $description ?? 'Işıklı ve tekerlekli ayakkabı modelleri' }}">
        <meta name="twitter:image" content="{{ $ogImage ?? asset('whatsapp-cover.png') }}">

        <!-- Structured Data -->
        @if(isset($schema))
            {!! $schema !!}
        @endif
        {{-- Global Organization + WebSite schema (tüm sayfalarda) --}}
        @if(app()->bound(\App\Services\SchemaService::class))
            {!! app(\App\Services\SchemaService::class)->organization() !!}
            {!! app(\App\Services\SchemaService::class)->website() !!}
        @else
            <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Patenli Ayakkabılar',
                'url' => url('/'),
                'logo' => asset('favicon.png?v=' . time()),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
            </script>
        @endif

        <!-- Analytics -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"></noscript>
        
        <!-- Alpine Plugins -->
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>

        <!-- PayTR iFrame Resizer -->
        <script defer src="https://www.paytr.com/js/iframeResizer.min.js?v2"></script>

        <!-- FontAwesome -->
        <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" as="style" onload="this.onload=null;this.rel='stylesheet'" />
        <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" /></noscript>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            [x-cloak] { display: none !important; }
            @keyframes pageSlideHorizontal {
                0% { opacity: 0; transform: translateX(30px); }
                100% { opacity: 1; transform: none; }
            }
            .page-transition-effect {
                animation: pageSlideHorizontal 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
            }
        </style>
        @stack('head-scripts')
    </head>
    <body class="bg-brand-light text-brand-dark font-sans antialiased flex flex-col min-h-screen overflow-x-hidden">
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-583KKT3Q"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        
        @persist('header-wrapper')
            <livewire:frontend.header />
        @endpersist

        @persist('cart-drawer-wrapper')
            <livewire:frontend.cart-drawer />
        @endpersist

        @persist('search-modal-wrapper')
            <livewire:frontend.search-modal />
        @endpersist
        
        <main class="flex-grow page-transition-effect">
            {{ $slot }}
        </main>

        @persist('footer-wrapper')
            <!-- Bottom Marquee -->
            <div class="bg-white border-y border-gray-200 py-5 sm:py-7 overflow-hidden w-full relative">
                <div class="marquee-content flex whitespace-nowrap items-center">
                    @for ($i = 0; $i < 24; $i++)
                        <span class="text-black font-black text-xl sm:text-2xl md:text-3xl tracking-[0.2em] uppercase mx-6 md:mx-12">HER YERDE KAY</span>
                    @endfor
                </div>
            </div>

            @php
                $footerSettings = \App\Models\Setting::whereIn('key', [
                    'footer_description',
                    'footer_copyright',
                    'footer_facebook',
                    'footer_instagram',
                    'footer_tiktok',
                    'footer_twitter',
                    'footer_youtube',
                ])->pluck('value', 'key')->toArray();
                
                $footerDesc = $footerSettings['footer_description'] ?? 'Çocukların eğlenirken güvende olması için ürün seçimini, kargo sürecini ve satış sonrası desteği kolaylaştırıyoruz.';
                $footerCopy = $footerSettings['footer_copyright'] ?? '© ' . date('Y') . ' Patenli Ayakkabılar. Tüm hakları saklıdır.';
            @endphp
            <footer class="bg-brand-black text-brand-white pt-16 pb-24 md:pb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                        <div class="col-span-2">
                            <a href="{{ route('home') }}" class="text-2xl font-black text-white tracking-tighter mb-4 inline-block" wire:navigate>
                                PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                            </a>
                            <p class="text-gray-400 max-w-md">{{ $footerDesc }}</p>
                            
                            <div class="mt-6 flex flex-wrap items-center gap-6">
                                <!-- Social Icons -->
                                <div class="flex items-center space-x-4">
                                    @if(!empty($footerSettings['footer_instagram']))
                                    <a href="{{ $footerSettings['footer_instagram'] }}" aria-label="Instagram" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                                    </a>
                                    @endif
                                    @if(!empty($footerSettings['footer_tiktok']))
                                    <a href="{{ $footerSettings['footer_tiktok'] }}" aria-label="TikTok" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                        <svg class="h-5 w-5 mt-0.5" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"><path d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.9 162.9 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/></svg>
                                    </a>
                                    @endif
                                    @if(!empty($footerSettings['footer_facebook']))
                                    <a href="{{ $footerSettings['footer_facebook'] }}" aria-label="Facebook" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                                    </a>
                                    @endif
                                    @if(!empty($footerSettings['footer_twitter']))
                                    <a href="{{ $footerSettings['footer_twitter'] }}" aria-label="Twitter" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                        <svg class="h-5 w-5 mt-0.5" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
                                    </a>
                                    @endif
                                    @if(!empty($footerSettings['footer_youtube']))
                                    <a href="{{ $footerSettings['footer_youtube'] }}" aria-label="YouTube" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" clip-rule="evenodd" /></svg>
                                    </a>
                                    @endif
                                </div>

                                <!-- ETBIS Seal -->
                                @if(config('services.etbis.site_id'))
                                <div>
                                    <a href="https://etbis.ticaret.gov.tr/tr/SiteSorgulamaSonuc?siteId={{ config('services.etbis.site_id') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 group bg-white/5 border border-gray-800 rounded-lg py-2 px-3 hover:bg-white/10 transition-colors">
                                        <div class="bg-green-500/10 p-1.5 rounded-md flex-shrink-0">
                                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </div>
                                        <div class="flex flex-col text-left">
                                            <span class="text-xs font-black text-white tracking-wider leading-none mb-1">ETBİS</span>
                                            <span class="text-[10px] font-medium text-gray-400 leading-none">Güvenli E-Ticaret</span>
                                        </div>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-bold mb-4">Hızlı Menü</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li><a href="{{ route('home') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Ana Sayfa</a></li>
                                <li><a href="{{ route('products.index') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Tüm Ürünler</a></li>
                                <li><a href="{{ route('blog.index') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Rehber Merkezi</a></li>
                                <li><a href="{{ route('pages.show', 'beden-rehberi') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Beden Rehberi</a></li>
                                <li><a href="{{ route('contact') }}" class="hover:text-brand-orange transition-colors" wire:navigate>İletişim</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold mb-4">Kurumsal</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li><a href="{{ route('pages.show', 'hakkimizda') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Hakkımızda</a></li>
                                <li><a href="{{ route('pages.show', 'sikca-sorulan-sorular') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Sıkça Sorulan Sorular</a></li>
                                <li><a href="{{ route('pages.show', 'iade-ve-degisim') }}" class="hover:text-brand-orange transition-colors" wire:navigate>İade ve Değişim</a></li>
                                <li><a href="{{ route('pages.show', 'on-bilgilendirme-formu') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Ön Bilgilendirme Formu</a></li>
                                <li><a href="{{ route('pages.show', 'mesafeli-satis-sozlesmesi') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Mesafeli Satış Sözleşmesi</a></li>
                                <li><a href="{{ route('pages.show', 'gizlilik-politikasi') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Gizlilik Politikası</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Payment Icons -->
                    <div class="flex flex-wrap justify-center gap-2 mb-8">
                        <!-- Amex -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] overflow-hidden">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/amex.svg" alt="Amex" class="w-full h-full object-cover">
                        </div>
                        <!-- PayTR -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100" title="PayTR">
                            <span class="font-black text-[11px] leading-none tracking-tighter">
                                <span style="color: #0b2545;">Pay</span><span style="color: #00a8e1;">TR</span>
                            </span>
                        </div>
                        <!-- Troy -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100">
                            <span class="font-black text-[#00a8e1] tracking-tighter text-[11px] leading-none">TROY</span>
                        </div>
                        <!-- Visa -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1.5 overflow-hidden">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/visa.svg" alt="Visa" class="w-full h-full object-contain">
                        </div>
                        <!-- Havale / EFT -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100" title="Havale / EFT">
                            <div class="flex items-center justify-center space-x-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0b2545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 flex-shrink-0"><path d="M3 21h18"/><path d="M3 10h18"/><path d="M5 6l7-3 7 3"/><path d="M4 10v11"/><path d="M20 10v11"/><path d="M8 14v3"/><path d="M12 14v3"/><path d="M16 14v3"/></svg>
                                <span style="color: #0b2545;" class="font-black text-[9px] leading-none tracking-tighter">Havale</span>
                            </div>
                        </div>
                        <!-- Mastercard -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1.5 overflow-hidden">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/mastercard.svg" alt="Mastercard" class="w-full h-full object-contain">
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-8 flex flex-col items-center justify-center text-sm text-gray-500 text-center">
                        <p>{{ $footerCopy }}</p>
                    </div>
                </div>
            </footer>
        @endpersist

        @persist('mobile-bottom-nav')
        <!-- Mobile Bottom Navigation Bar — Halikoy İkon Kütüphanesi -->
        <div class="md:hidden fixed inset-x-0 bottom-0 w-full bg-white border-t border-gray-100 z-[9999]" style="padding-bottom: env(safe-area-inset-bottom); transform: translateZ(0);">
            <div>
                <div class="grid grid-cols-6 h-[76px] w-full px-2 py-2">
                    
                    {{-- 1. Anasayfa — Halikoy home icon --}}
                    <a href="{{ route('home') }}" wire:navigate class="flex flex-col items-center justify-center gap-[6px] text-gray-800 active:text-black transition-colors">
                        <svg class="w-[24px] h-[24px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.3337 14.1667V10.4538C18.3337 9.09868 18.3337 8.42113 18.1681 7.79394C18.006 7.17971 17.7284 6.602 17.35 6.09172C16.9637 5.57066 16.4346 5.1474 15.3764 4.30088L14.9979 3.99805L14.9979 3.99804C13.2143 2.57117 12.3225 1.85774 11.3335 1.58413C10.4611 1.34279 9.53956 1.34279 8.66717 1.58413C7.67815 1.85774 6.78636 2.57118 5.00277 3.99805L5.00276 3.99805L4.62423 4.30088C3.56607 5.1474 3.037 5.57066 2.65064 6.09172C2.27227 6.602 1.99461 7.17971 1.83251 7.79394C1.66699 8.42113 1.66699 9.09868 1.66699 10.4538V14.1667C1.66699 16.4679 3.53247 18.3333 5.83366 18.3333C6.75413 18.3333 7.50033 17.5871 7.50033 16.6667V13.3333C7.50033 11.9526 8.61961 10.8333 10.0003 10.8333C11.381 10.8333 12.5003 11.9526 12.5003 13.3333V16.6667C12.5003 17.5871 13.2465 18.3333 14.167 18.3333C16.4682 18.3333 18.3337 16.4679 18.3337 14.1667Z"/>
                        </svg>
                        <span class="text-[11px] font-normal leading-none text-gray-800">Anasayfa</span>
                    </a>

                    {{-- 2. Katalog — Halikoy hamburger icon --}}
                    <button x-data @click="$dispatch('open-mobile-catalog')" class="flex flex-col items-center justify-center gap-[6px] text-gray-800 active:text-black transition-colors">
                        <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" d="M3 6H21M3 12H11M3 18H16"/>
                        </svg>
                        <span class="text-[11px] font-normal leading-none text-gray-800">Katalog</span>
                    </button>

                    {{-- 3. Arama — Halikoy search icon --}}
                    <button x-data @click="$dispatch('open-search')" class="flex flex-col items-center justify-center gap-[6px] text-gray-800 active:text-black transition-colors">
                        <svg class="w-[24px] h-[24px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.4007 17.4998L15.3707 14.4698M15.3707 14.4698C16.7279 13.1126 17.5674 11.2376 17.5674 9.1665C17.5674 5.02437 14.2095 1.6665 10.0674 1.6665C5.92525 1.6665 2.56738 5.02437 2.56738 9.1665C2.56738 13.3086 5.92525 16.6665 10.0674 16.6665C12.1385 16.6665 14.0135 15.827 15.3707 14.4698Z"/>
                        </svg>
                        <span class="text-[11px] font-normal leading-none text-gray-800">Arama</span>
                    </button>

                    {{-- 4. Tüm Ürünler — Halikoy grid icon --}}
                    <a href="{{ route('products.index') }}" wire:navigate class="flex flex-col items-center justify-center gap-[6px] text-gray-800 active:text-black transition-colors">
                        <svg class="w-[24px] h-[24px]" viewBox="0 0 21 20" fill="none" stroke="currentColor" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1.7666 4.8665C1.7666 3.7464 1.7666 3.18635 1.98459 2.75852C2.17634 2.3822 2.4823 2.07624 2.85862 1.88449C3.28644 1.6665 3.8465 1.6665 4.9666 1.6665H5.23327C6.35337 1.6665 6.91343 1.6665 7.34125 1.88449C7.71757 2.07624 8.02353 2.3822 8.21528 2.75852C8.43327 3.18635 8.43327 3.7464 8.43327 4.8665V5.13317C8.43327 6.25328 8.43327 6.81333 8.21528 7.24115C8.02353 7.61748 7.71757 7.92344 7.34125 8.11518C6.91343 8.33317 6.35337 8.33317 5.23327 8.33317H4.9666C3.8465 8.33317 3.28644 8.33317 2.85862 8.11518C2.4823 7.92344 2.17634 7.61748 1.98459 7.24115C1.7666 6.81333 1.7666 6.25328 1.7666 5.13317V4.8665Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.7666 4.8665C11.7666 3.7464 11.7666 3.18635 11.9846 2.75852C12.1763 2.3822 12.4823 2.07624 12.8586 1.88449C13.2864 1.6665 13.8465 1.6665 14.9666 1.6665H15.2333C16.3534 1.6665 16.9134 1.6665 17.3413 1.88449C17.7176 2.07624 18.0235 2.3822 18.2153 2.75852C18.4333 3.18635 18.4333 3.7464 18.4333 4.8665V5.13317C18.4333 6.25328 18.4333 6.81333 18.2153 7.24115C18.0235 7.61748 17.7176 7.92344 17.3413 8.11518C16.9134 8.33317 16.3534 8.33317 15.2333 8.33317H14.9666C13.8465 8.33317 13.2864 8.33317 12.8586 8.11518C12.4823 7.92344 12.1763 7.61748 11.9846 7.24115C11.7666 6.81333 11.7666 6.25328 11.7666 5.13317V4.8665Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1.7666 14.8665C1.7666 13.7464 1.7666 13.1863 1.98459 12.7585C2.17634 12.3822 2.4823 12.0762 2.85862 11.8845C3.28644 11.6665 3.8465 11.6665 4.9666 11.6665H5.23327C6.35337 11.6665 6.91343 11.6665 7.34125 11.8845C7.71757 12.0762 8.02353 12.3822 8.21528 12.7585C8.43327 13.1863 8.43327 13.7464 8.43327 14.8665V15.1332C8.43327 16.2533 8.43327 16.8133 8.21528 17.2412C8.02353 17.6175 7.71757 17.9234 7.34125 18.1152C6.91343 18.3332 6.35337 18.3332 5.23327 18.3332H4.9666C3.8465 18.3332 3.28644 18.3332 2.85862 18.1152C2.4823 17.9234 2.17634 17.6175 1.98459 17.2412C1.7666 16.8133 1.7666 16.2533 1.7666 15.1332V14.8665Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.7666 14.8665C11.7666 13.7464 11.7666 13.1863 11.9846 12.7585C12.1763 12.3822 12.4823 12.0762 12.8586 11.8845C13.2864 11.6665 13.8465 11.6665 14.9666 11.6665H15.2333C16.3534 11.6665 16.9134 11.6665 17.3413 11.8845C17.7176 12.0762 18.0235 12.3822 18.2153 12.7585C18.4333 13.1863 18.4333 13.7464 18.4333 14.8665V15.1332C18.4333 16.2533 18.4333 16.8133 18.2153 17.2412C18.0235 17.6175 17.7176 17.9234 17.3413 18.1152C16.9134 18.3332 16.3534 18.3332 15.2333 18.3332H14.9666C13.8465 18.3332 13.2864 18.3332 12.8586 18.1152C12.4823 17.9234 12.1763 17.6175 11.9846 17.2412C11.7666 16.8133 11.7666 16.2533 11.7666 15.1332V14.8665Z"/>
                        </svg>
                        <span class="text-[11px] font-normal leading-none text-gray-800 whitespace-nowrap">Tüm Ürünler</span>
                    </a>

                    {{-- 5. Sepetim — Halikoy cart icon --}}
                    <button x-data @click="$dispatch('toggle-cart')" class="flex flex-col items-center justify-center gap-[6px] text-gray-800 active:text-black transition-colors relative">
                        <div class="relative">
                            <svg class="w-[24px] h-[24px]" viewBox="0 0 21 20" fill="none" stroke="currentColor" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M1.13281 0.833547L1.54948 0.833496V0.833496C2.78264 0.833526 3.86637 1.65101 4.20515 2.83672L4.3471 3.33355M4.3471 3.33355L5.63992 7.85843C6.11531 9.5223 6.35301 10.3542 6.83827 10.9717C7.26659 11.5168 7.82919 11.9412 8.47093 12.2033C9.19799 12.5002 10.0632 12.5002 11.7937 12.5002H12.8091C13.8588 12.5002 14.3837 12.5002 14.8433 12.39C15.9407 12.127 16.8759 11.4127 17.4184 10.4232C17.6456 10.0087 17.7837 9.50235 18.0599 8.4896V8.4896C18.3964 7.2559 18.5646 6.63905 18.5321 6.13859C18.4535 4.93171 17.6578 3.89005 16.5142 3.49667C16.0399 3.33355 15.4005 3.33355 14.1218 3.33355H4.3471ZM10.2995 16.6668C10.2995 17.5873 9.55329 18.3335 8.63281 18.3335C7.71234 18.3335 6.96615 17.5873 6.96615 16.6668C6.96615 15.7464 7.71234 15.0002 8.63281 15.0002C9.55329 15.0002 10.2995 15.7464 10.2995 16.6668ZM16.9661 16.6668C16.9661 17.5873 16.22 18.3335 15.2995 18.3335C14.379 18.3335 13.6328 17.5873 13.6328 16.6668C13.6328 15.7464 14.379 15.0002 15.2995 15.0002C16.22 15.0002 16.9661 15.7464 16.9661 16.6668Z"/>
                            </svg>
                            <span class="absolute -top-1.5 -right-2.5 bg-black text-white text-[8px] font-bold min-w-[15px] h-[15px] flex items-center justify-center rounded-full leading-none cart-badge-count" style="display: none;"></span>
                        </div>
                        <span class="text-[11px] font-normal leading-none text-gray-800">Sepetim</span>
                    </button>

                    {{-- 6. Hesabım — Halikoy account icon --}}
                    <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" wire:navigate class="flex flex-col items-center justify-center gap-[6px] text-gray-800 active:text-black transition-colors">
                        <svg class="w-[24px] h-[24px]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5.5" y="1.3335" width="9" height="9" rx="4.5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 12.917C11.25 12.917 13.3333 13.1948 13.75 13.3337C14.1667 13.4725 16.8333 14.0003 17.5 15.0003C18.3333 16.2503 18.3333 16.667 18.3333 18.3337"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 12.917C8.75 12.917 6.66667 13.1948 6.25 13.3337C5.83333 13.4725 3.16667 14.0003 2.5 15.0003C1.66667 16.2503 1.66667 16.667 1.66667 18.3337"/>
                        </svg>
                        <span class="text-[11px] font-normal leading-none text-gray-800">Hesabım</span>
                    </a>

                </div>
            </div>
        </div>
        @endpersist

        <x-frontend.toast-notification />
        
        <livewire:frontend.site-popup />

        <!-- Mobile Catalog Modal -->
        <div x-data="{ open: false }" 
             x-init="$watch('open', value => {
                 if (value) document.body.classList.add('overflow-hidden');
                 else document.body.classList.remove('overflow-hidden');
             })"
             @toggle-catalog.window="open = !open" 
             @keydown.escape.window="open = false"
             class="relative z-40" 
             x-cloak
             x-show="open">
            
            <div x-show="open" 
                 x-transition.opacity 
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm" style="z-index: 9995;" 
                 @click="open = false"></div>
                 
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full"
                 class="fixed inset-x-0 bottom-0 top-[10vh] md:top-[15vh] bg-white rounded-t-[2rem] shadow-[0_-20px_60px_-15px_rgba(0,0,0,0.3)] flex flex-col overflow-hidden" style="z-index: 9996;">
                 
                 <div class="px-6 py-8 border-b border-gray-100 flex flex-col justify-between relative">
                    <button @click="open = false" aria-label="Kataloğu Kapat" class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-black transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <div>
                        <p class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">KOLEKSİYON / {{ date('Y') }}</p>
                        <h2 class="text-4xl sm:text-5xl font-black text-gray-900 tracking-tighter">Katalog</h2>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" @click="open = false" wire:navigate class="inline-flex text-[11px] font-black text-gray-900 uppercase tracking-[0.2em] items-center gap-1.5 border-b-2 border-gray-900 pb-0.5 hover:text-brand-orange hover:border-brand-orange transition-colors">
                            TÜM ÜRÜNLER
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                 </div>
                 
                 <div class="flex-1 overflow-y-auto px-6 py-2 pb-24">
                    @php
                        $categories = \Illuminate\Support\Facades\Cache::remember('mobile_catalog_categories_v2', 3600, function () {
                            return \App\Models\Category::where('status', true)->withCount(['products' => function($q) {
                                $q->where('status', true);
                            }])->orderBy('sort_order')->get();
                        });
                    @endphp
                    
                    <ul class="flex flex-col">
                        @foreach($categories as $index => $category)
                        <li class="border-b border-gray-100 last:border-0">
                            <a href="{{ route('category.show', ['slug' => $category->slug]) }}" @click="open = false" wire:navigate class="flex items-center justify-between py-6 group">
                                <div class="flex items-center gap-5 sm:gap-8">
                                    <span class="text-sm font-bold text-gray-300 w-6">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-3xl sm:text-4xl font-black text-gray-900 group-hover:text-brand-orange transition-colors tracking-tight">{{ $category->name }}</span>
                                </div>
                                <div class="flex items-center gap-3 sm:gap-4">
                                    <span class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest whitespace-nowrap">{{ $category->products_count }} ÜRÜN</span>
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-brand-orange transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                 </div>
            </div>
        </div>
        <!-- Telefonla Arama Widget'ı -->
        @php
            $callWidgetActive = \Illuminate\Support\Facades\Cache::remember('setting_call_widget_active', 3600, function() {
                return \App\Models\Setting::where('key', 'call_widget_active')->value('value') ?? '1';
            });
        @endphp
        
        @if($callWidgetActive === '1')
        <style>
            .call-widget {
                position: fixed;
                z-index: 50;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #0B132B;
                color: white;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                right: 20px;
                bottom: calc(85px + env(safe-area-inset-bottom)); /* iOS'ta alt bar kapanınca kaymayı önlemek için */
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
                transition: all 0.3s ease;
            }
            @media (min-width: 768px) {
                .call-widget {
                    bottom: 24px;
                    right: 24px;
                }
            }
            .call-widget:hover {
                transform: scale(1.1);
                filter: brightness(1.25);
            }
        </style>
        <a href="tel:08503073164" class="call-widget group" aria-label="Bizi Arayın">
            <svg class="w-6 h-6 animate-[pulse_2s_ease-in-out_infinite]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            
            <!-- Tooltip -->
            <span class="absolute top-1/2 -translate-y-1/2 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap shadow-xl pointer-events-none tracking-wide hidden md:block" style="background-color: #0B132B; right: 70px;">
                Bizi Arayın
            </span>
        </a>
        @endif

        <!-- Google Customer Reviews -->
        <style>
            @media (max-width: 767px) {
                #gcr-badge-container,
                #gcr-badge-container iframe,
                iframe[id*="gapi_ratingbadge"],
                iframe[name*="gapi_ratingbadge"],
                iframe[src*="ratingbadge"],
                iframe[src*="customerreviews"],
                gmp-ratingbadge {
                    bottom: calc(65px + env(safe-area-inset-bottom)) !important;
                    left: 5px !important;
                }
            }
        </style>
        <script src="https://apis.google.com/js/platform.js?onload=renderGoogleGCR" async defer></script>
        <script>
          window.renderGoogleGCR = function() {
            var ratingBadgeContainer = document.createElement("div");
            ratingBadgeContainer.id = "gcr-badge-container";
            document.body.appendChild(ratingBadgeContainer);
            window.gapi.load('ratingbadge', function() {
              window.gapi.ratingbadge.render(ratingBadgeContainer, {"merchant_id": 5828544730, "position": "BOTTOM_LEFT"});
              
              // Sadece mobilde alt menünün üstünde durması için
              if (window.innerWidth < 768) {
                var applyStyles = function() {
                  var elements = document.querySelectorAll('#gcr-badge-container, #gcr-badge-container iframe, iframe[src*="customerreviews"], iframe[src*="ratingbadge"], iframe[name*="gapi_ratingbadge"], gmp-ratingbadge');
                  elements.forEach(function(el) {
                    el.style.setProperty('bottom', 'calc(65px + env(safe-area-inset-bottom))', 'important');
                    el.style.setProperty('left', '5px', 'important');
                    if (el.parentElement && el.parentElement !== document.body && el.parentElement.id !== 'gcr-badge-container' && el.parentElement.style.position === 'fixed') {
                      el.parentElement.style.setProperty('bottom', 'calc(65px + env(safe-area-inset-bottom))', 'important');
                      el.parentElement.style.setProperty('left', '5px', 'important');
                    }
                  });
                };

                var checkInterval = setInterval(applyStyles, 100);
                setTimeout(function() { clearInterval(checkInterval); }, 6000);
              }
            });

            if (typeof window.triggerGoogleOptIn === 'function') {
                window.triggerGoogleOptIn();
            }
          }
        </script>

        @livewireScripts

        <!-- Global Toast Notification -->
        <div x-data="{ show: false, message: '', type: 'warning' }"
             @show-notification.window="message = $event.detail.message; type = $event.detail.type || 'warning'; show = true; setTimeout(() => show = false, 4000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             x-cloak
             class="fixed bottom-20 md:bottom-6 left-1/2 -translate-x-1/2 z-[100] max-w-md w-[calc(100%-2rem)]">
            <div class="flex items-center gap-3 px-5 py-4 rounded-2xl shadow-2xl border"
                 :class="type === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-800'">
                <i class="fa-solid fa-triangle-exclamation text-lg shrink-0"
                   :class="type === 'warning' ? 'text-amber-500' : 'text-red-500'"></i>
                <p class="text-sm font-medium flex-1" x-text="message"></p>
                <button @click="show = false" class="shrink-0 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </body>
</html>
