<div class="min-h-screen bg-white py-16 lg:py-24">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">İletişime Geçin</h1>
            <p class="text-slate-500 text-lg md:text-xl font-medium max-w-2xl mx-auto">
                Size nasıl yardımcı olabiliriz? İhtiyaçlarınızı veya sorularınızı bizimle paylaşın.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-12 lg:gap-16">
            
            <!-- Contact Info -->
            <div class="md:col-span-2 space-y-8">
                <div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">Müşteri Hizmetleri</h3>
                    <p class="text-slate-500 mb-2">Hafta içi 09:00 - 18:00 saatleri arasında bize ulaşabilirsiniz.</p>
                    <a href="tel:+908503073164" class="text-2xl font-bold text-brand-orange hover:text-orange-600 transition-colors">
                        0850 307 31 64
                    </a>
                </div>

                <div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">Merkez Ofis</h3>
                    <p class="text-slate-600 leading-relaxed">
                        YEŞİLPINAR MAH. ÇİÇEKSUYU CAD. NO: 130 İÇ KAPI NO: 3<br>
                        EYÜPSULTAN / İSTANBUL
                    </p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="md:col-span-3">
                <div class="bg-gray-50 p-8 sm:p-10 rounded-3xl border border-gray-100 shadow-sm">
                    @if($isSuccess)
                        <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-center font-medium">
                            Mesajınız başarıyla gönderildi. Size en kısa sürede dönüş yapacağız.
                        </div>
                    @endif

                    <form wire:submit="submit" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <input wire:model="name" type="text" class="w-full border-gray-300 rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white transition-all shadow-sm" placeholder="Ad Soyad">
                                @error('name') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input wire:model="email" type="email" class="w-full border-gray-300 rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white transition-all shadow-sm" placeholder="E-posta Adresi">
                                @error('email') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <input wire:model="subject" type="text" class="w-full border-gray-300 rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white transition-all shadow-sm" placeholder="Telefon Numarası">
                            @error('subject') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <textarea wire:model="message" rows="5" class="w-full border-gray-300 rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white transition-all shadow-sm resize-none" placeholder="Mesajınız..."></textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold text-lg py-4 rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center">
                            <span wire:loading.remove wire:target="submit">Mesajı Gönder</span>
                            <span wire:loading wire:target="submit" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Gönderiliyor...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
