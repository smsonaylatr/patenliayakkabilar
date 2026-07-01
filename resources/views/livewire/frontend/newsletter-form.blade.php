<div class="bg-brand-dark py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Fırsatları Kaçırmayın!</h2>
        <p class="text-gray-400 mb-8">Yeni ürünler ve size özel indirimlerden ilk siz haberdar olun.</p>
        <form wire:submit.prevent="subscribe" class="flex flex-col sm:flex-row gap-4 justify-center">
            <input type="email" wire:model="email" placeholder="E-posta adresiniz..." required class="px-6 py-4 rounded-xl flex-grow max-w-md focus:ring-2 focus:ring-brand-orange outline-none">
            <button type="submit" class="bg-brand-orange text-white font-bold px-8 py-4 rounded-xl hover:bg-orange-600 transition-colors">Abone Ol</button>
        </form>
    </div>
</div>
