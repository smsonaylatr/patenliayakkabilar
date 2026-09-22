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

class ShippingUpdateMail extends Mailable implements ShouldQueue
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
        return new Envelope(
            from: new Address('siparis@patenliayakkabilar.com', 'Patenli Ayakkabılar'),
            subject: "{$emoji} Siparişiniz {$this->statusText} - #{$this->order->order_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shipping-update',
        );
    }
}
