<div class="min-h-[70vh] bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 text-center relative">
        <!-- Confetti Background (Optional) -->
        <div class="absolute inset-0 pointer-events-none opacity-20 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-green-300 via-transparent to-transparent"></div>
        
        <div class="relative z-10">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 mb-2">Siparişiniz Alındı!</h2>
            <p class="text-gray-500 mb-8 text-lg">Bizi tercih ettiğiniz için teşekkür ederiz.</p>
            
            <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Sipariş Numaranız</p>
                <p class="text-2xl font-black text-brand-orange"><?php echo e($order_number); ?></p>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">Siparişinizin durumunu "Sipariş Takip" sayfasından veya e-posta adresinize gönderilen linkten takip edebilirsiniz.</p>
                </div>
            </div>
            
            <div class="flex flex-col gap-3">
                <a href="<?php echo e(route('home')); ?>" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all active:scale-[0.98] inline-block">
                    Alışverişe Devam Et
                </a>
                <a href="#" class="w-full bg-transparent hover:bg-gray-50 text-gray-700 font-bold py-4 px-6 rounded-xl transition-all inline-block border border-gray-200">
                    Siparişimi Görüntüle
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\frontend\order-success.blade.php ENDPATH**/ ?>