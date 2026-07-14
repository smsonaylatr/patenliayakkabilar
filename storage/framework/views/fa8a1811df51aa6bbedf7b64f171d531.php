<div class="min-h-[80vh] bg-gray-50/50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden min-h-[600px] flex flex-col md:flex-row">
            
            <!-- Elegant Sidebar -->
            <div class="w-full md:w-72 bg-gray-50/50 border-r border-gray-100 p-8 flex flex-col">
                <div class="mb-12">
                    <h2 class="text-xs font-bold tracking-widest text-gray-400 uppercase mb-1">Hesabım</h2>
                    <p class="text-xl font-black text-gray-900"><?php echo e(auth()->user()->name); ?></p>
                </div>
                
                <nav class="flex-1 space-y-2">
                    <a href="<?php echo e(route('account.dashboard')); ?>" class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all <?php echo e(request()->routeIs('account.dashboard') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('account.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-900'); ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="font-medium">Genel Bakış</span>
                    </a>
                    
                    <a href="<?php echo e(route('account.orders')); ?>" class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all <?php echo e(request()->routeIs('account.orders') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('account.orders') ? 'text-white' : 'text-gray-400 group-hover:text-gray-900'); ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="font-medium">Siparişlerim</span>
                    </a>

                    <a href="<?php echo e(route('account.profile')); ?>" class="group flex items-center px-4 py-3 text-sm rounded-xl transition-all <?php echo e(request()->routeIs('account.profile') ? 'bg-black text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('account.profile') ? 'text-white' : 'text-gray-400 group-hover:text-gray-900'); ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="font-medium">Ayarlar</span>
                    </a>
                </nav>

                <div class="mt-8">
                    <button wire:click="logout" class="flex items-center w-full px-4 py-3 text-sm font-medium text-red-500 rounded-xl hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Oturumu Kapat
                    </button>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 p-8 md:p-12 lg:p-16">
                <?php echo e($slot); ?>

            </div>
            
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\components\account-layout.blade.php ENDPATH**/ ?>