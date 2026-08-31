<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                <!-- Close Button -->
                <button type="button" wire:click="closeModal" aria-label="Kapat" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>

                <div class="p-6 sm:p-8">
                    @if($isSuccess)
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl mb-4 shadow-xs animate-bounce">
                                <i class="fa-solid fa-bell-circle-check"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Talebiniz Alındı!</h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-6">{{ $message }}</p>
                            <button type="button" wire:click="closeModal" class="w-full h-12 bg-gray-900 text-white font-bold rounded-full hover:bg-black transition-colors shadow-md">
                                Tamam
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Gelince Haber Ver</h3>
                                <p class="text-xs text-gray-500">Stoklar yenilendiğinde anında bilgilendirileceksiniz.</p>
                            </div>
                        </div>

                        @if($product)
                        <div class="mb-6 p-3.5 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-3">
                            @if($product->images->first())
                                <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0">
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $product->name }}</p>
                                @if($selectedVariant)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 text-[11px] font-semibold bg-amber-100 text-amber-800 rounded">
                                        {{ $selectedVariant->size }} Beden
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <form wire:submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">E-Posta Adresi <span class="text-red-500">*</span></label>
                                <input type="email" wire:model="email" placeholder="ornek@email.com" class="w-full h-12 rounded-xl border border-gray-200 px-4 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                                @error('email') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Telefon Numarası (SMS için isteğe bağlı)</label>
                                <input type="tel" wire:model="phone" placeholder="05XX XXX XX XX" class="w-full h-12 rounded-xl border border-gray-200 px-4 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                                @error('phone') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-start gap-2 pt-1">
                                <input type="checkbox" id="kvkkConsent" wire:model="kvkkConsent" class="mt-1 rounded text-gray-900 focus:ring-gray-900">
                                <label for="kvkkConsent" class="text-xs text-gray-500 leading-tight">
                                    Stok durumu hakkında e-posta/SMS bilgilendirmesi almayı kabul ediyorum.
                                </label>
                            </div>
                            @error('kvkkConsent') <span class="text-xs text-red-500 block font-medium">{{ $message }}</span> @enderror

                            <button type="submit" wire:loading.attr="disabled" class="w-full h-13 mt-2 bg-gray-900 text-white font-bold rounded-full hover:bg-black transition-colors shadow-lg flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="submit"><i class="fa-solid fa-paper-plane text-xs"></i> Haber Ver</span>
                                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Kaydediliyor...
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
