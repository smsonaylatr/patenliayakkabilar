<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Instagram Çekilişi | Patenli Ayakkabılar' }}</title>
    <meta name="description" content="{{ $description ?? 'Patenli Ayakkabılar resmi Instagram çekiliş katılım formu.' }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    
    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0f172a; }
    </style>
</head>
<body class="antialiased text-slate-100 min-h-screen flex flex-col justify-between selection:bg-pink-500 selection:text-white">
    
    <!-- Minimal Header (Branding & Back Link) -->
    <header class="w-full border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-pink-500 via-purple-500 to-teal-400 p-0.5 shadow-lg shadow-pink-500/20 group-hover:scale-105 transition-transform">
                    <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center text-lg">
                        👟
                    </div>
                </div>
                <span class="font-extrabold text-base sm:text-lg tracking-tight text-white group-hover:text-pink-300 transition-colors">
                    Patenli<span class="text-pink-400">Ayakkabılar</span>
                </span>
            </a>

            <div class="flex items-center gap-3">
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" 
                   class="hidden sm:inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-800 border border-slate-700 text-pink-300 hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    <span>@patenliayakkabilarcom</span>
                </a>
                <a href="/" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1 py-1.5 px-3 rounded-lg hover:bg-slate-800">
                    <span>Mağazaya Dön</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Minimal Footer -->
    <footer class="w-full border-t border-slate-800/80 bg-slate-950 py-6 text-center text-xs text-slate-500 space-y-2">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                © {{ date('Y') }} Patenli Ayakkabılar®. Tüm Hakları Saklıdır.
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="/gizlilik-politikasi" target="_blank" class="hover:underline">Gizlilik & KVKK</a>
                <a href="/mesafeli-satis-sozlesmesi" target="_blank" class="hover:underline">Katılım Koşulları</a>
                <a href="/iletisim" target="_blank" class="hover:underline">İletişim</a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
