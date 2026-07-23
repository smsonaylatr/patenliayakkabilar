<!DOCTYPE html>
<html lang="tr" dir="ltr">
    <head>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MTHFWWCB');</script>
        <!-- End Google Tag Manager -->

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZMY22BQK1N"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-ZMY22BQK1N');
        </script>
        

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
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
        <title>{{ $title ?? 'Patenli Ayakkabılar | Tekerlekli Ayakkabı Modelleri ve Fiyatları' }}</title>
        <meta name="description" content="{{ $description ?? 'Çocuklar için en güvenli ve eğlenceli patenli ayakkabı modelleri. Işıklı, tek ve çift tekerlekli seçeneklerle ücretsiz kargo fırsatı.' }}">
        <meta name="robots" content="{{ $robots ?? 'index, follow' }}">
        <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Alpine Plugins -->
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>

        <!-- PayTR iFrame Resizer -->
        <script src="https://www.paytr.com/js/iframeResizer.min.js?v2"></script>

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
    </head>
    <body class="bg-brand-light text-brand-dark font-sans antialiased flex flex-col min-h-screen overflow-x-hidden">
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MTHFWWCB"
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
                            
                            <div class="mt-6 flex space-x-4">
                                <!-- Social Icons -->
                                @if(!empty($footerSettings['footer_instagram']))
                                <a href="{{ $footerSettings['footer_instagram'] }}" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                                </a>
                                @endif
                                @if(!empty($footerSettings['footer_tiktok']))
                                <a href="{{ $footerSettings['footer_tiktok'] }}" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                    <svg class="h-5 w-5 mt-0.5" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true"><path d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.9 162.9 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/></svg>
                                </a>
                                @endif
                                @if(!empty($footerSettings['footer_facebook']))
                                <a href="{{ $footerSettings['footer_facebook'] }}" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                                </a>
                                @endif
                                @if(!empty($footerSettings['footer_twitter']))
                                <a href="{{ $footerSettings['footer_twitter'] }}" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                    <svg class="h-5 w-5 mt-0.5" fill="currentColor" viewBox="0 0 512 512" aria-hidden="true"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
                                </a>
                                @endif
                                @if(!empty($footerSettings['footer_youtube']))
                                <a href="{{ $footerSettings['footer_youtube'] }}" target="_blank" class="text-gray-400 hover:text-brand-orange transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" clip-rule="evenodd" /></svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-bold mb-4">Hızlı Menü</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li><a href="{{ route('home') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Ana Sayfa</a></li>
                                <li><a href="{{ route('products.index') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Tüm Ürünler</a></li>
                                <li><a href="{{ route('blog.index') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Rehber Merkezi</a></li>
                                <li><a href="{{ route('order.tracking') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Sipariş Takip</a></li>
                                <li><a href="{{ route('contact') }}" class="hover:text-brand-orange transition-colors" wire:navigate>İletişim</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold mb-4">Kurumsal</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li><a href="{{ route('pages.show', 'hakkimizda') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Hakkımızda</a></li>
                                <li><a href="{{ route('pages.show', 'sikca-sorulan-sorular') }}" class="hover:text-brand-orange transition-colors" wire:navigate>Sıkça Sorulan Sorular</a></li>
                                <li><a href="{{ route('pages.show', 'iade-ve-degisim') }}" class="hover:text-brand-orange transition-colors" wire:navigate>İade ve Değişim</a></li>
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
                        <!-- Apple Pay -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100">
                            <svg viewBox="0 0 384 512" class="w-2.5 h-2.5 mr-0.5 text-black" fill="currentColor"><path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/></svg>
                            <span class="font-bold text-black text-[10px] leading-none">Pay</span>
                        </div>
                        <!-- Troy -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100">
                            <span class="font-black text-[#00a8e1] tracking-tighter text-[11px] leading-none">TROY</span>
                        </div>
                        <!-- Visa -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1.5 overflow-hidden">
                            <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/visa.svg" alt="Visa" class="w-full h-full object-contain">
                        </div>
                        <!-- Google Pay -->
                        <div class="bg-white rounded flex items-center justify-center w-[50px] h-[32px] px-1 overflow-hidden border border-gray-100">
                            <span class="font-bold text-gray-600 text-[10px] leading-none flex items-center">
                                <span class="text-blue-500 mr-0.5 text-[11px]">G</span>Pay
                            </span>
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
        <!-- Mobile Bottom Navigation Bar (Floating Action Button Design) -->
        <div class="md:hidden fixed inset-x-0 bottom-0 w-full bg-white border-t border-gray-200 z-[9999] shadow-[0_-10px_30px_rgba(0,0,0,0.08)]" style="padding-bottom: env(safe-area-inset-bottom); transform: translateZ(0);">
            <div class="flex justify-between items-center h-[64px] px-1 relative w-full max-w-full">
                
                <a href="{{ route('home') }}" wire:navigate class="flex flex-col items-center justify-center w-full h-full text-brand-black hover:text-brand-orange transition-colors">
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-[10px] font-medium leading-none tracking-wide mt-1">Ana Sayfa</span>
                </a>
                
                <button x-data @click="$dispatch('toggle-catalog')" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-brand-orange transition-colors">
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="text-[10px] font-medium leading-none tracking-wide mt-1">Katalog</span>
                </button>
                
                <!-- Center Floating Button (Sepet) -->
                <div class="relative w-full flex justify-center h-full pointer-events-none">
                    <button x-data @click="$dispatch('toggle-cart')" class="pointer-events-auto absolute flex items-center justify-center w-[68px] h-[68px] bg-black text-white rounded-full border-[6px] border-white shadow-[0_8px_20px_rgba(0,0,0,0.2)] hover:bg-gray-900 hover:scale-105 transition-all duration-300" style="top: -32px;">
                        <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </button>
                    <!-- Text below the floating button -->
                    <span class="absolute text-[10px] font-medium text-gray-900 pointer-events-none leading-none tracking-wide" style="bottom: 11px;">Sepet</span>
                </div>
                
                <a href="{{ route('order.tracking') }}" wire:navigate class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-brand-orange transition-colors">
                    <!-- Package / Order tracking icon -->
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="text-[10px] font-medium whitespace-nowrap leading-none tracking-wide mt-1">Sipariş Takip</span>
                </a>
                
                <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" wire:navigate class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-brand-orange transition-colors">
                    <svg class="w-[24px] h-[24px] mb-[4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-[10px] font-medium leading-none tracking-wide mt-1">Hesabım</span>
                </a>
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
                    <button @click="open = false" class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-black transition-colors">
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
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" @click="open = false" wire:navigate class="flex items-center justify-between py-6 group">
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
                z-index: 99999;
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

        @livewireScripts
    </body>
</html>
