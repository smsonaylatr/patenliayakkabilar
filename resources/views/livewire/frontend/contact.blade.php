<div class="min-h-screen bg-white py-16 lg:py-24">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">İletişime Geçin</h1>
            <p class="text-slate-500 text-lg md:text-xl font-medium max-w-2xl mx-auto">
                Size nasıl yardımcı olabiliriz? İhtiyaçlarınızı veya sorularınızı bizimle paylaşın.
            </p>
        </div>

        <div class="w-full">
            @if($isSuccess)
                <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-center">
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
</div>
