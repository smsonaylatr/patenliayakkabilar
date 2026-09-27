<x-layouts.app>
    <x-slot:title>Sayfa Bulunamadı | Patenli Ayakkabılar</x-slot:title>
    <x-slot:description>Aradığınız sayfa bulunamadı veya kaldırılmış olabilir.</x-slot:description>
    <x-slot:robots>noindex, follow</x-slot:robots>

    <section class="min-h-[60vh] sm:min-h-[70vh] flex items-center justify-center bg-gray-50 px-3 sm:px-6 py-8 sm:py-16 md:py-24">
        <div class="max-w-xl mx-auto text-center w-full">
            {{-- 404 Gradient Başlık --}}
            <h1 class="text-[4.5rem] sm:text-[8rem] md:text-[11rem] font-black leading-none tracking-tight text-gray-400 select-none drop-shadow-xs" style="color: #6b6b6b;">
                404
            </h1>

            {{-- Mesaj --}}
            <h2 class="mt-1 sm:mt-3 text-lg sm:text-2xl md:text-3xl font-bold text-gray-900">
                Aradığınız sayfa bulunamadı
            </h2>
            <p class="mt-1.5 sm:mt-3 text-xs sm:text-base text-gray-500 max-w-xs sm:max-w-md mx-auto leading-relaxed">
                Sayfa taşınmış, kaldırılmış veya hiç var olmamış olabilir.
            </p>

            {{-- Öneri Kartları (Mobilde yan yana 3 sütun kompakt) --}}
            <div class="mt-6 sm:mt-10 grid grid-cols-3 gap-2 sm:gap-4">
                {{-- Ana Sayfa --}}
                <a href="{{ route('home') }}" wire:navigate
                   class="group flex flex-col items-center justify-center gap-1.5 sm:gap-3 rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 md:p-6 shadow-xs sm:shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-gray-500/30">
                    <span class="flex items-center justify-center w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-gray-500/10 text-gray-500 transition group-hover:bg-gray-500 group-hover:text-white" style="color: #6b6b6b;">
                        <i class="fa-solid fa-house text-sm sm:text-lg"></i>
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-gray-900 truncate w-full text-center">Ana Sayfa</span>
                </a>

                {{-- Ürünler --}}
                <a href="{{ route('products.index') }}" wire:navigate
                   class="group flex flex-col items-center justify-center gap-1.5 sm:gap-3 rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 md:p-6 shadow-xs sm:shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-gray-500/30">
                    <span class="flex items-center justify-center w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-gray-500/10 text-gray-500 transition group-hover:bg-gray-500 group-hover:text-white" style="color: #6b6b6b;">
                        <i class="fa-solid fa-bag-shopping text-sm sm:text-lg"></i>
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-gray-900 truncate w-full text-center">Ürünler</span>
                </a>

                {{-- İletişim --}}
                <a href="{{ route('contact') }}" wire:navigate
                   class="group flex flex-col items-center justify-center gap-1.5 sm:gap-3 rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 md:p-6 shadow-xs sm:shadow-sm ring-1 ring-gray-100 transition hover:shadow-md hover:ring-gray-500/30">
                    <span class="flex items-center justify-center w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-gray-500/10 text-gray-500 transition group-hover:bg-gray-500 group-hover:text-white" style="color: #6b6b6b;">
                        <i class="fa-solid fa-envelope text-sm sm:text-lg"></i>
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-gray-900 truncate w-full text-center">İletişim</span>
                </a>
            </div>

            {{-- Arama Bağlantısı --}}
            <div class="mt-5 sm:mt-8">
                <button type="button"
                        x-data
                        @click="$dispatch('open-search')"
                        class="inline-flex items-center justify-center gap-1.5 sm:gap-2 text-xs sm:text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors py-1.5 px-3 rounded-lg hover:bg-gray-100/80 cursor-pointer" style="color: #6b6b6b;">
                    <i class="fa-solid fa-magnifying-glass text-xs sm:text-sm"></i>
                    <span>Ürünlerimizde arama yapın</span>
                </button>
            </div>
        </div>
    </section>
</x-layouts.app>
