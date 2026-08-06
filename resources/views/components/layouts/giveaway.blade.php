<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Instagram Çekilişi | Patenli Ayakkabılar®' }}</title>
    <meta name="description" content="{{ $description ?? 'Patenli Ayakkabılar® resmi Instagram çekiliş katılım formu.' }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    
    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #f8fafc; }
        /* Prevent iOS Zoom on Input Focus */
        @media screen and (max-width: 767px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
</head>
<body class="antialiased text-gray-900 min-h-screen flex flex-col justify-between bg-slate-50">
    
    <!-- Top Announcement Bar -->
    <div class="bg-black text-white text-[10px] sm:text-xs font-semibold tracking-wider uppercase py-1.5 sm:py-2 px-2 text-center border-b border-gray-800 leading-tight">
        INSTAGRAM RESMİ ÇEKİLİŞ PORTALI • PATENLİ AYAKKABILAR®
    </div>

    <!-- Official Corporate Header (Logo Centered Only) -->
    <header class="w-full bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3.5 sm:py-4 flex items-center justify-center">
            <!-- Official Brand Logo -->
            <a href="/" class="text-xl sm:text-2xl font-black text-gray-900 tracking-tighter uppercase whitespace-nowrap text-center">
                PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
            </a>
        </div>
    </header>

    <!-- Main Body Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Corporate Footer -->
    <footer class="w-full bg-white border-t border-gray-200 py-8 sm:py-10 text-xs text-gray-500 mt-12">
        <div class="max-w-4xl mx-auto px-4 text-center space-y-4">
            <!-- Brand Logo Centered -->
            <div>
                <a href="/" class="text-lg sm:text-xl font-black text-gray-900 tracking-tighter uppercase inline-block">
                    PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                </a>
            </div>

            <!-- Footer Navigation Links -->
            <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                <a href="/gizlilik-politikasi" target="_blank" class="hover:text-black transition-colors">Gizlilik Politikası</a>
                <span class="text-gray-300">•</span>
                <a href="/mesafeli-satis-sozlesmesi" target="_blank" class="hover:text-black transition-colors">Katılım Koşulları</a>
                <span class="text-gray-300">•</span>
                <a href="/iletisim" target="_blank" class="hover:text-black transition-colors">İletişim</a>
                <span class="text-gray-300">•</span>
                <a href="tel:08503073164" class="hover:text-black transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    0850 307 31 64
                </a>
            </div>

            <!-- Security & Copyright Line -->
            <div class="pt-3 border-t border-gray-100 space-y-1 text-gray-500 text-[11px]">
                <p>© {{ date('Y') }} Patenli Ayakkabılar®. Tüm Hakları Saklıdır.</p>
                <p class="text-[10px] text-gray-400">Resmi Instagram Çekiliş Portalı • Güvenli KVKK Altyapısı</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
