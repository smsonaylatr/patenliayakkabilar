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

    <!-- Corporate Footer (Centered) -->
    <footer class="w-full bg-white border-t border-gray-200 py-8 text-xs text-gray-500">
        <div class="max-w-4xl mx-auto px-4 text-center space-y-4 flex flex-col items-center justify-center">
            <div>
                <a href="/" class="font-black text-base text-gray-900 tracking-tighter uppercase inline-block">
                    PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                </a>
            </div>
            <div class="flex flex-wrap justify-center items-center gap-3 sm:gap-6 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                <a href="/gizlilik-politikasi" target="_blank" class="hover:text-black transition-colors">Gizlilik Politikası</a>
                <span class="text-gray-300 hidden sm:inline">•</span>
                <a href="/mesafeli-satis-sozlesmesi" target="_blank" class="hover:text-black transition-colors">Katılım Koşulları</a>
                <span class="text-gray-300 hidden sm:inline">•</span>
                <a href="/iletisim" target="_blank" class="hover:text-black transition-colors">İletişim</a>
            </div>
            <p class="text-gray-500 text-[11px] font-medium pt-2 border-t border-gray-100 w-full max-w-xs sm:max-w-sm">
                © {{ date('Y') }} Patenli Ayakkabılar®. Tüm Hakları Saklıdır.
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
