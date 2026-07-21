<div 
    x-data="{ open: false }" 
    x-on:open-cart.window="open = true"
    x-on:toggle-cart.window="open = !open"
    @keydown.escape.window="open = false"
    x-cloak
    class="relative z-[65]" 
    aria-labelledby="slide-over-title" 
    role="dialog" 
    aria-modal="true"
    x-show="open"
    style="display: none;"
>
    <!-- Backdrop with blur -->
    <div x-show="open" 
         x-transition:enter="ease-in-out duration-500" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" 
         @click="open = false"></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Shorten the drawer on mobile to stop exactly at the navbar (64px) -->
            <div class="pointer-events-none fixed top-0 bottom-[64px] md:bottom-0 md:inset-y-0 right-0 flex max-w-full pl-0 sm:pl-10">
                <div x-show="open" 
                     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="pointer-events-auto w-screen max-w-md">
                    
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl rounded-tl-[2rem] rounded-bl-[2rem] sm:rounded-bl-[2rem]">
                        <!-- Header -->
                        <div class="flex-1 overflow-y-auto px-6 py-8 sm:px-8">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-6">
                                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2" id="slide-over-title">
                                    Alışveriş Sepeti
                                    @if($items->sum('quantity') > 0)
                                        <span class="bg-gray-100 text-gray-600 text-sm font-medium px-2.5 py-0.5 rounded-full">{{ $items->sum('quantity') }}</span>
                                    @endif
                                </h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" @click="open = false" class="relative -m-2 p-2 text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-full transition-all">
                                        <span class="absolute -inset-0.5"></span>
                                        <span class="sr-only">Kapat</span>
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8">
                                <div class="flow-root">
                                    <ul role="list" class="-my-6 divide-y divide-gray-100">
                                        @forelse($items as $item)
                                            <li class="flex py-6 group">
                                                <div class="h-28 w-24 flex-shrink-0 overflow-hidden rounded-2xl bg-gray-50 shadow-sm transition-transform duration-300 group-hover:scale-105">
                                                    <img src="{{ $item->product->images->first() ? $item->product->images->first()->image_url : asset('img/placeholder.svg') }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover object-center">
                                                </div>

                                                <div class="ml-4 flex flex-1 flex-col justify-between">
                                                    <div>
                                                        <div class="flex justify-between text-base font-semibold text-gray-900">
                                                            <h3>
                                                                <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-blue-600 transition-colors" wire:navigate>{{ $item->product->name }}</a>
                                                            </h3>
                                                            <p class="ml-4 font-bold text-red-600 whitespace-nowrap">{{ number_format($item->price * $item->quantity, 2) }} ₺</p>
                                                        </div>
                                                        @if($item->variant)
                                                            <p class="mt-1 text-sm text-gray-500 bg-gray-100 inline-block px-2 py-0.5 rounded-md">Beden: <span class="font-medium">{{ $item->variant->size }}</span></p>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex flex-1 items-end justify-between text-sm mt-4">
                                                        <!-- Pill Quantity Selector -->
                                                        <div class="flex items-center bg-gray-50 rounded-full border border-gray-200 p-0.5">
                                                            <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-white hover:shadow-sm transition-all">-</button>
                                                            <span class="w-8 text-center font-medium text-gray-900">{{ $item->quantity }}</span>
                                                            <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-white hover:shadow-sm transition-all">+</button>
                                                        </div>

                                                        <div class="flex">
                                                            <button wire:click="removeItem({{ $item->id }})" type="button" class="font-medium text-red-500 hover:text-red-600 hover:underline transition-all flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                Sil
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="py-20 flex flex-col items-center justify-center text-center">
                                                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">Sepetiniz Boş</h3>
                                                <p class="text-gray-500 max-w-[200px]">Alışverişe başlamak için ürünlerimize göz atın.</p>
                                                <button @click="open = false" class="mt-6 text-black font-semibold border-b-2 border-black pb-1 hover:text-gray-600 hover:border-gray-600 transition-colors">
                                                    Alışverişe Devam Et
                                                </button>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        @if(count($items) > 0)
                            <div class="border-t border-gray-100 bg-gray-50 px-6 py-6 sm:px-8 rounded-bl-[2rem] pb-10 md:pb-6">
                                <div class="flex justify-between text-lg font-bold text-gray-900 mb-2">
                                    <p>Ara Toplam</p>
                                    <p class="text-red-600 whitespace-nowrap">{{ number_format($total, 2) }} ₺</p>
                                </div>
                                <p class="text-sm text-gray-500 mb-4 flex items-center gap-1">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Kargo ve vergiler ödeme adımında hesaplanır.
                                </p>
                                
                                @if($items->contains(fn($item) => $item->product->has_installments))
                                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <p class="text-xs font-semibold text-emerald-800">Sepetinizde vade farksız 3 taksit imkanı olan ürün(ler) var!</p>
                                </div>
                                @endif

                                <div class="mt-2">
                                    <a href="{{ route('checkout') }}" @click="open = false" class="relative flex items-center justify-center w-full px-6 py-4 overflow-hidden font-bold text-white bg-black rounded-2xl group transition-all hover:shadow-[0_0_20px_rgba(0,0,0,0.3)] hover:-translate-y-1" wire:navigate>
                                        <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                                        <span class="relative flex items-center gap-2">
                                            Siparişi Tamamla
                                            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
