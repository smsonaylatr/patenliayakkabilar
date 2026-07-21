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
                <h1 class="text-3xl font-black text-brand-dark mb-2 tracking-tight">Yeni Şifre Belirle</h1>
                <p class="text-gray-500 text-sm">Hesabınız için yeni ve güvenli bir şifre belirleyin.</p>
            </div>

            <form wire:submit="resetPassword" class="space-y-5 relative z-10">
                <!-- Email (readonly or editable) -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-gray-700 ml-1">E-posta Adresiniz</label>
                    <input wire:model="email" type="email" id="email" class="w-full text-sm bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed" readonly>
                    @error('email') <span class="text-red-500 text-xs font-bold ml-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-gray-700 ml-1">Yeni Şifre</label>
                    <input wire:model="password" type="password" id="password" class="w-full text-sm bg-gray-50 border @error('password') border-red-300 ring-1 ring-red-300 @else border-gray-200 @enderror rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:bg-white transition-all text-gray-700" placeholder="••••••••">
                    @error('password') <span class="text-red-500 text-xs font-bold ml-1">{{ $message }}</span> @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-gray-700 ml-1">Yeni Şifre (Tekrar)</label>
                    <input wire:model="password_confirmation" type="password" id="password_confirmation" class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:bg-white transition-all text-gray-700" placeholder="••••••••">
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-brand-dark text-white font-bold text-base rounded-xl py-3 shadow-[0_8px_25px_rgba(31,41,55,0.3)] hover:shadow-[0_12px_35px_rgba(31,41,55,0.4)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-center group relative overflow-hidden">
                        <span wire:loading.remove wire:target="resetPassword" class="relative z-10 flex items-center justify-center">
                            Şifremi Sıfırla
                        </span>
                        <span wire:loading wire:target="resetPassword" class="relative z-10">
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
        </div>
    </div>
</div>
