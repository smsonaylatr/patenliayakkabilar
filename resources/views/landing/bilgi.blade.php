<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patenli Ayakkabılar - Ürün İnceleme</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { background-color: #f8fafc; }
        .product-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    </style>
</head>
<body class="antialiased text-gray-800" x-data="{ showModal: false, selectedProduct: null, phone: '', email: '', isSubmitting: false, success: false, error: '' }">

    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 mb-4">Koleksiyonumuzu Keşfedin</h1>
            <p class="text-lg text-gray-500">Işıklı ve tekerlekli paten modellerimizle tanışın.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-8">
            @foreach($products as $product)
            <div @click="selectedProduct = {{ $product->id }}; showModal = true; success = false; error = '';" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 product-card cursor-pointer relative group">
                <div class="w-full overflow-hidden bg-gray-200 relative aspect-square">
                    @if($product->images->count() > 0)
                        <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 text-xs md:text-base transition-transform duration-500 group-hover:scale-105">Görsel Yok</div>
                    @endif
                    
                    <div class="hidden md:flex absolute inset-0 transition-all duration-300 items-center justify-center opacity-0 group-hover:opacity-100" style="background-color: rgba(0,0,0,0.15);">
                        <div class="transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 bg-white/90 backdrop-blur-sm text-black font-semibold py-2 px-5 rounded-full shadow-lg flex items-center gap-2 border border-white/50">
                            <span class="text-sm md:text-base">İncele</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak style="display: none;">
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 transition-opacity backdrop-blur-md"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showModal" @click.away="showModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-2xl font-bold leading-6 text-gray-900 mb-2" id="modal-title">Ürün Hakkında Bilgi Alın</h3>
                                <p class="text-sm text-gray-500 mb-6">Müşteri temsilcilerimiz ürünle ilgili detaylı bilgi ve görselleri iletmek üzere size ulaşacaktır.</p>
                                
                                <div x-show="success" class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl border border-green-200 flex items-center gap-3">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="font-medium">Talebiniz başarıyla alındı. En kısa sürede iletişime geçeceğiz!</span>
                                </div>

                                <div x-show="error" x-text="error" class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200 text-sm"></div>

                                <form x-show="!success" @submit.prevent="submitForm">
                                    <div class="mb-4">
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numaranız *</label>
                                        <input type="tel" x-model="phone" id="phone" required placeholder="05XX XXX XX XX" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition-all">
                                    </div>
                                    <div class="mb-6">
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta Adresiniz (Opsiyonel)</label>
                                        <input type="email" x-model="email" id="email" placeholder="ornek@mail.com" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black focus:border-black outline-none transition-all">
                                    </div>
                                    <button type="submit" :disabled="isSubmitting" class="w-full bg-black text-white font-bold py-3 px-4 rounded-xl hover:bg-gray-800 transition-colors disabled:opacity-70 flex justify-center items-center gap-2">
                                        <span x-show="!isSubmitting">Gönder ve Bilgi Al</span>
                                        <span x-show="isSubmitting">Gönderiliyor...</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitForm() {
            this.isSubmitting = true;
            this.error = '';
            
            fetch('/bilgi/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: this.selectedProduct,
                    phone: this.phone,
                    email: this.email
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isSubmitting = false;
                if(data.success) {
                    this.success = true;
                    this.phone = '';
                    this.email = '';
                    setTimeout(() => {
                        this.showModal = false;
                    }, 4000);
                } else {
                    this.error = data.message || 'Bir hata oluştu.';
                }
            })
            .catch(error => {
                this.isSubmitting = false;
                this.error = 'Sistemsel bir hata oluştu, lütfen tekrar deneyin.';
            });
        }
    </script>
</body>
</html>
