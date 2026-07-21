<style>
    @media (max-width: 1023px) {
        .mobile-auth-spacing {
            padding-top: 5px !important;
            align-items: flex-start !important;
        }
    }
</style>
<div class="min-h-screen bg-brand-light pb-24 lg:py-24 flex justify-center lg:items-center mobile-auth-spacing">
    <div class="container mx-auto px-4 max-w-md">
        
        <div class="bg-white/80 backdrop-blur-2xl border border-white/60 rounded-3xl p-8 shadow-[0_30px_60px_rgba(0,0,0,0.08)] relative overflow-hidden">
            <!-- Decorative element -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full blur-[60px] opacity-20 pointer-events-none"></div>

            <div class="text-center mb-8 relative z-10">
                <h1 class="text-3xl font-black text-brand-dark mb-2 tracking-tight">Giriş Yap</h1>
                <p class="text-gray-500 text-sm">Hesabınıza giriş yaparak alışverişe devam edin.</p>
            </div>

            <form wire:submit="login" class="space-y-5 relative z-10">
                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-gray-700 ml-1">E-posta Adresiniz</label>
                    <input wire:model="email" type="email" id="email" class="w-full text-sm bg-gray-50 border @error('email') border-red-300 ring-1 ring-red-300 @else border-gray-200 @enderror rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:bg-white transition-all text-gray-700" placeholder="ornek@email.com">
                    @error('email') <span class="text-red-500 text-xs font-bold ml-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between ml-1">
                        <label for="password" class="text-xs font-bold text-gray-700">Şifre</label>
                        <a wire:navigate href="{{ route('password.request') }}" class="text-xs text-brand-blue hover:underline">Şifremi Unuttum</a>
                    </div>
                    <input wire:model="password" type="password" id="password" class="w-full text-sm bg-gray-50 border @error('password') border-red-300 ring-1 ring-red-300 @else border-gray-200 @enderror rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:bg-white transition-all text-gray-700" placeholder="••••••••">
                    @error('password') <span class="text-red-500 text-xs font-bold ml-1">{{ $message }}</span> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center ml-1">
                    <input wire:model="remember" id="remember" type="checkbox" class="w-4 h-4 text-brand-blue bg-gray-100 border-gray-300 rounded focus:ring-brand-blue focus:ring-2">
                    <label for="remember" class="ml-2 text-sm font-medium text-gray-700">Beni Hatırla</label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-brand-dark text-white font-bold text-base rounded-xl py-3 shadow-[0_8px_25px_rgba(31,41,55,0.3)] hover:shadow-[0_12px_35px_rgba(31,41,55,0.4)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-center group relative overflow-hidden">
                        <span wire:loading.remove wire:target="login" class="relative z-10 flex items-center justify-center">
                            Giriş Yap
                        </span>
                        <span wire:loading wire:target="login" class="relative z-10">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <!-- Hover shine -->
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:animate-[shine_1.5s_ease-in-out_infinite] skew-x-12 z-0"></div>
                    </button>
                </div>
            </form>

            <div class="mt-8 relative z-10">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white/80 backdrop-blur-2xl text-gray-500">Veya şununla devam edin</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google ile Giriş Yap
                    </a>
                </div>
            </div>

            <div class="mt-8 text-center relative z-10">
                <p class="text-sm text-gray-500">
                    Hesabınız yok mu? 
                    <a href="{{ route('register') }}" class="font-bold text-brand-dark hover:text-brand-blue transition-colors">Hemen Kayıt Olun</a>
                </p>
            </div>

        </div>
    </div>
</div>
