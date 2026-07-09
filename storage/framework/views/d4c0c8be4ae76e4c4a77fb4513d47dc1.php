<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActive && $imageUrl): ?>
        <div x-data="sitePopup()" 
             x-show="showPopup"
             x-cloak
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="closePopup()"
                 class="relative max-w-lg w-full mx-4 sm:mx-auto bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Close Button -->
                <button @click="closePopup()" class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-black/20 hover:bg-black/40 text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Image / Link -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($linkUrl): ?>
                    <a href="<?php echo e($linkUrl); ?>" class="block w-full h-full">
                        <img src="<?php echo e($imageUrl); ?>" alt="Site Pop-up" class="w-full h-auto object-cover block">
                    </a>
                <?php else: ?>
                    <img src="<?php echo e($imageUrl); ?>" alt="Site Pop-up" class="w-full h-auto object-cover block">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('sitePopup', () => ({
                    showPopup: false,
                    init() {
                        const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD formatında bugün
                        const lastSeen = localStorage.getItem('sitePopupLastSeen');

                        if (lastSeen !== today) {
                            // Göstermeden önce azıcık bekle ki hemen pat diye çıkmasın
                            setTimeout(() => {
                                this.showPopup = true;
                            }, 1000);
                        }
                    },
                    closePopup() {
                        this.showPopup = false;
                        const today = new Date().toISOString().split('T')[0];
                        localStorage.setItem('sitePopupLastSeen', today);
                    }
                }))
            })
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\frontend\site-popup.blade.php ENDPATH**/ ?>