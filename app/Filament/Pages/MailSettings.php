<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Mail\OrderConfirmationMail;
use App\Mail\ShippingUpdateMail;
use App\Mail\WelcomeMail;
use App\Mail\AbandonedCartReminderMail;
use App\Mail\StockBackMail;
use App\Mail\GibInvoiceMail;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class MailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-envelope';
    }

    public static function getNavigationLabel(): string
    {
        return 'E-Posta Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Site Yönetimi';
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'E-Posta Sunucusu & Gönderim Ayarları';
    }

    protected string $view = 'filament.pages.mail-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username',
            'smtp_password', 'smtp_from_address', 'smtp_from_name',
            'mail_order_confirmation', 'mail_shipping_update', 'mail_welcome',
            'mail_abandoned_cart', 'mail_stock_notification', 'mail_invoice'
        ])->pluck('value', 'key')->toArray();

        $this->form->fill([
            'smtp_host' => $settings['smtp_host'] ?? config('mail.mailers.smtp.host', ''),
            'smtp_port' => $settings['smtp_port'] ?? config('mail.mailers.smtp.port', 465),
            'smtp_encryption' => $settings['smtp_encryption'] ?? 'ssl',
            'smtp_username' => $settings['smtp_username'] ?? config('mail.mailers.smtp.username', ''),
            'smtp_password' => $settings['smtp_password'] ?? '',
            'smtp_from_address' => $settings['smtp_from_address'] ?? config('mail.from.address', ''),
            'smtp_from_name' => $settings['smtp_from_name'] ?? config('mail.from.name', 'Patenli Ayakkabılar'),
            'mail_order_confirmation' => filter_var($settings['mail_order_confirmation'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_shipping_update' => filter_var($settings['mail_shipping_update'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_welcome' => filter_var($settings['mail_welcome'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_abandoned_cart' => filter_var($settings['mail_abandoned_cart'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_stock_notification' => filter_var($settings['mail_stock_notification'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mail_invoice' => filter_var($settings['mail_invoice'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Section::make('SMTP Sunucu Ayarları')
                    ->schema([
                        TextInput::make('smtp_host')
                            ->label('SMTP Sunucusu')
                            ->placeholder('mail.patenliayakkabilar.com'),
                        TextInput::make('smtp_port')
                            ->label('Port')
                            ->numeric()
                            ->default(465)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ((int)$state === 465) {
                                    $set('smtp_encryption', 'ssl');
                                } elseif ((int)$state === 587) {
                                    $set('smtp_encryption', 'tls');
                                }
                            })
                            ->helperText('Plesk/cPanel için önerilen SSL: 465 (STARTTLS: 587)'),
                        Select::make('smtp_encryption')
                            ->label('Şifreleme')
                            ->options([
                                'ssl' => 'SSL (Port 465)',
                                'tls' => 'TLS (Port 587)',
                                'none' => 'Şifreleme Yok',
                            ])
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state === 'ssl') {
                                    $set('smtp_port', 465);
                                } elseif ($state === 'tls') {
                                    $set('smtp_port', 587);
                                }
                            }),
                        TextInput::make('smtp_username')
                            ->label('Kullanıcı Adı (E-Posta)')
                            ->email(),
                        TextInput::make('smtp_password')
                            ->label('Şifre')
                            ->password()
                            ->revealable(),
                        TextInput::make('smtp_from_address')
                            ->label('Varsayılan Gönderici Adresi')
                            ->email(),
                        TextInput::make('smtp_from_name')
                            ->label('Gönderici Adı'),
                        TextInput::make('test_recipient_email')
                            ->label('Test Maili Alıcı Adresi')
                            ->placeholder('info@patenliayakkabilar.com veya kişisel e-postanız (örn. Gmail)')
                            ->email()
                            ->helperText('Test mailinin iletileceği adres. Boş bırakırsanız mevcut oturum açan kullanıcı e-postası kullanılır.')
                            ->columnSpan(2),
                    ])->columns(2),

                Section::make('E-Posta Hesapları')
                    ->description('Sistemde kullanılan varsayılan e-posta adresleri (Bilgi Amaçlıdır)')
                    ->schema([
                        Placeholder::make('info')
                            ->label('Genel Bilgilendirme')
                            ->content('info@patenliayakkabilar.com'),
                        Placeholder::make('siparis')
                            ->label('Sipariş Bildirimleri')
                            ->content('siparis@patenliayakkabilar.com'),
                        Placeholder::make('destek')
                            ->label('Müşteri Destek')
                            ->content('destek@patenliayakkabilar.com'),
                        Placeholder::make('isbirligi')
                            ->label('İş Birliği & Pazarlama')
                            ->content('isbirligi@patenliayakkabilar.com'),
                    ])->columns(2),

                Section::make('E-Posta Bildirim Ayarları')
                    ->schema([
                        Toggle::make('mail_order_confirmation')
                            ->label('Sipariş Onay Maili')
                            ->default(true),
                        Toggle::make('mail_shipping_update')
                            ->label('Kargo Güncelleme Maili')
                            ->default(true),
                        Toggle::make('mail_welcome')
                            ->label('Hoşgeldin Maili')
                            ->default(true),
                        Toggle::make('mail_abandoned_cart')
                            ->label('Terk Edilen Sepet Hatırlatması')
                            ->default(true),
                        Toggle::make('mail_stock_notification')
                            ->label('Stok Bildirimi')
                            ->default(true),
                        Toggle::make('mail_invoice')
                            ->label('Fatura E-Postası')
                            ->default(true),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Placeholder ve geçici test alanlarını kaydetme
            if (in_array($key, ['info', 'siparis', 'destek', 'isbirligi', 'stats', 'test_recipient_email'])) {
                continue;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => is_bool($value) ? ($value ? '1' : '0') : $value]);
        }

        // SMTP config cache'ini temizle
        Cache::forget('mail_smtp_settings');

        Notification::make()
            ->title('Ayarlar kaydedildi')
            ->success()
            ->send();
    }

    protected function prepareSmtpAndRecipient(): array
    {
        $data = $this->form->getState();

        if (!empty($data['smtp_host'])) {
            $port = (int)($data['smtp_port'] ?? 465);
            $encryption = $data['smtp_encryption'] ?? ($port === 465 ? 'ssl' : 'tls');

            config([
                'mail.mailers.smtp.host' => $data['smtp_host'],
                'mail.mailers.smtp.port' => $port,
                'mail.mailers.smtp.username' => $data['smtp_username'] ?? null,
                'mail.mailers.smtp.password' => $data['smtp_password'] ?? null,
            ]);

            if (!empty($data['smtp_from_address'])) {
                config([
                    'mail.from.address' => $data['smtp_from_address'],
                    'mail.from.name' => $data['smtp_from_name'] ?? config('mail.from.name', 'Patenli Ayakkabılar®'),
                ]);
            }

            if ($port === 465 || ($encryption === 'ssl' && $port !== 587)) {
                config(['mail.mailers.smtp.scheme' => 'smtps']);
            } else {
                config(['mail.mailers.smtp.scheme' => 'smtp']);
            }

            app('mail.manager')->purge('smtp');
        }

        $recipient = !empty($data['test_recipient_email'])
            ? trim($data['test_recipient_email'])
            : (auth()->user()?->email ?: ($data['smtp_username'] ?? 'info@patenliayakkabilar.com'));

        $fromAddress = $data['smtp_from_address'] ?: ($data['smtp_username'] ?: config('mail.from.address'));
        $fromName = $data['smtp_from_name'] ?: config('mail.from.name', 'Patenli Ayakkabılar®');

        return [$recipient, $fromAddress, $fromName];
    }

    public function sendTestMail(): void
    {
        $this->sendNotificationTest('system');
    }

    public function sendNotificationTest(string $type): void
    {
        try {
            [$recipient, $fromAddress, $fromName] = $this->prepareSmtpAndRecipient();

            switch ($type) {
                case 'system':
                    Mail::send('emails.test-mail', [
                        'fromAddress' => $fromAddress,
                        'recipient' => $recipient,
                    ], function ($message) use ($recipient, $fromAddress, $fromName) {
                        if (!empty($fromAddress)) {
                            $message->from($fromAddress, $fromName);
                        }
                        $message->to($recipient)
                            ->subject('Patenli Ayakkabılar® — E-Posta Sistemi Doğrulama Bildirimi (' . now()->timezone('Europe/Istanbul')->format('H:i') . ')');
                    });
                    $title = 'Genel Sistem Test Maili Gönderildi ✅';
                    break;

                case 'order_confirmation':
                    $order = $this->getSampleOrder($recipient);
                    Mail::to($recipient)->send(new OrderConfirmationMail($order));
                    $title = 'Sipariş Onay Test Maili Gönderildi ✅';
                    break;

                case 'shipping_update':
                    $order = $this->getSampleOrder($recipient);
                    $order->status = 'shipped';
                    $order->shipping_company = $order->shipping_company ?: 'Yurtiçi Kargo';
                    $order->cargo_tracking_code = $order->cargo_tracking_code ?: 'YK' . rand(1000000000, 9999999999);
                    Mail::to($recipient)->send(new ShippingUpdateMail($order));
                    $title = 'Kargo Güncelleme Test Maili Gönderildi ✅';
                    break;

                case 'welcome':
                    $user = auth()->user() ?? User::first();
                    if (!$user) {
                        $user = new User([
                            'name' => 'Değerli Müşterimiz',
                            'email' => $recipient,
                        ]);
                    }
                    Mail::to($recipient)->send(new WelcomeMail($user));
                    $title = 'Hoşgeldin Test Maili Gönderildi ✅';
                    break;

                case 'abandoned_cart':
                    $cart = $this->getSampleCart();
                    Mail::to($recipient)->send(new AbandonedCartReminderMail($cart));
                    $title = 'Terk Edilen Sepet Test Maili Gönderildi ✅';
                    break;

                case 'stock':
                    $product = Product::with('variants')->first();
                    if (!$product) {
                        $product = new Product(['name' => 'Flash 4 Tekerlekli Işıklı Paten']);
                    }
                    $variant = $product->variants?->first();
                    Mail::to($recipient)->send(new StockBackMail($product, $variant));
                    $title = 'Stok Bildirim Test Maili Gönderildi ✅';
                    break;

                case 'invoice':
                    $order = $this->getSampleOrder($recipient);
                    Mail::to($recipient)->send(new GibInvoiceMail($order));
                    $title = 'E-Arşiv Fatura Test Maili Gönderildi ✅';
                    break;

                default:
                    throw new \InvalidArgumentException('Geçersiz bildirim türü.');
            }

            Notification::make()
                ->title($title)
                ->body($recipient . ' adresine başarıyla iletildi.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Test Maili Gönderilemedi ❌')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getSampleOrder(string $recipient): Order
    {
        $order = Order::with('items')->latest()->first();

        if ($order) {
            $cloned = clone $order;
            $cloned->customer_email = $recipient;
            return $cloned;
        }

        $dummy = new Order();
        $dummy->id = 1;
        $dummy->order_number = 'ORD-' . date('Y') . '-' . rand(1000, 9999);
        $dummy->customer_name = auth()->user()?->name ?? 'Ahmet Yılmaz';
        $dummy->customer_email = $recipient;
        $dummy->customer_phone = '0555 123 45 67';
        $dummy->shipping_address = 'Bağdat Caddesi No: 120 D: 5';
        $dummy->shipping_district = 'Kadıköy';
        $dummy->shipping_city = 'İstanbul';
        $dummy->payment_method = 'credit_card';
        $dummy->shipping_method = 'yurtici';
        $dummy->shipping_company = 'Yurtiçi Kargo';
        $dummy->cargo_tracking_code = 'YK9876543210';
        $dummy->subtotal = 1899.90;
        $dummy->shipping_cost = 0.00;
        $dummy->discount_amount = 0.00;
        $dummy->total_amount = 1899.90;
        $dummy->status = 'processing';
        $dummy->created_at = now();

        $item = new OrderItem();
        $item->product_name = 'Flash 4 Tekerlekli Işıklı Patenli Ayakkabı';
        $item->variant_info = 'Siyah-Kırmızı / 36 Numara';
        $item->quantity = 1;
        $item->unit_price = 1899.90;
        $item->total_price = 1899.90;

        $dummy->setRelation('items', collect([$item]));

        return $dummy;
    }

    protected function getSampleCart(): object
    {
        $cart = Cart::with(['items.product.images', 'items.variant'])->latest()->first();

        if ($cart && $cart->items->isNotEmpty()) {
            return $cart;
        }

        $product = Product::with('images')->first();

        return (object)[
            'user' => (object)[
                'name' => auth()->user()?->name ?? 'Değerli Müşterimiz',
            ],
            'items' => collect([
                (object)[
                    'product' => $product ?: (object)[
                        'name' => 'Flash 4 Tekerlekli Işıklı Patenli Ayakkabı',
                        'price' => 1899.90,
                        'discount_price' => 1699.90,
                        'images' => collect([]),
                    ],
                    'variant' => (object)[
                        'color' => 'Siyah / Kırmızı',
                        'size' => '36',
                    ],
                    'quantity' => 1,
                ]
            ]),
        ];
    }
}
