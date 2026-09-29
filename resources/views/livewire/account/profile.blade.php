<x-account-layout>
    <div class="max-w-2xl">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 mb-4 sm:mb-6 tracking-tight">Profil Ayarları</h1>
        
        <form wire:submit="updateProfile" class="mb-6 sm:mb-10">
            <h2 class="text-sm sm:text-base md:text-lg font-bold text-gray-900 mb-2.5 sm:mb-4">Kişisel Bilgiler</h2>
            <div class="space-y-3.5 sm:space-y-5 bg-gray-50/60 p-3.5 sm:p-6 md:p-8 rounded-2xl sm:rounded-3xl border border-gray-100">
                <div>
                    <label for="name" class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Ad Soyad</label>
                    <input wire:model="name" type="text" id="name" x-on:input="$el.value = $el.value.split(' ').map(w => w.charAt(0).toLocaleUpperCase('tr-TR') + w.slice(1).toLocaleLowerCase('tr-TR')).join(' ')" class="w-full bg-white border @error('name') border-red-300 ring-1 ring-red-300 @else border-gray-200 @enderror rounded-xl px-3.5 py-2.5 sm:px-4 sm:py-3 text-base sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-xs">
                    @error('name') <span class="text-red-500 text-[11px] sm:text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">E-posta Adresi</label>
                    <input wire:model="email" type="email" id="email" class="w-full bg-white border @error('email') border-red-300 ring-1 ring-red-300 @else border-gray-200 @enderror rounded-xl px-3.5 py-2.5 sm:px-4 sm:py-3 text-base sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-xs">
                    @error('email') <span class="text-red-500 text-[11px] sm:text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="pt-1 sm:pt-2">
                    <button type="submit" class="w-full sm:w-auto bg-black text-white font-bold text-xs sm:text-sm px-6 py-3 sm:px-8 sm:py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-md shadow-black/10 flex items-center justify-center min-w-[160px]">
                        <span wire:loading.remove wire:target="updateProfile">Değişiklikleri Kaydet</span>
                        <span wire:loading wire:target="updateProfile">Kaydediliyor...</span>
                    </button>
                </div>
            </div>
        </form>

        <form wire:submit="updatePassword">
            <h2 class="text-sm sm:text-base md:text-lg font-bold text-gray-900 mb-2.5 sm:mb-4">Şifre Değiştir</h2>
            <div class="space-y-3.5 sm:space-y-5 bg-gray-50/60 p-3.5 sm:p-6 md:p-8 rounded-2xl sm:rounded-3xl border border-gray-100">
                <div>
                    <label for="current_password" class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Mevcut Şifre</label>
                    <input wire:model="current_password" type="password" id="current_password" class="w-full bg-white border @error('current_password') border-red-300 ring-1 ring-red-300 @else border-gray-200 @enderror rounded-xl px-3.5 py-2.5 sm:px-4 sm:py-3 text-base sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-xs" placeholder="••••••••">
                    @error('current_password') <span class="text-red-500 text-[11px] sm:text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_password" class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Yeni Şifre</label>
                    <input wire:model="new_password" type="password" id="new_password" class="w-full bg-white border @error('new_password') border-red-300 ring-1 ring-red-300 @else border-gray-200 @enderror rounded-xl px-3.5 py-2.5 sm:px-4 sm:py-3 text-base sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-xs" placeholder="••••••••">
                    @error('new_password') <span class="text-red-500 text-[11px] sm:text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Yeni Şifre (Tekrar)</label>
                    <input wire:model="new_password_confirmation" type="password" id="new_password_confirmation" class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 sm:px-4 sm:py-3 text-base sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-xs" placeholder="••••••••">
                </div>
                <div class="pt-1 sm:pt-2">
                    <button type="submit" class="w-full sm:w-auto bg-black text-white font-bold text-xs sm:text-sm px-6 py-3 sm:px-8 sm:py-3.5 rounded-xl hover:bg-gray-800 transition-colors shadow-md shadow-black/10 flex items-center justify-center min-w-[160px]">
                        <span wire:loading.remove wire:target="updatePassword">Şifreyi Güncelle</span>
                        <span wire:loading wire:target="updatePassword">Güncelleniyor...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-account-layout>
