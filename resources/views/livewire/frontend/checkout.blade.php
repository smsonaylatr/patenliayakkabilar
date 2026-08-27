<div class="min-h-screen bg-gray-50 pt-8 pb-24" x-data="{ activeModal: null }" @open-modal.window="activeModal = $event.detail">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Güvenli Ödeme</h1>
            <p class="text-gray-500 mt-1">Siparişinizi tamamlamak için lütfen bilgilerinizi girin.</p>
        </div>

        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="font-medium text-sm">{{ session('error') }}</p>
            </div>
        @endif
        
        <div class="flex flex-col-reverse lg:flex-row gap-10">
            <!-- Sol Taraf: Form -->
            <div class="flex-1">
                @if($paytr_token)
                    <div class="mb-4">
                        <button type="button" wire:click="editInformation" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-black transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            Bilgileri Düzenle
                        </button>
                    </div>
                    <div class="bg-white p-0 sm:p-6 rounded-2xl shadow-sm sm:border sm:border-gray-100">
                        <!-- PayTR Iframe -->
                        @if($payment_method === 'wire_transfer')
                            <div wire:poll.3s="checkOrderStatus">
                                <div wire:ignore>
                                    <iframe x-data x-init="$nextTick(() => { if(typeof iFrameResize !== 'undefined') { iFrameResize({ checkOrigin: false }, $el); } })" src="https://www.paytr.com/odeme/api/{{ $paytr_token }}" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%; min-height: 400px; color-scheme: light;"></iframe>
                                </div>
                            </div>
                        @else
                            <div wire:ignore>
                                <iframe x-data x-init="$nextTick(() => { if(typeof iFrameResize !== 'undefined') { iFrameResize({ checkOrigin: false }, $el); } })" src="https://www.paytr.com/odeme/guvenli/{{ $paytr_token }}" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%; min-height: 400px; color-scheme: light;"></iframe>
                            </div>
                        @endif
                    </div>
                @else
                <form wire:submit.prevent="placeOrder" class="space-y-8">
                    
                    <!-- İletişim Bilgileri -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white text-xs">1</span>
                            İletişim Bilgileri
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                                <input type="text" wire:model.blur="customer_name" autocomplete="name" x-on:input="$el.value = $el.value.split(' ').map(w => w.charAt(0).toLocaleUpperCase('tr-TR') + w.slice(1).toLocaleLowerCase('tr-TR')).join(' ')" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors" placeholder="Adınız ve Soyadınız">
                                @error('customer_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                <input type="email" wire:model.blur="customer_email" autocomplete="email" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors" placeholder="ornek@email.com">
                                @error('customer_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon Numarası</label>
                                <input type="tel" wire:model.blur="customer_phone" autocomplete="tel" x-mask:dynamic="$input.startsWith('+') ? '+99 (999) 999 99 99' : ($input.startsWith('9') ? '99 (999) 999 99 99' : '0 (999) 999 99 99')" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors" placeholder="0 (5XX) XXX XX XX">
                                @error('customer_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Teslimat Adresi -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white text-xs">2</span>
                            Teslimat Adresi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İl</label>
                                <select wire:model.live="shipping_city" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors">
                                    <option value="">İl Seçiniz</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                                @error('shipping_city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İlçe</label>
                                <select wire:model.live="shipping_district" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors" {{ empty($districts) ? 'disabled' : '' }}>
                                    <option value="">İlçe Seçiniz</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                                @error('shipping_district') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-sm font-medium text-gray-700">Mahalle <span class="text-red-500">*</span></label>
                                    <span wire:loading wire:target="shipping_district" class="text-xs text-brand-orange font-medium animate-pulse">
                                        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Mahalleler yükleniyor...
                                    </span>
                                </div>
                                @if(!empty($neighborhoods))
                                    <select wire:model.live="shipping_neighborhood" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors">
                                        <option value="">Mahalle Seçiniz</option>
                                        @foreach($neighborhoods as $neighborhood)
                                            <option value="{{ $neighborhood }}">{{ $neighborhood }} Mah.</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" wire:model.blur="shipping_neighborhood" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors" placeholder="{{ empty($shipping_district) ? 'Önce ilçe seçiniz...' : 'Mahalle adını yazınız veya seçiniz...' }}" {{ empty($shipping_district) ? 'disabled' : '' }}>
                                @endif
                                @error('shipping_neighborhood') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Açık Adres (Cadde, Sokak, Bina No, Daire)</label>
                                <textarea wire:model.blur="shipping_address" autocomplete="street-address" rows="3" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors custom-scrollbar" placeholder="Cadde, sokak, bina ve daire no..."></textarea>
                                @error('shipping_address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sipariş Notu (Opsiyonel)</label>
                                <textarea wire:model.blur="customer_note" rows="2" class="w-full px-4 py-3 text-base rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors custom-scrollbar" placeholder="Kuryeye veya mağazaya iletmek istedikleriniz..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Ödeme Yöntemi -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white text-xs">3</span>
                            Ödeme Yöntemi
                        </h2>
                        
                        <div class="space-y-3">
                            <!-- Kredi Kartı Seçeneği (PayTR) -->
                            <label class="flex items-start p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors {{ $payment_method === 'credit_card' ? 'border-brand-orange bg-orange-50/30' : 'border-gray-200' }}">
                                <div class="flex items-center h-5">
                                    <input wire:model.live="payment_method" type="radio" value="credit_card" class="w-5 h-5 text-brand-orange border-gray-300 focus:ring-brand-orange focus:ring-offset-2">
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between items-center">
                                        <span class="block text-sm font-bold text-gray-900">Kredi / Banka Kartı</span>
                                        <div class="flex gap-1">
                                            <i class="fa-brands fa-cc-visa text-xl text-blue-800"></i>
                                            <i class="fa-brands fa-cc-mastercard text-xl text-red-600"></i>
                                        </div>
                                    </div>
                                    <span class="block text-xs text-gray-500 mt-0.5">PayTR güvencesiyle 256-bit SSL şifreli ödeme. (Taksit İmkanı)</span>
                                </div>
                            </label>

                            <!-- Havale/EFT Seçeneği -->
                            <label class="flex items-start p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors {{ $payment_method === 'wire_transfer' ? 'border-brand-orange bg-orange-50/30' : 'border-gray-200' }}">
                                <div class="flex items-center h-5">
                                    <input wire:model.live="payment_method" type="radio" value="wire_transfer" class="w-5 h-5 text-brand-orange border-gray-300 focus:ring-brand-orange focus:ring-offset-2">
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between items-center">
                                        <span class="block text-sm font-bold text-gray-900">Havale / EFT</span>
                                        <div class="flex gap-1">
                                            <i class="fa-solid fa-building-columns text-xl text-brand-orange"></i>
                                        </div>
                                    </div>
                                    <span class="block text-xs text-gray-500 mt-0.5">PayTR güvencesiyle %100 güvenli Havale/EFT işlemi. Ödemeniz onaylandığında siparişiniz anında işleme alınır.</span>
                                </div>
                            </label>

                            @if($isCodAllowed)
                            <!-- Kapıda Ödeme Seçeneği -->
                            <label class="flex items-start p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors {{ $payment_method === 'cash_on_delivery' ? 'border-black bg-gray-50' : 'border-gray-200' }}">
                                <div class="flex items-center h-5">
                                    <input wire:model.live="payment_method" type="radio" value="cash_on_delivery" class="w-5 h-5 text-black border-gray-300 focus:ring-0 focus:outline-none">
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-gray-900">Kapıda Ödeme</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">Ürünü teslim alırken nakit veya kredi kartı ile ödeyin (200 ₺ Kapıda Ödeme Hizmet Bedeli + Ürün Başı 1 ₺ Kargo).</span>
                                </div>
                            </label>
                            @endif
                        </div>
                    </div>

                    <!-- Mobil İçin Buton (Sadece Mobilde Görünür) -->
                    <div class="mt-6">
                        <!-- SMS Consent (Mobil) -->
                        <div class="mb-3">
                            <label class="flex items-start cursor-pointer group">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input wire:model="sms_consent" type="checkbox" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                                </div>
                                <div class="ml-3 text-xs text-gray-500 leading-relaxed group-hover:text-gray-700 transition-colors">
                                    Kampanya, duyuru ve sepet hatırlatmalarından haberdar olmak için iletişim izni veriyorum.
                                </div>
                            </label>
                        </div>
                        <!-- Terms & Delivery Consent (Mobil) -->
                        <div class="mb-4">
                            <label class="flex items-start cursor-pointer group">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input wire:model="terms_consent" type="checkbox" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                                </div>
                                <div class="ml-3 text-xs text-gray-600 leading-relaxed group-hover:text-gray-900 transition-colors">
                                    <button type="button" @click="$dispatch('open-modal', 'on-bilgilendirme')" class="font-bold text-black underline hover:text-brand-orange transition-colors">Ön Bilgilendirme Formu</button> ve <button type="button" @click="$dispatch('open-modal', 'mesafeli-sozlesme')" class="font-bold text-black underline hover:text-brand-orange transition-colors">Mesafeli Satış Sözleşmesi</button>'ni okudum, kabul ediyorum.
                                </div>
                            </label>
                            @error('terms_consent') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        @if(!$paytr_token)
                            <button type="submit" onclick="document.querySelector('form').dispatchEvent(new Event('submit', {cancelable: true}))" class="w-full bg-black hover:bg-gray-800 text-white font-bold text-lg py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="placeOrder">Siparişi Onayla</span>
                                <span wire:loading wire:target="placeOrder">İşleniyor...</span>
                                <svg wire:loading.remove wire:target="placeOrder" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </button>
                        @endif
                    </div>

                </form>
                @endif
            </div>

            <!-- Sağ Taraf: Sipariş Özeti -->
            <div class="w-full lg:w-[400px]">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 border-b pb-4">Sipariş Özeti</h2>
                    
                    <!-- Ürünler -->
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-3 pt-2 custom-scrollbar">
                        @forelse($cartItems as $item)
                            <div class="flex gap-4">
                                <div class="w-16 h-16 rounded-xl bg-gray-100 flex-shrink-0 border border-gray-200 relative">
                                    <span class="absolute -top-2 -right-2 bg-black text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full z-10 shadow-sm">{{ $item->quantity }}</span>
                                    @if($item->product && $item->product->images->first())
                                        <img src="{{ $item->product->images->first()->image_url }}" class="w-full h-full object-cover rounded-xl">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 rounded-xl">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 flex flex-col justify-center">
                                    <h4 class="text-sm font-bold text-gray-900 line-clamp-1">{{ $item->product ? $item->product->name : 'Bilinmeyen Ürün' }}</h4>
                                    @if($item->variant)
                                        <span class="text-xs text-gray-500 mt-0.5">Beden/Seçenek: {{ $item->variant->size }}</span>
                                    @endif
                                    <div class="text-sm font-bold text-red-600 mt-1">
                                        {{ number_format($item->price, 2) }} ₺
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">Sepetiniz boş.</p>
                        @endforelse
                    </div>

                    <!-- Kupon Kodu -->
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        @if($applied_coupon)
                            <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                    <div>
                                        <span class="text-sm font-bold text-emerald-800">{{ $applied_coupon->code }}</span>
                                        <span class="text-xs text-emerald-600 block">-{{ number_format($coupon_discount, 2) }} ₺ indirim</span>
                                    </div>
                                </div>
                                <button wire:click="removeCoupon" type="button" class="text-emerald-600 hover:text-red-500 transition-colors p-1" title="Kuponu Kaldır">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @else
                            <div x-data="{ open: false }">
                                <button type="button" @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-black transition-colors w-full">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                    <span>İndirim Kodu Ekle</span>
                                    <svg class="w-3.5 h-3.5 ml-auto transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="open" x-collapse x-cloak class="mt-3">
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="coupon_code" wire:keydown.enter="applyCoupon" class="flex-1 px-3 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-0 focus:outline-none focus:border-black transition-colors uppercase placeholder:normal-case" placeholder="Kupon kodunuz">
                                        <button wire:click="applyCoupon" type="button" class="px-4 py-2.5 bg-black hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-colors whitespace-nowrap flex items-center gap-1.5">
                                            <span wire:loading.remove wire:target="applyCoupon">Uygula</span>
                                            <span wire:loading wire:target="applyCoupon"><i class="fa-solid fa-spinner fa-spin"></i></span>
                                        </button>
                                    </div>
                                    @if($coupon_error)
                                        <p class="text-xs text-red-500 mt-2 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ $coupon_error }}
                                        </p>
                                    @endif
                                    @if($coupon_message)
                                        <p class="text-xs text-emerald-600 mt-2 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ $coupon_message }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Fiyat Toplamları -->
                    <div class="mt-5 pt-4 border-t border-gray-100 space-y-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Ara Toplam</span>
                            <span>{{ number_format($subtotal, 2) }} ₺</span>
                        </div>
                        @if($couponDiscount > 0)
                        <div class="flex justify-between text-sm text-emerald-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                İndirim
                            </span>
                            <span class="font-medium">-{{ number_format($couponDiscount, 2) }} ₺</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Kargo <span class="text-[10px] text-gray-400 ml-1">{{ $payment_method === 'cash_on_delivery' ? '(200 ₺ Hizmet Bedeli + Ürün Başı 1 ₺)' : '(Ürün Başı 1 ₺)' }}</span></span>
                            <span class="text-gray-900 font-medium">{{ number_format($shippingPrice, 2) }} ₺</span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-gray-900 pt-3 border-t border-gray-100 mt-3">
                            <span>Toplam</span>
                            <span class="text-red-600">{{ number_format($grandTotal, 2) }} ₺</span>
                        </div>
                    </div>

                    <!-- Desktop İçin Buton -->
                    <div class="mt-8 hidden lg:block">
                        <!-- SMS Consent (Desktop) -->
                        <div class="mb-3">
                            <label class="flex items-start cursor-pointer group">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input wire:model="sms_consent" type="checkbox" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                                </div>
                                <div class="ml-3 text-xs text-gray-500 leading-relaxed group-hover:text-gray-700 transition-colors">
                                    Kampanya, duyuru ve sepet hatırlatmalarından haberdar olmak için iletişim izni veriyorum.
                                </div>
                            </label>
                        </div>
                        <!-- Terms & Delivery Consent (Desktop) -->
                        <div class="mb-4">
                            <label class="flex items-start cursor-pointer group">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input wire:model="terms_consent" type="checkbox" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                                </div>
                                <div class="ml-3 text-xs text-gray-600 leading-relaxed group-hover:text-gray-900 transition-colors">
                                    <button type="button" @click="$dispatch('open-modal', 'on-bilgilendirme')" class="font-bold text-black underline hover:text-brand-orange transition-colors">Ön Bilgilendirme Formu</button> ve <button type="button" @click="$dispatch('open-modal', 'mesafeli-sozlesme')" class="font-bold text-black underline hover:text-brand-orange transition-colors">Mesafeli Satış Sözleşmesi</button>'ni okudum, kabul ediyorum.
                                </div>
                            </label>
                            @error('terms_consent') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        @if(!$paytr_token)
                            <button wire:click="placeOrder" class="w-full bg-black hover:bg-gray-800 text-white font-bold text-lg py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="placeOrder">Siparişi Onayla</span>
                                <span wire:loading wire:target="placeOrder">İşleniyor...</span>
                                <svg wire:loading.remove wire:target="placeOrder" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </button>
                        @endif
                    </div>
                    
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        256-bit SSL ile güvenli ödeme
                    </div>
                </div>
            </div>

    <!-- Sözleşme & Bilgilendirme Modal Penceresi -->
    <div x-show="activeModal !== null" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="activeModal !== null" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="activeModal = null" 
                 class="fixed inset-0 transition-opacity"
                 style="background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Ön Bilgilendirme Formu Modal -->
            <div x-show="activeModal === 'on-bilgilendirme'" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full p-6 sm:p-8">
                <div class="flex justify-between items-center pb-4 border-b border-gray-100 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-file-contract text-brand-orange"></i>
                        Ön Bilgilendirme Formu
                    </h3>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('pages.show', 'on-bilgilendirme-formu') }}" target="_blank" class="text-xs font-bold text-teal-600 hover:underline flex items-center gap-1">
                            Tam Sayfa
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <button type="button" @click="activeModal = null" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="prose prose-sm max-w-none text-gray-700 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar space-y-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">1. Satıcı Bilgileri</h4>
                        <p class="text-xs text-gray-600 mt-1">Unvanı: Patenli Ayakkabılar<br>E-posta: destek@patenliayakkabilar.com<br>Müşteri Hizmetleri: 0850 123 45 67</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">2. Sözleşme Konusu Ürün ve Fiyat</h4>
                        <p class="text-xs text-gray-600 mt-1">Siparişe konu ürünlerin türü, miktarı, birim fiyatı ve KDV dahil toplam tutarı bu sipariş özetinde belirtildiği gibidir.</p>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-emerald-900 text-xs">
                        <h4 class="font-bold text-emerald-950 text-sm mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-truck-fast text-emerald-600"></i>
                            3. Ortalama Teslimat Süresi & Kargo Şartları
                        </h4>
                        <p class="leading-relaxed">Sipariş verilen ürünler, yasal 30 günlük azami süreyi aşmamak kaydıyla <strong>ortalama 1-3 iş günü</strong> (ürün detay sayfasında özel teslimat süresi belirtilen ürünlerde ilgili süre geçerlidir) içerisinde anlaşmalı kargo firmasına teslim edilerek ALICI'nın bildirdiği adrese ulaştırılır.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">4. Cayma Hakkı</h4>
                        <p class="text-xs text-gray-600 mt-1">ALICI, ürünü teslim aldığı tarihten itibaren 14 (on dört) gün içerisinde hiçbir gerekçe göstermeksizin ve cezai şart ödemeksizin cayma hakkını kullanabilir.</p>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="button" @click="activeModal = null" class="px-6 py-2.5 bg-black hover:bg-gray-800 text-white font-bold rounded-xl text-sm transition-colors">Kapat</button>
                </div>
            </div>

            <!-- Mesafeli Satış Sözleşmesi Modal -->
            <div x-show="activeModal === 'mesafeli-sozlesme'" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full p-6 sm:p-8">
                <div class="flex justify-between items-center pb-4 border-b border-gray-100 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-brand-orange"></i>
                        Mesafeli Satış Sözleşmesi
                    </h3>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('pages.show', 'mesafeli-satis-sozlesmesi') }}" target="_blank" class="text-xs font-bold text-teal-600 hover:underline flex items-center gap-1">
                            Tam Sayfa
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <button type="button" @click="activeModal = null" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="prose prose-sm max-w-none text-gray-700 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar space-y-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Madde 1 - Taraflar</h4>
                        <p class="text-xs text-gray-600 mt-1">İşbu sözleşme Patenli Ayakkabılar (SATICI) ile patenliayakkabilar.com üzerinden sipariş veren Müşteri (ALICI) arasında dijital ortamda onaylanarak yürürlüğe girmiştir.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Madde 2 - Konu</h4>
                        <p class="text-xs text-gray-600 mt-1">İşbu sözleşmenin konusu, ALICI'nın SATICI'ya ait web sitesinden siparişini yaptığı ürünün satışı ve teslimi hususlarında 6502 sayılı Kanun uyarınca tarafların hak ve yükümlülüklerinin saptanmasıdır.</p>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-emerald-900 text-xs">
                        <h4 class="font-bold text-emerald-950 text-sm mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-truck-clock text-emerald-600"></i>
                            Madde 3 - Ortalama Teslimat Süresi & İfa Şekli
                        </h4>
                        <p class="leading-relaxed">Sipariş konusu ürün/ürünler, ALICI'nın belirttiği teslimat adresine, yasal 30 günlük süreyi aşmamak kaydıyla <strong>ortalama 1-3 iş günü</strong> içerisinde faturası ile birlikte paketlenmiş olarak kargo firmasına teslim edilir ve ALICI'ya ulaştırılır.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Madde 4 - Cayma Hakkı</h4>
                        <p class="text-xs text-gray-600 mt-1">ALICI, malı teslim aldığı tarihten itibaren 14 gün içerisinde hiçbir gerekçe göstermeksizin sözleşmeden cayma hakkına sahiptir.</p>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="button" @click="activeModal = null" class="px-6 py-2.5 bg-black hover:bg-gray-800 text-white font-bold rounded-xl text-sm transition-colors">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div wire:key="error-scroller-{{ Str::random() }}" x-data x-init="
            $nextTick(() => {
                setTimeout(() => {
                    const firstError = document.querySelector('.text-red-500');
                    if (firstError) {
                        const inputEl = firstError.previousElementSibling;
                        if (inputEl && ['INPUT', 'SELECT', 'TEXTAREA'].includes(inputEl.tagName)) {
                            inputEl.focus();
                            setTimeout(() => {
                                const y = inputEl.getBoundingClientRect().top + window.scrollY - 100;
                                window.scrollTo({ top: y, behavior: 'smooth' });
                            }, 50);
                        } else {
                            const y = firstError.getBoundingClientRect().top + window.scrollY - 100;
                            window.scrollTo({ top: y, behavior: 'smooth' });
                        }
                    }
                }, 100);
            });
        "></div>
    @endif
</div>
