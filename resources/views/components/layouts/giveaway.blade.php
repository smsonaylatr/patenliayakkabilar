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

    <!-- Premium Corporate Dark Footer -->
    <footer class="w-full bg-black text-white pt-12 pb-8 border-t border-gray-900 mt-12 text-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-gray-800">
                <!-- Column 1: Brand & Bio -->
                <div class="md:col-span-2 space-y-4 text-center md:text-left">
                    <a href="/" class="text-xl sm:text-2xl font-black text-white tracking-tighter uppercase inline-block">
                        PATENLİ<span class="font-light">AYAKKABILAR&reg;</span>
                    </a>
                    <p class="text-gray-400 text-xs leading-relaxed max-w-sm mx-auto md:mx-0">
                        Türkiye'nin lider patenli ayakkabı markası. Orijinal ürün garantisi, güvenli ödeme ve hızlı kargo ile çocukların mutluluğunu tasarlıyoruz.
                    </p>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 pt-1">
                        <!-- Instagram Button -->
                        <a href="https://instagram.com/patenliayakkabilar" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider px-3.5 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white transition-colors border border-gray-800">
                            <svg class="w-4 h-4 fill-current text-pink-500" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            <span>@patenliayakkabilar</span>
                        </a>
                        
                        <!-- SSL Badge -->
                        <div class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-400 bg-emerald-950/60 border border-emerald-800/60 px-3 py-2 rounded-lg">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>256-Bit SSL Güvenliği</span>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Hızlı Bağlantılar -->
                <div class="space-y-3 text-center md:text-left">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white">Hızlı Bağlantılar</h4>
                    <ul class="space-y-2 text-gray-400 font-medium">
                        <li><a href="/" class="hover:text-white transition-colors">Mağaza Ana Sayfa</a></li>
                        <li><a href="/cekilis" class="hover:text-white transition-colors">Çekiliş Başvurusu</a></li>
                        <li><a href="/iletisim" target="_blank" class="hover:text-white transition-colors">İletişim & Destek</a></li>
                    </ul>
                </div>

                <!-- Column 3: Kurumsal & Yasal -->
                <div class="space-y-3 text-center md:text-left">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white">Yasal Bilgilendirme</h4>
                    <ul class="space-y-2 text-gray-400 font-medium">
                        <li><a href="/gizlilik-politikasi" target="_blank" class="hover:text-white transition-colors">Gizlilik Politikası</a></li>
                        <li><a href="/mesafeli-satis-sozlesmesi" target="_blank" class="hover:text-white transition-colors">Mesafeli Satış Sözleşmesi</a></li>
                        <li><a href="/on-bilgilendirme-formu" target="_blank" class="hover:text-white transition-colors">Çekiliş & KVKK Metni</a></li>
                    </ul>
                </div>
            </div>

            <!-- Payment Logos & Copyright Bar -->
            <div class="pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
                <p class="text-gray-500 text-[11px] font-medium">
                    © {{ date('Y') }} Patenli Ayakkabılar®. Tüm Hakları Saklıdır.
                </p>

                <!-- Payment Method Icons -->
                <div class="flex flex-wrap justify-center items-center gap-2 opacity-90">
                    <div class="bg-white rounded flex items-center justify-center w-[44px] h-[28px] px-1 overflow-hidden border border-gray-100">
                        <span class="font-black text-[#00a8e1] tracking-tighter text-[10px] leading-none">TROY</span>
                    </div>
                    <div class="bg-white rounded flex items-center justify-center w-[44px] h-[28px] px-1 overflow-hidden">
                        <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/visa.svg" alt="Visa" class="w-full h-full object-contain">
                    </div>
                    <div class="bg-white rounded flex items-center justify-center w-[44px] h-[28px] px-1 overflow-hidden">
                        <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/logo/mastercard.svg" alt="Mastercard" class="w-full h-full object-contain">
                    </div>
                    <div class="bg-white rounded flex items-center justify-center w-[44px] h-[28px] px-1 overflow-hidden border border-gray-100">
                        <span class="font-bold text-gray-700 text-[9px] leading-none">PayTR</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
