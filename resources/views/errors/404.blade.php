<x-layouts.app>
    <x-slot:title>Sayfa Bulunamadı | Patenli Ayakkabılar</x-slot:title>
    <x-slot:description>Aradığınız sayfa bulunamadı veya kaldırılmış olabilir.</x-slot:description>
    <x-slot:robots>noindex, follow</x-slot:robots>

    <section class="min-h-[70vh] flex items-center justify-center bg-gray-50 px-4 py-16 sm:py-24">
        <div class="max-w-2xl mx-auto text-center">
            {{-- 404 Gradient Başlık --}}
            <h1 class="text-[10rem] sm:text-[12rem] font-black leading-none tracking-tight text-gray-500 select-none" style="color: #6b6b6b;">
                404
            </h1>

            {{-- Mesaj --}}
            <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900">
                Aradığınız sayfa bulunamadı
            </h2>
            <p class="mt-3 text-base sm:text-lg text-gray-500 max-w-md mx-auto">
                Sayfa taşınmış, kaldırılmış veya hiç var olmamış olabilir.
            </p>

            {{-- Öneri Kartları --}}
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Ana Sayfa --}}
                <a href="{{ route('home') }}" wire:navigate
                   class="group flex flex-col items-center gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-gray-500/30">
                    <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-gray-500/10 text-gray-500 transition group-hover:bg-gray-500 group-hover:text-white" style="color: #6b6b6b;">
                        <i class="fa-solid fa-house text-lg"></i>
                    </span>
                    <span class="text-sm font-semibold text-gray-900">Ana Sayfa</span>
                </a>

                {{-- Ürünler --}}
                <a href="{{ route('products.index') }}" wire:navigate
                   class="group flex flex-col items-center gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-gray-500/30">
                    <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-gray-500/10 text-gray-500 transition group-hover:bg-gray-500 group-hover:text-white" style="color: #6b6b6b;">
                        <i class="fa-solid fa-bag-shopping text-lg"></i>
                    </span>
                    <span class="text-sm font-semibold text-gray-900">Ürünler</span>
                </a>

                {{-- İletişim --}}
                <a href="{{ route('contact') }}" wire:navigate
                   class="group flex flex-col items-center gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-gray-500/30">
                    <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-gray-500/10 text-gray-500 transition group-hover:bg-gray-500 group-hover:text-white" style="color: #6b6b6b;">
                        <i class="fa-solid fa-envelope text-lg"></i>
                    </span>
                    <span class="text-sm font-semibold text-gray-900">İletişim</span>
                </a>
            </div>

            {{-- Arama Bağlantısı --}}
            <div class="mt-8">
                <button type="button"
                        x-data
                        @click="$dispatch('open-search')"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors cursor-pointer" style="color: #6b6b6b;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Ürünlerimizde arama yapmak için tıklayın
                </button>
            </div>
        </div>
    </section>
</x-layouts.app>
