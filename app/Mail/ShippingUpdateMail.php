<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ShippingUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $statusText;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->statusText = match($order->status) {
            'preparing' => 'Hazırlanıyor',
            'shipped' => 'Kargoya Verildi',
            'delivered' => 'Teslim Edildi',
            default => $order->status,
        };
    }

    public function envelope(): Envelope
    {
        $emoji = match($this->order->status) {
            'shipped' => '🚚',
            'delivered' => '✅',
            default => '📦',
        };

        [$fromAddress, $fromName] = \App\Models\Setting::getMailSender('shipping', 'Patenli Ayakkabılar®', 'siparis@patenliayakkabilar.com');

        $defaultSubject = "{$emoji} Siparişiniz {$this->statusText} - #{$this->order->order_number}";
        $subject = \App\Models\Setting::getMailSubject('shipping', $defaultSubject, [
            '{siparis_no}' => $this->order->order_number,
            '{order_number}' => $this->order->order_number,
            '{kargo_durumu}' => $this->statusText,
            '{durum}' => $this->statusText,
            '{kargo_firmasi}' => $this->order->shipping_company ?: 'Kargo',
            '{takip_kodu}' => $this->order->cargo_tracking_code ?: '',
            '{musteri_adi}' => $this->order->customer_name ?: 'Değerli Müşterimiz',
            '{magaza_adi}' => 'Patenli Ayakkabılar®',
        ]);

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shipping-update',
        );
    }
}
