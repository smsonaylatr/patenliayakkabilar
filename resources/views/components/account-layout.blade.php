<div class="min-h-[70vh] sm:min-h-[80vh] bg-gray-50/50 py-4 sm:py-8 md:py-12">
    <div class="max-w-7xl mx-auto px-2.5 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-xs sm:shadow-sm border border-gray-100 overflow-hidden min-h-0 md:min-h-[600px] flex flex-col md:flex-row">
            
            <!-- Elegant Sidebar -->
            <div class="w-full md:w-72 bg-gray-50/50 border-b md:border-b-0 md:border-r border-gray-100 p-3.5 sm:p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <div class="mb-3 sm:mb-6 md:mb-12 flex items-center justify-between md:block">
                        <div>
                            <h2 class="text-[10px] sm:text-xs font-bold tracking-widest text-gray-400 uppercase mb-0.5 sm:mb-1">Hesabım</h2>
                            <p class="text-base sm:text-xl font-black text-gray-900 truncate max-w-[200px] sm:max-w-none">{{ auth()->user()->name }}</p>
                        </div>
                        <button wire:click="logout" class="md:hidden flex items-center px-2.5 py-1.5 text-xs font-semibold text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Çıkış
                        </button>
                    </div>
                    
                    <nav class="grid grid-cols-3 gap-1.5 md:flex md:flex-col md:space-y-2">
                        <a href="{{ route('account.dashboard') }}" class="group flex flex-col md:flex-row items-center justify-center md:justify-start px-2 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm rounded-xl transition-all {{ request()->routeIs('account.dashboard') ? 'bg-black text-white shadow-sm md:shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 bg-white md:bg-transparent border border-gray-100 md:border-transparent' }}">
                            <svg class="w-4 h-4 md:w-5 md:h-5 md:mr-3 mb-1 md:mb-0 {{ request()->routeIs('account.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-900' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="font-medium text-center md:text-left truncate w-full">Genel Bakış</span>
                        </a>
                        
                        <a href="{{ route('account.orders') }}" class="group flex flex-col md:flex-row items-center justify-center md:justify-start px-2 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm rounded-xl transition-all {{ request()->routeIs('account.orders') ? 'bg-black text-white shadow-sm md:shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 bg-white md:bg-transparent border border-gray-100 md:border-transparent' }}">
                            <svg class="w-4 h-4 md:w-5 md:h-5 md:mr-3 mb-1 md:mb-0 {{ request()->routeIs('account.orders') ? 'text-white' : 'text-gray-400 group-hover:text-gray-900' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <span class="font-medium text-center md:text-left truncate w-full">Siparişlerim</span>
                        </a>

                        <a href="{{ route('account.profile') }}" class="group flex flex-col md:flex-row items-center justify-center md:justify-start px-2 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm rounded-xl transition-all {{ request()->routeIs('account.profile') ? 'bg-black text-white shadow-sm md:shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 bg-white md:bg-transparent border border-gray-100 md:border-transparent' }}">
                            <svg class="w-4 h-4 md:w-5 md:h-5 md:mr-3 mb-1 md:mb-0 {{ request()->routeIs('account.profile') ? 'text-white' : 'text-gray-400 group-hover:text-gray-900' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="font-medium text-center md:text-left truncate w-full">Ayarlar</span>
                        </a>
                    </nav>
                </div>

                <div class="hidden md:block mt-8">
                    <button wire:click="logout" class="flex items-center w-full px-4 py-3 text-sm font-medium text-red-500 rounded-xl hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Oturumu Kapat
                    </button>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 p-3.5 sm:p-6 md:p-10 lg:p-14">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
