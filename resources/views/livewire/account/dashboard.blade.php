<x-account-layout>
    <div class="max-w-3xl">
        <h1 class="text-3xl font-black text-gray-900 mb-2">Hoş Geldiniz, {{ auth()->user()->name }}</h1>
        <p class="text-gray-500 text-base mb-12 leading-relaxed">Hesap özetinize buradan ulaşabilirsiniz. Siparişlerinizi takip edebilir, hesap bilgilerinizi güncelleyebilirsiniz.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-12">
            <!-- Orders Card -->
            <a href="{{ route('account.orders') }}" class="group flex flex-col p-8 bg-white border border-gray-100 rounded-3xl hover:border-black hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-black group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6 text-gray-900 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Siparişlerim</h3>
                <p class="text-sm text-gray-500 flex-1">Son siparişlerinizi, teslimat durumunu ve kargo takiplerini görüntüleyin.</p>
                <div class="mt-6 flex items-center text-sm font-bold text-gray-900">
                    İncele
                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </a>

            <!-- Settings Card -->
            <a href="{{ route('account.profile') }}" class="group flex flex-col p-8 bg-white border border-gray-100 rounded-3xl hover:border-black hover:shadow-xl transition-all duration-300">
                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-black group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6 text-gray-900 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hesap Ayarları</h3>
                <p class="text-sm text-gray-500 flex-1">Şifre, e-posta adresi ve diğer kişisel bilgilerinizi güvenle yönetin.</p>
                <div class="mt-6 flex items-center text-sm font-bold text-gray-900">
                    Düzenle
                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </a>
        </div>
    </div>
</x-account-layout>
