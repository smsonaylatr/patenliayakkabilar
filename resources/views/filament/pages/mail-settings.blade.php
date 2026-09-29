<x-filament-panels::page>
    <style>
        .test-center-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .test-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 16px;
            border-radius: 10px;
            border: 1px solid rgba(156, 163, 175, 0.2);
            background: rgba(156, 163, 175, 0.04);
            transition: all 0.2s ease;
        }
        .test-card:hover {
            border-color: #ff4e00;
            background: rgba(255, 78, 0, 0.04);
        }
        .test-card-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .test-card-icon {
            font-size: 26px;
            line-height: 1;
        }
        .test-card-title {
            font-size: 14px;
            font-weight: 600;
        }
        .test-card-badge {
            font-size: 11px;
            font-family: monospace;
            opacity: 0.6;
            margin-top: 2px;
        }
        .test-card-desc {
            font-size: 12px;
            opacity: 0.75;
            margin-top: 10px;
            line-height: 1.5;
        }
        .test-card-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid rgba(156, 163, 175, 0.15);
        }
    </style>

    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 24px; display: flex; align-items: center; gap: 12px;">
            <x-filament::button type="submit">
                Ayarları Kaydet
            </x-filament::button>

            <x-filament::button color="gray" wire:click="sendTestMail" type="button">
                ✉️ Genel Test Maili Gönder
            </x-filament::button>
        </div>
    </form>

    <!-- Bildirim Şablonları Test Merkezi -->
    <x-filament::section style="margin-top: 32px;">
        <x-slot name="heading">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">🧪</span>
                <span style="font-size: 16px; font-weight: 600;">Bildirim Şablonları Test Merkezi</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Yukarıdaki formda belirttiğiniz <strong>Test Maili Alıcı Adresi</strong>'ne dilediğiniz bildirim şablonunun anlık testini gönderebilirsiniz.
        </x-slot>

        <div class="test-center-grid">
            
            <!-- 1. Sipariş Onay -->
            <div class="test-card">
                <div>
                    <div class="test-card-header">
                        <span class="test-card-icon">🛍️</span>
                        <div>
                            <div class="test-card-title">Sipariş Onay Maili</div>
                            <div class="test-card-badge">OrderConfirmationMail</div>
                        </div>
                    </div>
                    <div class="test-card-desc">
                        Müşterinin siparişi tamamlandığında giden ürün, adres, tutar ve kargo bilgilerini içeren onay şablonu.
                    </div>
                </div>
                <div class="test-card-footer">
                    <x-filament::button size="sm" color="primary" wire:click="sendNotificationTest('order_confirmation')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 2. Kargo Güncelleme -->
            <div class="test-card">
                <div>
                    <div class="test-card-header">
                        <span class="test-card-icon">🚚</span>
                        <div>
                            <div class="test-card-title">Kargo Güncelleme Maili</div>
                            <div class="test-card-badge">ShippingUpdateMail</div>
                        </div>
                    </div>
                    <div class="test-card-desc">
                        Sipariş kargoya verildiğinde takip kodu ve ilerleme çubuğuyla birlikte müşteriye giden bilgilendirme şablonu.
                    </div>
                </div>
                <div class="test-card-footer">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('shipping_update')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 3. Hoşgeldin Maili -->
            <div class="test-card">
                <div>
                    <div class="test-card-header">
                        <span class="test-card-icon">👋</span>
                        <div>
                            <div class="test-card-title">Hoşgeldin Maili</div>
                            <div class="test-card-badge">WelcomeMail</div>
                        </div>
                    </div>
                    <div class="test-card-desc">
                        Siteye yeni üye olan müşterilere gönderilen kurumsal karşılama ve avantajlar şablonu.
                    </div>
                </div>
                <div class="test-card-footer">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('welcome')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 4. Terk Edilen Sepet -->
            <div class="test-card">
                <div>
                    <div class="test-card-header">
                        <span class="test-card-icon">🛒</span>
                        <div>
                            <div class="test-card-title">Terk Edilen Sepet Maili</div>
                            <div class="test-card-badge">AbandonedCartReminderMail</div>
                        </div>
                    </div>
                    <div class="test-card-desc">
                        Sepetinde ürün bırakıp ayrılan kullanıcılara otomatik giden ürün hatırlatması şablonu.
                    </div>
                </div>
                <div class="test-card-footer">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('abandoned_cart')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 5. Stok Bildirimi -->
            <div class="test-card">
                <div>
                    <div class="test-card-header">
                        <span class="test-card-icon">📦</span>
                        <div>
                            <div class="test-card-title">Stok Bildirim Maili</div>
                            <div class="test-card-badge">StockBackMail</div>
                        </div>
                    </div>
                    <div class="test-card-desc">
                        Tükenen bir ürün tekrar stoğa girdiğinde stok alarmı kuran müşterilere gönderilen şablon.
                    </div>
                </div>
                <div class="test-card-footer">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('stock')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

            <!-- 6. E-Arşiv Fatura -->
            <div class="test-card">
                <div>
                    <div class="test-card-header">
                        <span class="test-card-icon">🧾</span>
                        <div>
                            <div class="test-card-title">E-Arşiv Fatura Maili</div>
                            <div class="test-card-badge">GibInvoiceMail</div>
                        </div>
                    </div>
                    <div class="test-card-desc">
                        Resmi GİB E-Arşiv faturası kesildiğinde müşteriye iletilen fatura indirme ve görüntüleme şablonu.
                    </div>
                </div>
                <div class="test-card-footer">
                    <x-filament::button size="sm" color="gray" wire:click="sendNotificationTest('invoice')" type="button">
                        Test Gönder
                    </x-filament::button>
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-panels::page>
