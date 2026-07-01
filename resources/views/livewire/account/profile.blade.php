<x-account-layout>
    <div class="max-w-2xl">
        <h1 class="text-3xl font-black text-gray-900 mb-8">Profil Ayarları</h1>
        
        <form wire:submit="updateProfile" class="mb-16">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Kişisel Bilgiler</h2>
            <div class="space-y-6 bg-gray-50/50 p-8 rounded-3xl border border-gray-100">
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Ad Soyad</label>
                    <input wire:model="name" type="text" id="name" class="w-full bg-white border @error('name') border-red-300 @else border-gray-200 @enderror rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    @error('name') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">E-posta Adresi</label>
                    <input wire:model="email" type="email" id="email" class="w-full bg-white border @error('email') border-red-300 @else border-gray-200 @enderror rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    @error('email') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>
                <div class="pt-2">
                    <button type="submit" class="bg-black text-white font-bold text-sm px-8 py-4 rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center justify-center min-w-[160px]">
                        <span wire:loading.remove wire:target="updateProfile">Değişiklikleri Kaydet</span>
                        <span wire:loading wire:target="updateProfile">Kaydediliyor...</span>
                    </button>
                </div>
            </div>
        </form>

        <form wire:submit="updatePassword">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Şifre Değiştir</h2>
            <div class="space-y-6 bg-gray-50/50 p-8 rounded-3xl border border-gray-100">
                <div>
                    <label for="current_password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Mevcut Şifre</label>
                    <input wire:model="current_password" type="password" id="current_password" class="w-full bg-white border @error('current_password') border-red-300 @else border-gray-200 @enderror rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    @error('current_password') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Yeni Şifre</label>
                    <input wire:model="new_password" type="password" id="new_password" class="w-full bg-white border @error('new_password') border-red-300 @else border-gray-200 @enderror rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    @error('new_password') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Yeni Şifre (Tekrar)</label>
                    <input wire:model="new_password_confirmation" type="password" id="new_password_confirmation" class="w-full bg-white border border-gray-200 rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                </div>
                <div class="pt-2">
                    <button type="submit" class="bg-black text-white font-bold text-sm px-8 py-4 rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center justify-center min-w-[160px]">
                        <span wire:loading.remove wire:target="updatePassword">Şifreyi Güncelle</span>
                        <span wire:loading wire:target="updatePassword">Güncelleniyor...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-account-layout>
