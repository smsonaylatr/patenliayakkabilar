<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendCustomerMail')
                ->label('E-Posta Gönder')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->modalHeading('Müşteriye E-Posta Gönder')
                ->modalWidth('lg')
                ->form([
                    TextInput::make('recipient_email')
                        ->label('Alıcı E-Posta Adresi')
                        ->default(fn () => $this->record->customer_email)
                        ->email()
                        ->required(),
                    Select::make('mail_type')
                        ->label('E-Posta Şablonu')
                        ->options(fn () => [
                            'order_confirmation' => '🛍️ Sipariş Onay E-Postası (OrderConfirmationMail)',
                            'shipping_update' => '🚚 Kargo & Durum Bilgilendirme (ShippingUpdateMail)',
                            'invoice' => $this->record->gib_invoice_status === 'signed' ? '📄 Resmi GİB E-Arşiv Fatura (GibInvoiceMail)' : '📄 Standart Sipariş Faturası (OrderInvoiceMail)',
                        ])
                        ->default('shipping_update')
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $order = $this->record;
                    $email = trim($data['recipient_email']);
                    try {
                        switch ($data['mail_type']) {
                            case 'order_confirmation':
                                Mail::to($email)->send(new \App\Mail\OrderConfirmationMail($order));
                                $title = 'Sipariş Onay E-Postası';
                                break;
                            case 'shipping_update':
                                Mail::to($email)->send(new \App\Mail\ShippingUpdateMail($order));
                                $title = 'Kargo & Durum E-Postası';
                                break;
                            case 'invoice':
                                if ($order->gib_invoice_status === 'signed') {
                                    Mail::to($email)->send(new \App\Mail\GibInvoiceMail($order));
                                    $title = 'GİB E-Arşiv Fatura E-Postası';
                                } else {
                                    $invoiceUrl = route('orders.gib-invoice', $order);
                                    Mail::to($email)->send(new \App\Mail\OrderInvoiceMail($order, $invoiceUrl));
                                    $title = 'Fatura E-Postası';
                                }
                                break;
                            default:
                                throw new \Exception('Geçersiz şablon seçimi.');
                        }
                        Notification::make()
                            ->title("{$title} Gönderildi ✅")
                            ->body("{$email} adresine e-posta başarıyla ulaştırıldı.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('E-Posta Gönderilemedi ❌')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
