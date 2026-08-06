<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Resmi Instagram Çekilişi | Patenli Ayakkabılar®' }}</title>
    <meta name="description" content="{{ $description ?? 'Patenli Ayakkabılar® resmi Instagram çekiliş katılım formu. Şansınızı deneyin ve orijinal patenli ayakkabı kazananı olun.' }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    
    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; }
        /* Prevent iOS Zoom on Input Focus */
        @media screen and (max-width: 767px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
</head>
<body class="antialiased text-slate-900 min-h-screen flex flex-col justify-between bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-100 via-slate-50 to-slate-100">
    
    <!-- Top Announcement Bar -->
    <div class="bg-slate-950 text-slate-200 text-[10px] sm:text-xs font-bold tracking-[0.15em] uppercase py-2 px-3 text-center border-b border-slate-800 leading-tight flex items-center justify-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
        <span>CANLI ÇEKİLİŞ PORTALI • PATENLİ AYAKKABILAR® • RESMİ KATILIM</span>
    </div>

    <!-- Official Corporate Header (Logo Centered Only) -->
    <header class="w-full bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 shadow-sm transition-all">
        <div class="max-w-6xl mx-auto px-4 py-4 sm:py-5 flex items-center justify-center">
            <!-- Official Brand Logo -->
            <a href="/" class="text-xl sm:text-2xl font-black text-slate-900 tracking-tighter uppercase whitespace-nowrap text-center hover:opacity-90 transition-opacity">
                PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
            </a>
        </div>
    </header>

    <!-- Main Body Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Premium Corporate Footer -->
    <footer class="w-full bg-white border-t border-slate-200/80 py-10 sm:py-12 text-xs text-slate-500 mt-16 shadow-inner">
        <div class="max-w-4xl mx-auto px-4 text-center space-y-5">
            <!-- Brand Logo Centered -->
            <div>
                <a href="/" class="text-xl font-black text-slate-900 tracking-tighter uppercase inline-block">
                    PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                </a>
            </div>

            <!-- Footer Navigation Links -->
            <div class="flex flex-wrap justify-center items-center gap-6 sm:gap-8 text-xs sm:text-sm font-semibold text-slate-600">
                <a href="/gizlilik-politikasi" target="_blank" class="hover:text-slate-900 transition-colors">Gizlilik Politikası</a>
                <a href="/mesafeli-satis-sozlesmesi" target="_blank" class="hover:text-slate-900 transition-colors">Katılım Koşulları</a>
                <a href="/iletisim" target="_blank" class="hover:text-slate-900 transition-colors">İletişim</a>
            </div>

            <!-- Phone Support Badge -->
            <div class="pt-1">
                <a href="tel:08503073164" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors border border-slate-200/80">
                    <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>Müşteri Hizmetleri: 0850 307 31 64</span>
                </a>
            </div>

            <!-- Security & Copyright Line -->
            <div class="pt-4 border-t border-slate-100 space-y-1 text-slate-500 text-[11px]">
                <p class="font-medium">© {{ date('Y') }} Patenli Ayakkabılar®. Tüm Hakları Saklıdır.</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Resmi Instagram Çekiliş Portalı • 256-Bit SSL Güvenli Altyapı</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
