<div class="min-h-screen bg-white py-16 lg:py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">İletişime Geçin</h1>
            <p class="text-slate-500 text-lg md:text-xl font-medium max-w-2xl mx-auto">
                Size nasıl yardımcı olabiliriz? İhtiyaçlarınızı veya sorularınızı bizimle paylaşın.
            </p>
        </div>


        <div class="w-full">
            @if($isSuccess)
                <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-center font-medium">
                    Mesajınız başarıyla gönderildi. Size en kısa sürede dönüş yapacağız.
                </div>
            @endif

            <form wire:submit="submit" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input wire:model="name" type="text" class="w-full border border-gray-400 rounded-md px-4 py-3 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-gray-600 focus:ring-0" placeholder="Ad">
                        @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input wire:model="email" type="email" class="w-full border border-gray-400 rounded-md px-4 py-3 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-gray-600 focus:ring-0" placeholder="E-posta">
                        @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <input wire:model="subject" type="text" class="w-full border border-gray-400 rounded-md px-4 py-3 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-gray-600 focus:ring-0" placeholder="Telefon numarası">
                    @error('subject') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <textarea wire:model="message" rows="5" class="w-full border border-gray-400 rounded-md px-4 py-3 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-gray-600 focus:ring-0 resize-none" placeholder="Yorum"></textarea>
                    @error('message') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-black hover:bg-gray-900 text-white font-bold text-lg py-4 rounded-md transition-colors flex items-center justify-center">
                    <span wire:loading.remove wire:target="submit">Gönder</span>
                    <span wire:loading wire:target="submit">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>
        
    <!-- Contact Info & Map Below Form -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center text-left">
                <!-- Info -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Müşteri Hizmetleri & WhatsApp</h3>
                        <div class="flex flex-wrap items-center gap-3">
                            <p class="text-slate-600 font-medium text-lg w-full sm:w-auto">0850 307 31 64</p>
                            <div class="flex items-center gap-2">
                                <a href="tel:08503073164" class="inline-flex items-center justify-center bg-black hover:bg-gray-800 text-white text-xs font-bold py-1.5 px-4 rounded-full shadow-sm transition-colors gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    Hemen Ara
                                </a>
                                <a href="https://wa.me/908503073164" target="_blank" class="inline-flex items-center justify-center bg-[#25D366] hover:bg-[#1da851] text-white text-xs font-bold py-1.5 px-4 rounded-full shadow-sm transition-colors gap-1.5">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    Mesaj At
                                </a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Merkez Ofis</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">
                            YEŞİLPINAR MAH. ÇİÇEKSUYU CAD. NO: 130/3<br>
                            EYÜPSULTAN / İSTANBUL
                        </p>
                    </div>
                </div>
                
                <!-- Map -->
                <div class="w-full sm:w-64 sm:h-64 md:w-72 md:h-72 ml-auto rounded-xl border border-gray-200 overflow-hidden shadow-sm aspect-square">
                    <iframe src="https://maps.google.com/maps?q=Yeşilpınar%20Mah.%20Çiçeksuyu%20Cad.%20No:130%20Eyüpsultan%2Fİstanbul&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
