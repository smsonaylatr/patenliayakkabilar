<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Büyük Lansman | Patenli Ayakkabılar</title>
    <meta name="description" content="Patenli Ayakkabılar yeni nesil eğlence deneyimiyle çok yakında sizlerle. Lansmana özel fırsatları kaçırmamak için hemen kaydolun.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
        
        .glass-panel {
            background: rgba(20, 20, 20, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }
        
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(90deg, #ff6b00, #ffb000);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.4;
            animation: float 10s infinite ease-in-out alternate;
            z-index: 0;
            pointer-events: none;
        }

        .orb-1 { width: 450px; height: 450px; background: #ff6b00; top: -15%; left: -10%; }
        .orb-2 { width: 350px; height: 350px; background: #9d00ff; bottom: -10%; right: -5%; animation-delay: -5s; }
        .orb-3 { width: 250px; height: 250px; background: #00f0ff; top: 40%; left: 40%; animation-delay: -2.5s; opacity: 0.2; }

        @keyframes float {
            0% { transform: translateY(0) scale(1) translateX(0); }
            100% { transform: translateY(50px) scale(1.1) translateX(30px); }
        }
        
        .floating-shoe {
            animation: shoe-float 6s ease-in-out infinite;
            filter: drop-shadow(0 30px 40px rgba(0,0,0,0.7));
        }

        @keyframes shoe-float {
            0% { transform: translateY(0px) rotate(-5deg); }
            50% { transform: translateY(-25px) rotate(-1deg); }
            100% { transform: translateY(0px) rotate(-5deg); }
        }
        
        .input-glow:focus-within {
            box-shadow: 0 0 20px rgba(255, 107, 0, 0.2);
        }
    </style>
</head>
<body class="bg-[#050505] text-white min-h-screen flex items-center justify-center relative selection:bg-brand-orange selection:text-white overflow-hidden">

    <!-- Background Elements -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    
    <!-- Grid Pattern Overlay for Texture -->
    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 50px 50px; pointer-events: none;"></div>

    <main class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-8 py-12 h-full">
        
        <!-- Left Content -->
        <div class="flex-1 text-center lg:text-left flex flex-col items-center lg:items-start pt-10 lg:pt-0">
            <!-- Badge -->
            <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)" 
                 x-show="show" x-transition:enter="transition ease-out duration-700 transform" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                 class="inline-flex items-center gap-2 px-5 py-2 rounded-full glass-panel text-xs sm:text-sm font-semibold text-[#ff6b00] mb-8 uppercase tracking-[0.2em]">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#ff6b00] opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#ff6b00]"></span>
                </span>
                Yakında Sizlerle
            </div>
            
            <!-- Headline -->
            <h1 x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)" 
                x-show="show" x-transition:enter="transition ease-out duration-700 transform delay-100" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                class="text-5xl sm:text-6xl lg:text-[5rem] font-black mb-6 leading-[1.1] tracking-tight">
                Yeni Nesil <br class="hidden sm:block" />
                <span class="text-gradient">Eğlenceye</span><br class="block sm:hidden"/> Hazır Mısın?
            </h1>
            
            <!-- Subtitle -->
            <p x-data="{ show: false }" x-init="setTimeout(() => show = true, 700)" 
               x-show="show" x-transition:enter="transition ease-out duration-700 transform delay-200" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
               class="text-lg sm:text-xl text-gray-300 mb-10 max-w-xl mx-auto lg:mx-0 font-light leading-relaxed">
                Çocuklar için en güvenli, en tarz patenli ayakkabılar geliyor. İlk öğrenen sen ol, lansmana özel <strong class="text-white font-semibold">%30 indirimi</strong> kaçırma.
            </p>
            
            <!-- Countdown Timer -->
            <div x-data="countdown()" class="flex justify-center lg:justify-start gap-3 sm:gap-5 mb-12 w-full max-w-lg">
                <template x-for="(time, index) in timeData" :key="time.label">
                    <div class="glass-panel flex-1 h-24 sm:h-28 rounded-2xl flex flex-col items-center justify-center transform transition-all duration-300 hover:-translate-y-2 hover:border-[#ff6b00]/40 hover:shadow-[0_10px_30px_rgba(255,107,0,0.15)] cursor-default">
                        <span class="text-3xl sm:text-5xl font-black text-white" x-text="time.value"></span>
                        <span class="text-[10px] sm:text-xs text-gray-400 uppercase tracking-widest mt-1 sm:mt-2 font-medium" x-text="time.label"></span>
                    </div>
                </template>
            </div>
            
            <!-- Email Form -->
            <form x-data="{ show: false, submitted: false }" x-init="setTimeout(() => show = true, 1100)" 
                  x-show="show" x-transition:enter="transition ease-out duration-700 transform delay-400" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                  class="flex flex-col sm:flex-row gap-3 w-full max-w-lg mx-auto lg:mx-0 input-glow transition-all rounded-xl relative" 
                  @submit.prevent="submitted = true; setTimeout(() => { $el.reset(); submitted = false; }, 4000)">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input type="email" required placeholder="E-posta adresiniz..." class="w-full glass-panel bg-transparent rounded-xl pl-11 pr-4 py-4 sm:py-5 text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#ff6b00] focus:border-[#ff6b00] transition-all font-light text-base">
                </div>
                <button type="submit" class="bg-gradient-to-r from-[#ff6b00] to-[#ff9500] hover:from-[#e56000] hover:to-[#ff7b00] text-white font-bold rounded-xl px-8 py-4 sm:py-5 transition-all duration-300 transform hover:scale-[1.03] hover:shadow-[0_0_20px_rgba(255,107,0,0.5)] active:scale-95 flex items-center justify-center gap-2 whitespace-nowrap">
                    <span x-show="!submitted">Haber Ver</span>
                    <span x-show="submitted" class="flex items-center gap-2" style="display: none;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Kaydedildi
                    </span>
                    <svg x-show="!submitted" class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
        
        <!-- Right Visuals -->
        <div class="flex-1 w-full hidden lg:flex items-center justify-center relative mt-10 lg:mt-0"
             x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)" 
             x-show="show" x-transition:enter="transition ease-out duration-1000 transform delay-300" x-transition:enter-start="opacity-0 scale-90 translate-x-12" x-transition:enter-end="opacity-100 scale-100 translate-x-0">
            
            <!-- Glow behind shoe -->
            <div class="absolute w-[80%] h-[80%] bg-gradient-to-tr from-[#ff6b00]/30 to-[#9d00ff]/30 blur-[80px] rounded-full mix-blend-screen"></div>
            
            <!-- Shoe Image -->
            <!-- fallback image from unsplash as placeholder for premium look -->
            <div class="relative w-full max-w-[550px] aspect-square flex items-center justify-center">
                 <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1200&auto=format&fit=crop" 
                      alt="Premium Patenli Ayakkabı" 
                      class="relative z-10 w-full h-auto object-contain floating-shoe rounded-3xl transform border border-white/10"
                      style="mask-image: linear-gradient(to bottom, black 80%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, black 80%, transparent 100%);">
            </div>
            
            <!-- Floating Feature Badges -->
            <div class="absolute top-[20%] left-[5%] glass-panel px-4 py-2 rounded-full text-xs font-semibold tracking-wide flex items-center gap-2 animate-[float_8s_ease-in-out_infinite_reverse]">
                <div class="w-2 h-2 rounded-full bg-[#00f0ff]"></div>
                Güvenli Kilit Sistemi
            </div>
            
            <div class="absolute bottom-[25%] right-[0%] glass-panel px-4 py-2 rounded-full text-xs font-semibold tracking-wide flex items-center gap-2 animate-[float_7s_ease-in-out_infinite]">
                <div class="w-2 h-2 rounded-full bg-[#9d00ff]"></div>
                Premium Malzeme
            </div>
        </div>
        
    </main>

    <!-- Socials bottom right (Desktop) -->
    <div class="absolute bottom-8 right-8 z-20 hidden md:flex flex-col gap-4">
        <a href="#" class="w-11 h-11 rounded-full glass-panel flex items-center justify-center text-gray-400 hover:text-white hover:border-[#ff6b00]/50 hover:bg-[#ff6b00]/10 hover:-translate-y-1 transition-all duration-300">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
        </a>
        <a href="#" class="w-11 h-11 rounded-full glass-panel flex items-center justify-center text-gray-400 hover:text-white hover:border-[#9d00ff]/50 hover:bg-[#9d00ff]/10 hover:-translate-y-1 transition-all duration-300">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
    </div>

    <!-- Alpine Script for Countdown -->
    <script>
        function countdown() {
            return {
                targetDate: new Date().getTime() + (14 * 24 * 60 * 60 * 1000) + (10 * 60 * 60 * 1000) + (35 * 60 * 1000), // ~14.4 days from now
                timeData: [
                    { label: 'GÜN', value: '14' },
                    { label: 'SAAT', value: '10' },
                    { label: 'DAKİKA', value: '35' },
                    { label: 'SANİYE', value: '00' }
                ],
                init() {
                    setInterval(() => {
                        const now = new Date().getTime();
                        const distance = this.targetDate - now;

                        if (distance < 0) {
                            this.timeData = [
                                { label: 'GÜN', value: '00' },
                                { label: 'SAAT', value: '00' },
                                { label: 'DAKİKA', value: '00' },
                                { label: 'SANİYE', value: '00' }
                            ];
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        this.timeData = [
                            { label: 'GÜN', value: String(days).padStart(2, '0') },
                            { label: 'SAAT', value: String(hours).padStart(2, '0') },
                            { label: 'DAKİKA', value: String(minutes).padStart(2, '0') },
                            { label: 'SANİYE', value: String(seconds).padStart(2, '0') }
                        ];
                    }, 1000);
                }
            }
        }
    </script>
</body>
</html>
