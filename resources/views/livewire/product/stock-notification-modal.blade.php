<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-neutral-950 text-left shadow-[0_25px_60px_-15px_rgba(0,0,0,0.95)] transition-all sm:my-8 sm:w-full sm:max-w-md border border-neutral-800">
                <!-- Close Button -->
                <button type="button" wire:click="closeModal" aria-label="Kapat" class="absolute top-4 right-4 text-neutral-400 hover:text-white w-9 h-9 rounded-full bg-neutral-900 border border-neutral-800 hover:bg-neutral-800 flex items-center justify-center transition-colors cursor-pointer z-10">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>

                <div class="p-6 sm:p-8">
                    @if($isSuccess)
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-emerald-950/80 text-emerald-400 border border-emerald-800/80 rounded-full flex items-center justify-center mx-auto text-2xl mb-4 shadow-lg animate-bounce">
                                <i class="fa-solid fa-bell-circle-check"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Talebiniz Alındı!</h3>
                            <p class="text-sm text-neutral-300 leading-relaxed mb-6">{{ $message }}</p>
                            <button type="button" wire:click="closeModal" class="w-full h-12 bg-neutral-900 hover:bg-black text-white font-bold rounded-full border border-neutral-700 hover:border-neutral-500 transition-colors shadow-lg cursor-pointer">
                                Tamam
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-3.5 mb-5">
                            <div class="w-11 h-11 rounded-2xl bg-neutral-900 border border-neutral-800 text-white flex items-center justify-center text-lg shrink-0 shadow-inner">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white tracking-tight">Gelince Haber Ver</h3>
                                <p class="text-xs text-neutral-400 mt-0.5">Stoklar yenilendiğinde anında e-posta veya SMS ile bilgilendirileceksiniz.</p>
                            </div>
                        </div>

                        @if($product)
                        <div class="mb-5 p-3.5 bg-neutral-900/90 rounded-2xl border border-neutral-800 flex items-center gap-3.5 shadow-inner">
                            @if($product->images->first())
                                <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="w-13 h-13 rounded-xl object-cover border border-neutral-700/80 shrink-0 bg-neutral-950">
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm font-bold text-white truncate">{{ $product->name }}</p>
                                @if($selectedVariant)
                                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 text-[11px] font-semibold bg-neutral-800 text-neutral-200 border border-neutral-700 rounded-md">
                                        {{ $selectedVariant->size }} Beden
                                    </span>
                                @else
                                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 text-[11px] font-semibold bg-neutral-800 text-neutral-300 border border-neutral-700 rounded-md">
                                        Tüm Bedenler
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        <form wire:submit.prevent="submit" class="space-y-4">
                            @if($product && $product->variants->count() > 0)
                            <div>
                                <label class="block text-xs font-semibold text-neutral-300 mb-1.5">Beden Tercihi</label>
                                <div class="relative">
                                    <select wire:model.live="variantId" class="w-full h-12 rounded-xl border border-neutral-700 px-4 text-sm focus:border-white focus:outline-none focus:ring-1 focus:ring-white bg-neutral-900 text-white appearance-none cursor-pointer pr-10">
                                        <option value="" class="bg-neutral-900 text-white">Tüm Bedenler (Fark Etmez)</option>
                                        @foreach($product->variants as $v)
                                            <option value="{{ $v->id }}" class="bg-neutral-900 text-white">
                                                {{ $v->size }} Beden {{ $v->stock <= 0 ? '(Tükendi)' : '(Stokta: '.$v->stock.')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-neutral-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-xs font-semibold text-neutral-300 mb-1.5">E-Posta Adresi <span class="text-red-400">*</span></label>
                                <input type="email" wire:model="email" placeholder="ornek@email.com" class="w-full h-12 rounded-xl border border-neutral-700 px-4 text-sm focus:border-white focus:outline-none focus:ring-1 focus:ring-white bg-neutral-900 text-white placeholder-neutral-500 transition-colors">
                                @error('email') <span class="text-xs text-red-400 mt-1.5 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-neutral-300 mb-1.5">Telefon Numarası <span class="text-neutral-500 font-normal">(SMS için isteğe bağlı)</span></label>
                                <input type="tel" wire:model="phone" placeholder="05XX XXX XX XX" class="w-full h-12 rounded-xl border border-neutral-700 px-4 text-sm focus:border-white focus:outline-none focus:ring-1 focus:ring-white bg-neutral-900 text-white placeholder-neutral-500 transition-colors">
                                @error('phone') <span class="text-xs text-red-400 mt-1.5 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-start gap-2.5 pt-1">
                                <input type="checkbox" id="kvkkConsent" wire:model="kvkkConsent" class="mt-1 h-4 w-4 rounded bg-neutral-900 border-neutral-700 text-black focus:ring-white cursor-pointer accent-white">
                                <label for="kvkkConsent" class="text-xs text-neutral-400 leading-tight cursor-pointer select-none">
                                    Stok durumu hakkında e-posta ve SMS bilgilendirmesi almayı kabul ediyorum.
                                </label>
                            </div>
                            @error('kvkkConsent') <span class="text-xs text-red-400 block font-medium">{{ $message }}</span> @enderror

                            <button type="submit" wire:loading.attr="disabled" class="w-full h-13 mt-3 bg-black hover:bg-neutral-900 text-white font-bold rounded-full border border-neutral-700 hover:border-neutral-500 transition-all duration-200 shadow-xl flex items-center justify-center gap-2 cursor-pointer hover:scale-[1.01]">
                                <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                                    <i class="fa-solid fa-paper-plane text-xs"></i> Haber Ver
                                </span>
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
