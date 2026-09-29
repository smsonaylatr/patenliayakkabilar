<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center gap-3">
            <x-filament::button type="submit">
                Ayarları Kaydet
            </x-filament::button>

            <x-filament::button color="gray" wire:click="sendTestMail" type="button">
                ✉️ Genel Test Maili Gönder
            </x-filament::button>
        </div>
    </form>

    <!-- Bildirim Şablonları Test Merkezi -->
    <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-100 dark:border-white/5 gap-2">
            <div>
                <h3 class="text-base font-semibold text-gray-950 dark:text-white flex items-center gap-2">
                    <span class="text-lg">🧪</span> Bildirim Şablonları Test Merkezi
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Yukarıdaki formda belirttiğiniz <strong>Test Maili Alıcı Adresi</strong>'ne dilediğiniz bildirim şablonunun anlık testini gönderebilirsiniz.
                </p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            
            <!-- 1. Sipariş Onay -->
            <div class="flex flex-col justify-between p-4 rounded-lg border border-gray-200/80 dark:border-white/10 bg-gray-50/60 dark:bg-white/[0.02] hover:border-primary-500/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🛍️</span>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Sipariş Onay Maili</h4>
                            <span class="text-[11px] text-gray-400 font-mono">OrderConfirmationMail</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2.5">
                        Müşterinin siparişi tamamlandığında giden ürün, adres, tutar ve kargo bilgilerini içeren onay şablonu.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-end">
                    <x-filament::button size="sm" color="primary" wire:click="sendNotificationTest('order_confirmation')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 2. Kargo Güncelleme -->
            <div class="flex flex-col justify-between p-4 rounded-lg border border-gray-200/80 dark:border-white/10 bg-gray-50/60 dark:bg-white/[0.02] hover:border-primary-500/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🚚</span>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Kargo Güncelleme Maili</h4>
                            <span class="text-[11px] text-gray-400 font-mono">ShippingUpdateMail</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2.5">
                        Sipariş kargoya verildiğinde takip kodu ve ilerleme çubuğuyla birlikte müşteriye giden bilgilendirme şablonu.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-end">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('shipping_update')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 3. Hoşgeldin Maili -->
            <div class="flex flex-col justify-between p-4 rounded-lg border border-gray-200/80 dark:border-white/10 bg-gray-50/60 dark:bg-white/[0.02] hover:border-primary-500/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">👋</span>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Hoşgeldin Maili</h4>
                            <span class="text-[11px] text-gray-400 font-mono">WelcomeMail</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2.5">
                        Siteye yeni üye olan müşterilere gönderilen kurumsal karşılama ve avantajlar şablonu.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-end">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('welcome')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 4. Terk Edilen Sepet -->
            <div class="flex flex-col justify-between p-4 rounded-lg border border-gray-200/80 dark:border-white/10 bg-gray-50/60 dark:bg-white/[0.02] hover:border-primary-500/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🛒</span>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Terk Edilen Sepet Maili</h4>
                            <span class="text-[11px] text-gray-400 font-mono">AbandonedCartReminderMail</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2.5">
                        Sepetinde ürün bırakıp ayrılan kullanıcılara otomatik giden ürün hatırlatması şablonu.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-end">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('abandoned_cart')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 5. Stok Bildirimi -->
            <div class="flex flex-col justify-between p-4 rounded-lg border border-gray-200/80 dark:border-white/10 bg-gray-50/60 dark:bg-white/[0.02] hover:border-primary-500/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📦</span>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Stok Bildirim Maili</h4>
                            <span class="text-[11px] text-gray-400 font-mono">StockBackMail</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2.5">
                        Tükenen bir ürün tekrar stoğa girdiğinde stok alarmı kuran müşterilere gönderilen şablon.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-end">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('stock')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 6. E-Arşiv Fatura -->
            <div class="flex flex-col justify-between p-4 rounded-lg border border-gray-200/80 dark:border-white/10 bg-gray-50/60 dark:bg-white/[0.02] hover:border-primary-500/50 transition-colors">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🧾</span>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">E-Arşiv Fatura Maili</h4>
                            <span class="text-[11px] text-gray-400 font-mono">GibInvoiceMail</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2.5">
                        Resmi GİB E-Arşiv faturası kesildiğinde müşteriye iletilen fatura indirme ve görüntüleme şablonu.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200/60 dark:border-white/5 flex items-center justify-end">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('invoice')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

        </div>
    </div>
</x-filament-panels::page>

